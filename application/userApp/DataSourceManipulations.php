<?php
// This class performs various manipulations to a datasource file

class DataSourceManipulations
{
    private $auth;
    private $generic;
    private $debug;
    private $datasources;
    private $s3;
    private $csvFiles;

    public function __construct( $auth ){

        $this->debug = false;

        // Init Auth Class
        $this->auth = $auth;

        // Model for generic database table
        Zend_Loader::loadClass('Generic');
        $this->generic = new Generic();

        // Datasource class
        Zend_Loader::loadClass('DataSources');
        $this->datasources = new DataSources();

        Zend_Loader::loadClass( 'S3Usage' );
        $this->s3 = new S3Usage();

        Zend_Loader::loadClass( 'CSVFiles' );
        $this->csvFiles = new CSVFiles();
    }
    public function setDebugTrue(){

        $this->debug = true;
    }
    public function searchAndReplaceColumn( $request_vars ){
        // Search and replace a value in a column in a file

        $datasource_id_seq = $request_vars->getParam( 'datasource_id_seq' );  
        $column = $request_vars->getParam( 'column' );
        $search = $request_vars->getParam( 'search' );
        $replace = $request_vars->getParam( 'replace' );

        $result['result'] = 'Failed';

        if( is_numeric( $datasource_id_seq ) &&
                $column != '' &&
                $search != '' ){
    
            // Set the request var with this datasource_id_seq and get the file
            $data['datasource_id_seq'] = $datasource_id_seq;
            $request_vars->setParams( $data );

            $datasource_file_content = $this->datasources->getSourceFile( $request_vars );
            $datasource_file_array = json_decode( $datasource_file_content, true );

            for( $i=0; $i<=count( $datasource_file_array['data'] ); $i++ ){
            // Foreach row, do the search and replace on the column specified

                if( isset( $datasource_file_array['data'][$i][$column] ) ){
                    // Perform the search and replace on the column
                    $datasource_file_array['data'][$i][$column] = preg_replace( '/'.$search.'/', $replace, $datasource_file_array['data'][$i][$column] ); 
                }
            }

            // Update the source file with the new changes
            $data['json_data'] = json_encode( $datasource_file_array );
            $request_vars->setParams( $data );

            $result = json_decode( $this->datasources->updateSourceFile( $request_vars ) );
        }
        return json_encode( $result );
    }
    public function searchAndReplaceColumnCSV( $request_vars ){
        // Search and replace a value in a column in a file

        $datasource_id_seq = $request_vars->getParam( 'datasource_id_seq' );
        $column = $request_vars->getParam( 'column' );
        $search = $request_vars->getParam( 'search' );
        $replace = $request_vars->getParam( 'replace' );

        // Set Auth Token
        $data['authToken'] = $this->auth->getAuthToken();
        $request_vars->setParams( $data );

        $result['result'] = 'Failed';

        if( is_numeric( $datasource_id_seq ) &&
                $column != '' &&
                $search != '' ){

            // Set the files datasource_id_seq                                                                      
            $this->csvFiles->setDatasourceFile( $datasource_id_seq );                                               
            $this->csvFiles->setAuthToken( $data['authToken'] );

            // Get the file so it is on the local file system                                                       
            $this->csvFiles->getFile(); 

            // Get the header positions of the column the user wants to search and replace
            $search_column_id_position = $this->csvFiles->getItemHeadPosition( $column );

            $header_count = count( $this->csvFiles->getHeaders() );

            // Create Output File
            $outputFile = new CSVFiles();
            $outputFile->openFileForWrite(); 

            $isEndOfFile = false;
            $isFirstItteration = true;

            while( ! $isEndOfFile ){                                                                                

                $data = $this->csvFiles->returnOne();                                                               

                // Set end of file var                                                                              
                $isEndOfFile = $data['isLastRow'];

                if( ! $isEndOfFile && isset( $data['data'] ) && count( $data['data'] ) == $header_count ){ 

                    if( $isFirstItteration ){
                        // Insert header only on the first itteration
                        $outputFile->writeContent( $this->csvFiles->convertArrayToCSV( $this->csvFiles->getHeaders() ) );

                        $isFirstItteration = false;
                    }

                    // Replace the desired field per the user's input
                    $data['data'][$search_column_id_position] = preg_replace( '/'.$search.'/', $replace, $data['data'][$search_column_id_position] );

                    $outputFile->writeContent( $outputFile->convertArrayToCSV( $data['data'] ) );
                }
            }

            // Replace the datasource file on S3
            $file_attributes = json_decode( $this->datasources->getAFile( $request_vars ), true );

            $newTempFileName = $outputFile->getTempWriteDir().$file_attributes[0]['filesystem_name'];
            $didCopy = copy( $outputFile->getFileWritePathName(), $newTempFileName );
            if( $didCopy ){                                 
                $this->s3->upload( $newTempFileName );

            }

            // Remove Temp File                                                                                     
            unlink( $newTempFileName );

            // Close out $outputFile                                                                                
            $outputFile->cleanUp();
            $this->csvFiles->cleanUp();

        }
        return '[]';
    }
    public function csvToJson( $request_vars ){
        // Convert a CSV file to a JSON file

        set_time_limit(0);
        ini_set('memory_limit', '6024M');

        $datasource_id_seq = $request_vars->getParam( 'datasource_id_seq' );
        $authToken = $request_vars->getParam( 'authToken' );
        $user_id_seq = $request_vars->getParam( 'user_id_seq' );

        $result['result'] = 'Failed';

        if( is_numeric( $datasource_id_seq ) ){

            // Check if this file belongs to this user
            $userOwnsFile = json_decode( $this->datasources->userOwnsFile( $request_vars ), true );

            if( $userOwnsFile['results'] ){
                // User owns this file

                $datasource_file_content = $this->datasources->getSourceFile( $request_vars ); 

                // Spit file by a line return
                $lines_array = preg_split( '/\n/', $datasource_file_content, -1, PREG_SPLIT_NO_EMPTY );

                if( count( $lines_array ) > 2 ){
                    // Greater than 2, need a header row and at lease 1 data row

                    $isFirstRow = true; // This is the header row
                    $header_array = array(); // Holds only the header values
                    $data_array['data'] = array(); // Holds the entire to be json array

                    foreach( $lines_array as $anUnSplitRow ){

                        // Remove the \n at the end of the string if any
                        //$anUnSplitRow = preg_replace( '/\n/','', $anUnSplitRow );

                        // Parse out the CSV to an array
                        $values_array = preg_split( '/,/', $anUnSplitRow );
            
                        if( $isFirstRow ){
                            // Header row

                            $isFirstRow = false;

                            // Remove last spot in array if it is the \n
                            if( preg_match( '/\n/', $values_array[count( $values_array )-1] ) == 1 )
                                array_pop( $values_array );
                            //$values_array[count( $values_array )-1] = preg_replace( '/\n/','', $values_array[count( $values_array )-1] );

                            // Save Header
                            $header_array = $values_array;
                        }else{
                            // Data rows

                            $temp_array = array();

                            for( $i=0; $i<count( $header_array ); $i++ ){

                                if( isset( $values_array[$i] ) ){

                                    $temp_array[trim($header_array[$i])] = trim( $values_array[$i] );
                                }
                            }
                            array_push( $data_array['data'], $temp_array );
                        }
                    }
                }

                // Update this file on S3 with the new json data structure
                $data['json_data'] = json_encode( $data_array );
                $request_vars->setParams( $data );
                $result = json_decode( $this->datasources->updateSourceFile( $request_vars ) );

           }
        }
        return json_encode( $result );
    }

}
