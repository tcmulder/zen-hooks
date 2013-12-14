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

// export (mysqldump) a database
function db_export($db_creds, $db_dir){
    exec('/usr/bin/mysqldump -hlocalhost -ul1_p -p\'passward\' l1_p > ' . $db_dir .'db.sql');
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
        $far .= 'http://'.$server.'1.zenman.com/'.$client.'/'.$proj;
        //execute find and replace
        exec($far);
    }
}