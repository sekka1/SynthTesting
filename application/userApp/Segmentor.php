<?php
// Creates the arbitrary mapping that Mahout needs

class Segmentor
{
    private $auth;
    private $authToken;
    private $generic;
    private $s3;
    private $debug;
    private $datasources;
    private $mapping;
    private $csvFiles;
    private $utilities;
    private $fileUploadUrl;
    private $upload_file_outcome_array;

    public function __construct(){

        $this->fileUploadUrl = 'http://www.algorithms.io/data/index/class/DataSources/method/upload/';

        $this->debug = false;

        // Model for generic database table
        Zend_Loader::loadClass('Generic');
        $this->generic = new Generic();

        Zend_Loader::loadClass( 'S3Usage' );
        $this->s3 = new S3Usage();
    
        Zend_Loader::loadClass( 'DataSources' );
        $this->datasources = new DataSources();

        Zend_Loader::loadClass( 'Mapping' );
        $this->mapping = new Mapping();

        Zend_Loader::loadClass( 'CSVFiles' );
        $this->csvFiles = new CSVFiles();

        Zend_Loader::loadClass( 'Utilities' );                                                                                 
        $this->utilities = new Utilities(); 

        $this->upload_file_outcome_array = array();
    }
    public function setDebugTrue(){

        $this->debug = true;
    }
    public function byField( $request_vars ){

        $datasource_id_seq = $request_vars->getParam( 'datasource_id_seq' );
        $segmentField = $request_vars->getParam( 'segmentField' );
        $userOwnsFile = json_decode( $this->datasources->userOwnsFile( $request_vars ), true );
        $this->authToken = $request_vars->getParam( 'authToken' );

        if( is_numeric( $datasource_id_seq ) && $userOwnsFile['results'] > 0 && $segmentField != '' ){

            // Set the files datasource_id_seq                                                                        
            $this->csvFiles->setDatasourceFile( $datasource_id_seq );                                                 
            $this->csvFiles->setAuthToken( $this->authToken ); 

            // Get the file so it is on the local file system                                                         
            $this->csvFiles->getFile();

            // Get the header positions of the segmentField
            $segmentField_id_position = $this->csvFiles->getItemHeadPosition( $segmentField );

            $isEndOfFile = false;                                                                                     
            $i = 1; 

            $segmentsArray = array(); // An array holding all the various segments it found in this file
            $csvFilesArray = array(); // Holds all the various segments csfFiles object

            while( ! $isEndOfFile ){

                $data = $this->csvFiles->returnOne();

                // Set end of file var      
                $isEndOfFile = $data['isLastRow'];

                if( ! $isEndOfFile && isset( $data['data'] ) ){

                    // Check if the user specified segmentField is in the data array
                    if( isset( $data['data'][$segmentField_id_position] ) ){

                        $data['data'][$segmentField_id_position] = strtolower( $data['data'][$segmentField_id_position] );

                        // Setting blank segments
                        if( $data['data'][$segmentField_id_position] == '' )
                            $data['data'][$segmentField_id_position] = 'blank';

                        // Check if we have seen this segment already
                        if( in_array( strtolower( $data['data'][$segmentField_id_position] ), $segmentsArray ) ){
                            // Already seen this segment, put item in that segment file

                            // Write this to the current output
                            $csvFilesArray[$data['data'][$segmentField_id_position]]->writeContent( $csvFilesArray[$data['data'][$segmentField_id_position]]->convertArrayToCSV( $data['data'] ) );
                        }else{
                            // First time we have seen this segment.  Create a new file for this and write it out

                            $csvFilesArray[$data['data'][$segmentField_id_position]] = new CSVFiles();

                            $csvFilesArray[$data['data'][$segmentField_id_position]]->openFileForWrite();

                            // Write header to this file
                            $csvFilesArray[$data['data'][$segmentField_id_position]]->writeContent( $csvFilesArray[$data['data'][$segmentField_id_position]]->convertArrayToCSV( $this->csvFiles->getHeaders() ) );

                            // Write the data row to this file
                            $csvFilesArray[$data['data'][$segmentField_id_position]]->writeContent( $csvFilesArray[$data['data'][$segmentField_id_position]]->convertArrayToCSV( $data['data'] ) );

                            // Push this current segment into the $segmentsArray array
                            array_push( $segmentsArray, strtolower( $data['data'][$segmentField_id_position] ) );
                        }
                    }
                }

                /////////////////////                                                                             
                $i++;                                                                                 

                //if( $i == 2000 )
                //    break; 
            }

            // Upload and delete each file in the array
            foreach( $csvFilesArray as $segment_name=>$aFileObject ){

                // Upload
                $this->uploadFile( $segment_name, $aFileObject );

                // Delete
                $aFileObject->cleanUp();
            }
        }

        // Clean up the CSVFile                                                                                           
        $this->csvFiles->cleanUp();

        return json_encode( $this->upload_file_outcome_array );
    }
    public function uploadFile( $segment_name, $aFileObject ){
        // Upload file via curl
        
        // Variables for the upload POST
        $url = $this->fileUploadUrl;
        $post_params['theFile'] = '@'.$aFileObject->getFileWritePathName();
        $post_params['authToken'] = $this->authToken;
        $post_params['type'] = 'Auto Generated - '.$segment_name;
        $post_params['friendly_name'] = $segment_name;
        $post_params['friendly_description'] = 'Segment: '.$segment_name;
        $post_params['version'] = '1';

        $outcome = $this->utilities->curlPost( $url, $post_params );

        $outcome = str_replace( " \n", "", $outcome );

        $output_data['datasource_id_seq'] = $outcome;
        $output_data['type'] = $post_params['type'];
        $output_data['friendly_name'] = $post_params['friendly_name'];
        $output_data['friendly_description'] = $post_params['friendly_description'];
        $output_data['version'] = $post_params['version']; 

        if( is_numeric( $outcome ) )
            array_push( $this->upload_file_outcome_array, $output_data );

    }
}
