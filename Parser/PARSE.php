<?php

require_once __DIR__ . '/CurlParser.php';
require_once __DIR__ . '/ArrayFormatter.php';
require_once __DIR__ . '/sql_connector.php';
require_once __DIR__ . '/oddToRightKeys.php';
require_once __DIR__ . '/doubleKeys.php';
require_once __DIR__ . '/measuringUnitKeys.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>Parser</title>
</head>

<body>
  <?php

  ini_set('memory_limit', '10000M');
  //unset time limit for this script
  set_time_limit(0);


  // DOWNLOAD 
  $connector = new CurlParser;

  $json = file_get_contents(__DIR__ . '/jsonArray.json');
  //json into array, true is for converting into associative array
  $CarsArray = json_decode($json, true);

  //grab ALL and write into jsonArray.json
  //get array of brand URLS
  $brandURLs = $connector->get_urls("https://www.auto-data.net/en/allbrands", "//html/body/div[@id='outer']//div[@class='brands']//a");

  //if CarsArray is not empty
  for ($i = 0; $i < count($CarsArray); $i++) {
    array_shift($brandURLs);
  }
  file_put_contents(__DIR__ ."/logs.txt",count ($CarsArray), FILE_APPEND);
  file_put_contents(__DIR__ ."/logs.txt",print_r($brandURLs,1), FILE_APPEND);

  foreach ($brandURLs as $brandURL) {
    //log
    file_put_contents(__DIR__ . "/logs.txt", "\n" . $brandURL . " =>MODELS", FILE_APPEND);
    //search in $brandURL by XPath and log results with strlen($brandURL)tabulation
    $modelURLs = $connector->get_urls($brandURL, "//html/body/div[@id='outer']/ul[@class= 'modelite']//a", strlen($brandURL));

    // // save $CarsArray in jsonArray.json
    // file_put_contents(__DIR__ . "/jsonArray.json", json_encode($CarsArray, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

    foreach ($modelURLs as $modelURL) {
      file_put_contents(__DIR__ . "/logs.txt", "\n" . $modelURL . " =>GENERATIONS", FILE_APPEND);
      $generationURLs = $connector->get_urls($modelURL, "/html/body/div[@id='outer']/table//th/a", strlen($modelURL));

      foreach ($generationURLs as $generationURL) {
        if ($generationURL=="https://www.auto-data.net/en/toyota-tundra-iii-double-cab-standard-bed-generation-8684"){continue;}
        file_put_contents(__DIR__ . "/logs.txt", "\n" . $generationURL . " =>MODIFICATIONS(ENGINES)", FILE_APPEND);
        $modificationURLs = $connector->get_urls($generationURL, "/html/body/div[@id='outer']/table//th/a", strlen($generationURL));

        foreach ($modificationURLs as $modificationURL) {
          file_put_contents(__DIR__ . "/logs.txt", "\n" . $modificationURL . " =>CAR", FILE_APPEND);
          $carArray = $connector->get_car($modificationURL);
          $connector->set_car($carArray, $CarsArray, strlen($modificationURL));

           // save $CarsArray in jsonArray.json
          file_put_contents(__DIR__ . "/jsonArray.json", json_encode($CarsArray, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }
      }
    }
  }




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