<?php
require 'Model/Init.php';
require 'Model/Scraper.php';
$scraper = new Scraper();

$outputs = $scraper->getOutputs();
$date = date('Y-md');
$csv = 'cc-export-'.$date.'.csv';
$data[] = implode('","', array(
    'EAN',
    'Title',
    'Size',
    'Unit Measure',
    'Price',
    'Promo',
    'Price per kg',
    'Original price',
    'Available',
    'Zip Code',
    'Address',
    'City',
    'Link',
    'Date'
));
foreach($outputs as $row){
    $data[] = implode('","', array(
        $row['ean'],
        stripslashes(str_replace(',', ' ', trim(preg_replace('/\s+/', ' ', html_entity_decode($row['title']))))),
        $row['size'],
        $row['unit_measure'],
        $row['price'],
        $row['is_promo'],
        $row['price_per_kg'],
        $row['original_price'],
        $row['available'],
        stripslashes(str_replace(',', ' ', trim(preg_replace('/\s+/', ' ', html_entity_decode($row['zip_code']))))),
        stripslashes(str_replace(',', ' ', trim(preg_replace('/\s+/', ' ', html_entity_decode($row['address']))))),
        stripslashes(str_replace(',', ' ', trim(preg_replace('/\s+/', ' ', html_entity_decode($row['city']))))),
        $row['link'],
        $row['date']
    ));
}

$file = fopen($csv,"w");
foreach ($data as $line){
    fputcsv($file, explode('","',$line));
}
fclose($file);



// Output CSV-specific headers

header('Content-Type: text/csv; charset=utf-8');
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"" . basename($csv) . "\"");
readfile($csv);