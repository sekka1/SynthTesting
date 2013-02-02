<?php
// Creates the arbitrary mapping that Mahout needs

class CombineFiles
{
    private $auth;
    private $generic;
    private $s3;
    private $debug;
    private $datasources;
    private $mapping;
    private $csvFiles;

    public function __construct(){

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
    }
    public function setDebugTrue(){

        $this->debug = true;
    }
    public function append( $request_vars ){

        $source_datasource_id_seq = $request_vars->getParam( 'source_datasource_id_seq' );
        $addition_datasource_id_seq = $request_vars->getParam( 'addition_datasource_id_seq' );

        // Check both file is owned by this user
        $data['datasource_id_seq'] = $source_datasource_id_seq;
        $request_vars->setParams( $data );
        $userOwnsFile_source = json_decode( $this->datasources->userOwnsFile( $request_vars ), true );
        $file_attributes = json_decode( $this->datasources->getAFile( $request_vars ), true );

        $data['datasource_id_seq'] = $addition_datasource_id_seq;
        $request_vars->setParams( $data );
        $userOwnsFile_source_addition = json_decode( $this->datasources->userOwnsFile( $request_vars ), true );

        $authToken = $request_vars->getParam( 'authToken' );

        if( is_numeric( $source_datasource_id_seq ) && $userOwnsFile_source['results'] > 0 && $userOwnsFile_source_addition['results'] > 0 ){

                // Set the files source_datasource_id_seq
                $this->csvFiles->setDatasourceFile( $source_datasource_id_seq );
                $this->csvFiles->setAuthToken( $authToken );

                // Set the files addition_datasource_id_seq
                $additionCsvFile = new CSVFiles();
                $additionCsvFile->setDatasourceFile( $addition_datasource_id_seq );
                $additionCsvFile->setAuthToken( $authToken );

                // Get the file so it is on the local file system
                $this->csvFiles->getFile();
                $additionCsvFile->getFile();
                
                // Create Output File
                $outputFile = new CSVFiles();
                $outputFile->openFileForWrite();

                $search_column_id_position = $this->csvFiles->getItemHeadPosition( 'none' );// So object gets the header
                $header_count = count( $this->csvFiles->getHeaders() );

                $isEndOfFile = false;
                $isFirstItteration = true;

                // Loop through the source file and put it in the out file
                while( ! $isEndOfFile ){

                    $data = $this->csvFiles->returnOne();

                    // Set end of file var
                    $isEndOfFile = $data['isLastRow'];

                    if( ! $isEndOfFile && isset( $data['data'] ) && count( $data['data'] ) == $header_count ){
                        // Check one more time b/c we have to actually get the row before we know if it is the end

                        if( $isFirstItteration ){
                            // First itteration, put header into the out file

                            $outputFile->writeContent( $this->csvFiles->convertArrayToCSV( $this->csvFiles->getHeaders() ) );

                            $isFirstItteration = false;      
                        }

                        // Put the line of data into the output file
                        $outputFile->writeContent( $outputFile->convertArrayToCSV( $data['data'] ) );
                    }
                }

                $isEndOfFile = false;

                // Loop through the second file and put it in the output file
                while( ! $isEndOfFile ){

                    $data = $additionCsvFile->returnOne();

                    // Set end of file var
                    $isEndOfFile = $data['isLastRow'];

                    if( ! $isEndOfFile && isset( $data['data'] ) && count( $data['data'] ) == $header_count ){
                        // Check one more time b/c we have to actually get the row before we know if it is the end

                        // Put the line of data into the output file
                        $outputFile->writeContent( $outputFile->convertArrayToCSV( $data['data'] ) );
                    }                                                                                                                
                }

                // Upload Rec File to S3
                $newTempFileName = $outputFile->getTempWriteDir().$file_attributes[0]['filesystem_name'];
                $didCopy = copy( $outputFile->getFileWritePathName(), $newTempFileName );

                if( $didCopy )
                    $this->s3->upload( $newTempFileName );

                // Remove Temp File
                unlink( $newTempFileName );

                // Close out $recCsvFile
                $this->csvFiles->cleanUp();
                $additionCsvFile->cleanUp();
                $outputFile->cleanUp();
        }

        return '[]';
    }
}
