<?php
require_once("../../../../includes/main.php");
$data = [];
// return servers
if(isset($_GET['simple'])){
	$data = "http://".Servers::GetHub()['url']."/";
} else {
	$data['hub'] = Servers::GetHub();
}
OutputJson($data);
?>