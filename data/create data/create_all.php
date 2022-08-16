<?php
ini_set('memory_limit', '10000M');
//unset time limit for this script
set_time_limit(0);
include_once(__DIR__."/../../Formatter/FORMAT.php");
include_once('create_uncount_cars_db(json).php');
include_once('create_uncount_cars_db(php).php');
include_once('create_count_cars_index(json).php');
include_once('create_count_cars_index(php).php');
include_once('create_uncount_cars_index(json).php');
