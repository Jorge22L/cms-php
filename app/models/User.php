<?php

namespace App\CmsPhp\Models;

use App\CmsPhp\core\Model;
use PDO;

class User extends Model {
    protected $table = "users";

    public $id;
    public $username;
    public $email;
    public $password;
    public $role;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        parent::__construct($db);
    }

    public function create()
    {
        $query = "INSERT INTO $this->table SET username=:username, email=:email,password=:password, role=:role";

        $stmt = $this->db->prepare($query);

        $this->password = password_hash($this->password, PASSWORD_BCRYPT);

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":role", $this->role);

        if($stmt->execute()){
            return true;
        }

        return false;
    }

    public function emailExists(){
        $query = "SELECT id, username, password, role FROM $this->table WHERE email = ? LIMIT 0,1";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();

        $num = $stmt->rowCount();

        if($num > 0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->password = $row['password'];
            $this->role = $row['role'];

            return true;
        }

        return false;
    }

    public function getAuthors() {
        $query = "SELECT id, username FROM $this->table  WHERE role IN ('admin', 'author')";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}