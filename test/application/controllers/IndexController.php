<?php

require_once APPLICATION_PATH . '/controllers/AlgorithmsController.php';

class IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    private $authToken;

    public function setUp()
    {
        // Assign and instantiate in one step:
        $this->bootstrap = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );

        parent::setUp();
    }
	
}