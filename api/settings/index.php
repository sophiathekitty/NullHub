<?php
require_once("../../includes/main.php");
$data = [];
//$data['post'] = $_POST;
//$data['get'] = $_GET;
//$data['request'] = $_REQUEST;
if(isset($_GET['name'],$_GET['value'])){
    $data = Settings::SaveSettingsVar($_GET['name'],$_GET['value']);
} elseif(isset($_GET['name'],$_GET['default'])){
    $data = Settings::LoadSettingsVar($_GET['name'],$_GET['default']);
} elseif(isset($_GET['name'])){
    $data = Settings::LoadSettingsVar($_GET['name']);
} elseif(isset($_GET['pallet'])){
    $data['settings'] = Settings::LoadSettingsPallet($_GET['pallet']);
} else {
    $data['settings'] = Settings::LoadAllSettings();
}
if(defined("SETUP_MODE") and isset($data['settings'])){
    $data['settings'][] = ["name"=>"type","value"=>"setup"];
    $data['settings'][] = ["name"=>"name","value"=>"new device"];
}
OutputJson($data);
?>
