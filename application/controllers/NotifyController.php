<?php

/*
This is an authenticated photos action
*/

class NotifyController extends Zend_Controller_Action
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
    public function indexAction(){}
	public function emailsaveAction(){
		
		$name = $this->_request->getParam('name');
		$def['is_authenticated'] = $this->_request->getParam('is_authenticated');
		$def['authUser'] = $this->_request->getParam('authUser');
		$def['authPassword'] = $this->_request->getParam('authPassword');
		$def['smtp'] = $this->_request->getParam('smtp');
		$def['fromEmail'] = $this->_request->getParam('fromEmail');
		$def['toEmail'] = $this->_request->getParam('toEmail');
		$def['subject_prefix'] = $this->_request->getParam('subject_prefix');
		$def['domain'] = $this->_request->getParam('domain');
		
		$notificationsTable = new Zend_Db_Table('notifications');
		
		$data = array(
					//'user_id' => $user_id,
					'name' => $name,
					'type' => 'email',
					'definition' => json_encode($def),
					'created' => new Zend_Db_Expr('NOW()'),
					'last_modified' => new Zend_Db_Expr('NOW()')
				);
				
		$notificationsTable->insert($data);
		
	}
}
