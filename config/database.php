<?php 
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '1234');
define('DB_NAME', 'cms_db');
define('DB_CHARSET', 'utf8mb4');


class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $charset = DB_CHARSET;
    public $conn; // Variable de conexión

    public function getConnection(){
        $this->conn = null;
        try
        {
            $dsn = "mysql:host=$this->host; dbname=$this->db_name; charset=$this->charset";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        }
        catch(PDOException $exception)
        {
            echo "Error de conexion: ". $exception->getMessage();
        }

        return $this->conn;
    }
}