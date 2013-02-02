<?php
/*
 * Created by MRR on 2012-05-29
 * Extends the jQuery upload handler class
 */

require_once("upload.class.php");
require_once("Entity/DataSources.php");
//include_once("datasource.class.php");

class Unknown_UploadHandler extends UploadHandler
{
        public function setZendController($zendController) {
            $this->_zendController = $zendController;
        }
        
	public function upload_finished($file) {

		$this->process_file($file);

		error_log("DEBUG201205291151: Finished file ".$file->name);	
	}

	public function process_file($file) {

            require_once("Entity/DataSources.php");
            $datasource = new \AlgorithmsIO\Entity\DataSources();
            $entityManager = $this->_zendController->entityManager();
            $entityManager->persist($datasource);
            $entityManager->flush();
            $datasource->set_status("pending");
            $datasource->set_filesystem_name($datasource->get_id());
            $datasource->set_originalFilename($file->name);
            $datasource->set_name($file->name);
            $datasource->set_privacy("private");
            $datasource->set_size($file->size);
            $datasource->set_user($this->_zendController->user());
            
            preg_match('/\.([^\.]*)$/', $file->name, $matches);
            $fileext = strtolower($matches[1]);
            error_log("DEBUG201211051617: fileext=".$fileext);
            
            $datasource->set_type("".$fileext."");
            $doctrine = \Zend_Registry::get("doctrine");
            $entityManager = $doctrine->getEntityManager();           
            $entityManager->persist($datasource);
            $entityManager->flush();

            $tfilename = "datasource_".$datasource->get_id().".".$fileext; //MRR20120803 - Hardcoded csv extension for now. That the way to go?
            rename("/tmp/".$file->name, "/tmp/".$tfilename);
            Zend_Loader::loadClass( 'S3Usage' );
            $this->s3 = new S3Usage();
            $basename = $this->s3->upload( "/tmp/".$tfilename );

            $datasource->set_filesystem_name($tfilename);
            $datasource->set_status("ready"); // Mark the dataset as ready since upload to S3 has completed.
            $entityManager->persist($datasource);
            $entityManager->flush();
            
            $maxlines = 50000;
            //list($params, $linesprocessed) = $this->processDataSourceParams("/tmp/".$tfilename, $maxlines);
            /*
             * This is type unknown, so we don't attempt to look at the data much 
            
            $datasource->set_outputParams(json_encode($params));
            $datasource->set_columns(count($params));
            if($linesprocessed < $maxlines) {
                // We processed all of the rows available
                $datasource->set_rows($linesprocessed);
                // If rows is not set, then we know min/max field values may be unreliable
            } 
            $datasource->set_rowsProcessed($linesprocessed);
            */
            $entityManager->persist($datasource);
            $entityManager->flush();            
            return;		
	}

        public function processDataSourceParams($source_file, $maxlines=50000) {

                return array($columnDefs, $linecount);
        }

}

?>
