<?php

/**
 * stores the location of a profile image that could be on this device or another device
 * as well as additional information about the image
 */
class ImageTags extends clsModel {
    public $table_name = "ImageTags";
    public $fields = [
        [
            'Field'=>"id",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>"auto_increment"
        ],[
            'Field'=>"guid",
            'Type'=>"varchar(100)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"tag",
            'Type'=>"varchar(20)",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ]
    ];
    private static $instance = null;
    /**
     * @return ImageTags|clsModel
     */
    private static function GetInstance(){
        if(is_null(ImageTags::$instance)) ImageTags::$instance = new ImageTags();
        return ImageTags::$instance;
    }
    /**
     * get all of the images (guid) with tag
     * @param string $tag the tag to look up
     * @return array array of profile image data arrays
     */
    public static function TaggedImages($tag){
        $instance = ImageTags::GetInstance();
        return $instance->LoadAllWhere(['tag'=>$tag]);
    }
    /**
     * get tags for image with guid
     * @param string $guid the image's guid | md5('/uploads/image.png');
     * @return array returns data array for image or null if it wasn't found
     */
    public static function TagsGUID($guid){
        $instance = ImageTags::GetInstance();
        return $instance->LoadAllWhere(['guid'=>$guid]);
    }
    /**
     * get all the images
     * @return array an array of all the profile image data arrays
     */
    public static function Images(){
        $instance = ImageTags::GetInstance();
        return $instance->LoadAll();
    }
    /**
     * save a profile image
     * @param array $data the data array of profile image to save
     * @return array save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function SaveTag($data){
        $instance = ImageTags::GetInstance();
        $data = $instance->CleanData($data);
        if(!is_null($instance->LoadWhere($data))) return defined("DEBUG") ? ['skipped'=>"tag already exists"] : null;
        if(isset($data['id'])) return $instance->Save($data,['id'=>$data['id']]);
        return $instance->Save($data);
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new ImageTags();
}
?>