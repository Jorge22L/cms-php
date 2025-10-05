<?php

namespace App\CmsPhp\models;

use App\CmsPhp\core\Model as CoreModel;
use PDO;

class Post extends CoreModel{
    protected $table = "posts";

    public $id;
    public $title;
    public $content;
    public $excerpt;
    public $image;
    public $user_id;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        parent::__construct($db);
    }

    public function create(){
        $query = "INSERT INTO $this->table SET title = :title, content = :content, excertp = :excerpt, image = :image, user_id = :user_id, status = :status";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":excerpt", $this->excerpt);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":status", $this->status);

        if($stmt->execute())
        {
            return true;
        }

        return false;
    }

    public function update(){
        $query = "UPDATE $this->table SET title = :title, content = :content, excertp = :excertp, image = :image, user_id = :user_id, status = :status, updated_at=NOW() WHERE id = :id";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":excerpt", $this->excerpt);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);


        if($stmt->execute())
        {
            return true;
        }

        return false;
    }

    public function getPublished(){
        $query = "SELECT p.*, u.username FROM $this->table p LEFT JOIN users u ON p.user_id = u.id WHERE p.status='published' ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBySlug($slug){
        $query = "SELECT p.*, u.username FROM $this->table p LEFT JOIN users u ON p.user_id = u.id WHERE p.id = :id OR REPLACE(LOWER(p.title), '','-') = :slug";
        

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $slug);
        $stmt->bindParam(":slug", $slug);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


}
