<?php
class CustomDatabase
{
    private static $instance = null;
    private $pdo;

    private function __construct($host, $dbName, $user, $pass)
    {
        try {
            $dsn = "mysql:host={$host};dbname={$dbName}";
            $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit;
        }
    }

    public static function get_instance($host, $dbName, $user, $pass)
    {
        if (!(self::$instance instanceof CustomDatabase)) {
            self::$instance = new CustomDatabase($host, $dbName, $user, $pass);
        }
        return self::$instance;
    }

    public function disconnect()
    {
        if ($this->pdo instanceof PDO) {
            $this->pdo = null;
        }
    }

    public function query($sql, $params = [])
    {   
        $default_return = array(false, false);
        if ($this->pdo) {
            try {
                $stmt = $this->pdo->prepare($sql);
                foreach ($params as $key => $param) {
                    $stmt->bindValue($key + 1, $param);
                }
                return array($stmt, $stmt->execute());
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                return $default_return;
            }
        } else {
            return $default_return;
        }
    }

    public function execute($sql, $params = []){
        $default_return = array(false, false);
        if ($this->pdo) {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $param) {
                $stmt->bindValue($key + 1, $param);
            }
            return array($stmt, $stmt->execute());
        }
    }

    private function generate_query_params($data){
        $keys = array_keys($data);
        $key_fields = implode(",", $keys);
        $placeholders = implode(", ", array_fill(0, count($keys), "?"));
        return array(array_values($data), $key_fields, $placeholders);
    }

    private function generate_key_questionmark($data){
        $keys = array_keys($data);
        $question_mark_key = array_map(function($key) { return "$key=?"; }, $keys);
        return array(array_values($data), $question_mark_key);
    }
    // $setClause = implode(", ", array_map(function($key) { return "$key=?"; }, array_keys($data)));

    private function fetch($sql, $params = []){
        list($stmt, $success) = $this->query($sql, $params);
        if ($success) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } else {
            return array();
        }
    }

    public function describe_table($table){
        $sql = "DESCRIBE {$table};";
        return $this->fetch($sql);
      }

    public function insert($table_name, $data){
        if($this->pdo){
            try{
                list($values, $key_fields, $placeholders) = $this->generate_query_params($data);
                $sql = "INSERT INTO {$table_name} ($key_fields) VALUES ($placeholders)";
                // echo $sql;
                return $this->execute($sql, $values)[1];
            } catch (PDOException $e) {
                if ($e instanceof PDOException && strpos($e->getCode(), '23000') !== false) {
                    // Handle the specific "Duplicate entry" error
                    // echo "Error: Integrity constraint violation - Duplicate entry for column.\n";
                } else if (strpos($e->getCode(), '1062') !== false) {
                    // Handle the specific "1062 Duplicate entry" error
                    // echo "Error: Duplicate entry for column 'column' in table 'table'.\n";
                } else {
                    // Catch any other PDOException and print its message
                    echo "Error: " . $e->getMessage();
                }
                return false;
            }
    
        }
    }

    public function update($table_name, $data, $conditions){
        if($this->pdo){
            list($values, $question_mark) = $this->generate_key_questionmark($data);
            $setClause = implode(", ", $question_mark);
            
            list($condition_values, $question_mark) = $this->generate_key_questionmark($conditions);
            $sql = "UPDATE {$table_name} SET {$setClause} WHERE " . implode(" AND ", $question_mark);
            return $this->execute($sql, array_merge($values, $condition_values))[1];

        }
    }
    public function delete($table_name, $conditions) {
        list($params, $question_mark) = $this->generate_key_questionmark($conditions);
        $sql = "DELETE FROM {$table_name} WHERE " . implode(" AND ", $question_mark);
        return $this->execute($sql, $params)[1];

    }

    public function truncate($table_name) {
        if($this->pdo){
            $sql = "TRUNCATE TABLE {$table_name}";
            return $this->execute($sql)[1];
        }
    }

    public function select($table_name, $fields="*", $conditions = []) {
        $fieldClause = is_array($fields) ? implode(', ', $fields) : $fields;
        $whereClause = "";
        $params = array();
        if (!empty($conditions)) {
            // list($params, $question_marks) = $this->generate_key_questionmark($conditions);
            // $whereClause = " WHERE " . implode(" AND ", $question_marks);
            $whereClause = " WHERE " . implode(" AND ", $conditions);
        }

        $sql = "SELECT {$fieldClause} FROM {$table_name}{$whereClause}";
        return $this->fetch($sql, $params);
        // list($stmt, $success) = $this->query($sql, $params);
        // if ($success) {
        //     $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //     return $results;
        // } else {
        //     return array();
        // }
    }
    // Add other database-related methods here (e.g., query, fetch, etc.)
}
class CustomBean{
    private $table_name;

    public function get_table_name(){ return $this->table_name; }

    public function set_table_name($value){ $this->table_name = $value; }

    public static function get_instance(){
        return new self();
    }

    public static function get_instance_with_attributes($attributes){
        $object = new self();
        foreach ($attributes as $key => $value) {
            // echo $key . " : " . $value;
            $object->$key = $value;
        }
        return $object;
    }
}

class CustomRepository{
  private $model;
  private $db;
//   private $table;

  public function __construct($table_name, $db){
    // $this->table = $table_name;
    $this->db = $db;
    $this->model = $this->generate_model($table_name);
  }

  public function model(){
    return $this->generate_model($this->model->get_table_name());
  }

  public function generate_model($table_name){
    $model = new CustomBean();
    $model->set_table_name($table_name);
    return $model;
  }

  public function generate_model_with_attributes($attributes){
    $model = CustomBean::get_instance_with_attributes($attributes);
    $model->set_table_name($this->model->get_table_name());
    return $model;
  }
  public function insert($model){
    $table_name = $model->get_table_name();
    $properties = get_object_vars($model);
    return $this->db->insert($table_name, $properties);
  }
  public function update(){}
  public function delete(){}
  public function fetch_all($selections="*", $conditions=array()){
    $table_name = $this->model->get_table_name();
    $results = $this->db->select($table_name, $selections, $conditions);
    $output = array();

    foreach($results as $row){ 
        $model = CustomBean::get_instance_with_attributes($row);
        $model->set_table_name($table_name);
        array_push($output, $model);
    }

    return $output;
  }
  public function set_model($model){$this->model = $model;}
}

class CustomService{

  private $repository;
  private $model;
  public function __construct($repository, $model){
    $this->repository = $repository;
    $this->model = $model;
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
}

class CustomORM{
//   private $table_name;
    private static $instance;
    private $db;

  private function __construct($host, $username, $password, $database){
    $this->db = CustomDatabase::get_instance($host, $username, $password, $database);
  }

  public static function get_orm($host, $username, $password, $database){
    if (!(self::$instance instanceof CustomORM)) {
        self::$instance = new CustomORM($host, $username, $password, $database);
        // echo self::$instance->db;
    }
    return self::$instance;
    // $instance = new CustomORM($host, $username, $password, $database);
  }

  public static function is_db_connected(){
    return (self::$instance instanceof CustomORM);
  }

  public static function create($table){
    echo "CREAET";
    // echo self::$instance->db;
    if (self::$instance instanceof CustomORM) {
        $results = self::$instance->db->describe_table($table);
        foreach($results as $row){
            echo "{";
            foreach($row as $key => $value){
                echo "".$key.": ".$value.",";
            }
            echo "}, ";
        }
    }
    return $table;
  }

  public static function repository($table){
    return new CustomRepository($table, self::$instance->db);
  }
}

class CustomDate{
    private $date;
    public function __construct($date_string=null){
        if($date_string)
            $date = DateTime::createFromFormat("Y-m-d H:i:s", $date_string);
        else
            $date = new DateTime();
        $this->date = $date;
    }

    public function last_month($date=null){
        return $this->first_day_of_month($date)->modify('-1 day');
    }

    public function get_sql_date($date=null){
        if($date)
            return $date->format('Y-m-d');
        else
            return $this->date->format('Y-m-d');
    }

    public function first_day_of_month($date=null){
        if($date)
            return $date->modify("first day of this month");
        else
            return $this->date->modify("first day of this month");
    }

    public function last_day_of_month($date=null){
        if($date)
            return $date->modify("last day of this month");
        else
            return $this->date->modify("last day of this month");
    }

    public function first_and_last_day_of_month(){
        $first = $this->get_sql_date($this->first_day_of_month());
        $last = $this->get_sql_date($this->last_day_of_month());
        return array($first, $last);
    }


}