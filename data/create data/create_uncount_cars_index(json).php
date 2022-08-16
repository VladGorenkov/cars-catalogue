<?php
/*
 * Create faceted index from data base
 */
require_once __DIR__ . "/../../src/Index.php";
/*
 * Getting products data from DB
 */
//json into array, True is for converting into associative array
$uncountCars = json_decode(file_get_contents(__DIR__ . '/../uncount-cars-db.json'), true);
function create_uncount_cars_index_json($uncountCars){
    $searchIndex = new Index();
    foreach ($uncountCars as $car) {
        $recordId = $car['id'];
        $itemData = [];
        foreach ($car['fields'] as $field => $value) {
            $itemData[$field] = $value;
        }

        $searchIndex->addRecord($recordId, $itemData);
    }
    // save index data to some storage
    $indexData = $searchIndex->getData();
    // We will use file for example
    return json_encode($indexData);
   
}
file_put_contents(__DIR__ . '/../uncount-cars-index.json',create_uncount_cars_index_json($uncountCars));

