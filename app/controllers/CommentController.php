<?php

namespace App\CmsPhp\controllers;

use App\CmsPhp\models\Comment;

class CommentController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function create($post_id, $user_id, $content, $status = 'pending') {
        $comment = new Comment($this->db);
        
        $comment->post_id = $post_id;
        $comment->user_id = $user_id;
        $comment->content = $content;
        $comment->status = $status;
        
        return $comment->create();
    }
    
    public function getByPost($post_id) {
        $comment = new Comment($this->db);
        return $comment->getByPost($post_id);
    }
    
    public function getAll() {
        $comment = new Comment($this->db);
        return $comment->getAll();
    }
    
    public function getPendingCount() {
        $comment = new Comment($this->db);
        return $comment->getPendingCount();
    }
    
    public function approve($id) {
        $query = "UPDATE comments SET status = 'approved' WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
    
    public function delete($id) {
        $comment = new Comment($this->db);
        return $comment->delete($id);
    }
}