<?php

require_once __DIR__ . "\..\Formatter\measuringUnitKeys.php";

ini_set('memory_limit', '10000M');
//unset time limit for this script
set_time_limit(0);

function index_to_keys($array, $pattern)
{

    //$array[$subArray,$subArray,$subArray]
    //$subArray     index=>value
    //$patternArray key=>key

    //$index_key    index=>key
    $index_key = array_values($pattern);
    //$key_index    key=>index
    $key_index = array_flip($index_key);

    foreach ($array as $subArrayKey => $subArray) {
        $newSubArray = [];
        foreach ($subArray as $index => $value) {
            if (!isset($index_key[$index])) {
                echo 'error or already unzipped';
                return $array;
            }
            $newSubArray[$index_key[$index]] = $value;
        }
        $array[$subArrayKey] = $newSubArray;
    }
    return $array;
}
function prettyPrintJson($relativeFilePath){
    file_put_contents(__DIR__ . $relativeFilePath, json_encode(json_decode(file_get_contents(__DIR__ .$relativeFilePath), true), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
  }
//json into array, 'true' is for converting into associative array
$carArrays = json_decode(file_get_contents(__DIR__ . '/../Formatter/CarArrays.json'), true);
$carArrays = index_to_keys($carArrays, $pattern);
file_put_contents(__DIR__ . '/../Formatter/CarArrays.json', json_encode($carArrays, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

//pretty print 
prettyPrintJson('/../Parser/jsonArray.json');
prettyPrintJson('/../data/count-cars-index.json');
prettyPrintJson('/../data/uncount-cars-db.json');