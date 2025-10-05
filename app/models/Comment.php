<?php

namespace App\CmsPhp\models;

use App\CmsPhp\core\Model;
use PDO;

class Comment extends Model{
    protected $table = "comments";

    public $id;
    public $post_id;
    public $user_id;
    public $content;
    public $status;
    public $created_at;

    public function __construct($db)
    {
        parent::__construct($db);
    }

    public function create(){
        $query = "INSERT INTO $this->table SET post_id = :post_id, user_id = :user_id, content = :content, status = :status";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(":post_id", $this->post_id);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":status", $this->status);

        if($stmt->execute())
        {
            return true;
        }

        return false;
    }

    public function getByPost($post_id)
    {
        $query = "SELECT c.*, u.sername FROM $this->table c LEFT JOIN users u on c.user_id = u.id WHERE c.post_id = :post_id AND c.status = 'approved' ORDER BY c.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":post_id", $post_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPendingCount()
    {
        $query = "SELECT COUNT(*) as count FROM $this->table WHERE status='pending'";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
}