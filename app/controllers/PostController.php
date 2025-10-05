<?php

namespace App\CmsPhp\controllers;

use App\CmsPhp\models\Post;

class PostController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function create($title, $content, $excerpt, $image, $user_id, $status) {
        $post = new Post($this->db);
        
        $post->title = $title;
        $post->content = $content;
        $post->excerpt = $excerpt;
        $post->image = $image;
        $post->user_id = $user_id;
        $post->status = $status;
        
        return $post->create();
    }
    
    public function update($id, $title, $content, $excerpt, $image, $user_id, $status) {
        $post = new Post($this->db);
        
        $post->id = $id;
        $post->title = $title;
        $post->content = $content;
        $post->excerpt = $excerpt;
        $post->image = $image;
        $post->user_id = $user_id;
        $post->status = $status;
        
        return $post->update();
    }
    
    public function getAll() {
        $post = new Post($this->db);
        return $post->getAll();
    }
    
    public function getPublished() {
        $post = new Post($this->db);
        return $post->getPublished();
    }
    
    public function getById($id) {
        $post = new Post($this->db);
        return $post->getById($id);
    }
    
    public function getBySlug($slug) {
        $post = new Post($this->db);
        return $post->getBySlug($slug);
    }
    
    public function delete($id) {
        $post = new Post($this->db);
        return $post->delete($id);
    }
}