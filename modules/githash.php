<?php
/**
 * get the current commit hash
 * @param string $path the root path of the repo
 * @return string|null returns FETCH_HEAD
 */
function GitHash($path){
    //echo "$path.git/ORIG_HEAD\n";
    if(is_file($path.".git/FETCH_HEAD")){
        $content=@file_get_contents($path.".git/FETCH_HEAD");
        list($content) = explode("\t\tbranch",$content);
        return str_replace('\n', '', $content);
    } else {
        return "dev";
    }
}
?>