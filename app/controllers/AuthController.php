<?php
namespace App\CmsPhp\controllers;

use App\CmsPhp\Models\User;
use Exception;

class AuthController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }
    }

    public function login($email, $password){
        $user = new User($this->db);
        $user->email = $email;

        if($user->emailExists() && password_verify($password, $user->password)){
            $_SESSION['user_id'] = $user->id;
            $_SESSION['username'] = $user->username;
            $_SESSION['user_role'] = $user->role;

            return true;
        }

        return false;
    }

    public function register($username, $email, $password){
        try{
            $user = new User($this->db);

            $user->username = $username;
            $user->email = $email;
            $user->password = $password;
            $user->role = 'user';

            if($user->create()){
                return true;
            }
        }
        catch(Exception $e){
            // Email duplicado
            return false;
        }

        return false;
    }

    public function isLoggedIn(){
        return isset($_SESSION['user_id']);
    }

    public function isAdmin(){
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    public function isAuthor(){
        return isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'author');
    }

    public function logout(){
        session_destroy();
        header("Location: index.php");
        exit();
    }

    public function getCurrentUser(){
        if($this->isLoggedIn()){
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['user_role']
            ];
        }

        return null;
    }
}