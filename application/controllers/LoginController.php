<?php

class LoginController extends Zend_Controller_Action
{
    public function preDispatch()
    {
	$this->debug=false;

        if (Zend_Auth::getInstance()->hasIdentity()) {
            // If the user is logged in, we don't want to show the login form;
            // however, the logout action should still be available
            if ('logout' != $this->getRequest()->getActionName()) {

                $this->_helper->redirector('index', 'dashboard'); // Commented out to prevent inability to return to login page - MRR20120605
            }
        } else {
            // If they aren't, they can't logout, so that action should
            // redirect to the login form
            if ('logout' == $this->getRequest()->getActionName()) {
                $this->_helper->redirector('index');
            }
        }

    }
    public function __call($method, $args)
    {
        if ('Action' == substr($method, -6)) {
            // If the action method was not found, render the error
            // template
	    $this->debug("ERROR201206051427: $method is not defined in ".get_Class($this));
            return $this->render('error');

            // Forward to another page
            //return $this->_forward('index');
        }

	$this->error("ERROR201206061058: Invalid Method $method was called");
        // all other methods throw an exception
        throw new Exception('Invalid method "'
                            . $method
                            . '" called',
                                500);

    }
    public function indexAction()
    {
	$this->debug("DEBUG201206061048: In indexAction()");
        $this->view->form = $this->getForm();

	//$form->getElement('referer')->setValue($this->_request->getParam('f'));
    }
    public function getForm()
    {
        return new Form_Login(array(
            'action' => '/login/process?f=' . $this->_request->getParam( 'f' ),
            'method' => 'post',
        ));
	
    }
    public function getAuthAdapter(array $params)
    {
        // Leaving this to the developer...
        // Makes the assumption that the constructor takes an array of
        // parameters which it then uses as credentials to verify identity.
        // Our form, of course, will just pass the parameters 'username'
        // and 'password'.

        // Retrieve the DB values from the application.ini.  This takes care of the staging, production values
        $bootstrap = $this->getInvokeArg('bootstrap');
        $conf = $bootstrap->getOption('resources');

	$dbAdapter = new Zend_Db_Adapter_Pdo_Mysql( array('host' => $conf['db']['params']['host'], 'port' => $conf['db']['params']['port'], 'dbname' => $conf['db']['params']['dbname'], 'password' => $conf['db']['params']['password'], 'username' => $conf['db']['params']['username'] ) );
	
	$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

	$authAdapter
            ->setTableName("users")
            ->setIdentityColumn('username')
            ->setCredentialColumn('password')
            ->setCredentialTreatment('MD5(?)')
        ;

	$authAdapter
            ->setIdentity($params['username'])
            ->setCredential($params['password'])
        ;
	$this->debug("DEBUG201206061049: Created Zend_Auth_Adapter_DbTable ",$authAdapter);

	return $authAdapter;
    }
    public function processAction()
    {
        $request = $this->getRequest();

        // Check if we have a POST request
        if (!$request->isPost()) {
            return $this->_helper->redirector('index');
        }

        // Get our form and validate it
        $form = $this->getForm();
        if (!$form->isValid($request->getPost())) {
	    
            // Invalid entries
            $this->view->form = $form;
            return $this->render('index'); // re-render the login form
        }

        // Get our authentication adapter and check credentials
        $adapter = $this->getAuthAdapter($form->getValues());
	$this->debug("DEBUG201206061050: After getAuthAdapter ",$adapter);
        $auth    = Zend_Auth::getInstance();
	$this->debug("DEBUG201206051348: auth=",$auth);
        $result  = $auth->authenticate($adapter);
	$this->debug("DEBUG201206051349: Authentication Result=",$result);
        if (!$result->isValid()) {
            // Invalid credentials
    	    $this->debug("DEBUG201206061100: Credentials were invalid",$result);
            $form->setDescription('Invalid credentials provided');
            $this->view->form = $form;
            return $this->render('index'); // re-render the login form
        }

        // We're authenticated! Redirect to the home page
	// Page user tried to goto when unauthed send them back here
    	$this->debug("DEBUG201206061101: Authentication Successful");
	$referer = $this->_request->getParam( 'f' );

	if( $referer == '' ){
		// Send to generic page
		//$this->_helper->redirector('index', 'index');
		$this->_helper->redirector('dashboard', 'index');
	} else {
		$this->error("DEBUG201206061046: **************** Redirecting to ".print_r($referer,true));
		// Send to the page the user wanted before authing
		$this->_redirect( $referer );
	}

    }
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('index'); // back to login page
    }
    public function forgotpassAction() 
    {
        //if (!$this->_request->isPost()) {
            $usersEmail = $this->_request->getParam( 'username' );

            $emailRegex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';

            if( preg_match( $emailRegex, $usersEmail ) ){

                // Retrieve the DB values from the application.ini.  This takes care of the staging, production values
                $bootstrap = $this->getInvokeArg('bootstrap');
                $conf = $bootstrap->getOption('app');

                $authEmail = $conf['emailSetting']['authEmail'];
                $authPassword = $conf['emailSetting']['authPassword'];
                $smtp = $conf['emailSetting']['smtp'];
                $fromEmail = $conf['emailSetting']['fromEmail'];
                $fromName = $conf['emailSetting']['fromName'];
                $bccEmail = $conf['emailSetting']['bccEmail'];
                $bccName = $conf['emailSetting']['bccName'];
                $subject = 'Temporary Password';
                $domain = $conf['emailSetting']['domain'];

                // Reset password
                require_once('AlgorithmsIO/classes/Users.php');
                $users = new Users();
                $users->setUserEmail( $usersEmail );
                $temp_password = $users->updateUsersPassword();
        
                $body = 'Here is your temporary password: '.$temp_password;
                $toEmail = $usersEmail;
                $email = $toEmail;

                require_once('AlgorithmsIO/classes/Utilities.php');
                $utilities = new Utilities();
                echo $utilities->email( $authEmail, $authPassword, $smtp, $fromEmail, $fromName, $toEmail, $email, $bccEmail, $bccName, $subject, $body );

            }
        //}
    }
    public function temppassAction() 
    {

    }
    private function debug($msg,$obj="") {
        if($this->debug) {
            if(gettype($obj)=="array" || gettype($obj)=="object") {
			error_log($msg.print_r($obj, true));
		} else {
			error_log($msg.$obj);
		}
	}
    }

    private function error($msg,$obj="") {
		if(gettype($obj)=="array" || gettype($obj)=="object") {
			error_log($msg.print_r($obj, true));
		} else {
			error_log($msg.$obj);
		}
    }

}

?>
