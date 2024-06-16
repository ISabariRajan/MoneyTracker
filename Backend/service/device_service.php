<?php
require_once $_SERVER['DOCUMENT_ROOT']."/MoneyTracker/repository/device_repository.php";
require_once $_SERVER['DOCUMENT_ROOT']."/MoneyTracker/models/device.php";
require_once "base_service.php";
class DeviceService extends BaseService{
    public function __construct(){
        parent::__construct(new DeviceRepository("device"),new DeviceModel());
        // $this->repository = new DeviceRepository("device");
        // $this->model = new DeviceModel();
        // echo $this->repository;
    }
}