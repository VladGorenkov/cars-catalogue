<?php
require_once __DIR__ . '/../../Formatter/ArrayFormatter.php';
require_once __DIR__ . '/../../Formatter/measuringUnitKeys.php';
?>
<?php
//create count-cars-index.json
$json = file_get_contents(__DIR__ . '/../../Formatter/CarArrays.json');
//json into array, True is for converting into associative array
$array = json_decode($json, true);
ArrayFormatter::sort_array_by_pattern($array, array_values($measuringUnitKeys));

function create_count_cars_index_json($inputArray)
{
  //get keys from a sample array
  $keys = array_keys($inputArray[rand(0, count($inputArray))]);

  foreach ($keys as $i => $key) {
    $keyArray = array();
    foreach ($inputArray as $carID=>$carArray) {
      if ($carArray[$key] !== "") {
        $keyArray[$carID+1] = $carArray[$key];
      }
    }
    asort($keyArray);
    unset($keys[$i]);
    $keys[$key] = $keyArray;
  }
  // unset($keys["ID"]);
  $outputArray = [];
  foreach ($keys as $field => &$sortedArray) {
    $outputArray[$field] = $sortedArray;
    // unset($keys[$field]);
  }
  file_put_contents(__DIR__ . "\..\count-cars-index.json", json_encode($outputArray, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
}
create_count_cars_index_json($array);

?>