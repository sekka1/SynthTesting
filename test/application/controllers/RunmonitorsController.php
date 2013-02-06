<?php

require_once APPLICATION_PATH . '/controllers/RunmonitorsController.php';

class RunmonitorsControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    public function setUp()
    {
        // Assign and instantiate in one step:
        $this->bootstrap = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );

        parent::setUp();
    }
	public function testExecuteMonitor(){
		
		$this->dispatch('/runmonitors/execute/key/123456789');
		
		$response = $this->getResponse();
		$body = $response->getBody();
echo $body;
		
		$this->assertController('index');           
        $this->assertAction('index');
		
		//$this->assertTrue(true);
	}	
}