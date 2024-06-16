<?php
// // Database configuration
// function test()
// {

//     $host = 'localhost';
//     $dbname = 'pi';
//     $username = 'pi';
//     $password = 'admin';
//     $charset = 'utf8mb4';
//     $CWD = getcwd();
//     require $_SERVER['DOCUMENT_ROOT'] . "/MoneyTracker/php_plugins/rb.php";
//     $CONNECTION_STRING = "mysql:host=" . $host . ";dbname=" . $dbname;

//     $db = R::setup($CONNECTION_STRING, "$username", "$password");
//     // Connection pool configuration
//     $poolSize = 5;
//     $idleTimeout = 60; // in seconds
//     $maxLifetime = 120; // in seconds
// }
// class Database
// {
//     private $host = "localhost";
//     private $dbName = "pi";
//     private $user = "pi";
//     private $pass = "admin";
//     private static $instance = null;
//     private $pdo;

//     private function __construct()
//     {
//         try {
//             $dsn = "mysql:host={$this->host};dbname={$this->dbName}";
//             $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
//             $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
//         } catch (PDOException $e) {
//             echo "Error: " . $e->getMessage();
//             exit;
//         }
//     }

//     public static function get_instance()
//     {
//         if (!(self::$instance instanceof Database)) {
//             self::$instance = new Database();
//         }
//         return self::$instance;
//     }

//     public function connect()
//     {
//         if (!$this->pdo) {
//             $this->__construct();
//         }
//     }

//     public function disconnect()
//     {
//         if ($this->pdo instanceof PDO) {
//             $this->pdo = null;
//         }
//     }

//     public function query($sql, $params = [])
//     {   
//         $default_return = array(false, false);
//         if ($this->pdo) {
//             try {
//                 $stmt = $this->pdo->prepare($sql);
//                 foreach ($params as $key => $param) {
//                     $stmt->bindValue($key, $param);
//                 }
//                 // if (count($params)) {
//                 // }
//                 return array($stmt, $stmt->execute());
//             } catch (PDOException $e) {
//                 echo "Error: " . $e->getMessage();
//                 return $default_return;
//             }
//         } else {
//             return $default_return;
//         }
//     }

//     private function generate_query_params($data){
//         $keys = array_keys($data);
//         $placeholders = implode(", ", array_fill(0, count($keys), "?"));
//         return array(array_values($data), $placeholders);
//     }

//     private function generate_key_questionmark($data){
//         $keys = array_keys($data);
//         $question_mark_key = array_map(function($key) { return "$key=?"; }, $keys);
//         return array(array_values($data), $question_mark_key);
//     }
//     // $setClause = implode(", ", array_map(function($key) { return "$key=?"; }, array_keys($data)));

//     public function insert($table_name, $data){
//         if($this->pdo){
            
//             list($values, $placeholders) = $this->generate_query_params($data);
//             $sql = "INSERT INTO {$table_name} ($placeholders) VALUES ($placeholders)";
//             return $this->query($sql, $values)[1];
    
//         }
//     }

//     public function update($table_name, $data, $conditions){
//         if($this->pdo){
//             list($values, $question_mark) = $this->generate_key_questionmark($data);
//             $setClause = implode(", ", $question_mark);
            
//             list($condition_values, $question_mark) = $this->generate_key_questionmark($conditions);
//             $sql = "UPDATE {$table_name} SET {$setClause} WHERE " . implode(" AND ", $question_mark);
//             return $this->query($sql, array_merge($values, $condition_values))[1];

//         }
//     }
//     public function delete($table_name, $conditions) {
//         list($params, $question_mark) = $this->generate_key_questionmark($conditions);
//         $sql = "DELETE FROM {$table_name} WHERE " . implode(" AND ", $question_mark);
//         return $this->query($sql, $params)[1];

//     }

//     public function truncate($table_name) {
//         if($this->pdo){
//             $sql = "TRUNCATE TABLE {$table_name}";
//             return $this->query($sql)[1];
//         }
//     }

//     public function select($table_name, $fields="*", $conditions = []) {
//         $fieldClause = is_array($fields) ? implode(', ', $fields) : $fields;
//         $whereClause = "";
//         $params = array();
//         if (!empty($conditions)) {
//             list($params, $question_marks) = $this->generate_key_questionmark($conditions);
//             $whereClause = " WHERE " . implode(" AND ", $question_marks);
//         }

//         $sql = "SELECT {$fieldClause} FROM {$table_name}{$whereClause}";
//         list($stmt, $success) = $this->query($sql, $params);
//         if ($success) {
//             $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
//             return $results;
//         } else {
//             return array();
//         }
//     }
//     // Add other database-related methods here (e.g., query, fetch, etc.)
// }