<?php
/**
 * @depreciated use ```Settings::LoadAllSettings()```
 */
function LoadSettingVars(){
    return clsDB::$db_g->select("SELECT * FROM `settings`;");
}
/**
 * @depreciated use ```Settings::LoadSettingsVar($name)```
 */
function LoadSettingVar($name){
    return Settings::LoadSettingsVar($name);
    /*
    $res = clsDB::$db_g->select("SELECT * FROM `settings` WHERE `name` = '$name';");
    if(count($res)){
        return $res[0]['value'];
    }
    return null;
    */
}
/**
 * @depreciated use ```Settings::SaveSettingsVar($name,$value)```
 */
function SaveSettingVar($name,$value){
    return Settings::SaveSettingsVar($name,$value);
    /*if(is_null(LoadSettingVar($name))){
        // insert new
        clsDB::$db_g->safe_insert('settings',['name'=>$name,'value'=>$value]);
    } else {
        // update existing
        clsDB::$db_g->safe_update('settings',['value'=>$value],['name'=>$name]);
    }*/
}
/**
 * handles loading the Settings Vars
 */
class Settings extends clsModel {
    private static $settings = [];
    private static $instance = null;
    /**
     * @return Settings
     */
    private static function GetInstance(){
        if(is_null(Settings::$instance)){
            Settings::$instance = new Settings();
        }
        return Settings::$instance;
    }
    /**
     * load all settings vars
     * @return array an array of all the settings $instance[0]['name'], $instance[0]['value']
     */
    public static function LoadAllSettings(){
        if(defined("SETUP_MODE")) return [];
        $instance = Settings::GetInstance();
        return $instance->LoadAll();
    }
    /**
     * loads a settings var
     * @param string $pallet the prefix to search with "weather_"
     * @return array an array of settings
     */
    public static function LoadSettingsPallet($pallet){
        $instance = [];
        if(defined("SETUP_MODE")) return $instance;
        $rows = clsDB::$db_g->select("SELECT * FROM `Settings` WHERE `name` LIKE '$pallet%'");
        foreach($rows as $row){
            $instance[$row['name']] = $row['value'];
        }
        return $instance;
    }
    /**
     * loads a settings var
     * @param string $name the name of the setting to load
     * @param string|null $default (optional) the default value to be saved if setting doesn't exist yet
     * @return string the value of the setting
     */
    public static function LoadSettingsVar($name,$default = null){
        if(defined("SETUP_MODE")) return $default;
        $instance = Settings::GetInstance();
        return $instance->LoadVar($name,$default);
    }
    /**
     * save a setting var
     * @param string $name the name of the setting to save
     * @param string $value the value to save for the setting
     * @return array returns save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function SaveSettingsVar($name,$value){
        if(defined("SETUP_MODE")) return $value;
        $instance = Settings::GetInstance();
        return $instance->SaveVar($name,$value);
    }
    /**
     * loads a settings var. will try to sync if 
     * @param string $name the name of the setting to load
     * @param string|null $default (optional) the default value to be saved if setting doesn't exist yet
     * @param int $cache_minutes lets you change how long to wait before syncing from the hub
     * @return string the value of the setting
     */
    public static function LoadSyncedSettingsVar($name,$default = null, $cache_minutes = 60){
        if(defined("SETUP_MODE")) return $default;
        $instance = Settings::GetInstance();
        if(SecondsToMinutes($instance->VarModifiedSeconds($name)) < $cache_minutes) return $instance->LoadVar($name,$default);
        $setting = ServerRequests::LoadHubJSON("/api/settings/?name=$name");
        if(!isset($setting['name'],$setting['value'])) return $instance->LoadVar($name,$default);
        $instance->SaveVar($setting['name'],$setting['value']);
        return $setting['value'];
    }
    /**
     * sync a setting from the main hub
     * @param string $name the name of the setting to sync from hub
     * @return array returns save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function SyncSettingsVar($name){
        $setting = ServerRequests::LoadHubJSON("/api/settings/?name=$name");
        if(!isset($setting['name'],$setting['value'])) return null;
        $instance = Settings::GetInstance();
        return $instance->SaveVar($setting['name'],$setting['value']);
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
    /**
     * get when the setting var was last modified
     * @param string $name the name of the setting to load
     * @return string the datetime of when the setting var was last modified
     */
    public function VarModified($name){
        $var = $this->LoadWhere(['name'=>$name]);
        if(!is_null($var)) return $var['modified'];
        return null;
    }
    /**
     * get when the setting var was last modified
     * @param string $name the name of the setting to load
     * @return string the datetime of when the setting var was last modified
     */
    public function VarModifiedSeconds($name){
        $var = $this->LoadWhere(['name'=>$name]);
        if(is_null($var)) return null;
        return time() - strtotime($var['modified']);
    }
    /**
     * loads a settings var
     * @param string $name the name of the setting to load
     * @param string|null $default (optional) the default value to be saved if setting doesn't exist yet
     * @return string the value of the setting
     */
    public function LoadVar($name,$default = null){
        if(isset(Settings::$settings[$name])) return Settings::$settings[$name];
        $var = $this->LoadWhere(['name'=>$name]);
        if(is_null($var) && !is_null($default)){
            $this->SaveVar($name,$default);
            return $default;
        }
        if(!is_null($var)) {
            Settings::$settings[$name] = $var['value'];
            return $var['value'];
        }
        return null;
    }
    /**
     * save a setting var
     * @param string $name the name of the setting to save
     * @param string $value the value to save for the setting
     * @return array returns save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
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