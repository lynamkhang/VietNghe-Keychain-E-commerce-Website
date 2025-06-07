<?php

require_once __DIR__ . '/../config/helpers.php';

class LanguageController {
    public function switchLanguage() {
        if (isset($_POST['language'])) {
            $lang = $_POST['language'];
            setLanguage($lang);
        }
        
        // Redirect back to the previous page
        $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
        header("Location: $redirect");
        exit;
    }
} 