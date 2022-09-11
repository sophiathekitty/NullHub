<?php
/**
 * takes a list of files and combines them into a single file to output as a css file
 * @param array $css_files a list of css files
 * @param bool $sort if set to true will sort the file list before combining
 */
function OutputCSSFromFileList($css_files, $sort = false){
    if($sort) sort($css_files);
    header('Access-Control-Allow-Origin: *');
    header("Content-type: text/css; charset: UTF-8");
    foreach($css_files as $css_file){
        echo "/* $css_file */\n";
        include_once($css_file);
        echo "\n\n\n";
    }
    if(!is_null(clsDB::$db_g)) clsDB::$db_g->CloseDB();
}
/**
 * takes a list of files and combines them into a single file to output as a minimized css file
 * @param array $css_files a list of css files
 * @param bool $sort if set to true will sort the file list before combining
 */
function OutputCSSFromFileListMin($css_files, $sort = false){
    if($sort) sort($css_files);
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
    if(!is_null(clsDB::$db_g)) clsDB::$db_g->CloseDB();
}  
?>