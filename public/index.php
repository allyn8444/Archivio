<?php
// echo "Hello word";

session_start(); // for login.php (we used SESSION in authenticate function)

require "../app/core/init.php";


$url = $_GET['url'] ?? 'home';
$url = strtolower($url);
$url = explode("/", $url);

$page_name = trim($url[0]);
$filename = "../app/pages/" . $page_name . ".php";
// ========================

$PAGE =  get_pagination_vars(); // For pagination (available for every page)
// echo "<pre>";
// print_r($PAGE);

// ======================================
if (file_exists($filename)) {
	require_once $filename;
} else {
	require_once "../app/pages/404.php";
}
