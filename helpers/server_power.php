<pre><?php
require_once("../includes/main.php");
$check_for_update = LoadSettingVar("check_for_update");
if(!is_null($check_for_update) && (int)$check_for_update == 1){
    UpdateFromGit();
}
echo "hello?\n";
// testing... should probably remove if it all works
UpdateFromGit();

?></pre>