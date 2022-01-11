<?php
/**
 * check to see if the shutdown_requested settings var is true and then sets the shutdown_requested to false before doing shutdown shell command
 */
function ShutdownDevice(){
    if(Settings::LoadSettingsVar('shutdown_requested',0)){
        Settings::SaveSettingsVar('shutdown_requested',0);
        shell_exec("sudo shutdown now");    
    }
}
/**
 * check to see if the reboot_requested settings var is true and then sets the reboot_requested to false before doing reboot shell command
 */
function RebootDevice(){
    if(Settings::LoadSettingsVar('reboot_requested',0)){
        Settings::SaveSettingsVar('reboot_requested',0);
        shell_exec("sudo shutdown -r now");    
    }
}
?>