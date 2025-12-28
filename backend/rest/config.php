<?php

// Set the reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ (E_NOTICE | E_DEPRECATED));

class Config {
    
    public static function DB_NAME() {
        return self::get_env("DB_NAME", "car-rental");
    }

    public static function DB_PORT() {
        return self::get_env("DB_PORT", 3306);
    }

    public static function DB_USER() {
        return self::get_env("DB_USER", "root");
    }

    public static function DB_PASSWORD() {
        return self::get_env("DB_PASSWORD", "harkeking333");
    }
    
    public static function DB_HOST() {
        return self::get_env("DB_HOST", "localhost");
    }

    public static function JWT_SECRET() {
        return self::get_env("JWT_SECRET", "harkeking321");
    }
    
    // Ova funkcija provjerava da li postoji environment varijabla
    // Ako postoji - koristi nju (za production/deployment)
    // Ako ne postoji - koristi default vrijednost (za lokalni razvoj)
    public static function get_env($name, $default) {
        return isset($_ENV[$name]) && trim($_ENV[$name]) != "" 
            ? $_ENV[$name] 
            : $default;
    }
}