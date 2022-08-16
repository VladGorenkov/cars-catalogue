<?php

require_once __DIR__ . '/ArrayFormatter.php';
require_once __DIR__ . '/oddToRightKeys.php';
require_once __DIR__ . '/doubleKeys.php';
require_once __DIR__ . '/measuringUnitKeys.php';
?>
  <?php

  ini_set('memory_limit', '10000M');
  //unset time limit for this script
  set_time_limit(0);





  //FORMAT ARRAY
  $json = file_get_contents(__DIR__ . '/../Parser/jsonArray.json');
  //json into array, true is for converting into associative array
  $array = json_decode($json, true);

  // replace odd keys
  ArrayFormatter::replace_keys($array, $oddToRightKeys);
  //add measuring units to keys
  ArrayFormatter::replace_keys($array,$measuringUnitKeys);
  //break double keys
  ArrayFormatter::breake_double_keys($array,$doubleKeys);
  // get all unique keys
  $allKeysArray = ArrayFormatter::get_all_keys($array);
  //add missing keys
  ArrayFormatter::add_missing_keys($array, $allKeysArray);
  //adjust all arrays to common pattern ($allKeysArray)
  ArrayFormatter::sort_array_by_pattern($array, array_values($pattern));
  //truncate array values by symbol
  ArrayFormatter::truncate_array_values_by($array,$measuringUnitKeys," ",0);
  ArrayFormatter::truncate_array_values_by($array,$measuringUnitKeys,"°",0);
  ArrayFormatter::truncate_array_values_by($array,$measuringUnitKeys,"v",0);
  ArrayFormatter::truncate_array_values_by($array,$measuringUnitKeys,"об",0);
  ArrayFormatter::truncate_array_values_by($array,$measuringUnitKeys,"H",0);
  ArrayFormatter::truncate_array_values_by($array,$measuringUnitKeys,">",1);
  ArrayFormatter::truncate_array_values_by($array,$measuringUnitKeys,"~",1);
  //replace array values by symbol on new symbol
  ArrayFormatter::replace_array_values_by($array,$measuringUnitKeys," ","");
  ArrayFormatter::replace_array_values_by($array,$measuringUnitKeys," ","");
  ArrayFormatter::replace_array_values_by($array,$measuringUnitKeys," ","");
  ArrayFormatter::replace_array_values_by($array,$measuringUnitKeys,"l","1");
  ArrayFormatter::replace_array_values_by($array,$measuringUnitKeys,",",".");
  //average array values by symbol
  ArrayFormatter::calculate_array_values_by($array,$measuringUnitKeys,"-",1);
  ArrayFormatter::calculate_array_values_by($array,$measuringUnitKeys,"−",1);
  ArrayFormatter::calculate_array_values_by($array,$measuringUnitKeys,"/",1);
  ArrayFormatter::calculate_array_values_by($array,$measuringUnitKeys,"[",1);
  ArrayFormatter::calculate_array_values_by($array,$measuringUnitKeys,"(",1);
  //summ array values by symbol
  ArrayFormatter::calculate_array_values_by($array,$measuringUnitKeys,"+",1);
  ArrayFormatter::truncate_array_values_by($array,$measuringUnitKeys,"/",0);
  //replace odd points
  ArrayFormatter::replace_array_values_by($array,$measuringUnitKeys,".","",-1);

  
  // //indexation
  // ArrayFormatter::recursive_indexation($array,1,3);

  // file_put_contents(__DIR__ . "/jsonData/FormattedJsonArray.json", json_encode($array, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

  // $decomposedArray=ArrayFormatter::decompose_array($array,0,1);
  // file_put_contents(__DIR__ . "/jsonData/BrandsID.json", json_encode($decomposedArray, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
  // $decomposedArray=ArrayFormatter::decompose_array($array,1,1);
  // file_put_contents(__DIR__ . "/jsonData/ModelsID.json", json_encode($decomposedArray, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
  // $decomposedArray=ArrayFormatter::decompose_array($array,2,1);
  // file_put_contents(__DIR__ . "/jsonData/GenerationsID.json", json_encode($decomposedArray, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
  // $decomposedArray=ArrayFormatter::decompose_array($array,3,1);
  // file_put_contents(__DIR__ . "/jsonData/ModificationsID.json", json_encode($decomposedArray, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

  $carArrays=ArrayFormatter::get_lowest_arrays($array);
  
  file_put_contents(__DIR__ . "/CarArrays.json", json_encode($carArrays, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));


  



  // //CREATE TABLE
  // $sqlCreateColumns = implode_create_columns($allKeysArray);
  // $sqlDataBase->query("CREATE TABLE `cars` ( `ID` INT NOT NULL AUTO_INCREMENT ,$sqlCreateColumns, PRIMARY KEY (`ID`)) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

  // //get columns (,,,)
  // $sqlInsertColumns = implode_insert_columns($allKeysArray);
  // //get arrays (,,,),(,,,),(,,,)...
  // $sqlInsertArrays = implode_insert_arrays͇($sortedArray);
  // //insert
  // $sqlDataBase->query("INSERT INTO `cars` $sqlInsertColumns VALUES $sqlInsertArrays");







  echo "<pre>";
  // print_r($carsArray);
  // print_r($decomposedArray);
  // print_r($carsArray);
  // print_r(array_diff($allKeysArray,array_values($pattern)));
  // print_r($allKeysArray);
  // print_r(array_values($pattern));

  // print_r($sortedArray);
  echo "</pre>";
  ?>