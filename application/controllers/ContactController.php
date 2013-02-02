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

        $this->authEmail = 'no-reply@algorithms.io';
        $this->authPassword = 'y&=752QN';
        $this->smtp = 'smtp.gmail.com';
        $this->fromEmail = 'support@algorithms.io';
        $this->fromName = 'Support';
        $this->bccEmail = 'core@algorithms.io';
        $this->bccName = 'Support';
        $this->subject = '';
        $this->domain = 'www.algorithms.io';
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
    public function confirmAction()
    {

       $phrase1 = isset($_POST['phrase1']) ? $_POST['phrase1'] : '';  
       $phrase2 = isset($_POST['phrase2']) ? $_POST['phrase2'] : '';  
       $phrase3 = isset($_POST['phrase3']) ? $_POST['phrase3'] : '';  
       $name = isset($_POST['name']) ? $_POST['name'] : '';  
       $phone = isset($_POST['phone']) ? $_POST['phone'] : '';  
       $info = isset($_POST['info']) ? $_POST['info'] : '';  
       $company = isset($_POST['company']) ? $_POST['company'] : '';  
       $wantbeta = isset($_POST['wantbeta']) ? $_POST['wantbeta'] : '';  
       $email = isset($_POST['email']) ? $_POST['email'] : '';  
        $this->view->email = $email;
        $this->view->isLoggedIn = $this->isLoggedIn;

        $emailRegex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';

        if( preg_match( $emailRegex, $email ) ){

                require_once('AlgorithmsIO/classes/Utilities.php');
                $utilities = new Utilities();

                $toEmail = 'support@algorithms.io';

                $body = "FullName: " . $name . "\nPhone: " . $phone . "\nEnteredInfo: " . $info . "\nCompany: " . $company . "\nWanttoSignup: " . $wantbeta . "\nemail: " . $email . "\n USER_TYPE:" . $phrase1 . "\n USE_CASE:". $phrase2 . "\nLINKED_FROM:".$phrase3;
                $this->subject = 'Contact Form Request';
                $utilities->email( $this->authEmail, $this->authPassword, $this->smtp, $this->fromEmail, $this->fromName, $toEmail, $email, $this->bccEmail, $this->bccName, $this->subject, $body );        
                }
    }
}