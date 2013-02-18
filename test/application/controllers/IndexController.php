<?php

//require_once APPLICATION_PATH . 'IndexController.php';

class IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
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
	public function testIndexPage(){
		
		$this->dispatch('/index/index');
		
		$response = $this->getResponse();
		$body = $response->getBody();
echo $body;
		
		$this->assertController('index');           
        $this->assertAction('index');
		
		//$this->assertTrue(true);
	}	
}