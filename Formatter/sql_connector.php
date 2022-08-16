<?php
$sqlDataBase = mysqli_connect('localhost', 'root', 'root', 'cars');
  if (!$sqlDataBase) {
    die('Ошибка подключения к БД');
  }

  function implode_create_columns($array)
  {
      $sqlString="";
    foreach ($array as $value) {
      $sqlString .= "`$value` VARCHAR(200) NOT NULL, ";
    }
    return substr_replace($sqlString, "", -2);
  }

  function implode_insert_columns($array)
  {
    $sqlArray="";
    foreach ($array as $value) {
      $sqlArray .= "`$value`, ";
    }
    return "(`ID`, " . substr_replace($sqlArray, "", -2) . ")";
  }

  function implode_insert_array($array)
  {
    $sqlArray="";
    foreach ($array as $value) {
      $sqlArray .= "'$value', ";
    }
    return "(NULL, " . substr_replace($sqlArray, "", -2) . ")";
  }

  function implode_insert_arrays͇($array)
  {
    $sqlArrays="";
    foreach ($array as $subArray) {
      $sqlArrays .= implode_insert_array($subArray) . ", ";
    }
    // return $sqlArrays;
    return substr_replace($sqlArrays, " ", -2);
  }

?>