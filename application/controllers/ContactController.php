<?php

class ContactController extends Zend_Controller_Action
{
    private $isLoggedIn;
    private $authEmail;
    private $authPassword;
    private $smtp;
    private $fromEmail;
    private $fromName;
    private $bccEmail;
    private $subject;
    private $domain;

    public function init()
    {
        /* Initialize action controller here */

        $this->authEmail = 'no-reply@userrobot.com';
        $this->authPassword = 'UR*%5KhV11';
        $this->smtp = 'smtp.gmail.com';
        $this->fromEmail = 'no-reply@userrobot.com';
        $this->fromName = 'Support';
        $this->bccEmail = 'garlandk@gmail.com';
        $this->bccName = 'Support';
        $this->subject = '';
        $this->domain = 'userrobot.com';
    }
    public function preDispatch(){

        // Authentication Piece
        $this->auth = Zend_Auth::getInstance();

        if(!$this->auth->hasIdentity()){
            $this->isLoggedIn = 'false';  
        } else {
            // User is valid and logged in
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
    public function postAction()
    {
        $this->confirmAction();
        exit;
    }
    public function indexAction()
    {
        $this->view->isLoggedIn = $this->isLoggedIn;
    }
    public function registerAction()
    {
        $this->view->isLoggedIn = $this->isLoggedIn;
    }
    public function getstartedAction()
    {
        $this->view->isLoggedIn = $this->isLoggedIn;
    }
    public function confirmAction()
    {

       //$phrase1 = isset($_POST['phrase1']) ? $_POST['phrase1'] : '';  
       //$phrase2 = isset($_POST['phrase2']) ? $_POST['phrase2'] : '';  
       //$phrase3 = isset($_POST['phrase3']) ? $_POST['phrase3'] : '';  
       $name = isset($_POST['name']) ? $_POST['name'] : '';  
       $phone = isset($_POST['phone']) ? $_POST['phone'] : '';  
       $info = isset($_POST['info']) ? $_POST['info'] : '';  
       $company = isset($_POST['company']) ? $_POST['company'] : '';  
       //$wantbeta = isset($_POST['wantbeta']) ? $_POST['wantbeta'] : '';  
       $email = isset($_POST['email']) ? $_POST['email'] : '';  
        $this->view->email = $email;
        $this->view->isLoggedIn = $this->isLoggedIn;

        $emailRegex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';

        if( preg_match( $emailRegex, $email ) ){

                require_once('AlgorithmsIO/classes/Utilities.php');
                $utilities = new Utilities();

                $toEmail = 'support@userrobot.com';

                $body = "FullName: " . $name . "\nPhone: " . $phone . "\nEnteredInfo: " . $info . "\nCompany: " . $company . "\nemail: " . $email;
                $this->subject = 'Contact Form Request';
                $utilities->email( $this->authEmail, $this->authPassword, $this->smtp, $this->fromEmail, $this->fromName, $toEmail, $email, $this->bccEmail, $this->bccName, $this->subject, $body );        
                }
    }
}
