<?php
define('VALIDATE_TABLES',true); // gathers all the models into clsModel::$models
require_once("../includes/main.php");
$data = [];
/**
 * Graph Null Query Language
 * i dunno... GraphQL sounds neat.. but also that it has a massive learning curve and is hard
 * to actually implement on the server... so why not just make my own super simple one that 
 * can do the stuff i need... i'm not sure how big of a priority this is... mostly a distraction
 * to work on if i have any ideas for it and another more useful thing i'm avoiding working on...
 * 
 * i think the best way to do this is have some way to pick the model and then i need a way to
 * pick between LoadWhere() and LoadAllWhere() and make sure those functions both can take a fields
 * list... and then i need a way to define the where and order and fields arrays that the functions
 * expect. and for a stretch goal have way for them to then load another model based on the parent
 * model loaded item(s)..
 * 
 */
/**
 * get models list
 */
if(isset($_GET['models'])){
    if($_GET['models'] == "verbose"){
        $data['models'] = [];
        foreach(clsModel::$models as $model){
            $data['models'][] = [
                "model"=>$model->table_name,
                "fields"=>$model->fields,
                "size"=>$model->SpaceUsed(),
                "rows"=>$model->RowsCount()
            ];
        }
        
    } else if($_GET['models'] == "list") {
        $data['models'] = [];
        foreach(clsModel::$models as $model){
            $data['models'][] = $model->table_name;
        }
    } else {
        $data['models'] = clsModel::$models;
    }
    OutputJson($data);
    die();
}
OutputJson(NullQuery::Handle());
die();






// old code... that i moved to a module NullQuery




/**
 * Get the query from the post data
 */
function GetQuery(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the JSON data from the request body
        $json = file_get_contents('php://input');
        return json_decode($json,true);
    }
    /*
    if(count($_POST)){
        $query = [];
        foreach($_POST as $key => $value){
            $json_key = json_decode($key,true);
            $json_value = json_decode($value,true);
            if(is_null($json_value) && !is_null($json_key)) $query[] = $json_key;
            if(!is_null($json_value)) $query[] = $json_value;
            if(is_null($json_key) && is_null($json_value)) Debug::Log(['key'=>$key,'value'=>$value]);
        }
        if(count($query) == 1) return $query[0];
        if(count($query) > 1){
            return $query;
        }
    }
    */
    return null;    
}
/**
 * get model
 */
function GetModel($key){
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
function ParseQuery($query,$parent = null){
    $data = [];
    if(!is_null($query)){
        foreach($query as $key => $value){
            if(is_object($value) || is_array($value)){
                $model = GetModel($key);
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
                        $kids = ParseQuery($children,$data[$key][$i]);
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
/**
 * try to run the query
 */
//$query = GetQuery();
OutputJson(ParseQuery(GetQuery()));
?>