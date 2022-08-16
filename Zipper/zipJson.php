<?php

require_once __DIR__ ."\..\Formatter\measuringUnitKeys.php";

ini_set('memory_limit', '10000M');
//unset time limit for this script
set_time_limit(0);

function keys_to_index($array, $pattern)
{

  //$array[$subArray,$subArray,$subArray]
  //$subArray     key=>value
  //$patternArray key=>key

  //$index_key    index=>key
  $index_key = array_values($pattern);
  //$key_index    key=>index
  $key_index = array_flip($index_key);

  foreach ($array as $subArrayKey=>$subArray) {
    $newSubArray=[];
    foreach($subArray as $key=>$value){
        if (!isset($key_index[$key])) {
            echo 'error or already zipped';
            return $array;
        }
        $newSubArray[$key_index[$key]] = $value;
    }
    $array[$subArrayKey]=$newSubArray;
  }
  return $array;
}

function trimJson($relativeFilePath){
  file_put_contents(__DIR__ . $relativeFilePath, json_encode(json_decode(file_get_contents(__DIR__ .$relativeFilePath), true)));
}

//json into array, 'true' is for converting into associative array
$carArrays = json_decode(file_get_contents(__DIR__ . '/../Formatter/CarArrays.json'), true);
$carArrays=keys_to_index($carArrays,$pattern);
file_put_contents(__DIR__ . '/../Formatter/CarArrays.json', json_encode($carArrays));

//trimJson
trimJson('/../Parser/jsonArray.json');
trimJson('/../data/count-cars-index.json');
trimJson('/../data/uncount-cars-db.json');
