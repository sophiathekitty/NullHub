<?php
/**
 * get the current commit hash
 * @param string $path the root path of the repo
 * @return string|null returns ORIG_HEAD
 */
function GitHash($path){
    //echo "$path.git/ORIG_HEAD\n";
    if(is_file($path.".git/ORIG_HEAD")){
        $content=@file_get_contents($path.".git/ORIG_HEAD");
        return $content;
    } else {
        return "dev";
    }
}
?>