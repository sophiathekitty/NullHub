<?php
class Templates {
    public static function FindTemplates(){
        global $root_path;
        $templates = Templates::CrawlFolder($root_path."templates/",[]);
        $plugins = FindPlugins($root_path."plugins/");
        foreach($plugins as $plugin){
            $templates = Templates::CrawlFolder($plugin."templates/",$templates);
        }
        return $templates;
    }
    public static function SectionTemplates(){
        global $root_path;
        $templates = Templates::CrawlFolder($root_path."templates/sections/",[]);
        $plugins = FindPlugins($root_path."plugins/");
        foreach($plugins as $plugin){
            $templates = Templates::CrawlFolder($plugin."templates/sections/",$templates);
        }
        return $templates;
    }
    public static function HeaderTemplates(){
        global $root_path;
        return Templates::CrawlFolder($root_path."templates/headers/",[]);
    }
    private static function CrawlFolder(string $path,array $templates){
        global $root_path;
        //echo "IncludeFolder: $path \n";
        if(!is_dir($path)) return $templates;
        $shared_models_dir = opendir($path);
        // LOOP OVER ALL OF THE  FILES    
        while ($file = readdir($shared_models_dir)) { 
            //echo "<br><i>$file</i> ".is_dir($path.$file)."  ".is_dir($file."/")." <br>";
            // IF IT IS NOT A FOLDER, AND ONLY IF IT IS A .php WE ACCESS IT
            if(!is_dir($file) && strpos($file, '.html')>0 && is_file($path.$file)) { 
                //echo "Require: $path$file\n";
                //require_once($path.$file);
                $file_path = $path.$file;
                $local_path = "/".substr($path,strlen($root_path));
                $url = "http://".LocalIp().$local_path.$file;
                //$url = "/".substr($file_path,strlen($root_path));
                //echo $url;
                $json_file = substr($file,0,strlen($file)-4)."json";
                $css_file = substr($file,0,strlen($file)-4)."css";
                $template = LoadJsonArray("http://".LocalIp().$local_path.$json_file);
                $template['template'] = $url;
                $template['template_name'] = substr($file,0,strlen($file)-5);
                //if(is_file($path.$css_file)) $templates['css'] = "http://".LocalIp().$local_path.$css_file;
                if(isset($template['api'])) $template['api'] = "http://".LocalIp().$template['api'];
                if(isset($template['item_template'])) $template['item_template'] = "http://".LocalIp().$template['item_template'];
                if(isset($template['models'])){
                    for($i = 0; $i < count($template['models']); $i++){
                        if(isset($template['models'][$i]['api'])) $template['models'][$i]['api'] = "http://".LocalIp().$template['models'][$i]['api'];
                        if(isset($template['models'][$i]['item_template'])) $template['models'][$i]['item_template'] = "http://".LocalIp().$template['models'][$i]['item_template'];
                    }
                }
                $templates[] = $template;
                //echo "included\n";
            } elseif(is_dir($path.$file) && $file != ".." && $file != "."){
                $templates = Templates::CrawlFolder($path.$file."/",$templates);
            }
        }
        // CLOSE THE DIRECTORY
        closedir($shared_models_dir);
        return $templates;
    }
}

?>