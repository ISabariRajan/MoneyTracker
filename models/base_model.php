<?php
class BaseModel{
  private $show_variables = array();
  public function __construct(){}
  public function get_public_bean(){
    $reflectionChildClass = new ReflectionClass(get_class($this));
    $class_vars = $reflectionChildClass->getProperties();
    $properties = array();
    foreach($class_vars as $property){
      array_push($properties, $property->getName());
    }
    return $properties;
  }

}