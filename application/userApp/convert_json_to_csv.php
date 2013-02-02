<?php
ini_set('memory_limit', '4024M');

$data1_json = file_get_contents( 'json_data-All-Category.txt' );

$data1 = json_decode( $data1_json, true );

$csv_output = '';

$isFirstItteration = true;

// For each line in the main data file 
foreach( $data1['data'] as $aRow ){

    $temp_output = '';

    if( $isFirstItteration ){
        // Put header in output string

        $isFirstItteration = false;

        foreach( $aRow as $key=>$val ){

            $temp_output .= $key.',';
        }

        // remove trailing comma
        $temp_output = preg_replace( '/,$/', '', $temp_output ); 

        $csv_output .= $temp_output . "\n";

        $temp_output = '';
    }

    // Save the data
    foreach( $aRow as $key=>$val ){
        $temp_output .= $val.',';
    }
    // remove trailing comma
    $temp_output = preg_replace( '/,$/', '', $temp_output );

    $csv_output .= $temp_output . "\n";
}

//print_r( $remapped_array );
// Output File:
$filename = "csv_output.csv";

$fp = fopen($filename, 'w+');
fwrite($fp, $csv_output);
fclose($fp);


?>
