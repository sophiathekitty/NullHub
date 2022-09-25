<pre><?php
require_once("../includes/main.php");
$hub = Servers::GetHub();
print_r($hub);
if(Servers::IsHub()) echo "\nIs Hub?\n";
?></pre>