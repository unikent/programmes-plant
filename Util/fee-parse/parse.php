<?php

// Include csv iterator
include "csv.php";

$in = 'Feelist.csv';
$out = 'feedata.out.csv';

// Get data
$csvIterator = new CsvIterator($in);

// Add function to write output csv
function outputCSV($data, $out) {
    $outputBuffer = fopen($out, 'w');
    foreach($data as $val) {
        fputcsv($outputBuffer, $val);
    }
    fclose($outputBuffer);
}

// Add title row
$final = [['pos'=>'pos', 'home'=> 'home', 'away'=> 'away']];

foreach ($csvIterator as $row => $data) {

    // do somthing with $data
    $pos = $data[1];
    $type = $data[21]; // home os 1/3=home, 2=abroad
    $feecode = $data[30]; // field26

    // if item doesnt exist, make it
    if(!isset($final[$pos])) $final[$pos] = array('pos' => $pos);

    if(!isset($final[$pos]['home']) && $type == 1) $final[$pos]['home'] = $feecode;
    if(!isset($final[$pos]['away']) && $type == 2) $final[$pos]['away'] = $feecode;

}
// write
outputCSV($final, $out);

