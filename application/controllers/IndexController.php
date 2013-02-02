<?php

class IndexController extends Zend_Controller_Action
{
    private $isLoggedIn;

    public function init()
    {
        /* Initialize action controller here */
    }
    public function preDispatch(){

        // Authentication Piece
        $this->auth = Zend_Auth::getInstance();

        if(!$this->auth->hasIdentity()){
            $this->isLoggedIn = 'false';  
        } else {
            // User is valid and logged in
            $this->isLoggedIn = 'true';
        }
    }
    public function __call($method, $args)
    {
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
    public function indexAction()
    {
        $this->view->isLoggedIn = $this->isLoggedIn;
    }
}
