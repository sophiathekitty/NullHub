<?php
require_once("../../../includes/main.php");
$data = [];
$data['get'] = $_GET;
$data['post'] = $_POST;
$data['request'] = $_REQUEST;
$data['env'] = $_ENV;
$data['server'] = $_SERVER;
OutputJson($data);
?>
