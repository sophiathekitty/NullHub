<pre><?php
echo "hello ";
define('VALIDATE_TABLES',true);
require_once("../includes/main.php");
echo "world\n";

clsModel::ValidateTables();
// find extensions
$extensions = FindLocalExtensions();
foreach($extensions as $extension){
    echo "$extension\n";
    echo file_get_contents("http://".$_SERVER['HTTP_HOST'].Settings::LoadSettingsVar('path',"/")."extensions/".$extension."/helpers/validate_models.php");
}
?></pre>