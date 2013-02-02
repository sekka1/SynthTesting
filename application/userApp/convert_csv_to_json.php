<?php
ini_set('memory_limit', '4024M');

$handle = @fopen("/root/pericom-020212/categories.csv", "r");

$firstRowIsHeader = true;
$headerRowCount = 0;
$header = array();
$data['data'] = array();

$counter = 0;

if ($handle) {
    while (($buffer = fgets($handle, 4096)) !== false) {

        $counter++;

        $split = preg_split( '/,/', trim( $buffer ) );

        if( $firstRowIsHeader ){
            // Save Header

            $headerRowCount = count( $split );

            $header = $split;

            $firstRowIsHeader = false;
        }else{
            // Save the data

            if( count( $split ) == $headerRowCount ){
                // Verify this row is like the header row

                $temp = array();

                for( $i=0; $i<$headerRowCount; $i++ ){
                    // Put data in a named associative array

                    $temp[$header[$i]] = $split[$i];
                }
                array_push( $data['data'], $temp );
            }
        }
    }

    echo 'Total Lines: '.$counter."\n";

    // Final data
    //print_r( $data );
    $filename = "json_data.txt";
    $content = json_encode( $data );

    $fp = fopen($filename, 'w+');
    fwrite($fp, $content);
    fclose($fp);
}


?>
