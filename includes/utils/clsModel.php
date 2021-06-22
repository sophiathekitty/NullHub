<?php
/* 
ok... so if  i'm going to bother having a model class it should solve some basic problems

1. be able to install it's table
2. be able to validate and update it's table

and it should be able to lint nicely... so probably do everything as static functions
*/


class clsModel {
    public static $models = [];
    public static function ValidateTables(){
        echo "Validate Model Tables --- ".count(clsModel::$models)." \n";
        $i = 0;
        foreach(clsModel::$models as $model){
            $i++;
            echo "$i Model - ".$model->table_name." - \n";
            $model->ValidateTable();
            echo " - validated - \n";
        }
    }
    public function ValidateTable(){
        if(!clsDB::$db_g->has_table($this->table_name)){
            clsDB::$db_g->install_table($this->table_name,$this->fields);
            echo "Installing table...\n";
        }
        $table = clsDB::$db_g->describe_table($this->table_name);
        //print_r($table);
        //print_r($this->fields);
        $after = "";
        // check for missing or changed fields
        foreach($this->fields as $field){
            $has = $this->TableHasField($table,$field);
            switch($has){
                case "Missing":
                    // add the field
                    clsDB::$db_g->add_field($this->table_name,$field,$after);
                    break;
                case "Changed":
                    // add the field
                    //echo "Field changed: ".$field['Field']."\n";
                    clsDB::$db_g->update_field($this->table_name,$field);
                    //echo clsDB::$db_g->get_err();
                    break;
            }
            $after = $field['Field'];
        }
        // check for fields that have been removed
        foreach($table as $field){
            if($this->TableHasDepreciatedField($field)){
                clsDB::$db_g->remove_field($this->table_name,$field);
            }
        }
    }
    private function TableHasField($table,$field){
        foreach($table as $f){
            if($f['Field'] == $field['Field']){
                if(strtolower($f['Type']) != strtolower($field['Type'])) return "Changed";
                if(strtolower($f['Null']) != strtolower($field['Null'])) return "Changed";
                if(strtolower($f['Key']) != strtolower($field['Key'])) return "Changed";
                if(str_replace("()","",strtolower($f['Default'])) != str_replace("()","",strtolower($field['Default']))) return "Changed";
                if(str_replace("()","",strtolower($f['Extra'])) != str_replace("()","",strtolower($field['Extra']))) return "Changed";
                return "Found";
            }
        }
        return "Missing";
    }
    private function TableHasDepreciatedField($field){
        foreach($this->fields as $f){
            if($field['Field'] == $f['Field']) return false;
        }
        return true;
    }

    public function LoadAll(){
        return clsDB::$db_g->safe_select($this->table_name);
    }

    public function LoadById($id){
        $rows = clsDB::$db_g->safe_select($this->table_name,['id'=>$id]);
        if(count($rows) > 0) return $rows[0];
        return null;
    }
    public function LoadWhere($where,$order = null){
        $rows = clsDB::$db_g->safe_select($this->table_name,$where,$order);
        if(count($rows) > 0) return $rows[0];
        return null;
    }
    public function LoadAllWhere($where,$order = null){
        return clsDB::$db_g->safe_select($this->table_name,$where,$order);
    }
    public function LoadMostRecentlyCreated(){
        $rows = clsDB::$db_g->safe_select($this->table_name,null,['created'=>"DESC"]);
        if(count($rows) > 0) return $rows[0];
        return null;
    }
    public function LoadFieldAfter($field,$datetime){
        return clsDB::$db_g->select("SELECT * FROM `".$this->table_name."` WHERE `$field` > '$datetime';");
    }
    public function LoadFieldBefore($field,$datetime){
        return clsDB::$db_g->select("SELECT * FROM `".$this->table_name."` WHERE `$field` < '$datetime';");
    }
    public function LoadWhereFieldAfter($where,$field,$datetime){
        $where_txt = clsDB::$db_g->where_safe_string($where);
        return clsDB::$db_g->select("SELECT * FROM `".$this->table_name."` WHERE $where_txt AND `$field` > '$datetime';");
    }
    public function LoadWhereFieldBefore($where,$field,$datetime){
        $where_txt = clsDB::$db_g->where_safe_string($where);
        return clsDB::$db_g->select("SELECT * FROM `".$this->table_name."` WHERE $where_txt AND `$field` < '$datetime';");
    }
    public function LoadFieldBetween($field,$start,$end){
        return clsDB::$db_g->select("SELECT * FROM `".$this->table_name."` WHERE `$field` BETWEEN '$start' AND '$end';");
    }
    public function LoadFieldBetweenWhere($where_string,$field,$start,$end){
        return clsDB::$db_g->select("SELECT * FROM `".$this->table_name."` WHERE $where_string AND `$field` BETWEEN '$start' AND '$end';");
    }
    public function LoadFieldHour($field,$hour){
        if($hour < 10) $hour = "0$hour";
        $start = $hour.":00:00";
        $end = $hour.":59:59";
        return clsDB::$db_g->select("SELECT * FROM `".$this->table_name."` WHERE TIME(`$field`) BETWEEN '$start' AND '$end';");
    }
    public function LoadFieldHourWhere($where_string,$field,$hour){
        if($hour < 10) $hour = "0$hour";
        $start = $hour.":00:00";
        $end = $hour.":59:59";
        return clsDB::$db_g->select("SELECT * FROM `".$this->table_name."` WHERE $where_string AND TIME(`$field`) BETWEEN '$start' AND '$end';");
    }
    public function LoadFieldBetweenTime($field,$start,$end){
        return clsDB::$db_g->select("SELECT * FROM `".$this->table_name."` WHERE TIME(`$field`) BETWEEN '$start' AND '$end';");
    }
    public function Save($data,$where = null){
        // check for matching record
        $id = null;
        $row = null;
        if($where){
            $row = $this->LoadWhere($where);
        }
        if(is_null($row)){
            // record doesn't exist insert a new one
            $id = clsDB::$db_g->safe_insert($this->table_name,$data,$where);
        } else {
            // record already exists so update it
            $id = clsDB::$db_g->safe_update($this->table_name,$data,$where);
            $row = $this->LoadWhere($where);
        }
        return ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'row'=>$row];
    }

    public function PruneField($field,$seconds){
        $d = date("Y-m-d H:i:s",time()-$seconds);
        clsDB::$db_g->_query("DELETE FROM `".$this->table_name."` WHERE `$field` < '$d';");
    }

    public function CleanData($data){
        $clean = [];
        foreach($this->fields as $field){
            if(isset($data[$field['Field']])){
                $clean[$field['Field']] = $data[$field['Field']];
            }
        }
        return $clean;
    }
    private function SkipField($field,$skips){
        if(is_null($skips)) return true;
        foreach($skips as $skip){
            if($field == $skip) return false;
        }
        return true;
    }
    public function CleanDataSkipFields($data, $fields = null){
        $clean = [];
        foreach($this->fields as $field){
            if(isset($data[$field['Field']]) && $this->SkipField($field['Field'],$fields)){
                $clean[$field['Field']] = $data[$field['Field']];
            }
        }
        return $clean;
    }    // this needs to be overwritten by the individual models
    public function CleanDataSkipId($data){
        return $this->CleanDataSkipFields($data,['id']);
        $clean = [];
        foreach($this->fields as $field){
            if(isset($data[$field['Field']]) && $field['Field'] != 'id'){
                $clean[$field['Field']] = $data[$field['Field']];
            }
        }
        return $clean;
    }    // this needs to be overwritten by the individual models
    public $table_name = "Example";
    public $fields = [
        [
            "Field"=>"id",
            "Type"=>"int(11)",
            "Null"=>"NO",
            "Key"=>"PRI",
            "Default"=>"",
            "Extra"=>"auto_increment"
        ]
    ];
}
?>