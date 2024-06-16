<?php
require_once "db.php";
require_once "utils.php";


function create_foreign_key_for_table($table, $foreign){
  echo "Foreign<br>";
  if($foreign){
    if(is_map($foreign)){
      R::addForeignKey($table, $foreign['column'], $foreign['table'], $foreign['field']);
    } else if(is_array(($foreign))) {
      # It there are multiple foregin keys, then iterate
      foreach($foreign as $value){
        create_foreign_key_for_table($table, $value);
      }
    }
  }

}

function create_table($table, $columns){
  echo $table . "<br>";
  $table = R::dispense($table);

  # I will add constraints to the table, like foreign key, which is handled here
  $foreign = $columns['foreign'];
  create_foreign_key_for_table($table, $foreign);
  unset($columns['foreign']);
  
  foreach($columns as $column => $value){
    echo $column . "<br>";
    $table->$column = $value;
  }

  $id = R::store($table);
}

function delete_tables($tables){
  $temp_array = array();
  $no_of_tables = count($tables);
  $count = 0;
  while(!empty(($tables))){
    if($count == $no_of_tables){
      $tables = $temp_array;
      $temp_array = array();
      $count = 0;
      $no_of_tables = count($tables);
    }
    $tablename = $tables[$count];
    try{
      R::exec("DROP TABLE IF EXISTS $tablename;");
    } catch(Exception $e){
      if(strpos($e, "Integrity constraint violation") == false){

      } else {
        array_push( $temp_array, $tablename);
      }
    }
    $count++;
  }
}

try{
  # Get Existing tables from Database
  $existing_tables = R::inspect();
  delete_tables(($existing_tables));


  foreach($existing_tables as $tablename){
    // Execute the SQL command to drop the table
    R::exec("DROP TABLE IF EXISTS $tablename;");
  }

  # Tables to be created and the columns and Datatypes
  $tables = array(
    "client" => array(
      "client_name" => "TEST",
      "billing_type" => "TEST",
      "billing_rate" => 300,
    ),
    "devices" => array(
      "device_name" => "TEST",
      "client_id" => 1,
      "foreign" => array("column" => "client_id", "table" => "client", "field" => "client_id", "type" => "ONE_TO_MANY")
    ),
    "meeting" => array(
      "start_date_time" => '2023-01-01 12:30:00',
      "end_date_time" => '2023-01-01 12:30:00',
      "duration" => 20,
      "device_name" => "TEST",
      "foreign" => array("column" => "device_name", "table" => "devices", "field" => "device_name", "type" => "ONE_TO_MANY")

    )
  );

  # loop thorugh tables and create the table
  foreach($tables as $table => $columns){
    # If Table already exist, Dont add sample data
    if(!in_array($table, $existing_tables)){
      create_table($table, $columns);
    }
  }
} catch(Exception $e){
  $e = explode(":", $e);
  foreach($e as $error){
    echo $error . "<br>";
  }
}