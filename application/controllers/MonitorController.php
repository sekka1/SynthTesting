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
		
		$this->view->isLoggedIn = $this->isLoggedIn;
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
	public function apirestAction(){
		
		$action = $this->_request->getParam('ac');

		if($action == 'save'){
			$this->apiRestSave($this->_request);
		}
	}
	/*
	 * Saving and editing a XenDesktop monitoring
	 */
	private function xendesktopSave($request){
		
		$data['user_id'] = $this->user_id_seq;
		$data['name'] = $request->getParam('name');
		$data['class_name'] = 'xendesktop';
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
		
		$monitor_id = $this->_request->getParam('monitor_id');
		
		$monitorsTable = new Zend_Db_Table('monitors');
		
		if(is_numeric($monitor_id)){
			// Editing an already existing row
			$where[] = $monitorsTable->getAdapter()->quoteInto('user_id = ?', $this->user_id_seq);
			$where[] = $monitorsTable->getAdapter()->quoteInto('id = ?', $monitor_id);
			$monitorsTable->update($data, $where);
		}else{
			// New insert
			$monitorsTable->insert($data);
		}
	}
	/*
	 * Saving and editing a API REST monitoring
	 */
	private function apiRestSave($request){
		
		// Standard monitoring params
		$data['user_id'] = $this->user_id_seq;
		$data['class_name'] = 'apirest';
		$data['schedule'] = (int)$request->getParam('schedule');
		$data['is_active'] = 1;
		$data['name'] = $request->getParam('name');
		$data['notification_id'] = (int)$request->getParam('notification_id');
		$data['created'] = new Zend_Db_Expr('NOW()');
		$data['last_modified'] = new Zend_Db_Expr('NOW()');
		
		// Monitor specific params
		$def['url'] = $request->getParam('url');
		$def['post_params'] = json_decode($request->getParam('post_params'), true);
		$def['headers'] = json_decode($request->getParam('headers'), true);
		$def['regex_check'] = $request->getParam('regex_check');
		
		// Encode user specific params into json
		$data['definition'] = json_encode($def);
		
		// This parameter would be set if it was an edit action.  Else it would not
		$monitor_id = $this->_request->getParam('monitor_id');
		
		$monitorsTable = new Zend_Db_Table('monitors');
		
		if(is_numeric($monitor_id)){
			// Editing an already existing row
			$where[] = $monitorsTable->getAdapter()->quoteInto('user_id = ?', $this->user_id_seq);
			$where[] = $monitorsTable->getAdapter()->quoteInto('id = ?', $monitor_id);
			$monitorsTable->update($data, $where);
		}else{
			// New insert
			$monitorsTable->insert($data);
		}
	}
	/*
	 * Generic delete a Monitor functionality
	 */
	public function deleteAction(){
		$this->view->isLoggedIn = $this->isLoggedIn;
		
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
	/*
	 * Pause Monitor
	 */
	 public function pauseAction(){
	 	
		$id = (int)$this->_request->getParam('id');
		
	 	if(is_numeric($id)){
			$monitorsTable = new Zend_Db_Table('monitors');
			$where = array(
						'user_id = ?' => $this->user_id_seq,
						'id = ?' => $id
						);
			$data['is_active'] = 0;
			$monitorsTable->update($data, $where);
		}
	 }
	/*
	 * UnPause Monitor
	 */
	 public function unpauseAction(){
	 	
		$id = (int)$this->_request->getParam('id');
		
	 	if(is_numeric($id)){
			$monitorsTable = new Zend_Db_Table('monitors');
			$where = array(
						'user_id = ?' => $this->user_id_seq,
						'id = ?' => $id
						);
			$data['is_active'] = 1;
			$monitorsTable->update($data, $where);
		}
	 }
	public function editAction(){
		$this->view->isLoggedIn = $this->isLoggedIn;
		
		$monitor_id = (int)$this->_request->getParam('id');
		
		$monitorTable = new Zend_Db_Table('monitors');
		$select = $monitorTable->select()->where('user_id='.$this->user_id_seq)->where('id='.$monitor_id);
		$resultsRow = $monitorTable->fetchRow($select);
		
		$this->view->notificationList = $this->getNotificationList();
		$this->view->monitor_id = $monitor_id;
		$this->view->monitor = $resultsRow;
	}
	/*
	 * Retrieves a list of notifications this user owns
	 */
	 private function getNotificationList(){
	 	$notificationTable = new Zend_Db_Table('notifications');
		$select = $notificationTable->select()
									->where('user_id ='.$this->user_id_seq);
		$resultsRow = $notificationTable->fetchAll($select);
		$output = array();
		foreach($resultsRow as $aRow){
			$temp['id'] = $aRow->id;
			$temp['name'] = $aRow->name;
			array_push($output,$temp);
		}
		return $output;
	 }
}
