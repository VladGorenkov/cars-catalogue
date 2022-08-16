<?php
require_once __DIR__ . '/../../Formatter/ArrayFormatter.php';
require_once __DIR__ . '/../../Formatter/measuringUnitKeys.php';
?>
<?php
ini_set('memory_limit', '10000M');
//unset time limit for this script
set_time_limit(0);

// create uncount-cars-db.json
$json = file_get_contents(__DIR__ . '/../../Formatter/CarArrays.json');
//json into array, True is for converting into associative array
$array = json_decode($json, true);
ArrayFormatter::sort_array_by_pattern($array, array_values($uncountableKeys));

function create_uncount_cars_db_json($array){
  foreach ($array as $key => &$carArray) {
      $carArray['id'] = $key + 1;
      $carArray = array(
      "id" => $key + 1,
      "fields" => $carArray
    );
  }
  file_put_contents(__DIR__ . '\..\uncount-cars-db.json', json_encode($array, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
}
create_uncount_cars_db_json($array);
?>