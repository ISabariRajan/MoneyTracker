<?php
require_once "base_model.php";
// Device.php
class DeviceModel extends BaseModel {
  
  private $id;
  private $name;
  

  public function __construct() {
      // $this->id = $id;
      // $this->name = $name;
      $this->show_variables = array("name");
  }

  public function get_id(){
    return $this->id;
  }

  public function set_id($id){
    $this->id = $id;
  }

  public function get_name(){
    return $this->name;
  }
  public function set_name($name){
    $this->name = $name;
  }
}