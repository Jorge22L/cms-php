<?php

namespace App\CmsPhp\core;

class Helpers {
    public static function slugify($text) {
        // Reemplaza caracteres no alfanuméricos con guiones
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // Translitera a ASCII
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // Elimina caracteres no deseados
        $text = preg_replace('~[^-\w]+~', '', $text);
        // Convierte a minúsculas
        $text = strtolower($text);
        // Elimina guiones al inicio y final
        $text = trim($text, '-');
        // Elimina guiones múltiples
        $text = preg_replace('~-+~', '-', $text);
        
        return $text;
    }
    
    public static function formatDate($date) {
        return date('d/m/Y H:i', strtotime($date));
    }
    
    public static function truncate($text, $chars = 150) {
        if (strlen($text) > $chars) {
            $text = $text . " ";
            $text = substr($text, 0, $chars);
            $text = substr($text, 0, strrpos($text, ' '));
            $text = $text . "...";
        }
        return $text;
    }
    
    public static function uploadImage($file, $target_dir = "../public/assets/uploads/") {
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $target_file = $target_dir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $imageFileType;
        $target_file = $target_dir . $new_filename;
        
        // Verificar si es una imagen real
        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            return ["error" => "El archivo no es una imagen."];
        }
        
        // Verificar tamaño del archivo (5MB máximo)
        if ($file["size"] > 5000000) {
            return ["error" => "La imagen es demasiado grande."];
        }
        
        // Permitir ciertos formatos
        if (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
            return ["error" => "Solo se permiten archivos JPG, JPEG, PNG y GIF."];
        }
        
        // Intentar subir el archivo
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return ["success" => true, "filename" => $new_filename];
        } else {
            return ["error" => "Hubo un error al subir la imagen."];
        }
    }
    
    public static function sanitize($input) {
        return htmlspecialchars(strip_tags(trim($input)));
    }
}