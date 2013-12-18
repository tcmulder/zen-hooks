<?php


// set up the db prefix
$db_prefix = substr($branch, 0, 1) . '2_';
// set location of the wp-config.php file
$wp_file = $dir_proj . 'wp-config.php';
// if there's a wp-config.php file then grab it's contents
if(file_exists($wp_file) && is_file($wp_file) && is_readable($wp_file)) {
    $file = @fopen($wp_file, 'r');
    $file_content = fread($file, filesize($wp_file));
    @fclose($file);
// if there's no wp-config.php file
} else {
    // exit this script
    exit;
}
//  match the db credentials
preg_match_all('/define\s*?\(\s*?([\'"])(DB_NAME|DB_USER|DB_PASSWORD|DB_HOST|DB_CHARSET)\1\s*?,\s*?([\'"])([^\3]*?)\3\s*?\)\s*?;/si', $file_content, $defines);
// make sure everything got grabbed
if((isset($defines[2]) && ! empty($defines[2])) && (isset($defines[4]) && ! empty($defines[4]))) {
    // for each matched set of elements
    foreach($defines[2] as $key => $define) {
        switch($define) {
            // start grabbing db creds
            case 'DB_NAME':
                if(strstr($defines[4][$key], $db_prefix)){
                    $this_name = $defines[4][$key];
                    $key++;
                    $this_user = $defines[4][$key];
                    $key++;
                    $this_pass = $defines[4][$key];
                    $key++;
                    $this_host = $defines[4][$key];
                }
                break;
            // when we reach the end of what we're interested in
            case 'DB_CHARSET':
                $this_char = $defines[4][$key];
                break;
        }
    }
} else {
    exit;
}
// create an array of the db constants
$wp_db_creds = array('name' => $this_name, 'user' => $this_user, 'pass' => $this_pass, 'host' => $this_host, 'char' => $this_char);
// add the db prefix to the array
preg_match_all('/\$table_prefix  = \'(.+)\'/', $file_content, $db_prefix);
$wp_db_creds['prefix'] = $db_prefix[1][0];
// set siteurl
$siteurl = wp_siteurl($wp_db_creds);
if($siteurl){
    $wp_db_creds['siteurl'] = $siteurl;
}