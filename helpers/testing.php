<?php
//echo "Hello ";
//define("DEBUG","ECHO");
require_once("../includes/main.php");
//echo " World\n";

$model = new clsModel();
$data = [];
$data['item1'] = $model->RemoveTailNumber("item1");
$data['item2'] = $model->RemoveTailNumber("item 2");
$data['item3'] = $model->RemoveTailNumber("item");
OutputJson($data);
?>