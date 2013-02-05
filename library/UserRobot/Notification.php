<?php
/*
 * Class takes cares of sending notifications to users 
 * 
 */
 
 
class Notification{
	
    private $authUser = null;
    private $authPassword = null;
    private $smtp = null;
    private $fromEmail = null;
    private $fromName = null;
	private $toEmail = null;
	private $toName = null;
	private $body = null;
    private $subject = null;
    private $domain = null;
	
	public function __construct(){
		
	}
	public function setAuthUser($authUser){
		$this->authUser = $authUser;
	}
	public function setAuthPassword($authPassword){
		$this->authPassword = $authPassword;
	}
	public function setSMTP($smtp){
		$this->smtp = $smtp;
	}
	public function setFromEmail($fromEmail){
		$this->fromEmail = $fromEmail;
	}
	public function setFromName($fromName){
		$this->fromName = $fromName;
	}
	public function setToEmail($toEmail){
		$this->toEmail = $toEmail;
	}
	public function setToName($toName){
		$this->toName = $toName;
	}
	public function setBody($body){
		$this->body = $body;
	}
	public function setSubject($subject){
		$this->subject = $subject;
	}
	public function setDomain($domain){
		$this->domain = $domain;
	}
	public function send(){
		
		$config = array();
		
		if($this->authUser != null){
			$config = array('auth' => 'login',
	            'ssl' => 'tls',
	            'port' => 587,
	            'username' => $this->authUser,
	            'password' => $this->authPassword );
		}
		
	    $transport = new Zend_Mail_Transport_Smtp($this->smtp, $config);
	
	    $mail = new Zend_Mail();
	    $mail->setBodyText($this->body);
	    $mail->setFrom($this->fromEmail, $this->fromName);
	    $mail->addTo($this->toEmail, $this->toName);
	   	//$mail->addBcc($bccEmail, $bccName);
	    $mail->setSubject($this->subject);
	    $mail->send($transport);
	}
	/*
	 * Retrieves the notification parameters from the database used to send the email
	 */
	private function getNotificationParams($user_id, $notification_id){
		
		$notificationTable = new Zend_Db_Table('notifications');
		
		$select = $notificationTable->select()
						->where('user_id='.$user_id)
						->where('notification_id='.$notification_id);
		
		$rows = $monitorsTable->fetchAll($select);
		
		if($rows == 1){
			
			$this->authUser = $row->authUser;
	        $this->authPassword = $row->authPassword;
	        $this->smtp = $row->smtp;
	        $this->fromEmail = $row->fromEmail;
	        $this->fromName = 'User Robot';
	        $this->subject = $row->subject_prefix;
	        $this->domain = $row->domain;
		}
						
						
	}
}


