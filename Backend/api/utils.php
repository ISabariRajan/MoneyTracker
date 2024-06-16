<?php

header('Access-Control-Allow-Origin: http://localhost:3000');
// $user = $_POST['name'];
// echo ("Hello from server: $user");

// require_once  $_SERVER['DOCUMENT_ROOT']."/MoneyTracker/db.php";
require_once  $_SERVER['DOCUMENT_ROOT']."/MoneyTracker/CustomBean.php";
$host = "localhost";
$dbName = "pi";
$user = "pi";
$pass = "admin";
// $orm = 
CustomORM::get_orm($host, $dbName, $user, $pass);
function is_map($variable){
  return !($variable && !empty($variable) && is_int(current(array_keys($variable))));
}

function serialize_value($value){
  return strlen($value) . ':\"' . $value;
}
function serialize_key_value($key, $value){
  return serialize_value($key) . '\":' . serialize_value($value) . '"';
}
function serialize_data($data) {
  $serializedData = 'a:'; // Start with the array count and a colon
  $count = count($data);
  // "{"
  $key_value_array = array();
  foreach($data as $key => $value) {
    $key = addslashes("" . $key);
    // echo $key;
    $value = addslashes("" . $value);
    // echo $value;
    // $serializedData .= '{' . strlen($key) . ":" . '\"' . $key . '\"' . ':' . strlen($value) . ":" . '\"' . $value . '"';
    // echo $serializedData;
    // $serializedData = 
    array_push($key_value_array, serialize_key_value($key, $value));
  }
  $serializedData = "{" . join(",", $key_value_array) . "}";
  // "}"

  // for ($i = 0; $i < $count; $i++) {
  //     $key = addslashes($data[$i]['key']);
  //     $value = addslashes($data[$i]['value']);
      
  //     // Append the key and value to the serialized string with a comma and a colon
  //     $serializedData .= '{' . strlen($key) . ":" . '\"' . $key . '\"' . ':' . strlen($value) . ":" . '\"' . $value . '"';
      
  //     // Add a comma at the end of each element except for the last one
  //     if ($i < $count - 1) {
  //         $serializedData .= ',';
  //     }
  // }
  
  // $serializedData .= '}'; // Close the array
  echo $serializedData;
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

function map_to_object($data, $class_name){
  echo "CLASSNAME :  $class_name  ";
  $classRef = new ReflectionClass($class_name);
  $properties = $classRef->getProperties();

  $object = new $class_name();
  echo "RRR: " . get_class($object);
  foreach ($properties as $property) {
    $propertyName = $property->getName();
    $object->$propertyName = $data[$propertyName];
  }
  return $object;
}

function get_request_body(){
  $rawPostBody = file_get_contents('php://input');
  $decodedJson = json_decode($rawPostBody, true);

  // Check if the JSON decoding was successful
  if (JSON_ERROR_NONE === json_last_error()) {
    // The JSON was successfully decoded and can be used
    return $decodedJson;
  } else {
    return array();
  }
}

function format_date_to_sql($date){
  return $date->format('Y-m-d');
}

function get_request_params(){

}


// function map_to_object($data, $className) {
//   // Remove additional attributes from JSON data

//   $classRef = new ReflectionClass($className);
//   $properties = $classRef->getProperties();
  
//   foreach ($properties as $property) {
//       if (isset($data[$property->getName()])) {
//           unset($data[$property->getName()]);
//       }
//   }

//   // Convert the filtered JSON data to an array
//   $filteredData = array_values($data);

//   // Unserialize the filtered data into an object of the specified class
//   // return unserialize('O:4:"' . $className . '":{s:4:"name";N;s:6:"value";N;}E');
//   return unserialize_data(serialize_data($filteredData), $className);

// }

function map_string_to_object($jsonString, $className) {
  $data = json_decode($jsonString, true);
  return map_to_object($data, $className);
}