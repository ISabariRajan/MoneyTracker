<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
// echo "FIRST";
// Check if the request method is not POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
  // Send a 403 Forbidden response and exit
  header('HTTP/1.1 403 Forbidden');
  exit;
}
// echo "POST";
// echo $_SERVER['DOCUMENT_ROOT']."/MoneyTracker/utils.php";
# Load DB, using RedBeanPHP
require $_SERVER['DOCUMENT_ROOT']."/MoneyTracker/utils.php";
require $_SERVER['DOCUMENT_ROOT']."/MoneyTracker/CustomBean.php";
// require $_SERVER['DOCUMENT_ROOT']."/MoneyTracker/models/device.php";
// require $_SERVER['DOCUMENT_ROOT']."/MoneyTracker/repository/device_repository.php";
// require $_SERVER['DOCUMENT_ROOT']."/MoneyTracker/service/device_service.php";

function get_all_devices(){
  // echo "HI";
  // $device_repository = new DeviceRepository("device");
  // $devices = $device_repository->get_all();

  $service = new DeviceService();
  $devices = $service->get_all();
  // echo "BYE";
  return $devices;
}

// function get_all_devices(){return R::findAll("device");}
function get_missing_device_names($devices){
  $existing_device = get_all_devices();
  $device_names = array();
  // foreach($existing_device as $device){
  //   echo $device->name;
  //   array_push($device_names, $device->name);
  // }
  return $device_names;
}
function save_devices($devices){
  echo $orm;
  // try{
  //   // echo "SAVE";
  //   $device = new DeviceModel();
  //   // echo "III";
  //   foreach(get_missing_device_names($devices) as $device){
  //     // $device_object = R::dispense("device");
  //     // $device_object->name = $device;
  //     // R::store($device_object);
  //   }
  //   return get_all_devices();
  // } catch(Exception $e){
  //   return $e;
  // }
}

function save_meetings($device_map){
  try{
    $devices = array_keys($device_map);
    // Initialize RedBeanPHP
    // $bean = R::fetchModel('test_table'); // Replace 'test_table' with your actual table name
    // foreach($bean as $key => $value){
    //   echo $key;
    // }

    // Get metadata about the table
    // $metadata = R::dbMeta($bean);
    $devices = save_devices($devices);
    // $meetings = R::findAll("meetings");
    // foreach($meetings as $meeting){
    // }



    // foreach($devices as $device){
    //   foreach($device_map[$device] as $meeting){
    //     $meeting_object = R::dispense("meetings");
    //     $meeting_object->join_datetime = $meeting->join_datetime;
    //     $meeting_object->leave_datetime = $meeting->leave_datetime;
    //     $meeting_object->duration_in_minutes = $meeting->duration_in_minutes;
    //     // $meeting_object->device_name = $device;
    //     $meeting_object->type = $meeting->type;
    //     R::store($meeting_object);
    //   }
    // }
  } catch(Exception $e){
    return $e;
  }
  // foreach($device_map as $key => $value){

  // }
}

// echo "INIT";
$rawPostBody = file_get_contents('php://input');
// echo $rawPostBody;
$decodedJson = json_decode($rawPostBody, true);
// echo $decodedJson;

// Check if the JSON decoding was successful
if (JSON_ERROR_NONE === json_last_error()) {
    // The JSON was successfully decoded and can be used
    echo "Received JSON: ";
    echo save_meetings($decodedJson);

} else {
    http_response_code(400);
    echo "Error: Invalid JSON received.";
}
