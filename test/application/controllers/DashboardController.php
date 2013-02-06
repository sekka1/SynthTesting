<?php

require_once APPLICATION_PATH . '/controllers/DashboardController.php';

class DashboardControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
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
	public function testDashboardPage(){
		
		$this->dispatch('/dashboard/index');
		
		$response = $this->getResponse();
		$body = $response->getBody();
echo $body;
		
		$this->assertController('dashboard');           
        $this->assertAction('index');
		
		//$this->assertTrue(true);
	}	
}