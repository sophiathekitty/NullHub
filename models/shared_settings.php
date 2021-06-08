<?php
function LoadSettingVars(){
    return clsDB::$db_g->select("SELECT * FROM `settings`;");
}
function LoadSettingVar($name){
    $res = clsDB::$db_g->select("SELECT * FROM `settings` WHERE `name` = '$name';");
    if(count($res)){
        return $res[0]['value'];
    }
    return null;
}
function SaveSettingVar($name,$value){
    if(is_null(LoadSettingVar($name))){
        // insert new
        clsDB::$db_g->safe_insert('settings',['name'=>$name,'value'=>$value]);
    } else {
        // update existing
        clsDB::$db_g->safe_update('settings',['value'=>$value],['name'=>$name]);
    }
}

class Settings extends clsModel {
    private static $settings = null;
    public static function GetInstance(){
        if(is_null(Settings::$settings)){
            Settings::$settings = new Settings();
        }
        return Settings::$settings;
    }
    public static function LoadAllSettings(){
        $settings = Settings::GetInstance();
        return $settings->LoadAll();
    }
    public static function LoadSettingsVar($name,$default = null){
        $settings = Settings::GetInstance();
        return $settings->LoadVar($name,$default);
    }
    public static function SaveSettingsVar($name,$value){
        $settings = Settings::GetInstance();
        return $settings->SaveVar($name,$value);
    }
    public $table_name = "Settings";
    public $fields = [
        [
            'Field'=>"name",
            'Type'=>"varchar(50)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"value",
            'Type'=>"varchar(200)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"modified",
            'Type'=>"datetime",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"current_timestamp()",
            'Extra'=>"on update current_timestamp()"
        ]
    ];
    public function LoadVar($name,$default = null){
        $var = $this->LoadWhere(['name'=>$name]);
        if(is_null($var) && !is_null($default)){
            $this->SaveVar($name,$default);
            return $default;
        }
        if(!is_null($var)) return $var['value'];
        return null;
    }
    public function SaveVar($name,$value){
        if(is_null($this->LoadVar($name))){
            return $this->Save(['name'=>$name,'value'=>$value]);
        }
        return $this->Save(['value'=>$value],['name'=>$name]);
    }
}

if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new Settings();
}

?>