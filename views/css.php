<?php
function OutputCSSFromFileList($css_files){
    header('Access-Control-Allow-Origin: *');
    header("Content-type: text/css; charset: UTF-8");
    foreach($css_files as $css_file){
        echo "/* $css_file */\n";
        include_once($css_file);
        echo "\n\n\n";
    }
    if(json_last_error())
        echo json_last_error_msg();    
}    
?>