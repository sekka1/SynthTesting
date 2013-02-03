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
			}
			
		}else{
			echo 'Invalid Key';
		}
	}
	private function getMonitorsToRun(){
		
		$monitorsTable = new Zend_Db_Table('monitors');
		
		$select = $monitorsTable->select();
		
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
}
