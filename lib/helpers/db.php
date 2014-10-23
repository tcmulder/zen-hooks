<?php
// create a database
function db_create($db_creds){
	log_status('db_create: called');
	log_status('db_create: database credentials received');
	log_status('db_create: '.print_r($db_creds,1));
    // connect to mysql
    $link = mysql_connect('localhost', 'admin', 'iUu5xkAt/v8=');
    if($link) {
    	log_status('db_create: connected to mysql as root');
        // create the database
        $db = mysql_select_db($db_creds['name'], $link);
        // if the database doesn't exist already
        if (!$db) {
        	log_status('db_create: database '.$db_creds['name'].' does not exist');
            mysql_query('CREATE DATABASE IF NOT EXISTS '.$db_creds['name'], $link);
            log_status('db_create: ran create database '.$db_creds['name']);
            mysql_query('GRANT USAGE ON *.* TO '. $db_creds['user'].'@localhost IDENTIFIED BY \''.$db_creds['pass'].'\'', $link);
            mysql_query('GRANT ALL PRIVILEGES ON '.$db_creds['name'].'.* TO '.$db_creds['user'].'@localhost', $link);
            log_status('db_create: created user '.$db_creds['user'].' with privaleges for '.$db_creds['name']);
            mysql_query('FLUSH PRIVILEGES', $link);
            log_status('db_create: privileges flushed');
        } else {
        	log_status('db_create: database already exists');
            mysql_close($link);
            return false;
        }
    }
    mysql_close($link);
}

// export (mysqldump) a database
function db_export($db_creds, $db_dir){
	log_status('db_export: called');
	log_status('db_export: database credentials received');
	log_status('db_export: '.print_r($db_creds,1));
	log_status('db_export: database directory is '.$db_dir);
    // if the /.db/ directory doesn't exist
    if(!file_exists($db_dir)){
    	log_status('db_export: create /.db/ directory');
        // create the directory
        mkdir($db_dir);
    } else {
    	log_status('db_export: /.db/ directory exists');
    }
    // dump the database
    log_status('db_export: export /.db/db.sql');
    shell_exec('/usr/bin/mysqldump -h'.$db_creds['host'].' -u'.$db_creds['user'].' -p\''.$db_creds['pass'].'\' '.$db_creds['name'].' > '.$db_dir .'db.sql');
}

// import a database
function db_import($db_creds, $db_dir){
	log_status('db_import: called');
	log_status('db_import: database credentials received');
	log_status('db_export: the credentials are '.print_r($db_creds,1));
	log_status('db_import: database directory is '.$db_dir);
	// variable to store sql dump
	$db_dump = $db_dir.'db.sql';
	// if there is a /.db/db.sql file
    if(file_exists($db_dump)){
		log_status('db_import: file exists '.$db_dump);
	    // drop the database's tables
	    log_status('db_import: drop databases tables');
	    exec('mysqldump -h'.$db_creds['host'].' -u'.$db_creds['user'].' -p\''.$db_creds['pass'].'\' --no-data '.$db_creds['name'].' | grep ^DROP | mysql -h'.$db_creds['host'].' -u'.$db_creds['user'].' -p\''.$db_creds['pass'].'\' '.$db_creds['name']);
	    // import the /.db/db.sql file
	    log_status('db_import: import file '.$db_dump);
	    exec('mysql -h'.$db_creds['host'].' -u'.$db_creds['user'].' -p\''.$db_creds['pass'].'\' '.$db_creds['name'].' < '.$db_dump);
        return true;
	// if there is no /.db/db.sql
	} else {
		// report import as failed
		log_status('db_import: file does not exist '.$db_dump);
		return false;
	}
}

// find and replace in a database
function db_far($db_creds, $server, $server_version, $client, $proj) {
	log_status('db_far: called');
	log_status('db_far: database credentials received');
	log_status('db_far: '.print_r($db_creds,1));
	log_status('db_far: server is '.$server);
	log_status('db_far: client is '.$client);
	log_status('db_far: project is '.$proj);
    // if we have enough info
    if(count($db_creds) == 7 && $server && $client && $proj){
    	log_status('db_far: run far');
        // create find and replace command
        $far = 'php lib/helpers/far.php ';
        $far .= '\''.$db_creds['name'].'\' ';
        $far .= '\''.$db_creds['user'].'\' ';
        $far .= '\''.$db_creds['pass'].'\' ';
        $far .= '\''.$db_creds['host'].'\' ';
        $far .= '\''.$db_creds['char'].'\' ';
        $far .= '\''.$db_creds['siteurl'].'\' ';
        $far .= '\'http://'.$server.$server_version.'.zenman.com/sites/'.$client.'/'.$proj.'\'';
        //execute find and replace
        $output = shell_exec($far);
        log_status('db_far: ran with output ');
        log_status($output);
    // if we do not have all the info
    } else {
		if(count($db_creds) != 7){
			log_status('db_far: 7 perimeters not received');
		}
		if(!$server){
			log_status('db_far: server not set');
		}
		if(!$client){
			log_status('db_far: client not set');
		}
		if(!$proj){
			log_status('db_far: project not set');
		}
    	return false;
    }
}

// get and return the siteurl
function wp_siteurl($db_creds){
	log_status('wp_siteurl: called');
	log_status('wp_siteurl: database credentials received');
	log_status('wp_siteurl: '.print_r($db_creds,1));

    // aside: it seems ludicrous to me that it'd be impossible to test a database connection NOT as the
    // root user without throwing a php error, but I have to do it this way currently or it just fails

    // connect as the admin mysql user
    $link = mysql_connect('localhost', 'admin', 'iUu5xkAt/v8=');
    // if the connection succeeded
    if($link) {
    	log_status('wp_siteurl: connected to mysql as root user');
        // see if the database exists
        $db = mysql_select_db($db_creds['name'], $link);
        // if the database exists
        if($db) {
        	log_status('wp_siteurl: database '.$db_creds['name'].' found');
            // close the connection as the administrator
            mysql_close($link);
            // reopen a connection with the database credentials
            $mysqli = @new mysqli($db_creds['host'], $db_creds['user'], $db_creds['pass'], $db_creds['name']);
            log_status('wp_siteurl: connected to '.$db_creds['host'].' '.$db_creds['user'].' '.$db_creds['pass'].' '.$db_creds['name'].' with prefix '.$db_creds['prefix']);
            // check the siteurl and return it
            $siteurl = $mysqli->query('SELECT option_value FROM '.$db_creds['prefix'].'options WHERE option_name = "siteurl"');
            if($siteurl){
                $siteurl_val = $siteurl->fetch_object()->option_value;
                if($siteurl_val){
                    log_status('wp_siteurl: siteurl is "'.$siteurl_val.'"');
                    return $siteurl_val;
                } else {
                    log_status('wp_siteurl: siteurl value undetermined');
                    return false;
                }
            } else {
                log_status('wp_siteurl: database query for siteurl unsuccessful');
                return false;
            }
        }
    // if the connection failed
    } else {
    	mysql_close($link);
    	log_status('wp_siteurl: connection failed as root user');
    	return false;
    }
    mysql_close($link);
}