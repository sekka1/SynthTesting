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
    public function indexAction()
    {
	//print( $this->client_id_seq . ' - ' . $this->user_id_seq . '<br/>' );
		$this->view->isLoggedIn = $this->isLoggedIn;
		
		$this->view->hourString = $this->createHourString();
		
		$this->view->monitorStatuses = $this->getMonitorStatuses();
		
		$this->view->notificationList = $this->getNotificationList();

		//print_r($this->view->monitorStatuses);
    }
	private function createHourString(){
		
		$currentHourString = date('H');
		
		for($i=1; $i<24; $i++){
			$currentHourString .= ' ' . date('H', strtotime('-'.$i.' hour'));
		}
		
		return $currentHourString;
	}
	private function createHourArray(){
		
		$currentHourArray = array();
		array_push($currentHourArray, date('H'));
		
		for($i=1; $i<24; $i++){
			array_push($currentHourArray, date('H', strtotime('-'.$i.' hour')));
		}
		
		return $currentHourArray;
	}
	private function getMonitorStatuses(){
		
		$returnArray = array();
		
		// Get all monitors
		$monitorsTable = new Zend_Db_Table('monitors');
		
		$select = $monitorsTable->select();
		
		$rows = $monitorsTable->fetchAll($select);
		
		// Get the results for each monitor
		foreach($rows as $aMonitor){
			
			$data['monitor_name'] = $aMonitor->name;
			$data['monitor_id'] = $aMonitor->id;
			$data['monitor_is_active'] = $aMonitor->is_active;
 			
			//$data['results'] = $this->getHoursWorthOfResultsForAMonitor($aMonitor->id);
			$data['results'] = $this->getALl24HoursResultForAMonitor($aMonitor->id);
			
			array_push($returnArray, $data);
		}
//print_r($returnArray);
		return $returnArray;
	}
	/*
	 * Return all the results with timestamps of the last 24 hours
	 */
	 private function getALl24HoursResultForAMonitor($monitor_id){
	 
	 	$resultsTable = new Zend_Db_Table('results');
		
		$select = $resultsTable->select()->where('monitor_id ='.$monitor_id)
											->where('created > DATE_SUB( NOW(), INTERVAL 24 HOUR)')
											->order('created desc');
											
		$resultsRow = $resultsTable->fetchAll($select);
		
		// Format output
		$data = array();
		if(count($resultsRow)>0)
			$data['percentage_slices'] = 100/count($resultsRow);
		else
			$data['percentage_slices'] = 100;
		$data['data'] = array();
		foreach($resultsRow as $aRow){
			$temp['status'] = $aRow->status;
			$temp['created'] = $aRow->created;
			array_push($data['data'], $temp);
		}
		return $data;
	 }
	/*
	 * Returns the worst alert for each hour in the last 24 hours
	 */
	private function getHoursWorthOfResultsForAMonitor($monitor_id){
		
		$resultsTable = new Zend_Db_Table('results');
		
		$currentHoursArray = $this->createHourArray();
		
		$data = array();
		
		// Select each hour and massage it down to 6 points
			foreach($currentHoursArray as $anHour){
			
				$select = $resultsTable->select()->where('monitor_id ='.$monitor_id)->where('hour(created) ='. $anHour);
				
				$resultsRow = $resultsTable->fetchAll($select);
				
				if(count($resultsRow) > 0){
					$data[$anHour]['status'] = 1;
			
					foreach($resultsRow as $aResult){					
						// Just find if any are -1 or 0.  If so put that status on it for this hour
						
						if($aResult->status == -1){
							$data[$anHour]['status'] = -1;
							break;
						}
						if($aResult->status == 0){
							$data[$anHour]['status'] = -1;
						}
					}
				}else{
					$data[$anHour]['status'] = 0;
				}
			}
			
		return $data;
	}
	/*
	 * Returns the results for a monitor ID that is passed in for the last 24 hours
	 * 
	 * http://www.userrobot.com/dashboard/getmonitorresult24h/id/1
	 */
	public function getmonitorresult24hAction(){
		
		$this->_helper->layout->disableLayout();
		
		(int)$monitor_id = $this->_request->getParam('id');
		
		$this->view->output = $this->getResults($monitor_id);
	}
	private function getResults($monitor_id){
		
		$output = null;
				
		if(is_numeric($monitor_id)){
		
			$resultsTable = new Zend_Db_Table('results');
			
			$select = $resultsTable->select()
									->where('monitor_id ='.$monitor_id)
									->where('created > DATE_SUB( NOW(), INTERVAL 24 HOUR)')
									->order('created desc');
					
			$resultsRow = $resultsTable->fetchAll($select);
						
			$output['data'] = array();
			
			// Format data
			foreach($resultsRow as $aRow){
				$temp['status'] = $aRow->status;
				$temp['meta_data'] = json_decode($aRow->meta_data,true);
				$temp['created'] = $aRow->created;
				array_push($output['data'], $temp);
			}
			
			//print_r($output);
			$output = json_encode($output);
		}else{
			echo '{}';
		}
		
		return $output;
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
	/*
	 * Shows the details of a monitor for the last X hours
	 */
	 public function detailsAction(){
	 	
		$this->view->isLoggedIn = $this->isLoggedIn;
		
		(int)$monitor_id = $this->_request->getParam('monitor');
		$duration = $this->_request->getParam('duration');

		$this->view->output = $this->getResults($monitor_id);

	 }
}
