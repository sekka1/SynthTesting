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
		
		// Get the results for each monitor
		foreach($rows as $aMonitor){
			
			$data['monitor_name'] = $aMonitor->name;
			
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
		$data['percentage_slices'] = 100/count($resultsRow);
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
		
		if(is_numeric($monitor_id)){
		
			$resultsTable = new Zend_Db_Table('results');
			
			$select = $resultsTable->select()
									->where('monitor_id ='.$monitor_id)
									->where('created > DATE_SUB( NOW(), INTERVAL 24 HOUR)')
									->order('created asc');
					
			$resultsRow = $resultsTable->fetchAll($select);
			
			//print_r($resultsRow);
			
			$data['data'] = array();
			$data['type'] = 'bar';
			
			// Format data
			foreach($resultsRow as $aRow){
				$temp['unit'] = $aRow->created;
				$temp['value'] = $aRow->status;
				array_push($data['data'], $temp);
			}
			
			$output['JSChart']['dataset'] = array();
			array_push($output['JSChart']['dataset'], $data);
			
			//print_r($output);
			$this->view->output = json_encode($output);
		}else{
			echo '{}';
		}
	}
}
