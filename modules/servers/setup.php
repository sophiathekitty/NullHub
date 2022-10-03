<?php
/**
 * create the settings.php file
 */
function CreateSettingsFile($data){
    if(!(defined("SETUP_MODE"))) return;
    global $root_path;
    $text = "<?php\n";
    $text .= "\$device_info = [];\n";
    foreach($data as $key => $value)
        $text .= "\$device_info['$key'] = \"$value\";\n";
    $text .= "?>\n";
    $filename = "settings.php";
    if(defined("TEST_MODE")) $filename = "settings_test.php";
    Debug::Log("CreateSettingsFile",$filename,$text);
    $file = fopen($root_path.$filename, "w") or Debug::Die("Unable to open file!");
    fwrite($file,$text);
    fclose($file);
}
/**
 * get the current setup state
 * @return string "complete" if ```SetupComplete()``` otherwise SETUP_MODE message
 */
function SetupState(){
    if(SetupComplete()) return "complete";
    return constant("SETUP_MODE");
}
/**
 * checks if setup is complete
 * @return bool returns false if SETUP_MODE defined otherwise true
 */
function SetupComplete(){
    if(defined("SETUP_MODE")) return false;
    return true;
}
/**
 * run the setup services to sync important data from main hub
 * make sure that the servers have synced from hub and that this device isn't using
 * the setup info for locating main hub
 * @return string status message based on having synced servers table
 */
function SetupInstallDatabase(){
	//$content=file_get_contents("http://localhost/services/setup.php");
    $contents = ServerRequests::LoadLocalhostJSON('/services/setup.php');
    Debug::Log("SetupInstallDatabase",$contents);
	//$content=file_get_contents("http://localhost/api/info/servers/hub/");
    $hub = ServerRequests::LoadLocalhostJSON('/api/info/servers/hub/'); //json_decode($content,true);
    Debug::Log("SetupInstallDatabase",$hub);
    if(isset($hub['hub']) && $hub['hub']['mac_address'] != "device_info") return "database installed and synced";
    return "database install but failed to sync servers";
}
?>
