<?php
// require_once  $_SERVER['DOCUMENT_ROOT']."/MoneyTracker/db.php";
require_once  $_SERVER['DOCUMENT_ROOT']."/MoneyTracker/CustomBean.php";
$host = "localhost";
$dbName = "pi";
$user = "pi";
$pass = "admin";
$orm = CustomORM::get_orm($host, $dbName, $user, $pass);
function is_map($variable){
  return !($variable && !empty($variable) && is_int(current(array_keys($variable))));
}

function serialize_data($data) {
  $serializedData = 'a:'; // Start with the array count and a colon
  $count = count($data);
  
  for ($i = 0; $i < $count; $i++) {
      $key = addslashes($data[$i]['key']);
      $value = addslashes($data[$i]['value']);
      
      // Append the key and value to the serialized string with a comma and a colon
      $serializedData .= '{' . strlen($key) . ":" . '\"' . $key . '\"' . ':' . strlen($value) . ":" . '\"' . $value . '"';
      
      // Add a comma at the end of each element except for the last one
      if ($i < $count - 1) {
          $serializedData .= ',';
      }
  }
  
  $serializedData .= '}'; // Close the array
  return $serializedData;
}

function unserialize_data($serializedString, $className) {
  // Unserialize the string and convert it into an array
  $dataArray = unserialize($serializedString);
  
  // Create a new object of the specified class
  $object = new $className();
  
  // Iterate through the array elements and set properties on the object
  foreach ($dataArray as $key => $value) {
      if (is_int($key)) {
          // If the key is an integer, it's a property name
          $propertyName = 's:' . strlen($key) . ":" . $key;
          $object->$propertyName = $value;
      } else {
          // If the key is a string, it's a nested array
          if (is_array($value)) {
              $nestedProperty = 'a:' . count($value) . '{';
              
              foreach ($value as $item) {
                  $nestedProperty .= unserialize_data($item['key'], $className) . ',';
              }
              
              $nestedProperty .= '}';
              $object->$key = new StdClass();
              $object->$key->$propertyName = 'a:1:{i:0;' . $nestedProperty . '}';
          } else {
              // If the value is a simple type, set it directly on the object
              $object->$key = $value;
          }
      }
  }
  
  return $object;
}

function map_to_object($data, $className) {
  // Remove additional attributes from JSON data

  $classRef = new ReflectionClass($className);
  $properties = $classRef->getProperties();
  
  foreach ($properties as $property) {
      if (isset($data[$property->getName()])) {
          unset($data[$property->getName()]);
      }
  }

  // Convert the filtered JSON data to an array
  $filteredData = array_values($data);

  // Unserialize the filtered data into an object of the specified class
  // return unserialize('O:4:"' . $className . '":{s:4:"name";N;s:6:"value";N;}E');
  return unserialize_data(serialize_data($filteredData), $className);

}

function map_string_to_object($jsonString, $className) {
  $data = json_decode($jsonString, true);
  return map_to_object($data, $className);
}