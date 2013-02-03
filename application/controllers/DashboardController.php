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
		
		$this->view->monitorStatuses = $this->getMonitorStatuses();
		
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
		
		$resultsTable = new Zend_Db_Table('results');
		
		$currentHoursArray = $this->createHourArray();

		// Get the results for each monitor
		foreach($rows as $aMonitor){
			
			$data['monitor_name'] = $aMonitor->name;
			
			// Select each hour and massage it down to 6 points
			foreach($currentHoursArray as $anHour){
			
				$select = $resultsTable->select()->where('monitor_id ='.$aMonitor->id)->where('hour(created) ='. $anHour);
				
				$resultsRow = $resultsTable->fetchAll($select);
				
				if(count($resultsRow) > 0){
					$data['results'][$anHour]['status'] = 1;
			
					foreach($resultsRow as $aResult){					
						// Just find if any are -1 or 0.  If so put that status on it for this hour
						
						if($aResult->status == -1){
							$data['results'][$anHour]['status'] = -1;
							break;
						}
						if($aResult->status == 0){
							$data['results'][$anHour]['status'] = -1;
						}
					}
				}else{
					$data['results'][$anHour]['status'] = 0;
				}
			}
			array_push($returnArray, $data);
		}
		
		return $returnArray;
	}

}
