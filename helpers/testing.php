<pre><?php
echo "Hello ";
define("DEBUG","ECHO");
require_once("../includes/main.php");
echo " World\n";
$hub = Servers::GetHub();
print_r($hub);
if(Servers::IsHub()) echo "\nIs Hub?\n";
?></pre>