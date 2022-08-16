<?php
function recursive_indexation(&$array, string $ParentREFERENCE)
  {
    foreach ($array as $subArrayKey => &$subArray) {
      //if $subArrayKey of subArray==$ParentREFERENCE ->it's reference to Parent(it's not an array)->skip
      //if $subArray==string ->level of $array is the lowest level in ierarchy ->skip
      if ($subArrayKey == $ParentREFERENCE || !is_array($subArray)) {
        continue;
      }
      // add to subArrayS̲ of Array reference to parent [ParentREFERENCE]=>key of SubArray
      $array[$subArrayKey][$ParentREFERENCE] = $subArrayKey;
      //dig to the lowest level of subarrays->
      $this->recursive_indexation($subArray, $ParentREFERENCE);
    }
  }
function set_array_index($array, string $ParentREFERENCE = 'ParentREFERENCE')
  {
    //wipe Cache
    $this->indexedArray = $array;
    $this->recursive_indexation($this->indexedArray, $ParentREFERENCE);
    return $this->indexedArray;
  }

  ?>