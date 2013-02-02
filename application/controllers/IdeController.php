<?php

/*
This is an authenticated controller
*/
require_once("AlgorithmsIO/classes/AlgorithmsIO_Zend_Controller_Action.php");
class IdeController extends \AlgorithmsIO\AlgorithmsIO_Zend_Controller_Action
{

    public function init()
    {
	$this->debug=false;
	//$this->debug=false;

        //require_once("AlgorithmsIO/upload.cvs.class.php");
        //$this->upload_handler = new CVS_UploadHandler();
        //$this->upload_handler->setZendController($this);
        $this->_helper->layout()->disableLayout();
        parent::init();
        /* Initialize action controller here */

    }
 
    public function __call($method, $args)
    {
	$this->error("ERROR201207311442: $method is not defined in ".get_Class($this));
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
    
    public function postAction() 
    {
        $this->error("ERROR201207311443: Not Implemented");

    }
    
    public function delete() {
        $this->error("ERROR201207311444: Not Implemented");
    }
    
    public function get() {
        $this->error("ERROR201207311445: Not Implemented");
    }
    
    public function indexAction()
    {
        $this->view->username = $this->username;
        $this->view->usersAuthTokens = $this->usersAuthTokens;
        $this->view->token = $this->authToken()->get_token();
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
    
    public function inputparamsAction(){
        //FIXME: I'd really like this functionality to happen in JS and retrieve directly from API server - MRR20120824
        $this->_helper->viewRenderer->setNoRender(true);        
        $algorithm_id = $this->_request->getParam( 'id' );
        $entityManager = $this->entityManager();
        require_once("AlgorithmsIO/Entity/Algorithms.php");
        //$algorithms = $entityManager->getRepository('AlgorithmsIO\Entity\Algorithms')->findOneBy(array('user'=>$this->user(), 'id'=>$algorithm_id));
        $algo = $entityManager->getRepository('AlgorithmsIO\Entity\Algorithms')->findOneBy(array('id'=>$algorithm_id));

        //print_r($params);
        $result = array("data"=>"No Parameters","metadata"=>array("datatype"=>"None","description"=>"No Parameters")); // Default to failure
        if($algo){
            $params = $algo->get_inputParams();
            if($params) {
                $params = json_decode($params, true);
                $result = $this->paramToJsTreeArray($params);
            }
        } 
        echo json_encode($result);
    }

    public function outputparamsAction(){
        //FIXME: I'd really like this functionality to happen in JS and retrieve directly from API server - MRR20120824
        $this->_helper->viewRenderer->setNoRender(true);        
        $algorithm_id = $this->_request->getParam( 'id' );
        $entityManager = $this->entityManager();
        require_once("AlgorithmsIO/Entity/Algorithms.php");
        //$algorithms = $entityManager->getRepository('AlgorithmsIO\Entity\Algorithms')->findOneBy(array('user'=>$this->user(), 'id'=>$algorithm_id));
        $algo = $entityManager->getRepository('AlgorithmsIO\Entity\Algorithms')->findOneBy(array('id'=>$algorithm_id));

        //print_r($params);
        $result = array("data"=>"No Parameters","metadata"=>array("datatype"=>"None","description"=>"No Parameters")); // Default to failure
        if($algo){
            $params = $algo->get_outputParams();
            if($params) {
                $params = json_decode($params, true);
                $result = $this->paramToJsTreeArray($params);
            }
        } 
        echo json_encode($result);
    }

    public function librariesAction(){
        $this->warning("WARNING201208011751: Running demo libraries");
    }
    
    public function testAction(){
        $this->warning("WARNING201208021130: Running demo libraries");
        $this->indexAction();
    }    
    
    public function cancelAction(){
        $this->error("ERROR201207311446: Not Implemented");
    }
    
    //This function takes an Algorithm's input/output params structure and returns it in a format suitable for JSTree
    public function paramToJsTreeArray($params) {
        $return = array();
        
        foreach ($params as $key=>$value) {

            if(isset($params[$key]["children"])) {
                // Call recursive to get children
                $children = paramToJsonArray($params[$key]["children"]);
                $attr = $params[$key];
                unset($attr["children"]);
                $return[]=array(
                    "data"      =>$key." [".$params[$key]["datatype"]."]",
                    "children"  =>$children, 
                    "metadata"  =>$params[$key],
                );
            } else {
                $return[]=array(
                    "data"      =>$key." [".$params[$key]["datatype"]."]",
                    "metadata"  =>$params[$key],
                );
            }
             
        }
        return $return;
    }
}
