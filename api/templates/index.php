<?php
require_once("../../includes/main.php");
$data = [];
$data['templates'] = Templates::FindTemplates();
OutputJson($data);
?>