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
    public function indexAction(){
    	$this->view->isLoggedIn = $this->isLoggedIn;
		
		// Get all notification for this user
		$notificationsTable = new Zend_Db_Table('notifications');
		$select = $notificationsTable->select()->where('user_id='.$this->user_id_seq);
		$resultsRow = $notificationsTable->fetchAll($select);
		
		$this->view->notifications = $resultsRow;
    }
	public function emailsaveAction(){
		
		$this->view->isLoggedIn = $this->isLoggedIn;
		
		$name = $this->_request->getParam('name');
		$def['is_authenticated'] = $this->_request->getParam('is_authenticated');
		$def['authUser'] = $this->_request->getParam('authUser');
		$def['authPassword'] = $this->_request->getParam('authPassword');
		$def['smtp'] = $this->_request->getParam('smtp');
		$def['fromEmail'] = $this->_request->getParam('fromEmail');
		$def['toEmail'] = $this->_request->getParam('toEmail');
		$def['subject_prefix'] = $this->_request->getParam('subject_prefix');
		$def['domain'] = $this->_request->getParam('domain');
		
		$notify_id = $this->_request->getParam('notify_id');
		
		$notificationsTable = new Zend_Db_Table('notifications');
		
		$data = array(
					'user_id' => $this->user_id_seq,
					'name' => $name,
					'type' => 'email',
					'definition' => json_encode($def),
					'created' => new Zend_Db_Expr('NOW()'),
					'last_modified' => new Zend_Db_Expr('NOW()')
				);
		
		if(is_numeric($notify_id)){
			// Editing an already existing row
			$where[] = $notificationsTable->getAdapter()->quoteInto('user_id = ?', $this->user_id_seq);
			$where[] = $notificationsTable->getAdapter()->quoteInto('id = ?', $notify_id);
			$notificationsTable->update($data, $where);
		}else{
			// New insert
			$notificationsTable->insert($data);
		}
	}
	public function editAction(){
		$this->view->isLoggedIn = $this->isLoggedIn;
		
		$notify_id = (int)$this->_request->getParam('id');
		
		$notificationsTable = new Zend_Db_Table('notifications');
		$select = $notificationsTable->select()->where('user_id='.$this->user_id_seq)->where('id='.$notify_id);
		$resultsRow = $notificationsTable->fetchRow($select);
		
		$this->view->notify_id = $notify_id;
		$this->view->notification = $resultsRow;
	}
	/*
	 * Generic delete a Notification
	 */
	public function deleteAction(){
		$this->view->isLoggedIn = $this->isLoggedIn;
		
		$id = (int)$this->_request->getParam('id');
		
		if(is_numeric($id)){
			$notificationTable = new Zend_Db_Table('notifications');
			$where = array(
						'user_id = ?' => $this->user_id_seq,
						'id = ?' => $id
						);
			$notificationTable->delete($where);
		}
	}
}
