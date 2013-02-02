<?php
class PaypalzzzzzzController extends Zend_Rest_Controller
{
    private $apiCall;
    private $utilities;
    private $paypal;
    private $users;

    public function init()
    {
        $this->_helper->viewRenderer->setNoRender(true);

//        Zend_Loader::loadClass('Billing');
//        $this->apiCall = new Billing();

//        Zend_Loader::loadClass('AlgorithmDefinitions');
//        $this->algorithmsDefinition = new AlgorithmDefinitions();

        require_once('AlgorithmsIO/classes/Utilities.php');
        $this->utilities = new Utilities();

        require_once('AlgorithmsIO/classes/Paypal.php');
        $this->paypal = new Paypal();

        require_once('AlgorithmsIO/classes/Users.php');
        $this->users = new Users();
    }
    public function indexAction()
    {
        // Recieving a POST from Paypal's IPN.  This could be an order

        $isValidatedMessage = $this->paypal->validateMessage( $_POST );                                              
                                                                                                                     
        if( $isValidatedMessage ){                                                                                   
        // Validated Message from Paypal                                                                             
                                                                                                                     
            $isPaymentCompleted = $this->paypal->validatePaymentStatus( $_POST );                                    
            $isPurchaseValid = $this->paypal->validatePurchasedItem( $_POST );
                                                                                                                     
            if( $isPaymentCompleted && $isPurchaseValid ){                                                                            
            // Payment is verified.  Insert credits into this user's account                                         

                $transaction_id = $this->paypal->getTransactionId( $_POST );
                $users_email = $this->paypal->getUsersEmail( $_POST );
                $purchased_item = $this->paypal->getPurchasedItemNumber( $_POST );
                $user_id_seq = $this->users->getUserIdByEmailAddress( $users_email );

                // Insert Credits into DB for this User
                //
                $credit_id_seq = $this->paypal->insertCreditIntoUsersAccount( $user_id_seq, $transaction_id, $purchased_item );
                                                                                                                     
            }else{                                                                                                   
            // Payment is not completed.                                                                             
                                                                                                                     
                // Not sure what to do at this point                                                                 
            }                                                                                                        
        }else{                                                                                                       
        // Not Verified                                                                                              
                                                                                                                     
            // Not sure what to do at this point                                                                     
        }                                 

        $this->paypal->endofTransaction();

        $this->getResponse()
            ->setHeader('Content-Type','application/json')
            ->setHttpResponseCode(200)
            ->appendBody( 'Not Implemented 1' );
    }

    public function getAction()
    {
        // THis should be only a temporary function to add some credits to a user's account

        $staticKey = $this->_request->getParam( 'key' );
        $user_id_seq = $this->_request->getParam( 'user_id' );

        if( $staticKey == 'ddkeick98347fkwpemcje02jeeucmeDuw3932mw' ){

            if( is_numeric( $user_id_seq ) ){

                // Insert Credits into DB for this User                                                                                              
                //                                                                                                                                   
                $credit_id_seq = $this->paypal->insertCreditIntoUsersAccount( $user_id_seq, 0, 'AC-000500-000' );
            }
        }

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->appendBody("Not implemented 2");
    }

    public function postAction()
    {

        $isValidatedMessage = $this->paypal->validateMessage( $_POST );

        $this->getResponse()
            ->setHttpResponseCode(201)
            ->appendBody( "XXXX: " );

    }

    public function putAction()
    {
        $this->getResponse()
            ->appendBody("not implemented 3");

    }

    public function deleteAction()
    {
        $this->getResponse()
            ->setHttpResponseCode(204)
            ->appendBody( "not implemented 4" );

    }
}
?>
