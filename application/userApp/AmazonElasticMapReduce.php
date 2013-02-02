<?php
// NoSQL usage, based on Amazon DynamoDB

require_once dirname(__FILE__) . '/Amazon_SDK/sdk-1.5.3/sdk.class.php';

class AmazonElasticMapReduce
{
    private $debug;
    private $emr;

    public function __construct(){

        $this->debug = false;

        // Instantiate the class.
        $this->emr = new AmazonEMR();
    }
    public function setDebugTrue(){

        $this->debug = true;
    }
    public function runHadoopPigJob(){
        // Runs a Pig Hadoop job on Amazon EMR

        $returnData['outcome'] = 'success';

        //
        // Creating the Hadoop job
        //
        $name = 'gk_job_flow';
        $instances['MasterInstanceType'] = 'm1.small';
        $instances['SlaveInstanceType'] = 'm1.small';
        $instances['InstanceCount'] = '1';

        //
        // Creating the steps for the hadoop Pig job
        //
        $steps['LogUri'] = 's3://hadoop.algorithms.io/Pig-Scripts/log';

        // Step 1
        $steps['Steps'][0]['Name'] = 'Setup Hadoop Debugging';
        $steps['Steps'][0]['ActionOnFailure'] = 'TERMINATE_JOB_FLOW';
        $steps['Steps'][0]['HadoopJarStep']['Jar'] = 's3://elasticmapreduce/libs/script-runner/script-runner.jar';
        $steps1_args[0] = 's3://elasticmapreduce/libs/state-pusher/0.1/fetch';

        $steps['Steps'][0]['HadoopJarStep']['Args'] = $steps1_args;

        // Step 2
        $steps['Steps'][1]['Name'] = 'Setup Pig';
        $steps['Steps'][1]['ActionOnFailure'] = 'TERMINATE_JOB_FLOW';
        $steps['Steps'][1]['HadoopJarStep']['Jar'] = 's3://elasticmapreduce/libs/script-runner/script-runner.jar';
        $steps2_args[0] = 's3://elasticmapreduce/libs/pig/pig-script';
        $steps2_args[1] = '--base-path';
        $steps2_args[2] = 's3://elasticmapreduce/libs/pig/';
        $steps2_args[3] = '--install-pig';

        $steps['Steps'][1]['HadoopJarStep']['Args'] = $steps2_args;

        // Step 3
        $steps['Steps'][2]['Name'] = 'Run Pig Script';
        $steps['Steps'][2]['ActionOnFailure'] = 'TERMINATE_JOB_FLOW';
        $steps['Steps'][2]['HadoopJarStep']['Jar'] = 's3://elasticmapreduce/libs/script-runner/script-runner.jar';
        $steps3_args[0] = 's3://elasticmapreduce/libs/pig/pig-script';
        $steps3_args[1] = '--base-path';
        $steps3_args[2] = 's3://elasticmapreduce/libs/pig/';
        $steps3_args[3] = '--run-pig-script';
        $steps3_args[4] = '--args';
        $steps3_args[5] = '-p';
        $steps3_args[6] = 'INPUT=s3://elasticmapreduce/samples/pig-apache/input';
        $steps3_args[7] = '-p';
        $steps3_args[8] = 'OUTPUT=s3://hadoop.algorithms.io/Pig-Scripts/output';
        $steps3_args[9] = 's3://hadoop.algorithms.io/Pig-Scripts/referer-value-count.pig';

        $steps['Steps'][2]['HadoopJarStep']['Args'] = $steps3_args;

//print_r( $instances );
//print_r( $steps );

        $returnData['instances'] = $instances;
        $returnData['steps'] = $steps;

        $result = $this->emr->run_job_flow( $name, $instances, $steps );

        $status = $result->status;

        if( $status != '200' ){
            // Other than a success code

            $returnData['outcome'] = 'failed';
            $returnData['status'] = $status;
        } else {
            // Sucess
        
            $returnData['status'] = $status;
            $returnData['jobFlowId'] = $result->body->RunJobFlowResult->JobFlowId;
        }

//print_r( $result );

        return json_encode( $returnData );
    }
}
