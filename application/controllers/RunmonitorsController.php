<?php

/*
 * Runs scheduled monitors
 * */

class RunmonitorsController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */

    }
     public function preDispatch(){
     	
		$this->_helper->layout->disableLayout();

        // Authentication Piece
        /*
        $this->auth = Zend_Auth::getInstance();

        if(!$this->auth->hasIdentity()){
			$this->_redirect( '/login?f=' . $this->_request->getRequestUri() );
        } else {
                // User is valid and logged in
                $this->username = $this->auth->getIdentity();
				$this->isLoggedIn = 'true';
        }
		 * 
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
    public function indexAction(){}

	public function executeAction(){
		
		// Security key so that not everyone can just make this execute
		$key = $this->_request->getParam('key');
		
		if($key == '123456789'){
			
			//echo date('m') . '<br/>';
			//echo 02 % 1 . "\n";
			//echo 10 % 5 . "\n";
			//echo 11 % 9 . "\n";
			//echo 19 % 9 . "\n";
			//echo 18 % 9 . "\n";
			
			/*
			 * 02
				0 0 2 1 0 
			 */
			
			// Get monitors to run
			$monitors = $this->getMonitorsToRun();

			// Run each monitor
			foreach($monitors as $aMonitor){
					
				$result = $this->runMonitor($aMonitor);
				$this->saveMonitorTestResults($aMonitor->id, $result);
				
				// Send notifications here if needed 
				// Only send it if the last 3 test runs were a failure
				if($result['up_down'] == -1){
					if($this->last3TestFailed($aMonitor->id)){
						$this->sendNotification($aMonitor->notification_id, $aMonitor->name);
					}
				}
			}
			
		}else{
			echo 'Invalid Key';
		}
	}
	private function getMonitorsToRun(){
		
		$monitorsTable = new Zend_Db_Table('monitors');
		
		$select = $monitorsTable->select()
						->where('is_active=1');
		
		$rows = $monitorsTable->fetchAll($select);
		
		$pruned = $this->pruneScheduledMonitors($rows);
		
		return $pruned;
	}
	/*
	 * Only return the monitors that are scheduled to run
	 * 
	 */
	private function pruneScheduledMonitors($rows){
	
		$prunedRows = array();
	
		foreach($rows as $aMonitor){
			if((date('m') % $aMonitor->schedule) == 0)
				array_push($prunedRows, $aMonitor);
		}
		
		return $prunedRows;
	}
	private function runMonitor($monitorDefinition){
		
			$returnVal = 'none';
			$class = $monitorDefinition->class_name;
			$method = 'execute';
			$monitorInput = $monitorDefinition->definition;
		
            try{
                Zend_Loader::loadClass( $class );

                $anObject = new $class();

                if( method_exists( $anObject, $method ) ){

                    $returnVal =  $anObject->$method($monitorInput);
                }
                else{
                    echo 'Method does not exist!';
                }
            } catch( Exception $e ){

                echo 'Caught exception: ',  $e->getMessage(), "<br/>";
            }
			
			return $returnVal;
	}
	private function saveMonitorTestResults($monitor_id, $resultArray){
		print_r($resultArray);
		
		$resultsTable = new Zend_Db_Table('results');
		
		$data = array(
					'monitor_id' => $monitor_id,
					'status' => $resultArray['up_down'],
					'meta_data' => json_encode($resultArray['meta_data']),
					'created' => new Zend_Db_Expr('NOW()'),
					'last_modified' => new Zend_Db_Expr('NOW()')
				);
				
		$resultsTable->insert($data);
	}
	private function sendNotification($notification_id, $monitor_name){
		
		// No notification is set
		if($notification_id==null)
			return;
		
		$notificationTable = new Zend_Db_Table('notifications');
		
		$select = $notificationTable->select()
						->where('id='.$notification_id);
						
		$rows = $notificationTable->fetchAll($select);

		if(count($rows) == 1){
			// Send notification
			
			$emailDef = json_decode($rows[0]->definition, true);
print_r($emailDef);
			Zend_Loader::loadClass('Notification');
			$notification = New Notification();
			
			if($emailDef['is_authenticated']){
				$notification->setAuthUser($emailDef['authUser']);
				$notification->setAuthPassword($emailDef['authPassword']);
echo "<br>Authing...<br/>";
			}
			$notification->setSMTP($emailDef['smtp']);
			$notification->setFromEmail($emailDef['fromEmail']);
			$notification->setFromName('User Robot');
			$notification->setToEmail($emailDef['toEmail']);
			$notification->setToName('Human');
			$notification->setBody('Service Outage: ' . $monitor_name);
			$notification->setSubject('Service Outage: ' . $monitor_name);
			$notification->setDomain($emailDef['domain']);
			
			$notification->send();
			
		}
	}
	/*
	 * Returns true|false
	 * 
	 * True if the last 3 tests for this monitor ID was a failure also.
	 * 
	 * Dont want to keep on spamming the user for one failed test
	 */
	private function last3TestFailed($monitor_id){
		
		$resultsTable = new Zend_Db_Table('results');
		$select = $resultsTable->select()
						->where('monitor_id='.$monitor_id)
						->order('created desc')
						->limit('3');
		$rows = $resultsTable->fetchAll($select);
	
		$allFailed = false;
	
		// Check if they are all status -1
		if(count($rows)==3){
			if(($rows[0]->status==-1) && ($rows[1]->status==-1) && ($rows[2]->status==-1))
				$allFailed = true;
echo '<br/>Last 3 tests failed...<br/>';
		}
		
		return $allFailed;
	}
}
