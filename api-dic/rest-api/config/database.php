<?php 
    class Database {
        private $host = "127.0.0.1";
        private $database_name = "tourviet";
        private $username = "root";
        private $password = "";

        public $conn;

        public function getConnection(){
			 // set your default time-zone
	        date_default_timezone_set('Asia/Ho_Chi_Minh');
            $this->conn = null;
            try{
                $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database_name, $this->username, $this->password);
                $this->conn->exec("set names utf8");
            }catch(PDOException $exception){
                echo "Database could not be connected: " . $exception->getMessage();
            }
            return $this->conn;
        }

		public function getConnectionV2(){
            //$this->conn = null;
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database_name);
            if($this->conn->connect_error){
                die("Error failed to connect to MySQL: " . $this->conn->connect_error);
            }
            return $this->conn;
        }
    }  
?>