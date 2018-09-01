<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
session_start();

define('DB_USER', 'root');
define('DB_PWD', '');
define('DB_NAME', 'cc_scraper_db');
define('DB_HOST', 'localhost');
define('DB_DSN', 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME .'');

define('LIMIT_COUNT', 10);
$letters=array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'X', 'Y', 'Z', '$', ' ', '€','┬','á','/');
?>
