<?php

/*
This is an unauthenticated photos action
*/

class SignupController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */

    }
     public function preDispatch(){
/*
        // Authentication Piece
        $this->auth = Zend_Auth::getInstance();

        if(!$this->auth->hasIdentity()){
                 $this->_redirect( '/login?f=' . $this->_request->getRequestUri() );
        } else {
                // User is valid and logged in
                $this->username = $this->auth->getIdentity();
        }
*/
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
    public function indexAction(){
        // Initial sign up form

        if( $this->getRequest()->isPost() ){
            // User is trying to submit a signup

            $betaCode = $this->_request->getParam( 'betacode' );

            // Check if beta code is a valid one
            if( $betaCode == 'bunny' ){

                require_once('AlgorithmsIO/classes/Users.php');
                $users = new Users();

                $results = $users->initialSignUp( $this->_request ); 

                $this->view->hasSignedUp = 'true';
            }else{
                $this->view->hasSignedUp = 'false';
                $this->view->betaCodeError = 'Invalid Alpha Code';
            }
        }else{
            $this->view->hasSignedUp = 'false';
        }
    }
    public function verifyAction(){
        // Verify the user's sign up email address

        require_once('AlgorithmsIO/classes/Users.php');
        $users = new Users();

        $email = $this->_request->getParam( 'email' );
        $unique_id = $this->_request->getParam( 'id' );

        $this->view->verificationStatus = 'unknown';

        if( $email != '' && $unique_id != '' ){

            // Setup unit test db vars
            if( Zend_Session::$_unitTestEnabled )
                $users->unitTestVerificationInsert();

            $this->view->verificationStatus = $users->activateAccount( $email, $unique_id );
        }
    }
}
