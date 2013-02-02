<?php

/*
This is an authenticated photos action
*/

class DashboardController extends Zend_Controller_Action
{

    private $username;
    private $user_id_seq;
	
	private $isLoggedIn;

    public function init()
    {
        /* Initialize action controller here */

    }
     public function preDispatch(){

        // Authentication Piece
        $this->auth = Zend_Auth::getInstance();

        if(!$this->auth->hasIdentity()){
			$this->_redirect( '/login?f=' . $this->_request->getRequestUri() );
        } else {
                // User is valid and logged in
                $this->username = $this->auth->getIdentity();
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
	//print( $this->client_id_seq . ' - ' . $this->user_id_seq . '<br/>' );
		$this->view->isLoggedIn = $this->isLoggedIn;
		
		$this->view->hourString = $this->createHourString();
    }
	private function createHourString(){
		
		$currentHourString = date('H');
		
		for($i=1; $i<24; $i++){
			$currentHourString .= ' ' . date('H', strtotime('-'.$i.' hour'));
		}
		
		return $currentHourString;
	}

}
