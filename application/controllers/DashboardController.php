<?php

/*
This is an authenticated controller
*/
require_once("AlgorithmsIO/classes/AlgorithmsIO_Zend_Controller_Action.php");
class DashboardController extends \AlgorithmsIO\AlgorithmsIO_Zend_Controller_Action
{

    public function init()
    {
	//$this->debug=true;
	$this->debug=false;
	$this->debug("DEBUG201206061128: DashboardController Initialized");
        $this->_helper->layout()->disableLayout();
        parent::init();
        /* Initialize action controller here */

    }
    
    public function dashboardAction() {
        $this->indexAction();
    }
    public function indexAction()
    {
        require_once('AlgorithmsIO/classes/AuthToken.php');
        $authToken = new AuthToken();

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
    public function editdashboardAction() {
        
    }
    public function cancelAction(){

    }
    public function offering1Action(){

        $this->view->username = $this->username;
    }
}
