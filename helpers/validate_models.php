<?php
define('VALIDATE_TABLES',true);
require_once("../includes/main.php");
//define("DEBUG",true);
clsModel::ValidateTables();
ValidateColors();

// find extensions if we're not in test mode that is....
if(!defined("TEST_MODE")){
    $extensions = FindLocalExtensions();
    foreach($extensions as $extension){
        $res = ServerRequests::LoadLocalhostJSON("/extensions/".$extension."/helpers/validate_models.php");
        //echo file_get_contents("http://".$_SERVER['HTTP_HOST'].Settings::LoadSettingsVar('path',"/")."extensions/".$extension."/helpers/validate_models.php");
        Debug::LogGroup($extension,$res);
    }    
}
OutputJson([]);
?>