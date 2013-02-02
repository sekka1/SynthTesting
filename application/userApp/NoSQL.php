<?php
// NoSQL usage, based on Amazon DynamoDB

require_once dirname(__FILE__) . '/Amazon_SDK/sdk-1.5.3/sdk.class.php';

class NoSQL
{
    private $debug;
    private $dynamodb;

    public function __construct(){

        $this->debug = false;

        // Instantiate the class.
        $this->dynamodb = new AmazonDynamoDB();

        // Disable SSL
        @$this->dynamodb->disable_ssl();
    }
    public function setDebugTrue(){

        $this->debug = true;
    }
    public function getCount( $tablename, $datasource_id_seq, $start_count ){

/*
To get the full count you have to keep on calling this with the hight $start_count until the "LastEvaluatedKey" is NULL or not returned.
This is b/c: https://forums.aws.amazon.com/message.jspa?messageID=323365

    [body] => CFSimpleXML Object
        (
            [ConsumedCapacityUnits] => 512.5
            [Count] => 1838
            [LastEvaluatedKey] => CFSimpleXML Object
                (
                    [HashKeyElement] => CFSimpleXML Object
                        (
                            [N] => 1316
                        )

                    [RangeKeyElement] => CFSimpleXML Object
                        (
                            [N] => 1838
                        )

                )

        )



*/

        // This will get a count of how many items this query will return and return the count
        $response = $this->dynamodb->query(array(
                    'TableName' => $tablename,
                    'Count' => 'true',
                    'HashKeyValue' => array( AmazonDynamoDB::TYPE_NUMBER => (string)$datasource_id_seq ),
                    'RangeKeyCondition' => array(
                        'ComparisonOperator' => AmazonDynamoDB::CONDITION_GREATER_THAN,
                        'AttributeValueList' => array(
                            array( AmazonDynamoDB::TYPE_NUMBER => (string)$start_count )
                            )                                                                                                                                                
                        )
                    ));

        $body = $response->body->to_array()->getArrayCopy();

        return $body['Count'];
    }
    public function getItemsRange( $tablename, $datasource_id_seq, $limit, $lastLocation ){
        // Returns an array of the range that was specified and the last key it returned

        // Setting last location array for the query
        $ExclusiveStartKey_array['HashKeyElement'] = array( AmazonDynamoDB::TYPE_NUMBER => (string)$datasource_id_seq );
        $ExclusiveStartKey_array['RangeKeyElement'] = array( AmazonDynamoDB::TYPE_NUMBER => (string)$lastLocation );

        $response = $this->dynamodb->query(array(
                    'TableName' => $tablename,
                    'Limit' => $limit,
                    'ExclusiveStartKey' => $ExclusiveStartKey_array,
                    'HashKeyValue' => array( AmazonDynamoDB::TYPE_NUMBER => (string)$datasource_id_seq ),
                    'RangeKeyCondition' => array(
                        'ComparisonOperator' => AmazonDynamoDB::CONDITION_GREATER_THAN_OR_EQUAL,
                        'AttributeValueList' => array(
                            array( AmazonDynamoDB::TYPE_NUMBER => '0' )
                            )
                        )
                    ));

        $body = $response->body->to_array()->getArrayCopy(); 

        // Build return array
        $returnVar['Items'] = $body['Items'];    
        $returnVar['lastKey'] = $body['LastEvaluatedKey']['RangeKeyElement']['N'];

        return $returnVar;
    }
    public function updateItemArbitraryAttributes( $tablename, $datasource_id_seq, $item_number, $field_user_id, $field_item_id ){

            $response = $this->dynamodb->update_item(array(
                    'TableName' => $tablename,
                    'Key' => array(
                        'HashKeyElement' => array( // "datasource_id_seq" column
                            AmazonDynamoDB::TYPE_NUMBER => (string)$datasource_id_seq
                            ),
                        'RangeKeyElement' => array( // "item_id_seq" column
                            AmazonDynamoDB::TYPE_NUMBER => (string)$item_number
                            )
                        ),
                    'AttributeUpdates' => array(
                        'field_user_id' => array(
                            'Action' => AmazonDynamoDB::ACTION_PUT,
                            'Value' => array(AmazonDynamoDB::TYPE_STRING => (string)$field_user_id )
                            ),
                        'field_item_id' => array(
                            'Action' => AmazonDynamoDB::ACTION_PUT,
                            'Value' => array(AmazonDynamoDB::TYPE_STRING => (string)$field_item_id )
                            ),
                        )
                    ));                                                                                                                                         
            $result = 'Failed';

            if( $response->isOK() )
                $result = 'Success';

            return $result;
    }
}
