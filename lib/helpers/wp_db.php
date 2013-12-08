<?php
// grab the file's data
function read_wp_file($this_dir_proj, $db_prefix = 'fail'){
    // set location of the wp-config.php file
    $wp_file = $this_dir_proj . 'wp-config.php';
    // if there's a wp-config.php file then grab it's contents
    if(file_exists($wp_file) && is_file($wp_file) && is_readable($wp_file)) {
        $file = @fopen($wp_file, 'r');
        $file_content = fread($file, filesize($wp_file));
        @fclose($file);
    // if there's no wp-config.php file
    } else {
        // return false to signify as much
        return false;
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
                // start of common db creds
                case 'DB_CHARSET':
                    $this_char = $defines[4][$key];
                    break;
            }
        }
    }
    // create an array of the db creds and return it
    $wpcontent = array('name' => $this_name, 'user' => $this_user, 'pass' => $this_pass, 'host' => $this_host, 'char' => $this_char);
    print_r($wpcontent);
    return $wpcontent;
}