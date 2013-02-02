<?php

/*
This is an authenticated controller
*/
require_once("AlgorithmsIO/classes/AlgorithmsIO_Zend_Controller_Action.php");
class PreferencesController extends \AlgorithmsIO\AlgorithmsIO_Zend_Controller_Action
{

    public function init()
    {
	$this->debug=false;
	//$this->debug=false;
	$this->debug("DEBUG201206061128: Initialized");
        $this->_helper->layout()->disableLayout();
        /* Initialize action controller here */

    }
    
    public function dashboardAction() {
        $this->indexAction();
    }
    public function indexAction()
    {
        // Most of the view stuff is defined in AlgorithmsIO_Zend_Controller_Action->setViewDefaults()
        // This method just runs the index view
    }
    
    public function saveAction() {
        $this->_helper->viewRenderer->setNoRender();
        
        $user = $this->user();
        if($user->validate_password($_REQUEST["oldpassword"])) {
            // The oldpassword is valid
            if($_REQUEST["newpassword"]==$_REQUEST["confirmpassword"]) {
                // Password has been changed
                $user->set_password($_REQUEST["newpassword"]);
            }
            $user->set_firstName($_REQUEST["firstName"]);
            $user->set_lastName($_REQUEST["lastName"]);
            $em = $this->entityManager();
            $em->persist($user);
            $em->flush();
            $this->_redirect( "/dashboard/index" );
        }
        $this->_redirect( "/preferences/index" );
    }
    
    public function cancelAction(){

    }
    public function offering1Action(){

        $this->view->username = $this->username;
    }
}
