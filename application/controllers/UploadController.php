<?php

/*
This is an authenticated controller
*/
require_once("AlgorithmsIO/classes/AlgorithmsIO_Zend_Controller_Action.php");
class UploadController extends \AlgorithmsIO\AlgorithmsIO_Zend_Controller_Action
{

    public function init()
    {
	$this->debug=false;
	$this->debug("DEBUG201207251152: Initialized");

        $this->_helper->layout()->disableLayout();
        parent::init();
        /* Initialize action controller here */

    }
 
    public function __call($method, $args)
    {
	$this->error("ERROR201207251154: $method is not defined in ".get_Class($this));
        if ('Action' == substr($method, -6)) {
            // If the action method was not found, render the error
            // template
            return $this->render('error');

	    // Forward to another page
            //return $this->_forward('index');
        }

        // all other methods throw an exception
        throw new Exception('Invalid method "'
                            . $method
                            . '" called',
                                500);

    }
    
    public function get_upload_handler() {
        $filename = $_FILES["files"]["name"][0]; // TODO: FIXME: For now we worry only about the first filename and assume they are all of the same type --  MRR20121104
        $this->debug("201211041403: Filename=".$filename);
        preg_match('/\.([^\.]*)$/', $filename, $matches);
        $fileext = strtolower($matches[1]);
        
        switch($fileext) {
            case "csv": 
                require_once("AlgorithmsIO/upload.csv.class.php");
                $this->upload_handler = new CSV_UploadHandler();
                break;
            case "zzzzjpg":
                require_once("AlgorithmsIO/upload.jpg.class.php");
                $this->upload_handler = new CSV_UploadHandler();
                break;                
            default:
                require_once("AlgorithmsIO/upload.unknown.class.php");
                $this->upload_handler = new Unknown_UploadHandler();
        }
        $this->upload_handler->setZendController($this);
        return $this->upload_handler;
    }
    
    public function postAction() 
    {
        $this->debug("DEBUG201207261418: In postaction");
        $this->getResponse()
            ->setHeader('Pragma: no-cache')
            ->setHeader('Cache-Control: no-store, no-cache, must-revalidate')
            ->setHeader('Content-Disposition: inline; filename="files.json"')
            ->setHeader('X-Content-Type-Options: nosniff')
            ->setHeader('Access-Control-Allow-Origin: *')
            ->setHeader('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE')
            ->setHeader('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');
        
        $this->get_upload_handler();
        
        if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
            $this->upload_handler->delete();
        } else {
            $this->upload_handler->post();
            $files_objs=$this->upload_handler->get_file_objects();
            //ob_start();
            //var_dump($files_objs);
            //error_log(ob_get_contents());
            //ob_end_clean();
            //error_log("DEBUG201205291109: ".$upload_handler->options["upload_dir"]);
        }

    }
    
    public function delete() {
        $this->upload_handler->delete();
    }
    
    public function get() {
        $this->upload_handler->get();    
    }
    
    public function indexAction()
    {
        $this->getResponse()
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Cache-Control','no-store, no-cache, must-revalidate')
            ->setHeader('Content-Disposition','inline; filename="files.json"')
            ->setHeader('X-Content-Type-Options','nosniff')
            ->setHeader('Access-Control-Allow-Origin','*')
            ->setHeader('Access-Control-Allow-Methods','OPTIONS, HEAD, GET, POST, PUT, DELETE')
            ->setHeader('Access-Control-Allow-Headers','X-File-Name, X-File-Type, X-File-Size');
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'OPTIONS':
                break;
            case 'HEAD':
            case 'GET':
                $this->upload_handler->get();
                break;
            case 'POST':
                $this->get_upload_handler();
                if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
                    $this->upload_handler->delete();
                } else {
                    $this->upload_handler->post();
                    $files_objs=$this->upload_handler->get_file_objects();
                    ob_start();
                    var_dump($files_objs);
                    //error_log(ob_get_contents());
                    ob_end_clean();
                    //error_log("DEBUG201205291109: ".$upload_handler->options["upload_dir"]);
                }
                break;
            case 'DELETE':
                $this->upload_handler->delete();
                break;
            default:
                header('HTTP/1.1 405 Method Not Allowed');
        }

exit;
        Zend_Loader::loadClass('AuthToken');
        $authToken = new AuthToken();

	$this->debug(sprintf("DEBUG201206061127: username=%s, id=%s",$this->username,$this->user_id_seq));

        $this->view->username = $this->username;
        $this->view->usersAuthTokens = $this->usersAuthTokens;
	$this->view->authentication = $this->authObj;
	$this->view->security = $this->security;
	$algo = new \AlgorithmsIO\Algorithm();
	$algo->authobj($this->authObj);
	$this->view->algorithm = $algo;
	$path = realpath(APPLICATION_PATH.'/../library/AlgorithmsIO/');
	$this->view->localization = simplexml_load_file($path."/localization/en.xml");
        $config = new Zend_Config_Ini(
            APPLICATION_PATH . '/configs/application.ini',
            APPLICATION_ENV
        );
        $this->view->url = $config->app->params->url;
    }
    public function cancelAction(){

    }
    public function offering1Action(){

        $this->view->username = $this->username;
    }
}
