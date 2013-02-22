<?php

//$curlMon = new CurlMon();
//$definitionInput = '{"url":"http://v1.api.algorithms.io/jobs/run/40","post_params":{"method":"sync","outputType":"json","datasources":"[3297]","type":"user","item":196},"headers":["authToken: 2cf26b0ee492b7852e92463d41705558"],"regex_check":"recommendation\":\\\[{\"id"}';

//print_r( $curlMon->execute($definitionInput) );


class apiRest{
	
	private $ch;
	private $cookie;
	private $errors;
	
	private $url;
	private $post_params_array;  // Final array to be sent to cURL
	private $post_params_users; // User input array
	private $headers_array;  // Final array to be sent to cURL
	private $headers_users;  // User input array
	private $regex_check;
	
	public function __construct(){
		$this->ch = curl_init();
		
		$this->cookie="cookie.txt";
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		//curl_setopt($this->ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
		curl_setopt($this->ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 0);
		//curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookie);
		//curl_setopt($this->ch, CURLOPT_COOKIEFILE, '');  // Enables session support
		
		$this->errors = array();
		
		$this->post_params_array = array();
		$this->headers_array = array();
	}
	public function getErrors(){
		return $this->errors;
	}
	private function cleanUp(){
		curl_close($this->ch);
	}
	/*
	 * @input json $definitionInput
	 * 
	 * @return bool
	 * 
	 * This sets the input for this monitor and will return true or false if it is able to run or not
	 */
	public function setRunDefinition($definitionInput){
	
		$hasAllInput = true;
		
		$definition = json_decode($definitionInput, true);

		if(json_last_error() == 'JSON_ERROR_NONE'){

			if(isset($definition['url']))
				$this->url = $definition['url'];
			else		
				$hasAllInput = false;
			
			if(isset($definition['post_params']))
				$this->post_params_users = $definition['post_params'];
			else		
				$hasAllInput = false;
				
			if(isset($definition['headers']))
				$this->headers_users = $definition['headers'];
			else		
				$hasAllInput = false;
				
			if(isset($definition['regex_check']))
				$this->regex_check = $definition['regex_check'];
			else		
				$hasAllInput = false;
		}else{
			$hasAllInput = false;
			$error['error'] = 'Not a valid json';
			array_push($this->errors, $error);
		}
		
		return $hasAllInput;
	}
	/*
	 * @input json $definitionInput
	 * 
	 * @return array
	 * 
	 * This is the main entry point for the monitor framework to call this monitor
	 */
	public function execute($definitionInput){
		
		$results = array();
		
		$hasAllInputs = $this->setRunDefinition($definitionInput);
		
		if($hasAllInputs){
			
			// Parse the user's input in json into an array.  Also error checking on the inputs
			$this->parseHeaders();
			$this->parsePostParams();
			
			if(count($this->errors)>0){
				// There was an error parsing the inputs.  End the test run now
				$results['up_down'] = '0';
				$results['meta_data'] = $this->errors;
			}else{
				// Run the user's curl call
				
				$curl_results = $this->curlPost($this->url, $this->post_params_array, $this->headers_array);
				
				$regex_check_results = $this->check_regex($curl_results);
				
				if($regex_check_results){
					// Good
					$results['up_down'] = '1';
					$results['meta_data']['results'] = $curl_results;
				}else{
					// bad
					$results['up_down'] = '-1';
					$results['meta_data']['results'] = $curl_results;
					$results['meta_data']['post_params'] = $this->post_params_array;
					$results['meta_data']['header_params'] = $this->headers_array;
				}
			}
		}else{
			$results['up_down'] = '0';
			$results['meta_data'] = $this->errors;
		}
		
		return $results;
	}
	/*
	* Runs a curl command
	*
	* @input string $url - url for curl command
	* @input array $post_params - array of all the post parameters
	* @input array $headers = array of additional headers
	*
	* @return string - output of curl call
	*/
	private function curlPost( $url, $post_params, $headers = null ){
    // Does a post and optional file upload to a given url
    // INTPUT:
    /*
        $post_params['file'] = ‘@’.'/tmp/testfile.txt’;
        $post_params['submit'] = urlencode(’submit’);;
    */
        $returnVal = '';
//echo "\ncurlPost\n";
//print_r($post_params);
//print_r($headers);
//echo $url."\n";
        $ch = curl_init();
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post_params);

        // Optionally set header values
        if( $headers != null )
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($this->ch);

        if( $result )
            $returnVal = $result;
        else
            $returnVal = curl_error($this->ch); 

        return $returnVal;
	}
	private function parseHeaders(){

		foreach($this->headers_users as $key=>$val){
			$this->headers_array[$key] = $val;
		}
	}
	private function parsePostParams(){
		
		foreach($this->post_params_users as $key=>$val){
			$this->post_params_array[$key] = (string)$val;
		}
	}
	/*
	 * @input string
	 * 
	 * @output bool
	 * 
	 */
	 private function check_regex($string){
	 	
		$isGood = false;
		
		if(preg_match('/'.$this->regex_check.'/', $string)==1){
			$isGood = true;
		}
		
		return $isGood;
	 }
}

?>