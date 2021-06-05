<?php
function OutputJSFromFileList($js_files){
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/javascript; charset: UTF-8");
    foreach($js_files as $js_file){
        echo "/* $js_file */\n";
        include_once($js_file);
        echo "\n\n\n";
    }
}
function OutputJSFromFileListMin($js_files){
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/javascript; charset: UTF-8");
    $buffer = "";
    foreach($js_files as $js_file){
        //echo "/* $js_file */\n";
        $buffer .= file_get_contents($js_file);
        //echo "\n\n\n";
    }
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    $buffer = str_replace(': ', ':', $buffer);
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
    echo $buffer;
}

?>