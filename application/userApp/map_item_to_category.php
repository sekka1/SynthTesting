<?php
ini_set('memory_limit', '4024M');

$data1_json = file_get_contents( 'json_data-All.txt' );
$category_json = file_get_contents( 'json_data-category.txt' ); // Part number field is named "Item"

$data1 = json_decode( $data1_json, true );
$category = json_decode( $category_json, true );

$remapped_array['data'] = array();

// For each line in the main data file remap the item to the category
foreach( $data1['data'] as $aRow ){

    echo '.';

    $itemFound = false;
    $temp_category = '';

    // Try to find this item in the category list
    foreach( $category['data'] as $aCategory ){

        // Replace regex unsafe chars
        $aCategory['Item'] = str_replace( '/', '', $aCategory['Item'] );

        if( preg_match( '/^'.$aCategory['Item'].'/', $aRow['INTERNAL_PART_NUMBER'] ) ){
        // Item found
        
            echo 'x';

            // Add the category into the original data
            $aRow['category'] = $aCategory['Category'];
//echo $aRow['category']  . ' - ' $aCategory['Category'] . "\n";
            // Add this row into the remapped array
            array_push( $remapped_array['data'], $aRow );

            $itemFound = true;
        }
        if( $itemFound )
            break;
    }
    // Reset this for next searching itteration
    $itemFound = true;
}

//print_r( $remapped_array );
// Output File:
$filename = "json_data-All-Category.txt";
$content = json_encode( $remapped_array );

$fp = fopen($filename, 'w+');
fwrite($fp, $content);
fclose($fp);


?>
