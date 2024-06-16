<?php
class Database
{
    // private $host = "localhost";
    // private $dbName = "pi";
    // private $user = "pi";
    // private $pass = "admin";
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

    public function describe_table($table){
      $sql = "DESCRIBE {$table};";
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute();
    //   $
    }

    public static function get_instance($host, $dbName, $user, $pass)
    {
        if (!(self::$instance instanceof Database)) {
            self::$instance = new Database($host, $dbName, $user, $pass);
        }
        return self::$instance;
    }

    // public function connect()
    // {
    //     if (!$this->pdo) {
    //         $this->__construct();
    //     }
    // }

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
                    $stmt->bindValue($key, $param);
                }
                // if (count($params)) {
                // }
                return array($stmt, $stmt->execute());
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                return $default_return;
            }
        } else {
            return $default_return;
        }
    }

    private function generate_query_params($data){
        $keys = array_keys($data);
        $placeholders = implode(", ", array_fill(0, count($keys), "?"));
        return array(array_values($data), $placeholders);
    }

    private function generate_key_questionmark($data){
        $keys = array_keys($data);
        $question_mark_key = array_map(function($key) { return "$key=?"; }, $keys);
        return array(array_values($data), $question_mark_key);
    }
    // $setClause = implode(", ", array_map(function($key) { return "$key=?"; }, array_keys($data)));

    public function insert($table_name, $data){
        if($this->pdo){
            
            list($values, $placeholders) = $this->generate_query_params($data);
            $sql = "INSERT INTO {$table_name} ($placeholders) VALUES ($placeholders)";
            return $this->query($sql, $values)[1];
    
        }
    }

    public function update($table_name, $data, $conditions){
        if($this->pdo){
            list($values, $question_mark) = $this->generate_key_questionmark($data);
            $setClause = implode(", ", $question_mark);
            
            list($condition_values, $question_mark) = $this->generate_key_questionmark($conditions);
            $sql = "UPDATE {$table_name} SET {$setClause} WHERE " . implode(" AND ", $question_mark);
            return $this->query($sql, array_merge($values, $condition_values))[1];

        }
    }
    public function delete($table_name, $conditions) {
        list($params, $question_mark) = $this->generate_key_questionmark($conditions);
        $sql = "DELETE FROM {$table_name} WHERE " . implode(" AND ", $question_mark);
        return $this->query($sql, $params)[1];

    }

    public function truncate($table_name) {
        if($this->pdo){
            $sql = "TRUNCATE TABLE {$table_name}";
            return $this->query($sql)[1];
        }
    }

    public function select($table_name, $fields="*", $conditions = []) {
        $fieldClause = is_array($fields) ? implode(', ', $fields) : $fields;
        $whereClause = "";
        $params = array();
        if (!empty($conditions)) {
            list($params, $question_marks) = $this->generate_key_questionmark($conditions);
            $whereClause = " WHERE " . implode(" AND ", $question_marks);
        }

        $sql = "SELECT {$fieldClause} FROM {$table_name}{$whereClause}";
        list($stmt, $success) = $this->query($sql, $params);
        if ($success) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } else {
            return array();
        }
    }
    // Add other database-related methods here (e.g., query, fetch, etc.)
}
class CustomBean{

}

class CustomRepository{
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

  private function __construct($host, $username, $password, $database){
    $this->db = Database::get_instance($host, $username, $password, $database);
  }

  public static function get_orm($host, $username, $password, $database){
    if (!(self::$instance instanceof CustomORM)) {
        self::$instance = new CustomORM($host, $username, $password, $database);
    }
    return self::$instance;
    // $instance = new CustomORM($host, $username, $password, $database);
  }

  public function create($table){

  }

  public function bean($table){

  }
}