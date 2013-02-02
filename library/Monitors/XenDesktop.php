<?php

//$xendeskop = new XenDesktop();

/*
 * Right now it only tried to open the first desktop and in this
 * env the first desktop is non functional.  This will fail
 * 
$xendeskop->setBaseURL('http://eas-xen5.cisco.com');
$xendeskop->setDomain('cisco');
$xendeskop->setUserName('gakan');
$xendeskop->setPassword('fr33d0mGar1');
$xendeskop->setLoginType('Explicit');
*/

/*
 * Able to start desktop: VXI-PVS 
 *
$xendeskop->setBaseURL('http://citrix.vxidemocloud.com');
$xendeskop->setDomain('vxidemocloud');
$xendeskop->setUserName('gakan');
$xendeskop->setPassword('P@ssword1');
$xendeskop->setLoginType('Explicit');
*/

/*
 * Able to start desktop: ACE-PVS
 * 
$xendeskop->setBaseURL('http://myvdi.cisco.com');
$xendeskop->setDomain('cisco.com');
$xendeskop->setUserName('gakan');
$xendeskop->setPassword('fr33d0mGar1');
$xendeskop->setLoginType('Explicit');
*/
/*
$didLaunchDesktop = $xendeskop->testLaunchDesktop();

if($didLaunchDesktop)
	echo "Successfully launched desktop!\n";
else
	echo "Failed to launched desktop!\n";
*/
class XenDesktop{
	
	private $ch;
	private $cookie;
	private $baseURL;
	private $post_loginForm = '/Citrix/DesktopWeb/auth/login.aspx';
	private $login_form_path = '/Citrix/DesktopWeb/auth/login.aspx?CTX_FromLoggedoutPage=1';
	private $user;
	private $password;
	private $domain;
	private $loginType = 'Explicit';
	private $verion;
	private $loginPageSessionToken;
	
	private $CTX_Token; // Token after loging into the Citrix site
	
	private $errors;
	
	public function __construct(){
		$this->ch = curl_init();
		
		$this->cookie="cookie.txt";
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($this->ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
		curl_setopt($this->ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 0);
		curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($this->ch, CURLOPT_COOKIEFILE, '');  // Enables session support
		
		$this->errors = array();
	}
	
	public function setBaseURL($baseURL){
		$this->baseURL = $baseURL;
	}
	public function setUserName($user){
		$this->user = $user;
	}
	public function setPassword($password){
		$this->password = $password;
	}
	public function setDomain($domain){
		$this->domain = $domain;
	}
	public function setLoginType($loginType){
		$this->loginType = $loginType;
	}
	public function setVersion($verion){
		$this->verion = $verion;
	}
	public function getErrors(){
		return $this->errors;
	}
	
	/*
	 * On the login page there is a SESSION_LOGIN var that should be also passed into the login
	 * 
	 * MIGHT NOT NEED THIS - Seems to be logging in without this key passed in with the login
	 */
	public function retrieveLoginSessionToken(){
		echo $this->baseURL.$this->login_form_path."\n";
		//$content = file_get_contents($this->baseURL.$this->login_form_path);
		//echo $content;
	}
	/*
	 * This sets the input for this monitor and will return true or false if it is able to run or not
	 */
	public function setRunDefinition($definitionInput){
		
		$hasAllInput = true;
		
		$definition = json_decode($definitionInput, true);

		if(isset($definition['baseURL']))
			$this->setBaseURL($definition['baseURL']);
		else		
			$hasAllInput = false;

		if(isset($definition['domain']))
			$this->setDomain($definition['domain']);
		else		
			$hasAllInput = false;
		
		if(isset($definition['username']))
			$this->setUserName($definition['username']);
		else		
			$hasAllInput = false;
		
		if(isset($definition['password']))
			$this->setPassword($definition['password']);
		else		
			$hasAllInput = false;
		
		if(isset($definition['version']))
			$this->setVersion($definition['version']);
		else		
			$hasAllInput = false;
		
		return $hasAllInput;
	}
	public function execute($definitionInput){
		
		$results = array();
		
		$hasAllInputs = $this->setRunDefinition($definitionInput);
		
		if($hasAllInputs){
			$content = $this->login();
			
			$result1 = $this->executeLauncherASPX($content);
			//echo $result1;
			
			$result2 = $this->executeLaunchICA($result1);
			$verification = $this->verifyExecuteLaunchICA($result2);
			//echo $result2;
			
			if($verification){
				$results['up_down'] = '1';
				$results['meta_data'] = 'Connected to Desktop';
			}else{
				$results['up_down'] = '-1';
				$results['meta_data']['errors'] = $this->errors;
			}
			
			$this->cleanUp();
			
			if(count($this->errors)>0){
				//echo "\n\nErrors:\n";
				//print_r($this->errors);
				//echo "\n";
				$results['meta_data']['errors'] = $this->errors;
			}
		}else{
			$results['up_down'] = '0';
			$results['meta_data'] = 'Did not have the correct inputs';
		}
		
		return $results;
	}
	/*
	 * Login to the web interface
	 */
	private function login(){
		
		$post_params['LoginType'] = $this->loginType;
		$post_params['domain'] = $this->domain;
		$post_params['password'] = $this->password;
		$post_params['user'] = $this->user;
		$post_params['SESSION_TOKEN'] = $this->loginPageSessionToken;
		
		$headers = null;

        curl_setopt($this->ch, CURLOPT_URL, $this->baseURL.$this->post_loginForm);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post_params);
		
		// Optionally set header values
        if( $headers != null )
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		$result = curl_exec($this->ch);
		
		return $result;
	}
	/*
	 * This seems to gets things going and gets all the tokens in place
	 */
	private function executeLauncherASPX($content){
		
		$result = '';
		
		//$pattern = '/desktopName\'><a href="(.*=y)"/'; // works for vxidemocloud.com
		
		//
		// It was weird.  I couldnt regex after the href="xxxx" and just grab that.  Trying to do 
		// it in 2 steps then.
		//
		//  Getting the start of what I want then there is a lot of trailing stuff we weill need to get 
		//  rid of in step 2
		//
		$pattern = '/desktopName\'\><a href="(.*)\s/';
		preg_match($pattern, $content, $matches);
		
		if(count($matches)>0){
		
			//
			// Replace everything after the " which is the end of the URL.
			// This seems to work well.
			//
			$pattern = '/".*/';
			$path = preg_replace($pattern, '', $matches[1]);
		
//echo $path;
//echo "\nexecuteLauncherASPX:\n";
//print_r($matches);

			// Save CTX_Token
			$this->saveCTX_Token($path);
		
			$url = $this->baseURL.'/Citrix/DesktopWeb/site/'.$path;
//echo $url;
			curl_setopt($this->ch, CURLOPT_URL, $url);
			curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'GET');
		
			$result = curl_exec($this->ch);
		}else{
			// Error
			$temp['executeLauncherASPX_step_1'] = 'Did not find a desktopName';
			array_push($this->errors, $temp);
		}
		
		return $result;
	}
	/*
	 * This calls seems to get all the connection information that the ICA protocal should use or negociated?
	 * 
	 * Has specific names and IP addresses of the connection.  You can make a TCP connection the that IP and port below.
	 * Not sure how to make it do something though.
	 * 
	 * [Encoding]
InputEncoding=UTF8

[WFClient]
CPMAllowed=On
ProxyFavorIEConnectionSetting=Yes
ProxyTimeout=30000
ProxyType=Auto
ProxyUseFQDN=Off
RemoveICAFile=yes
SessionReliabilityTTL=60
TransparentKeyPassthr
[VXI-PVS $S6-3]
Address=173.36.201.41:1494
AudioBandwidthLimit=0
AutologonAllowed=ON
BrowserProtocol=HTTPonTCP
CGPAddress=*:2598
ClearPassword=9F38BED2BC5195
ClientAudio=On
	 * 
	 * 
	 */
	private function executeLaunchICA($content){
		
		$result = '';
		
		//$pattern = '/desktopName\'><a href="(.*=y)"/';
		$pattern = '/document.location.replace\(\'(.*)\'\);/';
		preg_match($pattern, $content, $matches);

//echo $content;
//print_r($matches);

		if(count($matches)>0){
		
			$url_path = str_replace('launcher.aspx', 'launch.ica', $matches[1]);
		
			$url = $this->baseURL.$url_path.'&CTX_Token='.$this->CTX_Token;
//echo "\n".$url."\n";
			curl_setopt($this->ch, CURLOPT_URL, $url);
			curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'GET');
		
			$result = curl_exec($this->ch);
		}else{
			// Error
			$temp['executeLaunchICA'] = 'There was no desktop to launch here';
			array_push($this->errors, $temp);
		}
		
		return $result;
	}
	/*
	 * Verifies what is expected to be returned by executeLaunchICA call
	 */
	private function verifyExecuteLaunchICA($content){
		
		$didVerify = true;
		
		// Check for the stanza [WFClient]
		$pattern = '/WFClient/';
		if(preg_match($pattern, $content) == 0)
			$didVerify = false;
		
		// Check for the Address field
		$pattern = '/Address=/';
		if(preg_match($pattern, $content) == 0)
			$didVerify = false;
		
		return $didVerify;
	}
	private function cleanUp(){
		curl_close($this->ch);
	}
	/*
	 * Find the CTX_Token in the string and save it
	 */
	private function saveCTX_Token($string){
		
		$pattern = '/CTX_Token=(.*)/';
		preg_match($pattern, $string, $matches);

		if(count($matches) > 0)
			$this->CTX_Token = $matches[1];
		else{
			$temp['saveCTX_Token'] = 'Did not find CTX_Token';
			array_push($this->errors, $temp);
		}
			
	}
}



?>