<?php

function say($str){
    echo $str."\n";
}

$server = 'stage';
$dir_root = dirname(__FILE__) . '/';
$dir_version = substr(dirname($dir_root), -1, 1);
$dir_base = dirname(dirname($dir_root)) . '/zen_' . $server . $dir_version . '/sites/';

say($server);
say($dir_root);
say($dir_version);
say($dir_base);



//dir_root     /YOUR_SERVER_ADDRESS/zen_dev2/zen-hooks/
//dir_web_root /YOUR_SERVER_ADDRESS/zen_dev2
//dir_version  2


?>
