<?php
Debug::Log("hello world");
if(isset(Debug::$debug[$test_name]))
    $tests['running'][$test_name] = "Pass";
else
    Debug::Log(isset(Debug::$debug[$test_name]), count(Debug::$debug[$test_name]))
?>