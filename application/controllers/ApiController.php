<?php

class ApiController extends Zend_Controller_Action
{
    private $auth;
    private $authToken;
    private $user_id_seq;
    private $isAuthorized;
    private $output;

    public function init()
    {
        $this->isAuthorized = false;
        $this->output = array();
        $this->output['api'] = array();
        $this->output['data'] = array();
    }
     public function preDispatch(){

        $this->_helper->layout->disableLayout();

        $this->authToken = $this->_request->getParam( 'authToken' );

        if( $this->authToken != '' ){

            require_once('AlgorithmsIO/classes/Auth.php');
            $this->auth = new Auth();
            $this->auth->setAuthToken( $this->authToken );

            if( $this->auth->isValid() ){
                // Valid Auth Token

                $this->isAuthorized = true;
                $this->user_id_seq = $this->auth->getUserId();

                $this->output['api']['Authentication'] = 'Success';
            }else{
                // Invalid Auth Token
                $this->output['api']['Authentication'] = 'Failed';    
            }
        }else{
            // No auth token found
            return $this->render('noauthtoken');
        }

     }
    public function __call($method, $args){
        // Catching undefined Actions

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
    public function notauthedAction(){
        echo $this->output;
    }
    public function actionAction(){

        if( $this->isAuthorized ){

            $class = $this->_request->getParam( 'class' );
            $method = $this->_request->getParam( 'method' );

            // Set User Params in the get vars so that it will be accessible
            $data['authToken'] = $this->authToken;
            $data['user_id_seq'] = $this->user_id_seq;
            $this->_request->setParams( $data );
    
            try{
                Zend_Loader::loadClass( $class );

                $anObject = new $class( $this->auth );

                if( method_exists( $anObject, $method ) ){

                    $returnVal =  $anObject->$method( $this->_request );

                    // Combine call with the rest of the output
                    $temp = json_decode( $returnVal, true );
                    $this->output['data'] = $temp;
                }
                else{
                    $this->output['api']['Error'] = 'Invalid method';
                }
            } catch( Exception $e ){

                echo 'Caught exception: ',  $e->getMessage(), "<br/>";
                $this->output['api']['Error'] = 'Invalid action';
            } 

            // Set output to the view
            $this->view->output = json_encode( $this->output );
        }else{
            $this->view->output = json_encode( $this->output );
        }
    }
    public function v1Action(){

        if( $this->isAuthorized ){

            $class = $this->_request->getParam( 'class' );
            $method = $this->_request->getParam( 'method' );

            // Set User Params in the get vars so that it will be accessible
            $data['authToken'] = $this->authToken;
            $data['user_id_seq'] = $this->user_id_seq;
            $this->_request->setParams( $data );

            try{
                require_once('AlgorithmsIO/classes/'.$class.'.php');
                $anObject = new $class( $this->auth );

                if( method_exists( $anObject, $method ) ){

                    $returnVal =  $anObject->$method( $this->_request );

                    // Combine call with the rest of the output
                    $temp = json_decode( $returnVal, true );
                    $this->output['data'] = $temp;
                }
                else{
                    $this->output['api']['Error'] = 'Invalid method';
                }
            } catch( Exception $e ){

                echo 'Caught exception: ',  $e->getMessage(), "<br/>";
                $this->output['api']['Error'] = 'Invalid action';
            }

            // Set output to the view
            $this->view->output = json_encode( $this->output );
        }else{
            $this->view->output = json_encode( $this->output );
        }
    }
}
