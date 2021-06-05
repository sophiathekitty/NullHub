<?php
function OutputCSSFromFileList($css_files){
    header('Access-Control-Allow-Origin: *');
    header("Content-type: text/css; charset: UTF-8");
    foreach($css_files as $css_file){
        echo "/* $css_file */\n";
        include_once($css_file);
        echo "\n\n\n";
    }
}
function OutputCSSFromFileListMin($css_files){
    header('Access-Control-Allow-Origin: *');
    header("Content-type: text/css; charset: UTF-8");
    $buffer = "";
    foreach($css_files as $css_file){
        $buffer .= file_get_contents($css_file);
    }
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    $buffer = str_replace(': ', ':', $buffer);
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
    echo $buffer;
}  
?>