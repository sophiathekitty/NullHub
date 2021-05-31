<?php
require_once("../../../includes/main.php");
$data = [];
$data['sections'] = Templates::SectionTemplates();
OutputJson($data);
?>