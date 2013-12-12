<?php
// create a database
function db_create($db_creds){
    // connect to mysql
    $link = mysql_connect('localhost', 'admin', 'iUu5xkAt/v8=');
    if($link) {
        // create the database
        $db = mysql_select_db($db_creds['name'], $link);

        // if the database doesn't exist already
        if (!$db) {
            mysql_query('CREATE DATABASE IF NOT EXISTS ' . $db_creds['name'], $link);
            mysql_query('GRANT USAGE ON *.* TO '. $db_creds['user'] . '@localhost IDENTIFIED BY \'' . $db_creds['pass'] . '\'', $link);
            mysql_query('GRANT ALL PRIVILEGES ON ' . $db_creds['name'] . '.* TO ' . $db_creds['user'] . '@localhost', $link);
            mysql_query('FLUSH PRIVILEGES', $link);
        } else {
            mysql_close($link);
            return false;
        }
    }
    mysql_close($link);
}

// // export (mysqldump) a database
// function db_export($db_creds, $db_dir){
//  shell_exec('mysqldump -h' . $db_creds['host'] . ' -u' . $db_creds['user'] . ' -p\'' . $db_creds['pass'] . '\' --no-data ' . $db_creds['name'] . ' | grep ^DROP | mysql -h' . $db_creds['host'] . ' -u' . $db_creds['user'] . ' -p\'' . $db_creds['pass'] . '\' ' . $db_creds['name']);
// }

// import a database
function db_import($db_creds, $db_dir){
    // drop the database's tables
    $drop = shell_exec('mysqldump -h' . $db_creds['host'] . ' -u' . $db_creds['user'] . ' -p\'' . $db_creds['pass'] . '\' --no-data ' . $db_creds['name'] . ' | grep ^DROP | mysql -h' . $db_creds['host'] . ' -u' . $db_creds['user'] . ' -p\'' . $db_creds['pass'] . '\' ' . $db_creds['name']);
    // import the /.db/db.sql file
    $import = shell_exec('mysql -h' . $db_creds['host'] . ' -u' . $db_creds['user'] . ' -p\'' . $db_creds['pass'] . '\' ' . $db_creds['name'] . ' < ' . $db_dir . 'db.sql');
    // signify if everything was successful or not
    if($drop && $import){
        return true;
    } else {
        return false;
    }
}

function db_far($db_creds, $db_to) {
    $db_from = false;

    $link = mysqli_connect("localhost", "l1_p", "passward", "l1_p");
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        return false;
    }
    $query = "SELECT option_value FROM wp_options WHERE option_name='siteurl'";
    if ($result = mysqli_query($link, $query)) {
        $some_obj = $result->fetch_object();
        $db_from = $some_obj->option_value;
        mysqli_free_result($result);
    }
    mysqli_close($link);

    if($db_from){

        // $test = shell_exec('mysql -h' . $db_creds['host'] . ' -u' . $db_creds['user'] . ' -p' . $db_creds['pass'] . ' ' . $db_creds['name'] . ' -e "SHOW TABLES"');

        $php_script = 'php lib/helpers/far.php ';
        $db_connect = $db_creds['name'] . ' ';
        $db_connect .= $db_creds['user'] . ' ';
        $db_connect .= $db_creds['pass'] . ' ';
        $db_connect .= $db_creds['host'] . ' ';
        $db_connect .= $db_creds['char'] . ' ';
        $from_to = $db_from . ' ' . $db_to;
        echo shell_exec($php_script . $db_connect . $from_to);
    }
}