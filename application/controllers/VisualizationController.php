<?php

/*
This is an authenticated controller
*/
require_once("AlgorithmsIO/classes/AlgorithmsIO_Zend_Controller_Action.php");
class VisualizationController extends \AlgorithmsIO\AlgorithmsIO_Zend_Controller_Action
{

    public function init()
    {
	$this->debug=false;
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

    public function indexAction()
    {
        $job_id = $this->_request->getParam( 'job_id' ) ? $this->_request->getParam( 'job_id' ) : 0;
        $visID = $this->_request->getParam( 'visualization_id') ? $this->_request->getParam( 'visualization_id') : 1;
        $ds_id = $this->_request->getParam( 'datasource_id') ? $this->_request->getParam( 'datasource_id') : 0;
           
        if ($ds_id) {
            // Our datasource and visualization were passed in. We just trust it'll work for now. - MRR 20121018
            $this->view->datasource_id=$ds_id;
            $this->view->visualization_id = $visID;            
        } else if($job_id) {
            # We have a job, so let's see if the job has a flow with a visualizaiton and datasource
            require_once("Entity/Jobs.php");
            require_once("Entity/Visualizations.php");
            $entityManager = $this->entityManager();
            $user_id = $this->user()->get_id();
            $jobEntity = $entityManager->getRepository('AlgorithmsIO\Entity\Jobs')->findOneBy(array('id'=>$job_id)); // TODO: Need to add security - MRR20121009 
            $flowEntity = $jobEntity->get_flow();
            $resultDataSource = $jobEntity->get_datasource_id();
            
            if(!$resultDataSource) {
                # We don't have a datasource, dunno what to do
                # $this->error("ERROR201210091351: The job ".$jobEntity->get_id()." does not have a datasource. Perhaps it is still running?");
                $resultDataSource = 2510; // Hardcoded for testing - REMOVE ME - TODO: MRR20121009
            }
            $this->view->datasource_id=$resultDataSource;
            if($flowEntity) {
                $visID = $jobEntity->get_visualization()->get_id();                
                $this->debug("DEBUG201212011756: visid=".$visID);
                $this->view->visualization_id = $visID;
            } else {
                $visID=1; # TODO: FIXME: Hardcoded for now
                $visEntity = $entityManager->getRepository('AlgorithmsIO\Entity\Visualizations')->findOneBy(array('id'=>$visID));
                $this->view->visualization = $visEntity;
                $this->view->visualization_id = $visID;
            }
            $this->view->jobEntity = $jobEntity;
        }
        
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
