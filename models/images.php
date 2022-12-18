<?php

/**
 * stores the location of a profile image that could be on this device or another device
 * as well as additional information about the image
 */
class ImageFile extends clsModel {
    public $table_name = "ImageFile";
    public $fields = [
        [
            'Field'=>"guid",
            'Type'=>"varchar(100)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"mac_address",
            'Type'=>"varchar(100)",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"file",
            'Type'=>"varchar(50)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"path",
            'Type'=>"varchar(200)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"user_id",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0",
            'Extra'=>""
        ],[
            'Field'=>"available",
            'Type'=>"tinyint(1)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"1",
            'Extra'=>""
        ],[
            'Field'=>"created",
            'Type'=>"datetime",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"current_timestamp()",
            'Extra'=>"on update current_timestamp()"
        ],[
            'Field'=>"removed",
            'Type'=>"datetime",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ]
    ];
    private static $instance = null;
    /**
     * @return ImageFile|clsModel
     */
    private static function GetInstance(){
        if(is_null(ImageFile::$instance)) ImageFile::$instance = new ImageFile();
        return ImageFile::$instance;
    }
    /**
     * get all of the profile pictures for a specific user
     * @param int $user_id the user's id
     * @return array array of profile image data arrays
     */
    public static function UserImages($user_id){
        $instance = ImageFile::GetInstance();
        return $instance->LoadAllWhere(['user_id'=>$user_id]);
    }
    /**
     * get an image by it's guid
     * @param string $guid the image's guid | md5('/uploads/image.png');
     * @return array|null returns data array for image or null if it wasn't found
     */
    public static function ImageGUID($guid){
        $instance = ImageFile::GetInstance();
        return $instance->LoadWhere(['guid'=>$guid]);
    }
    /**
     * get all the images
     * @return array an array of all the profile image data arrays
     */
    public static function Images(){
        $instance = ImageFile::GetInstance();
        return $instance->LoadAll();
    }
    /**
     * save a profile image
     * @param array $data the data array of profile image to save
     * @return array save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function SaveImage($data){
        $instance = ImageFile::GetInstance();
        if(isset($data['tags'])){
            $tags = explode(",",$data['tags']);
            if(!isset($data['guid'])){
                $data['guid'] = ImageFile::MakeGUID($data['path'],$data['file']);
            }
            foreach($tags as $tag){
                ImageTags::SaveTag(['guid'=>$data['guid'],'tag',$tag]);
            }
        }
        $data = $instance->CleanData($data);
        if(isset($data['guid']) && !is_null(ImageFile::ImageGUID($data['guid']))) return $instance->Save($data,['guid'=>$data['guid']]);
        $data['guid'] = ImageFile::MakeGUID($data['path'],$data['file']);
        return $instance->Save($data);
    }
    /**
     * Generates a guid for an image from it's path and filename
     * @param string $path the image path "/path/to/image/"
     * @param string $file the image filename "image.jpg"
     * @return string the guid hash of the filepath md5($path.$file)
     */
    public static function MakeGUID($path,$file){
        return md5($path.$file);
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new ImageFile();
}
?>