<?php
/**
 * takes a list of files and combines them into a single file to output as a js file
 * @param array $js_files a list of js files
 * @param bool $sort if set to true will sort the file list before combining
 */
function OutputJSFromFileList($js_files,$sort = true){
    if($sort) sort($js_files);
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/javascript; charset: UTF-8");
    foreach($js_files as $js_file){
        echo "/* $js_file */\n";
        include_once($js_file);
        echo "\n\n\n";
    }
    if(!is_null(clsDB::$db_g)) clsDB::$db_g->CloseDB();
}
/**
 * takes a list of files and combines them into a single file to output as a minimized js file
 * @param array $css_files a list of js files
 * @param bool $sort if set to true will sort the file list before combining
 */
function OutputJSFromFileListMin($js_files, $sort = true){
    if($sort) sort($js_files);
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
    if(!is_null(clsDB::$db_g)) clsDB::$db_g->CloseDB();
}

?>