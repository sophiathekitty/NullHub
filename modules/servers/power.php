<?php
function ShutdownDevice(){
    if(Settings::LoadSettingsVar('shutdown_requested',0)){
        Settings::SaveSettingsVar('shutdown_requested',0);
        shell_exec("sudo shutdown now");    
    }
}
function RebootDevice(){
    if(Settings::LoadSettingsVar('reboot_requested',0)){
        Settings::SaveSettingsVar('reboot_requested',0);
        shell_exec("sudo shutdown -r now");    
    }
}
?>