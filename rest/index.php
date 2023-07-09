<?php
require_once("../includes/main.php");
$requestPath = $_SERVER['REQUEST_URI'];
$requestParts = explode('/', $requestPath);
OutputJson($requestParts);
?>