<?php
require_once("../../../includes/main.php");
$data = [];
if(isset($_GET['pallet'])){
    $data['pallet'] = ColorPalletStamp($_GET['pallet']);
} else {
    $data['pallet'] = FullColorPallet();
}

OutputJson($data);
?>