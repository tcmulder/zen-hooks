<?php
// create a database
function db_create($db_creds){
/*temp*/shell_exec('echo " [ db create running ] " >> webhook.log');
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
/*temp*/shell_exec('echo " [ db create ran ] " >> webhook.log');
}

// export (mysqldump) a database
function db_export($db_creds, $db_dir){
/*temp*/shell_exec('echo " [ db export running ] " >> webhook.log');
    // if the /.db/ directory doesn't exist
    if(!file_exists($db_dir)){
        // create the directory
        mkdir($db_dir);
    }
    // dump the database
    shell_exec('/usr/bin/mysqldump -h' . $db_creds['host'] . ' -u' . $db_creds['user'] . ' -p\'' . $db_creds['pass'] . '\' ' . $db_creds['name'] . ' > ' . $db_dir .'db.sql');
/*temp*/shell_exec('echo " [ db export ran ] " >> webhook.log');
}

// import a database
function db_import($db_creds, $db_dir){
    // drop the database's tables
    exec('mysqldump -h' . $db_creds['host'] . ' -u' . $db_creds['user'] . ' -p\'' . $db_creds['pass'] . '\' --no-data ' . $db_creds['name'] . ' | grep ^DROP | mysql -h' . $db_creds['host'] . ' -u' . $db_creds['user'] . ' -p\'' . $db_creds['pass'] . '\' ' . $db_creds['name']);
    // import the /.db/db.sql file
    exec('mysql -h' . $db_creds['host'] . ' -u' . $db_creds['user'] . ' -p\'' . $db_creds['pass'] . '\' ' . $db_creds['name'] . ' < ' . $db_dir . 'db.sql');
}

// find and replace in a database
function db_far($db_creds, $server, $client, $proj) {
/*temp*/shell_exec('echo " [ db far running ] " >> webhook.log');
    // if we have enough info
    if(count($db_creds) == 7 && $server && $client && $proj){
        // create find and replace command
        $far = 'php lib/helpers/far.php ';
        $far .= $db_creds['name'] . ' ';
        $far .= $db_creds['user'] . ' ';
        $far .= $db_creds['pass'] . ' ';
        $far .= $db_creds['host'] . ' ';
        $far .= $db_creds['char'] . ' ';
        $far .= $db_creds['siteurl'] . ' ';
        $far .= 'http://'.$server.'2.zenman.com/sites/'.$client.'/'.$proj;
        //execute find and replace
        exec($far);
/*temp*/shell_exec('echo " [ db far ran ] " >> webhook.log');
    }
}

// get and return the siteurl
function wp_siteurl($db_creds){
/*temp*/shell_exec('echo " [ wp_siteurl running ] " >> webhook.log');

    // aside: it seems ludicrous to me that it's impossible to test a database connection
    // without throwing a php error, but I have to do it this way currently or it just fails

    // connect as the admin mysql user
    $link = mysql_connect('localhost', 'admin', 'iUu5xkAt/v8=');
    // if the connection succeeded
    if($link) {
        // see if the database exists
        $db = mysql_select_db($db_creds['name'], $link);
        // if the database exists
        if($db) {
            // close the connection as the administrator
            mysql_close($link);
            // reopen a connection with the database credentials
            $mysqli = @new mysqli($db_creds['host'], $db_creds['user'], $db_creds['pass'], $db_creds['name']);
            // check the siteurl and return it
            $siteurl = $mysqli->query("SELECT option_value FROM wp_options WHERE option_name = 'siteurl'")->fetch_object()->option_value;
            return $siteurl;
        }
    }
    mysql_close($link);
}