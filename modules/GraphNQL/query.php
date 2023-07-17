<?php
/**
 * Null Query
 */
class NullQuery{
    /**
     * run an incoming query
     */
    public static function Handle(){
        return NullQuery::ParseQuery(NullQuery::GetQuery());
    }
    /**
     * Get the query from the post data
     */
    private static function GetQuery(){
        $data = file_get_contents('php://input');
        if($data == "") return null;
        return json_decode($data,true);
    }
    /**
     * get model
     */
    private static function GetModel($key){
        foreach(clsModel::$models as $model){
            if($model->table_name == $key) return $model;
        }
    }
    /**
     * Parse the query
     * @param array $query the json query
     * @param array|null $parent the parent result calling this
     * @return array the json response
     */
    private static function ParseQuery($query,$parent = null){
        $data = [];
        if(!is_null($query)){
            foreach($query as $key => $value){
                if(is_object($value) || is_array($value)){
                    $model = NullQuery::GetModel($key);
                    if(is_null($model)) {
                        //JsonDie("Model:$key not found");
                        if(!isset($data['message'])) $data["message"] = "Model:$key not found.";
                        else $data["message"] .= " Model:$key not found.";
                        continue;
                    }
                    $where = [];
                    $fields = [];
                    $children = [];
                    foreach($value as $k=>$v){
                        if(!is_array($v) && !is_object($v)){
                            if($v != "*") $where[$k] = $v;
                            if(!is_null($parent) && strpos($v,":parent:") > -1){
                                $parent_key = str_replace(":parent:","",$v);
                                $where[$k] = $parent[$parent_key];
                            } else $fields[] = $k;
                        } else {
                            $children[$k] = $v;
                        }
                    }
                    $where = $model->CleanData($where);
                    $fields = $model->CleanFields($fields);
                    if(count($where) == 0) $where = null;
                    if(count($fields) == 0) $fields = null;
                    $data[$key] = $model->LoadAllWhere($where,null,$fields);
                    if(count($children) > 0){
                        for($i = 0; $i < count($data[$key]); $i++){
                            $kids = NullQuery::ParseQuery($children,$data[$key][$i]);
                            foreach($kids as $kid_key => $kid_value){
                                $data[$key][$i][$kid_key] = $kid_value;
                            }
                        }    
                    }
                }
            }
        }
        return $data;
    }
}
?>