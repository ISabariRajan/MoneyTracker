<?php
class BaseService{
  private $repository;
  private $model;

  // public function __construct(){}

  public function __construct($repository, $model){
    $this->repository = $repository;
    $this->model = $model;
  }

  public function set_repository($repository){
    $this->repository = $repository;
  }

  public function get_all(){
    // echo "Get_ALL";
    $properties = $this->model->get_public_bean();
    $rows = $this->repository->get_all();
    foreach($properties as $property){
      echo $property;
    }

    return ;
  }

  public function insertDevice($device){
    $this->repository->insert($device);
  }

  public function findDeviceById($id) {
      // This method should be implemented to interact with the database or data source.
      // For this example, we'll simply return null.
      return null;
  }

  public function updateDevice(DeviceModel $device, $data) {
      $device->name = $data['name'];
  }

  public function deleteDevice(DeviceModel $device) {
      // This method should be implemented to interact with the database or data source.
      // For this example, we'll simply return true.
      return true;
  }
}