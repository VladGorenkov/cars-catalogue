<?php
ini_set('memory_limit', '10000M');
//unset time limit for this script
set_time_limit(0);

//json into array, True is for converting into associative array
//create count-cars-index.php
$countCarsIndex = json_decode(file_get_contents(__DIR__ . '/../count-cars-index.json'), true);
function create_count_cars_index_php($inputArray)
{
    foreach ($inputArray as &$array) {
        foreach($array as $id =>$value){
            //double bracket         
            $array["[" . $id . "]"] = $value;
            unset($array[$id]);
        } 
    }
    foreach ($inputArray as &$array) {
        foreach($array as $id =>&$value){
            $value = "[[" . $value . "]]" . ",";
        } 
    }
    // foreach ($inputArray as $key => &$array) {
    //     //single bracket
    //     $array["field"] = "[" . $array["field"] . "]" . ",";
    //     foreach ($array["index"] as $key => &$value)
    //         //double bracket
    //         if (!ctype_digit(str_replace(".", "", $value))) {
    //             $value = "[[" . $value . "]]" . "," .$array["field"]. "NOT INTEGER";
    //         } else if (substr_count($value, ".") > 1) {
    //             $value = "[[" . $value . "]]" . "," .$array["field"]. "MANY POINTS";
    //         } else {
    //             $value = "[[" . $value . "]]" . ",";
    //         }
        // foreach ($array["index"] as $key => $value) {
        //     //double bracket
        //     $array["index"]["[" . $key . "]"] = $value;
        //     unset($array["index"][$key]);
        // }
    // }
    $strArray = print_r($inputArray, true);
    $strArray = str_replace("Array\n", "array ", $strArray);
    $strArray = str_replace("array        ", "array", $strArray);
    $strArray = str_replace("array         ", "array", $strArray);
    $strArray = str_replace("[[", "", $strArray);
    $strArray = str_replace("]]", "", $strArray);
    $strArray = str_replace("[", "'", $strArray);
    $strArray = str_replace("]", "'", $strArray);
    $strArray = str_replace(")\n", "),", $strArray);
    $strArray = str_replace("    ", "", $strArray);
    $strArray = str_replace("            ", "", $strArray);
    $strArray = str_replace("                    ", "", $strArray);
    $strArray = substr_replace($strArray, ";", -1);
    return "<?php return " . $strArray;
        }
file_put_contents(__DIR__ . '/../count-cars-index.php', create_count_cars_index_php($countCarsIndex));// PHP формат сохраняемого значения.