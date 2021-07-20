<?php
/**
 * ok... so if  i'm going to bother having a model class it should solve some basic problems
 * 
 * 1. be able to install it's table
 * 2. be able to validate and update it's table
 * 
 * and it should be able to lint nicely... so probably do everything as static functions
 */
class clsModel {
    public static $models = [];
    /**
     * validates the tables for all the registered models
     */
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
    /**
     * validates the model's table
     */
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
    /**
     * check if the model's table has a field
     * @param array $table the table structure
     * @param array $field the data array for a field
     * @return string Changed | Missing | Found
     */
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
    /**
     * checks if a database field is still present in the model
     * @param array $field field data array
     * @return bool false if the field is present in the model
     */
    private function TableHasDepreciatedField($field){
        foreach($this->fields as $f){
            if($field['Field'] == $f['Field']) return false;
        }
        return true;
    }
    /**
     * loads all of the rows in the table
     * @return array database rows
     */
    public function LoadAll(){
        return clsDB::$db_g->safe_select($this->table_name);
    }
    /**
     * loads by the `id` field
     * @param string|int $id the id of the record to be loaded
     * @return array|null returns the data array for the table row loaded or null if now rows found
     */
    public function LoadById($id){
        $rows = clsDB::$db_g->safe_select($this->table_name,['id'=>$id]);
        if(count($rows) > 0) return $rows[0];
        return null;
    }
    /**
     * does a where search and can order the results
     * @param array $where ['key'=>'value']
     * @return array|null returns the data array for the table rows loaded or null if now rows found
     */
    public function LoadWhere($where,$order = null){
        $rows = clsDB::$db_g->safe_select($this->table_name,$where,$order);
        if(count($rows) > 0) return $rows[0];
        return null;
    }
    /**
     * does a where search and can order the results
     * @param array $where ['key'=>'value']
     * @return array returns the array for the table rows loaded
     */
    public function LoadAllWhere($where,$order = null){
        return clsDB::$db_g->safe_select($this->table_name,$where,$order);
    }
    /**
     * loads the most recently created (uses created field in table)
     * @return array|null returns the data array for the table rows loaded or null if now rows found
     */
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
            $sql = clsDB::$db_g->last_sql;
            $row = $this->LoadWhere($where);
        }
        return ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row];
    }
    /**
     * prunes the table of old rows
     * @param string $field the name of the field used for dating rows
     * @param int $seconds how many seconds back to go before pruning
     */
    public function PruneField($field,$seconds){
        $d = date("Y-m-d H:i:s",time()-$seconds);
        clsDB::$db_g->_query("DELETE FROM `".$this->table_name."` WHERE `$field` < '$d';");
    }
    /**
     * strips out extra fields from data so it can be used to insert or update database
     * @param array $data keyed array of data
     * @return array keyed array of data stripped down to just the keys that are fields in the table
     */
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
    /**
     * strips out fields and cleans data object of fields that aren't in the table so it can be used to with safe inserts and safe updates.
     * @param array $data keyed array of data
     * @param array|null $fields array of field data arrays to strip out of the data
     * @return array keyed array of data stripped down to just the keys that are fields in the table
     */
    public function CleanDataSkipFields($data, $fields = null){
        $clean = [];
        foreach($this->fields as $field){
            if(isset($data[$field['Field']]) && $this->SkipField($field['Field'],$fields)){
                $clean[$field['Field']] = $data[$field['Field']];
            }
        }
        return $clean;
    }
    /**
     * strips out id field and cleans data object of fields that aren't in the table so it can be used to with safe inserts and safe updates.
     * @param array $data keyed array of data
     * @return array keyed array of data stripped down to just the keys that are fields in the table
     */
    public function CleanDataSkipId($data){
        return $this->CleanDataSkipFields($data,['id']);
        $clean = [];
        foreach($this->fields as $field){
            if(isset($data[$field['Field']]) && $field['Field'] != 'id'){
                $clean[$field['Field']] = $data[$field['Field']];
            }
        }
        return $clean;
    }
    // this needs to be overwritten by the individual models
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