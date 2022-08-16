<?php
require_once "src\Index.php";
require_once "src\Search.php";
require_once "src\Filter\ValueFilter.php";
require_once "Formatter\measuringUnitKeys.php";



function sortFilters($filtersData, $countableKeys, $uncountableKeys)
{
    if (!empty($filtersData)) {
        //sort urlFilters
        if (array_key_exists('urlFilters', $filtersData)) {
            $filtersData['countFilters'] = [];
            $filtersData['uncountFilters'] = [];
            foreach ($filtersData['urlFilters'] as $name => $values) {
                if (in_array($name, $countableKeys)) {
                    $filtersData['countFilters'][$name] = $values;
                } else if (in_array($name, $uncountableKeys)) {
                    $filtersData['uncountFilters'][$name] = $values;
                }
            }
        }
        if (!empty($filtersData['countFilters']) || !empty($filtersData['uncountFilters'])) {
            $filtersData['uncountFilters']['id'] = [];
        }
    }
    return $filtersData;
}

function UncountFiltersToIDs($filtersData, $indexData, &$countIndex)
{
    if (empty($filtersData['uncountFilters'])) {
        return;
    }
    foreach ($filtersData['uncountFilters'] as $activeFilter => $values) {
        if ($activeFilter == 'id') {
            continue;
        }
        foreach ($values as $value) {
            if (empty($filtersData['uncountFilters']['id'])) {
                $filtersData['uncountFilters']['id'] = $indexData[$activeFilter][$value];
            } else {
                $filtersData['uncountFilters']['id'] = array_intersect($filtersData['uncountFilters']['id'], $indexData[$activeFilter][$value]);
            }
        }
        // truncate $countIndex
        foreach ($countIndex as &$sortedArray) {
            $sortedArray = array_intersect_key($sortedArray, array_flip($filtersData['uncountFilters']['id']));
        }
        // echo $activeFilter;
        // echo print_r($filtersData['uncountFilters']['id']);
    }
    return  $filtersData['uncountFilters']['id'];
}
/**
 * @param int $element
 * @param int $round true - 'up' or false 'down'
 * @return mixed
 */
function binarySearch(float $element, array $keys, array $values, bool $round)
{
    $begin = 0;
    $end = count($values) - 1;
    $prev_begin = $begin;
    $prev_end = $end;
    while (true) {
        $position = round(($begin + $end) / 2);

        if (isset($values[$position])) {
            if ($values[$position] == $element) {
                switch ($round) {
                    case true:
                        if (isset($values[$position + 1])) {
                            while ($values[$position] == $values[$position + 1]) {
                                $position = $position + 1;
                                // echo '[+' . $position . '==>' . $values[$position] . ']                      ';
                            }
                            // echo 'FINAL' . $position;
                            return $position;
                        }
                    case false:
                            while (isset($values[$position - 1]) && $values[$position] == $values[$position - 1]) {
                                $position = $position - 1;
                                // echo '[-' . $position . '==>' . $values[$position] . ']                      ';
                            }
                        
                        // echo 'FINAL' . $position;
                        return $position;
                }
            }
            if ($values[$position] > $element) {
                $end = floor(($begin + $end) / 2);
            } elseif ($values[$position] < $element) {
                $begin = ceil(($begin + $end) / 2);
            }
        }
        if ($prev_begin == $begin && $prev_end == $end) {
            return $position;
        }
        $prev_begin = $begin;
        $prev_end = $end;
    }
}
function CountFiltersToIDs($filtersData, &$countIndex)
{
    if (empty($filtersData['uncountFilters'])) {
        return;
    }
    foreach ($filtersData['countFilters'] as $activeFilter => $value) {

        $IDs = array_keys($countIndex[$activeFilter]);
        $values = array_values($countIndex[$activeFilter]);

        $minPos = binarySearch($value[0], $IDs, $values, false);
        $maxPos = binarySearch($value[1], $IDs, $values, true);

        $IDs = array_slice($IDs, $minPos, $maxPos - $minPos + 1);
        // $values=array_slice($values, $minPos, $maxPos - $minPos + 1);
        if (empty($filtersData['uncountFilters']['id'])) {
            $filtersData['uncountFilters']['id'] = $IDs;
        } else {
            $filtersData['uncountFilters']['id'] = array_intersect($filtersData['uncountFilters']['id'], $IDs);
        }
        // truncate $countIndex
        foreach ($countIndex as $field => &$sortedArray) {
            if (array_key_exists($field, $filtersData['countFilters'])) {
                continue;
            }
            $sortedArray = array_intersect_key($sortedArray, array_flip($filtersData['uncountFilters']['id']));
        }
        // echo $activeFilter;
        // print_r($filtersData['uncountFilters']['id']);
    }
    return $filtersData['uncountFilters']['id'];
}

function createValueFilters($filtersData){
    $valueFilters = [];
    if (empty($filtersData['uncountFilters']['id'])) {
        unset($filtersData['uncountFilters']['id']);
    }
    foreach ($filtersData['uncountFilters'] as $field => $values) {
        $valueFilters[] = new ValueFilter($field, $values);
    }
    return $valueFilters;
}

/**
 * Find acceptable filters
 * @param Search $search
 * @param array $filters
 * @return array
 */
function findUncountableFilters(Search $search, array $uncountFilters): array
{
    $data = $search->findAcceptableFiltersCount($uncountFilters);
    foreach ($data as &$filterValues) {
        ksort($filterValues);
    }
    unset($filterValues);
    return $data;
}
function findCountableFilters(array $countIndex, array $filtersData): array
{
    foreach ($countIndex as $field => $sortedArray) {
        if (empty($sortedArray)) {
            unset($countIndex[$field]);
            continue;
        }
        $MIN = min($sortedArray);
        $MAX = max($sortedArray);
        if (array_key_exists($field, $filtersData['countFilters']) && count($filtersData['countFilters']) > 1) {
            //active filterS
            $min = $filtersData['countFilters'][$field][0];
            $max = $filtersData['countFilters'][$field][1];
            $gap = $max - $min;
            //MIN......min.gap.max......MAX
            if (($min - $MIN) > $gap && ($MAX - $max) > $gap) {
                $MIN = round($min - $gap);
                $MAX = round($max + $gap);
            }
            //MIN...min.gap.max.........MAX
            else if (($min - $MIN) < $gap && ($MAX - $max) > $gap) {
                $MIN = min($sortedArray);
                $MAX = round($MIN + 2 * $gap);
            }
            //MIN.........min.gap.max...MAX
            else if (($min - $MIN) > $gap && ($MAX - $max) < $gap) {
                $MIN = round($MAX - 2 * $gap);
                $MAX = max($sortedArray);
            }
            //MIN...min....gap....max...MAX
            else if (($min - $MIN) < $gap && ($MAX - $max) < $gap) {
                // $MIN=max($sortedArray);
                // $MAX=max($sortedArray);
            }
            $countIndex[$field] = [$MIN, $MAX, true];
        } else if (array_key_exists($field, $filtersData['countFilters']) && count($filtersData['countFilters']) == 1) {
            //active filter
            $countIndex[$field] = [$MIN, $MAX, true];
        } else {
            //inactive filterS
            $countIndex[$field] = [$MIN, $MAX, false];
        }
    }
    // foreach ($countFilters as $field=>$value){
    //     $countIndex[$field]=$countFilters[$field];
    // }
    return $countIndex;
}
/**
 * Find products using filters
 * @param Search $search
 * @param array $filters
 * @param string $category
 * @param int $pageLimit
 * @return array
 */
function findProducts(Search $search, array $filters, array $uncountDb, string $category, int $pageLimit, int $page = 0): array
{
    // find product id
    $data = $search->find($filters);
    $resultItems = [];
    $count = count($data);
    if (!empty($data)) {
        $data = array_chunk($data, $pageLimit);
        if ($page < 0) {
            $page = 0;
        };
        if ($page > array_key_last($data)) {
            $page = array_key_last($data);
        };
        $data = $data[$page];
        foreach ($data as $id) {
            $resultItems[] = $uncountDb[$id];
        }
    }
    return ['data' => $resultItems, 'count' => $count, 'pageLimit' => $pageLimit, 'maxPage' => ceil($count / $pageLimit),  'page' => $page];
}


header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


// Note it's just simplified example. Filter input using your framework
$category = $_GET['cat'];
$page = (int)$_GET['page'] - 1;
$pageLimit = 10;

$filtersData = sortFilters(json_decode($_POST['filters'], true), $measuringUnitKeys, $uncountableKeys);

$countIndex = include './data/' . 'count-' . $category . '-index.php';
$uncountDb = include './data/' . 'uncount-' . $category . '-db.php';
$indexData = json_decode(file_get_contents('./data/' . 'uncount-' . $category . '-index.json'), true);


//convert uncountFilters to IDs and truncate $countIndex
$filtersData['uncountFilters']['id'] = UncountFiltersToIDs($filtersData, $indexData, $countIndex);
//convert countFilters to IDs and truncate $countIndex
$filtersData['uncountFilters']['id'] = CountFiltersToIDs($filtersData, $countIndex);




$valueFilters = createValueFilters($filtersData);




// Load index by product category
// Use database to store index at your production

$searchIndex = new Index();
$searchIndex->setData($indexData);
// create search instance
$search = new Search($searchIndex);
$result = [
    'filters' => ['countFilters' => findCountableFilters($countIndex, $filtersData), 'uncountFilters' => findUncountableFilters($search, $valueFilters)],
    'results' => findProducts($search, $valueFilters, $uncountDb, $category, $pageLimit, $page),
];
echo json_encode($result);
