<?php
require_once("../../includes/main.php");
$data = [];
if(isset($_GET['name'],$_GET['value'])){
    $data = Settings::SaveSettingsVar($_GET['name'],$_GET['value']);
} elseif(isset($_GET['name'])){
    $data = Settings::LoadSettingsVar($_GET['name']);
}
OutputJson($data);
?>
