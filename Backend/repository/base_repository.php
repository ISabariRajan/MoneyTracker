<?php
require_once  $_SERVER['DOCUMENT_ROOT']."/MoneyTracker/db.php";

class BaseRepository{

  private $model;
  private $db;

  private $table;
  public function __construct($table_name){
    $this->table = $table_name;
    $this->db = Database::get_instance();
  }
  public function insert($model){
    return $this->db->query("SELECT * FROM $this->table")[1];
  }
  public function update(){}
  public function delete(){}
  public function get_all($params=array()){
    return $this->db->select($this->table);
  }
  public function set_model($model){$this->model = $model;}
}