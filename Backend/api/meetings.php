<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: application/json');

$request_method = $_SERVER['REQUEST_METHOD'];
# Load DB, using RedBeanPHP
require_once $_SERVER['DOCUMENT_ROOT']."/MoneyTracker/utils.php";
require_once $_SERVER['DOCUMENT_ROOT']."/MoneyTracker/CustomBean.php";

class MeetingsController{

  private $repository;

  public function __construct(){
    $repository = new stdClass();
    $repository->devices = CustomORM::repository("devices");
    $repository->meetings = CustomORM::repository("meetings");
    $this->repository = $repository;
  }

  private function get_all_devices(){
    return $this->repository->devices->fetch_all();
  }
  
  public function get_all_meetings($conditions=[]){
    return $this->repository->meetings->fetch_all("*", $conditions);
  }
  private function get_missing_device_names($devices){
  
    $existing_device = $this->get_all_devices();
    $device_names = array();
    foreach($existing_device as $device){
      array_push($device_names, $device->name);
    }
    $missing_devices = array();
    foreach($devices as $device){
      if(!in_array($device, $device_names)){
        array_push($missing_devices, $device);
      }
    }
    return $missing_devices;
  }
  public function save_devices($devices){
    try{
      foreach($this->get_missing_device_names($devices) as $device_name){
        $device = $this->repository->devices->model();
        $device->name = $device_name;
        $this->repository->devices->insert($device);
      }
      return true;
    } catch(Exception $e){
      return false;
    }
  }
  
  public function save_meetings($device_map){
    try{
      $devices = array_keys($device_map);
      $this->save_devices($devices);
      $devices = $this->get_all_devices();
      foreach($devices as $device){
        $name = $device->name;
        if(isset($device_map[$name])){
          $meetings = $device_map[$name];
          foreach($meetings as $meeting){
            $meeting_object = $this->repository->meetings->generate_model_with_attributes($meeting);
            $this->repository->meetings->insert($meeting_object);
          }
        }
      }
      return true;
    } catch(Exception $e){
      return $e;
    }
  }
}

$controller = new MeetingsController();
$response = new stdClass();
switch($request_method){
  case "GET":
    $this_month = isset($_GET["this_month"]);
    if(isset($_GET["this_month"])){
      $date = new CustomDate();
      list($first, $last) = $date->first_and_last_day_of_month();
      $date_condition = "BETWEEN '{$first}' AND '{$last}'";

      $response->condition = ["join_datetime $date_condition", "leave_datetime $date_condition"];
      $response->data = $controller->get_all_meetings($condition=$response->condition);
    } else if(isset($_GET["last_month"])){
      $date = new CustomDate();
      $last_month = $date->last_month();
      list($first, $last) = $date->first_and_last_day_of_month();
      $response->date = array($first, $last);
      $date_condition = "BETWEEN '{$first}' AND '{$last}'";

      $response->condition = ["join_datetime $date_condition", "leave_datetime $date_condition"];
      $response->data = $controller->get_all_meetings($condition=$response->condition);
    }

    break;
  case "POST":
    $request_body = get_request_body();
    $response->success = $controller->save_meetings($request_body);
    break;
}
echo json_encode($response);