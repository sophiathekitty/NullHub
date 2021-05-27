<?php
require_once("../../includes/main.php");
$data = [];

if(isset($_GET['pallet'])){
    $data['pallet'] = FullColorPallet();
} else {
    $colors = new Colors();
    $data['colors'] = $colors->LoadAll();
}

OutputJson($data);
?>