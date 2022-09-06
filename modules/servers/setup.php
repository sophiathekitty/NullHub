<?php
/**
 * create the settings.php file
 */
function CreateSettingsFile($data){
    if(!defined("SETUP_MODE")) return;
    global $root_path;
    $text = "<?php\n";
    $text .= "\$device_info = [];\n";
    foreach($data as $key => $value)
        $text .= "\$device_info['$key'] = \"$value\";\n";
    $text .= "?>\n";

    $file = fopen($root_path."settings.php", "w") or JsonDie("Unable to open file!");
    fwrite($file,$text);
    fclose($file);
}
function SetupState(){
    if(SetupComplete()) return "complete";
    return constant("SETUP_MODE");
}
function SetupComplete(){
    if(defined("SETUP_MODE")) return false;
    return true;
}
?>
