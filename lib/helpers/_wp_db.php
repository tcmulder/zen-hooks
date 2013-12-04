<?php
// find config
// get branch creds
// return creds

function read_wp_file($filename = '.wp-config/zen-config.php', $search_config = 'fail'){
    if(file_exists($filename) && is_file($filename) && is_readable($filename)) {
        $file = @fopen($filename, 'r');
        $file_content = fread($file, filesize($filename));
        @fclose($file);
    }
    preg_match_all('/define\s*?\(\s*?([\'"])(DB_NAME|DB_USER|DB_PASSWORD|DB_HOST|DB_CHARSET|ZEN_SERVER)\1\s*?,\s*?([\'"])([^\3]*?)\3\s*?\)\s*?;/si', $file_content, $defines);
    if((isset($defines[2]) && !empty($defines[2])) &&(isset($defines[4]) && !empty($defines[4]))) {
        foreach($defines[2] as $key => $define) {
            switch($define) {
                case 'ZEN_SERVER':
                    if(strstr($defines[4][$key], $search_config)){
                        $this_name = $defines[4][$key];
                        $key++;
                        $this_user = $defines[4][$key];
                        $key++;
                        $this_pass = $defines[4][$key];
                        $key++;
                        $this_host = $defines[4][$key];
                    }
                    break;
                case 'DB_CHARSET':
                    $this_char = $defines[4][$key];
                    break;
            }
        }
    }
    $wpcontent = array('name' => $this_name, 'user' => $this_user, 'pass' => $this_pass, 'host' => $this_host, 'char' => $this_char);
    return $wpcontent;
}