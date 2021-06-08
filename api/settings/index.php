<?php
require_once("../../includes/main.php");
$data = [];
$data['post'] = $_POST;
$data['get'] = $_GET;
$data['request'] = $_REQUEST;
if(isset($_GET['name'],$_GET['value'])){
    $data = Settings::SaveSettingsVar($_GET['name'],$_GET['value']);
} elseif(isset($_GET['name'])){
    $data = Settings::LoadSettingsVar($_GET['name']);
} else {
    $data['settings'] = Settings::LoadAllSettings();
}
OutputJson($data);
?>
