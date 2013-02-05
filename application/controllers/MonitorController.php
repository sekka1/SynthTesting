<?php

/*
This is an authenticated photos action
*/

class MonitorController extends Zend_Controller_Action
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
				$this->user_id_seq = $this->getUserID($this->username);
				$this->isLoggedIn = 'true';
        }
     }
	 private function getUserID($username){
	 	$userTable = new Zend_Db_Table('users');
		$select = $userTable->select()->where('username="'.$username.'"');
		$resultsRow = $userTable->fetchAll($select);
		return $resultsRow[0]->id;
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

	public function xendesktopAction(){
		
		$action = $this->_request->getParam('ac');

		if($action == 'save'){
			$this->xendesktopSave($this->_request);
		}
	}
	private function xendesktopSave($request){
		
		$data['user_id'] = $this->user_id_seq;
		$data['name'] = $request->getParam('name');
		$data['class_name'] = 'XenDesktop';
		$data['schedule'] = $request->getParam('schedule');
		$data['is_active'] = 1;
		$data['notification_id'] = $request->getParam('notification_id');
		$data['created'] = new Zend_Db_Expr('NOW()');
		$data['last_modified'] = new Zend_Db_Expr('NOW()');
		
		$def['baseURL'] = $request->getParam('baseURL');
		$def['domain'] = $request->getParam('domain');
		$def['username'] = $request->getParam('username');
		$def['password'] = $request->getParam('password');
		$def['version'] = $request->getParam('version');
		$data['definition'] = json_encode($def);
		
		$monitorsTable = new Zend_Db_Table('monitors');
		$monitorsTable->insert($data);
	}
	/*
	 * Generic delete a Monitor functionality
	 */
	public function deleteAction(){
		
		$id = (int)$this->_request->getParam('id');
		
		if(is_numeric($id)){
			$monitorsTable = new Zend_Db_Table('monitors');
			$where = array(
						'user_id = ?' => $this->user_id_seq,
						'id = ?' => $id
						);
			$monitorsTable->delete($where);
		}
	}
}
