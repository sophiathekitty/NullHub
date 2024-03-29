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
        Debug::Log("Validate Model Tables --- ".count(clsModel::$models));
        $i = 0;
        foreach(clsModel::$models as $model){
            $i++;
            Debug::LogGroup($model->table_name,"$i Model - ".$model->table_name." - ");
            $model->ValidateTable();
            Debug::LogGroup($model->table_name," - validated - ");
        }
    }
    /**
     * validates the model's table
     */
    public function ValidateTable(){
        if(!clsDB::$db_g->has_table($this->table_name)){
            Debug::LogGroup($this->table_name,"Installing table...",
                clsDB::$db_g->install_table($this->table_name,$this->fields));
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
     * get a list of the data fields. ignores all the meta data fields
     * @return array list of data fields
     */
    public function DataFields(){
        $fields = [];
        //print_r($this->fields);
        foreach($this->fields as $field){
            //print_r($field);
            if(     $field['Key'] == "" &&
                    $field['Field'] != "created" &&
                    $field['Field'] != "modified" &&
                    $field['Field'] != "id" &&
                    $field['Field'] != "guid" &&
                    $field['Null'] == "NO" &&
                    strpos($field['Field'],"_min") == false &&
                    strpos($field['Field'],"_max") == false &&
                    strpos($field['Field'],"_id") == false
            ){
                $fields[] = $field['Field'];
            }
        }
        return $fields;
    }
    /**
     * get a list of the meta data fields. ignores all the data fields
     * @return array list of meta data fields
     */
    public function MetaDataFields(){
        $fields = [];
        foreach($this->fields as $field){
            if(     $field['Key'] != "" ||
                    $field['Field'] == "created" ||
                    $field['Field'] == "modified" ||
                    $field['Field'] == "id" ||
                    $field['Field'] == "guid" ||
                    strpos($field['Field'],"_id") != -1
            ){
                $fields[] = $field['Field'];
            }
        }
        return $fields;
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
                if(strtolower($f['Key']) != strtolower($field['Key']) && strtolower($field['Key']) != "index") return "Changed";
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
     * add the guid to the data
     * @param array $data
     * @param string $args a list of field names. if none included will return null
     * @return array the $data with $data['guid']
     */
    public function AddGUID($data){
        if(isset($data['guid'])) return $data;
        $args = func_get_args();
        $fields = [];
        if(count($args) == 1) {
            $field = $this->guid_fields;
        } else{
            for($i = 1; $i < count($args); $i++){
                if(is_string($args[$i])) $fields[] = $args[$i];
            }    
        }
        if(count($fields) < 1) return null;
        $guid_text = "";
        foreach($fields as $field){
            if(isset($data[$field])) $guid_text .= $data[$field];
            else {
                if($field == "created") $guid_text .= date("Y-m-d H:i:s");
            }
        }
        if($guid_text == "") $guid_text = date("Y-m-d H:i:s").".".rand(0,10000);
        $data['guid'] = md5($guid_text);
        return $data;
    }
    /**
     * make a guid for the data
     * @param array $data
     * @param string $args a list of field names. if none included will return null
     * @return string the GUID string
     */
    public function MakeGUID($data){
        if(isset($data['guid'])) return $data['guid'];
        $args = func_get_args();
        $fields = [];
        if(count($args) == 1) {
            $field = $this->guid_fields;
        } else{
            for($i = 1; $i < count($args); $i++){
                if(is_string($args[$i])) $fields[] = $args[$i];
            }    
        }
        if(count($fields) < 1) return null;
        $guid_text = "";
        foreach($fields as $field){
            if(isset($data[$field])) $guid_text .= $data[$field];
            else {
                if($field == "created") $guid_text .= date("Y-m-d H:i:s");
            }
        }
        if($guid_text == "") $guid_text = date("Y-m-d H:i:s").".".rand(0,10000);
        return md5($guid_text);
    }
    /**
     * name without tail number "item 1"
     * @param string $name the name to remove the number from ("item 1")
     * @return string the $name without the number "item"
     */
    public function RemoveTailNumber($name){
        $number = substr($name,strlen($name)-1);
        while((is_numeric($number) || $number == " ")&&strlen($name)>2){
            $name = substr($name,0,strlen($name)-1);
            $number = substr($name,strlen($name)-1);
        }
        return $name;
    }
    /**
     * loads all of the rows in the table
     * @return array database rows
     */
    public function LoadAll($order=null){
        //Debug::LogGroup("LoadAllOrder",$order);
        return clsDB::$db_g->safe_select($this->table_name,null,$order);
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
     * @param array|null $order keyed array for order ["key"=>"ASC","foo"=>"DESC"] leave null to not include ORDER BY
     * @param array|null $fields the fields ['field1','field2'] if null uses *
     * @return array|null returns the data array for the table rows loaded or null if now rows found
     */
    public function LoadWhere($where,$order = null,$fields = null){
        $rows = clsDB::$db_g->safe_select($this->table_name,$where,$order,$fields);
        if(count($rows) > 0) return $rows[0];
        return null;
    }
    /**
     * does a where search and can order the results
     * @param array $where ['key'=>'value']
     * @param array|null $order keyed array for order ["key"=>"ASC","foo"=>"DESC"] leave null to not include ORDER BY
     * @param array|null $fields the fields ['field1','field2'] if null uses *
     * @return array returns the array for the table rows loaded
     */
    public function LoadAllWhere($where,$order = null,$fields = null){
        //Debug::Log("clsModel::LoadAllWhere",$where);
        return clsDB::$db_g->safe_select($this->table_name,$where,$order,$fields);
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
    /**
     * load WHERE `$field` > '$datetime'
     * @param string $field the name of the field to search by
     * @param string $datetime the date to search by YYYY:MM:DD HH:MM:SS
     */
    public function LoadFieldAfter($field,$datetime){
        return clsDB::$db_g->select("SELECT * FROM `".$this->table_name."` WHERE `$field` > '$datetime';");
    }
    /**
     * load WHERE `$field` < '$datetime'
     * @param string $field the name of the field to search by
     * @param string $datetime the date to search by YYYY:MM:DD HH:MM:SS
     */
    public function LoadFieldBefore($field,$datetime){
        return clsDB::$db_g->select("SELECT * FROM `".$this->table_name."` WHERE `$field` < '$datetime';");
    }
    /**
     * load all WHERE $where AND `$field` > '$datetime'
     * @param array $where a keyed array to build where text ['field'=>$value]
     * @param string $field the name of the field to search by
     * @param string $datetime the date to search by YYYY:MM:DD HH:MM:SS
     */
    public function LoadWhereFieldAfter($where,$field,$datetime, $order = "ASC"){
        $where_txt = clsDB::$db_g->where_safe_string($where);
        return clsDB::$db_g->select("SELECT * FROM `".$this->table_name."` WHERE $where_txt AND `$field` > '$datetime' ORDER BY `$field` $order;");
    }
    /**
     * load WHERE $where AND `$field` < '$datetime'
     * @param array $where a keyed array to build where text ['field'=>$value]
     * @param string $field the name of the field to search by
     * @param string $datetime the date to search by YYYY:MM:DD HH:MM:SS
     */
    public function LoadWhereFieldBefore($where,$field,$datetime){
        $where_txt = clsDB::$db_g->where_safe_string($where);
        return clsDB::$db_g->select("SELECT * FROM `".$this->table_name."` WHERE $where_txt AND `$field` < '$datetime';");
    }
    /**
     * load WHERE `$field` BETWEEN '$start' AND '$end'
     * @param string $field the name of the field to search by
     * @param string $start start date YYYY:MM:DD HH:MM:SS
     * @param string $end end date YYYY:MM:DD HH:MM:SS
     * @return array the table array $rows[0][$field]
     */
    public function LoadFieldBetween($field,$start,$end){
        return clsDB::$db_g->select("SELECT * FROM `".$this->table_name."` WHERE `$field` BETWEEN '$start' AND '$end';");
    }
    /**
     * load WHERE $where_string AND `$field` BETWEEN '$start' AND '$end'
     * @param string $where_string a WHERE string ex: `field` = 'value'
     * @param string $field the name of the field to search by
     * @param string $start start date YYYY:MM:DD HH:MM:SS
     * @param string $end end date YYYY:MM:DD HH:MM:SS
     * @return array the table array $rows[0][$field]
     */
    public function LoadFieldBetweenWhere($where_string,$field,$start,$end){
        return clsDB::$db_g->select("SELECT * FROM `".$this->table_name."` WHERE $where_string AND `$field` BETWEEN '$start' AND '$end';");
    }
    /**
     * load WHERE `$field` BETWEEN '$start' AND '$end'
     * @param string $start_field the name of the earlier field (`start_date`)
     * @param string $end_field the name of the later field (`stop_date`)
     * @param string $datetime end date YYYY:MM:DD HH:MM:SS
     * @return array the table array $rows[0]
     */
    public function LoadFieldsBetween($start_field,$end_field,$datetime,$order = null){
        $order_txt = "";
        if(!is_null($order)) $order_txt = clsDB::$db_g->order_safe_string($order);
        return clsDB::$db_g->select("SELECT * FROM `".$this->table_name."` WHERE `$start_field` <= '$datetime' AND `$end_field` >= '$datetime'$order_txt;");
    }
    /**
     * load WHERE `$field` BETWEEN '$start' AND '$end' ($start_field <= $datetime AND $stop_field >= $datetime)
     * @param string $start_field the name of the earlier field (`start_date`)
     * @param string $end_field the name of the later field (`stop_date`)
     * @param string $datetime end date YYYY:MM:DD HH:MM:SS
     * @return array the table array $rows[0]
     */
    public function LoadWhereFieldsBetween($where,$start_field,$end_field,$datetime,$order = null){
        $order_txt = "";
        if(!is_null($order)) $order_txt = clsDB::$db_g->order_safe_string($order);
        $where_string = clsDB::$db_g->where_safe_string($where);
        return clsDB::$db_g->select("SELECT * FROM `".$this->table_name."` $where_string AND `$start_field` <= '$datetime' AND `$end_field` >= '$datetime'$order_txt;");
    }
    /**
     * load WHERE TIME(`$field`) BETWEEN '$hour:00:00' AND '$hour:59:59'
     * @param string $field the field to search hour by
     * @param int $hour the hour of the day to search for
     * @return array the table array $rows[0][$field]
     */

    public function LoadFieldHour($field,$hour){
        if($hour < 10) $hour = "0$hour";
        $start = $hour.":00:00";
        $end = $hour.":59:59";
        return clsDB::$db_g->select("SELECT * FROM `".$this->table_name."` WHERE TIME(`$field`) BETWEEN '$start' AND '$end';");
    }
    /**
     * load WHERE $where_string AND TIME(`$field`) BETWEEN '$hour:00:00' AND '$hour:59:59'
     * @param string $where_string a WHERE string ex: `field` = 'value'
     * @param string $field the field to search hour by
     * @param int $hour the hour of the day to search for
     * @return array the table array $rows[0][$field]
     */
    public function LoadFieldHourWhere($where_string,$field,$hour){
        if($hour < 10) $hour = "0$hour";
        $start = $hour.":00:00";
        $end = $hour.":59:59";
        return clsDB::$db_g->select("SELECT * FROM `".$this->table_name."` WHERE $where_string AND TIME(`$field`) BETWEEN '$start' AND '$end';");
    }
    /**
     * load WHERE TIME(`$field`) BETWEEN '$start' AND '$end'
     * @param string $field the name of the field to search by
     * @param string $start start time HH:MM:SS
     * @param string $end end time HH:MM:SS
     * @return array the table array $rows[0][$field]
     */
    public function LoadFieldBetweenTime($field,$start,$end){
        return clsDB::$db_g->select("SELECT * FROM `".$this->table_name."` WHERE TIME(`$field`) BETWEEN '$start' AND '$end';");
    }
    /**
     * Save data to the table
     * @param array $data the keyed data array to save ['field_name'=>$field_value]
     * @param array|null $where (optional) keyed data array of where to save ['id'=>$id]
     * @param bool $check_modified check if $data['modified'] is older than the existing $row['modified'] and don't save stale data
     * @return array a save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public function Save($data,$where = null,$check_modified = false){
        // check for matching record
        $id = null;
        $row = null;
        if($where){
            $row = $this->LoadWhere($where);
        }
        if(is_null($row)){
            // record doesn't exist insert a new one
            $id = clsDB::$db_g->safe_insert($this->table_name,$data,$where);
            $sql = clsDB::$db_g->last_sql;
        } else {
            if($check_modified && isset($data['modified'])){
                $row = $this->LoadWhereFieldAfter($where,"modified",$data['modified']);
                if(!is_null($row) && count($row)){
                    return ['error'=>"data stale",'data'=>$data,'row'=>$row];
                }
            }
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
     * delete all records where `$field` = '$value'
     * @param string $field the name of the field used for dating rows
     * @param string $value the value the field needs to be for the row to be deleted
     */
    public function DeleteFieldValue($field,$value){
        clsDB::$db_g->_query("DELETE FROM `".$this->table_name."` WHERE `$field` = '$value';");
        return ['sql'=>clsDB::$db_g->last_sql,'error'=>clsDB::$db_g->get_err()];
    }
    /**
     * delete all records in table and reset auto_increment
     */
    public function Truncate(){
        clsDB::$db_g->_query("TRUNCATE `".$this->table_name."`;");
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
    /**
     * strips out extra fields from data so it can be used to insert or update database
     * @param array $data keyed array of data
     * @return array keyed array of data stripped down to just the keys that are fields in the table
     */
    public function CleanFields($fields){
        $clean = [];
        foreach($fields as $field){
            foreach($this->fields as $f){
                if($f['Field'] == $field){
                    $clean[] = $field;
                    break;
                }
            }
        }
        return $clean;
    }
    /**
     * should this field be included
     * @param string $field the field name to see if it should be skipped
     * @param array|null $skips the list of fields to skip ['field1','field2']
     * @return bool return true if no skips or if field wasn't found in skips list
     */
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
    /**
     * joins another model with this one. this should be the linking table
     * @param clsModel $model instance of the model to be joined with this one
     * @param string $on field to join the tables on `my_table`.`$on` = `model_table`.`$model_on`
     * @param string $model_on field to join the tables on `my_table`.`$on` = `model_table`.`$model_on`
     * @param array $model_where where array for fields in model being passed to this function
     * @param array $where where array for fields in the model the function is being called on
     * @return array keyed array of data from joined tables
     */
    public function JoinWhere($model, $on, $model_on ,$model_where, $where = null){
        //echo $this->table_name. " - ".$model->table_name."\n";
        $sql = "SELECT * FROM `".$this->table_name."` INNER JOIN ";
        $sql .= "`".$model->table_name."` on `".$model->table_name."`.`$model_on` = `".$this->table_name."`.`$on`";
        $sql .= " WHERE ";
        if(!is_null($model_where)) $sql .= clsDB::$db_g->where_safe_string($model_where,$model->table_name);
        $sql .= " ";
        if(!is_null($where)) $sql .= clsDB::$db_g->where_safe_string($where,$this->table_name);
        //echo $sql."\n\n";
        $rows = clsDB::$db_g->select($sql);
        $err = clsDB::$db_g->get_err();
        if($err != "") Debug::Log("clsModel::".$this->table_name."::JoinWhere",$sql,$err);
        return $rows;
    }    
    /**
     * joins another model with this one. this should be the linking table
     * @param clsModel $model instance of the model to be joined with this one
     * @param array $fields list of fields to select from the model running this function
     * @param array $model_fields list of fields to select from the model passed to this function
     * @param string $on field to join the tables on `my_table`.`$on` = `model_table`.`$model_on`
     * @param string $model_on field to join the tables on `my_table`.`$on` = `model_table`.`$model_on`
     * @param array $model_where where array for fields in model being passed to this function
     * @param array $where where array for fields in the model the function is being called on
     * @return array keyed array of data from joined tables
     */
    public function JoinFieldsWhere($model, $fields, $model_fields, $on, $model_on ,$model_where, $where = null){
        $sql = "SELECT ";
        // fields
        $first = true;
        foreach($fields as $field){
            if(!$first) $sql .= ", ";
            $first = false;
            $sql .= "`".$this->table_name."`.`$field`";
        }
        foreach($model_fields as $field){
            if(!$first) $sql .= ", ";
            $first = false;
            $sql .= "`".$model->table_name."`.`$field`";
        }
        $sql .= " FROM `".$this->table_name."` INNER JOIN ";
        $sql .= "`".$model->table_name."` on `".$model->table_name."`.`$model_on` = `".$this->table_name."`.`$on`";
        if(!is_null($where) || !is_null($model_where)){
            $sql .= " WHERE ";
            if(!is_null($model_where)) $sql .= clsDB::$db_g->where_safe_string($model_where,$model->table_name);
            $sql .= " ";
            if(!is_null($where)) $sql .= clsDB::$db_g->where_safe_string($where,$this->table_name);    
        }
        //echo "\n\n".$sql."\n\n";
        $rows = clsDB::$db_g->select($sql);
        $err = clsDB::$db_g->get_err();
        if($err != "") Debug::Log("clsModel::".$this->table_name."::JoinWhere",$sql,$err);
        return $rows;
    }
    /**
     * get the number of rows in this table
     */
    public function RowsCount(){
        $count = clsDB::$db_g->select("SELECT COUNT(*) FROM $this->table_name;");
        if(count($count) == 0 || !isset($count[0]["COUNT(*)"])) return "N/A";
        return (int)$count[0]["COUNT(*)"];
    }
    /**
     * get the number of rows in this table
     */
    public function SpaceUsed(){
        global $device_info;
        $size = clsDB::$db_g->select("SELECT 
        table_name AS `Table`, 
        round(((data_length + index_length) / 1024), 2) `Size` 
    FROM information_schema.TABLES 
    WHERE table_schema = '".$device_info['database']."'
        AND table_name = '".$this->table_name."';");
        if(count($size) == 0 || !isset($size[0]['Size'])) return "N/A";
        if($size[0]['Size'] > 1000) return round($size[0]['Size']/1024,2)." MB";
        return $size[0]['Size']." KB";
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
    public $guid_fields = [];
    public $hourly_type = "";
    /**
     * constructor function
     */
    public function __construct()
    {
        if($this->hourly_type != "") $this->add_hourly_fields();
    }
    /**
     * adds 'h0' through 'h23' fields with type $this->hourly_type if $this->hourly_type has been set by the model extending clsModel
     */
    public function add_hourly_fields()
    {
        if($this->hourly_type == "") return;
        foreach($this->fields as $field){
            if($field['Field'] == "h0") return;
        }
        for($i = 0; $i < 24; $i++){
            $this->fields[] = [
                'Field'=>"h".$i,
                'Type'=>$this->hourly_type,
                'Null'=>"NO",
                'Key'=>"",
                'Default'=>"",
                'Extra'=>""
            ];
        }    
    }
}
?>