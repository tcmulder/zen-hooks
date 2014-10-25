<?php

/*/////////////////////////////////////////////////////////////////Tell gitlab Everything's OK
Tell gitlab Everything's OK */
header( "HTTP/1.1 200 OK" );

/*/////////////////////////////////////////////////////////////////Set Up Error Logging
Set Up Error Logging */
ini_set("log_errors", 1);
ini_set("error_log", "webhook.log");
error_reporting(E_ALL);
ignore_user_abort(true);
date_default_timezone_set('America/Denver');

$dir_root = dirname(__FILE__) . '/';

try {

/*/////////////////////////////////////////////////////////////////Set Up Status Logging
Set Up Status Logging */

	function log_status($status){
	    if(isset($_GET['log'])){
	        global $dir_root;
	        $file = $dir_root.'webhook.log';

	        //extra debug info
	        $extra_debug = '';
	        if($_GET['log'] == 'debug'){
	        	$bt = debug_backtrace();
          		$caller = array_shift($bt);
          		$debug_line = str_pad($caller['line'],5);
          		$debug_file = str_pad(array_pop(explode('/', $caller['file'])),17);
          		$extra_debug = "$debug_line $debug_file " ;
	        }

	        file_put_contents($file, $extra_debug."$status\n", FILE_APPEND | LOCK_EX);
	        if($lines = count(file($file)) >= 100000){
	            $truncated = shell_exec("tail -n 1000 $file");
	            file_put_contents($file, $truncated, LOCK_EX);
	        }
	    } else {
	        return false;
	    }
	}
	log_status("\n\n\n\nzen-hooks start :::::::::::::::::::::::: [ ".date("Y-m-d H:i:s")." ]");

/*/////////////////////////////////////////////////////////////////Initialize Data
Initialize Data */

	$gitlab = json_decode(file_get_contents('php://input')); //data from gitlab
	$ip_addy = $_SERVER['REMOTE_ADDR'];
	log_status('gitlab data: '.($gitlab ? 'true' : 'false'));
	log_status('gitlab json: '.print_r($gitlab,1));
	// no need to continue if no data received or it's from an unauthorized source
	if($gitlab && ($ip_addy == 'YOUR_IP_ADDRESS')){

		$sha_before = $gitlab->before;
		log_status('sha before: '.$gitlab->before);

		$sha_after = $gitlab->after;
		log_status('sha after : '.$gitlab->after);

		$sha_cur = null;

		$client = (isset($_GET['client']) ? $_GET['client'] : false);
		if($client){
			log_status('client: '.$client);
		} else {
			throw new Exception('$_GET[\'client\'] does not exist');
		}
		$proj = (isset($_GET['project']) ? $_GET['project'] : false);
		if($proj){
			log_status('project: '.$proj);
		} else {
			throw new Exception('$_GET[\'project\'] does not exist');
		}
		$proj_type = (isset($_GET['type']) ? $_GET['type'] : false);
		if($proj_type){
			log_status('project type: '.$proj_type);
		} else {
			log_status('no project type defined');
		}

		$branch_parts = explode('/', $gitlab->ref);
		$branch = array_pop($branch_parts); //the last item is the branch
		log_status('branch: '.$branch);
		$branch_base_parts = explode('_', $branch);

		$server = $branch_base_parts[0];
		log_status('server: '.$server);

		$server_version = substr(dirname($dir_root), -1, 1);
		log_status('directory version: '.$server_version);

		$dir_base = dirname(dirname($dir_root)) . '/zen_' . $server . $server_version . '/sites/';
		log_status('directory base: '.$dir_base);

		// exit if the server (based on branch prefix) doesn't exist
		if(!file_exists($dir_base)){
			throw new Exception("Server [$dir_base] does not exist");
		}

		$dir_client = $dir_base . $client . '/';
		log_status('client directory: '.$dir_client);
		$dir_proj = $dir_client . $proj . '/';
		log_status('project directory: '.$dir_proj);

		$repo = $gitlab->repository->url;
		log_status('repo: '.$repo);

		// for wordpress sites
		if($proj_type == 'wp'){
			log_status('is type wordpress');
			// get all the database helper functions
			include_once 'lib/functions/db.php';
			// get the wordpress database credentials
			include_once 'lib/functions/wp_db.php';
			$wp_db_creds = wp_db($branch, $dir_proj, $server_version);
		}

/*/////////////////////////////////////////////////////////////////Run All the Commands
Run All the Commands */

		// try to initialize the repo
		include_once 'lib/includes/init_repo.php';
		// update the branch
		include_once 'lib/includes/update_repo.php';

		// for wordpress sites
		if($proj_type == 'wp'){
			log_status('is type wordpress');
			// grab all the database helper functions
			include_once 'lib/functions/db.php';
			// get the wordpress database credentials
			include_once 'lib/functions/wp_db.php';
			$wp_db_creds = wp_db($branch, $dir_proj, $server_version);
			// if the database credentials are established
			if($wp_db_creds){
				log_status('database credentials exist');
				// create a database (returns false if it's already there)
				db_create($wp_db_creds);
				// if the database import reports success
				if(db_import($wp_db_creds, $dir_proj . '.db/', $server, $client, $proj)){
					// re-check siteurl (the first one was for the initial database)
					$siteurl = wp_siteurl($wp_db_creds);
				    $wp_db_creds['siteurl'] = $siteurl;
				    log_status('siteurl: '.$wp_db_creds['siteurl']);
					// find and replace a database
					db_far($wp_db_creds, $server, $server_version, $client, $proj);
				}
			}
		}
		log_status("zen-hooks end :::::::::::::::::::::::::: [ ".date("Y-m-d H:i:s")." ]");
	// if data isn't right
	} else {
		// if no data was received from gitlab
		if($ip_addy != 'YOUR_IP_ADDRESS'){
			// show warning visibly just in case someone is visiting the url
			echo 'Permission denied to access zen-hooks';
			throw new Exception("Unauthorized access attempted from $ip_addy");
		// if the remote address is not the git server
		} elseif(!$gitlab) {
			throw new Exception('No data received from gitlab');
		}
	}
} catch (Exception $e) {
	//output the log
	error_log(sprintf("%s >> %s", date('Y-m-d H:i:s'), $e));
	log_status("\n\nzen-hooks end :::::::::::::::::::::::::: [ ".date("Y-m-d H:i:s")." ]");
}