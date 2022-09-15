<?php
$apis = ServerRequests::LoadLocalhostJSON('/api/');
if(isset($apis['apis'])){
    CheckApiLayer("null","api",$apis['apis']);
    $pass = true;
    foreach($tests['apis'] as $parent){
        foreach($parent as $child){
            if($child['res'] == "Fail") $pass = false;
        }
    }
    if($pass) $tests['running'][$test_name] = "Pass";
}

function CheckApiLayer($parent,$name,$apis){
    global $tests;
    foreach($apis as $key => $value){
        if($key == "local"){
            $time_before = microtime(true);
            $res = ServerRequests::LoadLocalhostJSON($value);
            $time_after = microtime(true);
            $tests['apis'][$parent][$name]['latency'] = $time_after - $time_before;    
            if(is_array($res) && count($res) > 0 || $name == "simple"){
                $tests['apis'][$parent][$name]['res'] = 'Pass';
            } else {
                $tests['apis'][$parent][$name]['res'] = 'Fail';
            }
        }
        if(is_array($value)){
            CheckApiLayer($name,$key,$value);
        }
    }
}
?>