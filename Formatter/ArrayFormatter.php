<?php
class ArrayFormatter
{
  public function __construct()
  {
  }

  /**
   * @param mixed $array input array 
   * @param mixed $callback name of function to change the lowest array
   * @param mixed $args array of args for callback function
   * @return void
   */
  protected static function recursive_array_digger(&$array, $callback, array $args, int $digLevel = PHP_INT_MAX)
  {

    if (is_array(end($array)) and $digLevel > 0) {
      //if $subArray of $array==string ->level of $array is the necessary lowest level in ierarchy  
      foreach ($array as &$subArray) {
        if (!is_array($subArray)) {
          continue;
        }
        // echo "INNER=>" . $digLevel;
        ArrayFormatter::recursive_array_digger($subArray, $callback, $args, $digLevel - 1);
      }
    } else {
      // echo "FINAL|";
      // echo "<pre>";
      // print_r($array);
      // echo "</pre>";

      //change this &$subArray
      $args[0] = &$array;
      //with $callback function 
      call_user_func_array($callback, $args);
    }
  }



  protected static function replacing_keys(&$array, array $combinedKeysArray)
  {
    // replace keys
    foreach ($combinedKeysArray as $replaceableKey => $replacingKey) {
      //if there's no replaceableKey in array->skip this array
      if (!array_key_exists($replaceableKey, $array)) {
        continue;
      }
      //replace keys
      $tempKeysArray = array_keys($array);
      $tempValuesArray = array_values($array);
      //find index of $replaceableKey 
      $replaceableKeyIndex = array_search($replaceableKey, array_keys($array));
      //replace $replaceableKey 
      $tempKeysArray[$replaceableKeyIndex] = $replacingKey;
      $array = array_combine($tempKeysArray, $tempValuesArray);
    }
  }
  /**
   * @param mixed $array input array 
   * @param mixed $combinedKeysArray [$replaceableKey => $replacingKey,...]
   * @return void
   */
  public static function  replace_keys(&$array, array $combinedKeysArray)
  {
    ArrayFormatter::recursive_array_digger($array, ["ArrayFormatter", "replacing_keys"], [array(), $combinedKeysArray]);
  }



  protected static function breaking_double_keys(&$array, $doubleKeysArray)
  {
    //0-breakable key 1-breakable symbol 2-new key
    foreach ($doubleKeysArray as $subArray) {
      $breakableKey = $subArray[0];
      $symbol = $subArray[1];
      $newKey = $subArray[2];
      if (!empty($array[$breakableKey]) && str_contains($array[$breakableKey], $symbol)) {
        $result = explode($symbol, $array[$breakableKey]);
        $array[$breakableKey] = trim($result[0]);
        if (!empty($result[1])) {
          $array[$newKey] = trim($result[1]);
        } else {
          $array[$newKey] = "";
        }
      }
    }
  }
  public static function  breake_double_keys(&$array, $doubleKeysArray)
  {
    ArrayFormatter::recursive_array_digger($array, ["ArrayFormatter", "breaking_double_keys"], [array(), $doubleKeysArray]);
  }



  protected static function getting_keys($inputArray, array &$outputArray)
  {
    foreach (array_keys($inputArray) as $key) {
      array_push($outputArray, $key);
    }
  }
  public static function  get_all_keys(&$array)
  {
    $keysArray = array();
    ArrayFormatter::recursive_array_digger($array, ["ArrayFormatter", "getting_keys"], [array(), &$keysArray]);
    return array_values(array_unique($keysArray));
  }



  protected static function adding_missing_keys(&$inputArray, array $keysArray)
  {
    foreach ($keysArray as $key) {
      if (!array_key_exists($key, $inputArray)) {
        $inputArray[$key] = "";
      }
    }
  }
  public static function  add_missing_keys(&$array, array $keysArray)
  {
    ArrayFormatter::recursive_array_digger($array, ["ArrayFormatter", "adding_missing_keys"], [array(), $keysArray]);
  }



  protected static function sorting_array(&$inputArray, array $patternArray)
  {
    $newArray = [];
    for ($i = 0; $i < count($patternArray); $i++) {
      if (array_key_exists($patternArray[$i], $inputArray)) {
        $newArray[$patternArray[$i]] = $inputArray[$patternArray[$i]];
      } else {
        $newArray[$patternArray[$i]] = '';
      }
    }
    $inputArray = $newArray;
  }
  public static function sort_array_by_pattern(&$array, $patternArray)
  {
    ArrayFormatter::recursive_array_digger($array, ["ArrayFormatter", "sorting_array"], [array(), $patternArray]);
  }


  protected static function truncating_array_values(&$array, $targetKeys, string $truncSymbol, bool $side)
  {
    // truncate values
    foreach ($array as $key => &$value) {
      if (in_array($key, $targetKeys) && str_contains(trim($value), $truncSymbol)) {
        $result = explode($truncSymbol, trim($value));
        $value = $result[$side];
      }
    }
  }
  /**
   * @param mixed $array input array 
   * @param mixed $targetKeys search only values with these keys
   * @param string $truncSymbol by this symbol value will be truncated
   * @param bool $side output side of value: left-0 right- 1 both-2
   * 
   * @return void
   */
  public static function  truncate_array_values_by(&$array, $targetKeys, string $truncSymbol, bool $side)
  {
    ArrayFormatter::recursive_array_digger($array, ["ArrayFormatter", "truncating_array_values"], [array(), $targetKeys, $truncSymbol, $side]);
  }

  protected static function replacing_array_values(&$array, $targetKeys, string $search, bool $replace, int $limit = null)
  {
    foreach ($array as $key => &$value) {
      if (in_array($key, $targetKeys) && str_contains(trim($value), $search)) {
        //replace all
        if ($limit == null || $limit == 0) {
          $value = str_replace($search, $replace, $value);
        }
        //first replace
        else if ($limit > 0 && substr_count(trim($value), $search) > 1) {
          $pos = strpos($value, $search);
          $value = substr_replace($value, $replace, $pos, strlen($search));
        }
        //last replace
        else if ($limit < 0 && substr_count(trim($value), $search) > 1) {
          $pos = strrpos($value, $search);
          $value = substr_replace($value, $replace, $pos, strlen($search));
        }
      }
    }
  }
  /**
   * @param mixed $array input array 
   * @param mixed $targetKeys search only values with these keys
   * @param string $search searching symbol 
   * @param string $replace new symbol 
   * @param int $pos 1:first replace -1:last replace 0:replace all
   * @return void 
   */
  public static function  replace_array_values_by(&$array, $targetKeys, string $search, string $replace, int $limit = 0)
  {
    ArrayFormatter::recursive_array_digger($array, ["ArrayFormatter", "replacing_array_values"], [array(), $targetKeys, $search, $replace, $limit]);
  }


  protected static function averaging_array_values(&$array, $targetKeys, string $symbol, int $roundPrecision)
  {
    $exceptions = [];
    //exceptions for "/"
    switch ($symbol) {
      case "/":
        $exceptions = [
          ($array["Brand"] == "Audi" && $array["Model"] == "S6" && $array["Generation"] == "S6 Avant (C8)" && $array["Modification (Engine)"] == "3.0 TDI V6 (349 Hp) quattro Tiptronic"),
          ($array["Brand"] == "Audi" && $array["Model"] == "S6" && $array["Generation"] == "S6 (C8)" && $array["Modification (Engine)"] == "3.0 TDI V6 (349 Hp) quattro Tiptronic"),
          ($array["Brand"] == "Audi" && $array["Model"] == "S7" && $array["Generation"] == "S7 Sportback (C8)" && $array["Modification (Engine)"] == "3.0 TDI V6 (349 Hp) quattro Tiptronic"),
        ];
    }
    if (in_array(1, $exceptions)) {
      echo "exception " . $symbol . ": " . $array["Brand"] . " " . $array["Model"] . " " . $array["Generation"] . " " . $array["Modification (Engine)"] . "\n";
    } else {
      foreach ($array as $key => &$value) {
        if (in_array($key, $targetKeys)) {
          if (str_contains($value, $symbol)) {
            //exceptions for "-"
            if (str_contains($value, "/") && $symbol == "-") {
              $value = str_replace("/", "-", $value);
            }
            //exceptions for "["
            if ($symbol == "[" || $symbol == "(") {
              $value = str_replace(-1, "", $value);
            }
            $result = explode($symbol, trim($value));
            $value = round(array_sum($result) / 2, $roundPrecision);
            // if(count($result)>2){
            //   echo "exception ".$symbol.": ". $array["Brand"]. " " . $array["Model"]. " " . $array["Generation"]. " " . $array["Modification (Engine)"] . "\n";
            //   echo print_r($result)."average between ".count($result)." =".$value."\n";
            // }
          }
        }
      }
    }
  }
  protected static function summing_array_values(&$array, $targetKeys, string $symbol)
  {
    foreach ($array as $key => &$value) {
      if (in_array($key, $targetKeys)) {
        if (str_contains($value, $symbol)) {
          $result = explode($symbol, trim($value));
          $value = array_sum($result);
          //   if(count($result)>2){
          //   echo "exception ".$symbol.": ". $array["Brand"]. " " . $array["Model"]. " " . $array["Generation"]. " " . $array["Modification (Engine)"] . "\n";
          //   echo print_r($result)."sum between ".count($result)." =".$value."\n";
          // }
        }
      }
    }
  }
  /**
   * @param mixed $array input array 
   * @param mixed $targetKeys search only values with these keys
   * @param string $symbol by this symbol value will be averaged
   * @param int $roundPrecision of arithmetic mean
   * 
   * @return void
   */
  public static function  calculate_array_values_by(&$array, $targetKeys, string $symbol, $roundPrecision = 1)
  {
    switch ($symbol) {
      case "-":
        ArrayFormatter::recursive_array_digger($array, ["ArrayFormatter", "averaging_array_values"], [array(), $targetKeys, $symbol, $roundPrecision]);
        break;
      case "−":
        ArrayFormatter::recursive_array_digger($array, ["ArrayFormatter", "averaging_array_values"], [array(), $targetKeys, $symbol, $roundPrecision]);
        break;
      case "/":
        ArrayFormatter::recursive_array_digger($array, ["ArrayFormatter", "averaging_array_values"], [array(), $targetKeys, $symbol, $roundPrecision]);
        break;
      case "[":
        ArrayFormatter::recursive_array_digger($array, ["ArrayFormatter", "averaging_array_values"], [array(), $targetKeys, $symbol, $roundPrecision]);
        break;
      case "(":
        ArrayFormatter::recursive_array_digger($array, ["ArrayFormatter", "averaging_array_values"], [array(), $targetKeys, $symbol, $roundPrecision]);
        break;
      case "+":
        ArrayFormatter::recursive_array_digger($array, ["ArrayFormatter", "summing_array_values"], [array(), $targetKeys, $symbol]);
        break;
    }
  }


  /**
   * @param mixed $array input array 
   * @param mixed $targetKeys search only values with these keys
   * @param string $symbol by this symbol value will be averaged
   * @param int $roundPrecision of arithmetic mean
   * 
   * @return void
   */
  public static function  sum_array_values_by(&$array, $targetKeys, string $symbol, $roundPrecision)
  {
    ArrayFormatter::recursive_array_digger($array, ["ArrayFormatter", "summing_array_values"], [array(), $targetKeys, $symbol, $roundPrecision]);
  }



  protected static function decomposition($inputArray, array &$outputArray, bool $ID)
  {
    foreach ($inputArray as $subKey => $subArray) {
      if (is_array($subArray)) {
        foreach ($subArray as $subSubKey => $subSubArray) {
          if (is_array($subSubArray) and !$ID) {
            array_push($outputArray, [$subKey, $subSubKey]);
          } else if (!is_array($subSubArray) and $ID and $subSubKey == "ID") {
            array_push($outputArray, [$subKey, $subSubArray]);
          }
        }
      }
    }
  }
  public static function  decompose_array(&$array, int $decomposeLevel, bool $ID)
  {
    $outputArray = array();
    ArrayFormatter::recursive_array_digger($array, ["ArrayFormatter", "decomposition"], [array(), &$outputArray, $ID], $decomposeLevel);
    return $outputArray;
  }



  protected static function get_array($inputArray, array &$outputArray)
  {
    array_push($outputArray, $inputArray);
  }
  public static function  get_lowest_arrays(&$array)
  {
    $outputArray = array();
    ArrayFormatter::recursive_array_digger($array, ["ArrayFormatter", "get_array"], [array(), &$outputArray]);
    return $outputArray;
  }



  public static function recursive_indexation(&$array, $i = 1, $zeros = 3)
  {
    $I = $i;
    foreach ($array as &$subArray) {
      if (is_array($subArray)) {
        if (key_exists("ID", $array)) {
          $str = $array["ID"] . '>' . str_pad($i, $zeros, '0', STR_PAD_LEFT);
          $subArray = ["ID" => $str] + $subArray;
          $i++;
          // if ($i > 99) {
          // echo $subArray["ID"]."too big ID";
          // }
          ArrayFormatter::recursive_indexation($subArray, $I, $zeros);
        } else {
          $str = str_pad($i, $zeros, '0', STR_PAD_LEFT);
          $subArray = ["ID" => $str] + $subArray;
          $i++;
          ArrayFormatter::recursive_indexation($subArray, $I, $zeros);
        }
      }
    }
  }
}
