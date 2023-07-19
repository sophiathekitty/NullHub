<?php
require_once("../../includes/main.php");
$services = Services::AllServices();
function ServiceStatus($service){
    $status = "ok";
    $start_time = strtotime($service['last_start']);
    $done_time = strtotime($service['last_done']);
    if($start_time > $done_time){
        // is running? or not finishing?
        if($start_time - $done_time > MinutesToSeconds(5)) $status = "error";
        else $status = "running";
    }
    return $status;
}
?>
<dialog class="side menu" id="services-list">
    <h1>Services</h1>
    <ul id="services"><?php foreach($services as $service) {?>
        <li status="<?=ServiceStatus($service);?>" last_start="<?=date("D g:ia",strtotime($service['last_start']));?>" last_done="<?=date("D g:ia",strtotime($service['last_done']));?>">
            <a href="#" service="<?=$service['name'];?>"><?=$service['name'];?></a>
        </li>
    <?php } ?></ul>
</dialog>
