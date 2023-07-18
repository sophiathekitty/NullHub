<?php
require_once("../../includes/main.php");
$services = Services::AllServices();
?>
<dialog class="side menu" id="services-list">
    <h1>Services</h1>
    <ul id="services"><?php foreach($services as $service) {?>
        <li last_start="<?=date("D g:ia",strtotime($service['last_start']));?>" last_done="<?=date("D g:ia",strtotime($service['last_done']));?>">
            <a href="#" service="<?=$service['name'];?>"><?=$service['name'];?></a>
        </li>
    <?php } ?></ul>
</dialog>
