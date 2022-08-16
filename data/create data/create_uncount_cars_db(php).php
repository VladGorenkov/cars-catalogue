<?php
//json into array, True is for converting into associative array
//create count-cars-index.php
$uncountCars = json_decode(file_get_contents(__DIR__ . '/../uncount-cars-db.json'), true);
function create_uncount_cars_db_php($inputArray){
    foreach ($inputArray as $key => $array) {
        $inputArray["leftBracket" . $array["id"] . "rightBracket"] = $array;
        unset($inputArray[$key]);
    }
    foreach ($inputArray as $key => &$array) {
        $array["id"] =  $array["id"] . ",";
        foreach ($array["fields"] as &$value)
            $value ="[" .$value. "]" . ",";
    }
    $strArray = print_r($inputArray, true);
    $strArray = str_replace("Array\n", "array ", $strArray);
    $strArray = str_replace("array        ", "array", $strArray);
    $strArray = str_replace("array         ", "array", $strArray);
    $strArray = str_replace("[leftBracket", "", $strArray);
    $strArray = str_replace("rightBracket]", "", $strArray);
    // Escaping quotation marks
    $strArray = str_replace("'", "\'", $strArray);
    $strArray = str_replace("[", "'", $strArray);
    $strArray = str_replace("]", "'", $strArray);
    $strArray = str_replace(")\n", "),", $strArray);
    $strArray = str_replace("    ", "", $strArray);
    $strArray = str_replace("            ", "", $strArray);
    $strArray = str_replace("                    ", "", $strArray);
    $strArray = substr_replace($strArray, ";", -1);
    return "<?php return " . $strArray;
}
file_put_contents(__DIR__ . '/../uncount-cars-db.php', create_uncount_cars_db_php($uncountCars));// PHP формат сохраняемого значения.