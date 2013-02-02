<?php

/*
This is an authenticated controller
*/
require_once("AlgorithmsIO/classes/AlgorithmsIO_Zend_Controller_Action.php");
class FlowsController extends \AlgorithmsIO\AlgorithmsIO_Zend_Controller_Action
{

    public function init()
    {
	$this->debug=false;
	//$this->debug=false;
	$this->debug("DEBUG201206061128: Initialized");
        $this->_helper->layout()->disableLayout();
        parent::init();
        
        /* Initialize action controller here */

    }
    
    public function indexAction()
    {
        require_once('AlgorithmsIO/classes/AuthToken.php');
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
