<?php
/*/////////////////////////////////////////////////////////////////Set Up Error Logging
Set Up Error Logging */
ini_set("log_errors", 1);
ini_set("error_log", "webhook.log");
error_reporting(E_ALL);
ignore_user_abort(true);
date_default_timezone_set('America/Denver');
/*temp*/shell_exec('echo "index    ****    ****    ****    ****    ****    ****    ****    ['.date('Y-m-d H:i:s').']    " >> webhook.log');

/*/////////////////////////////////////////////////////////////////Initialize Data
Initialize Data */
try {
	$gitlab = json_decode(file_get_contents('php://input')); //data from gitlab
	$client = $_GET['client'];
	$proj = $_GET['project'];
	$proj_type = $_GET['type'];

	$branch_parts = explode('/', $gitlab->ref);
	$branch = array_pop($branch_parts); //the last item is the branch
	$branch_base_parts = explode('_', $branch);

	// exit if the branch isn't found
	if(!isset($branch)){
		throw new Exception("Branch [$branch] not set");
	}

	$server = $branch_base_parts[0];

	$dir_base = '/YOUR_SERVER_ADDRESS/zen_'.$server.'2/sites/';

	// exit if the server (based on branch prefix) doesn't exist
	if(!file_exists($dir_base)){
		throw new Exception("Server [$dir_base] does not exist");
	}

	$dir_root = '/YOUR_SERVER_ADDRESS/zen_dev2/zenpository/';
	$dir_client = $dir_base . $client . '/';
	$dir_proj = $dir_client . $proj . '/';

	$repo = $gitlab->repository->url;

	// for wordpress sites
	if($proj_type == 'wp'){
/*temp*/shell_exec('echo " [ start first wp ] " >> webhook.log');
		// get all the database helper functions
		include_once 'lib/helpers/db.php';
		// get the wordpress database credentials
		include_once 'lib/helpers/wp_db.php';
		$wp_db_creds = wp_db($branch, $dir_proj);
	}

/*/////////////////////////////////////////////////////////////////Run All the Commands
Run All the Commands */

	// try to initialize the repo
	include_once 'lib/tasks/init_repo.php';
	// update the branch
	include_once 'lib/tasks/update_repo.php';

	// for wordpress sites
	if($proj_type == 'wp'){
/*temp*/shell_exec('echo " [ start second wp ] " >> webhook.log');
		// grab all the database helper functions
		include_once 'lib/helpers/db.php';
		// get the wordpress database credentials
		include_once 'lib/helpers/wp_db.php';
		$wp_db_creds = wp_db($branch, $dir_proj);
		// if the database credentials are established
		if($wp_db_creds){
/*temp*/shell_exec('echo " [ wp_db_creds exist ] " >> webhook.log');
			// create a database (returns false if it's already there)
			db_create($wp_db_creds);
			// import a database
			db_import($wp_db_creds, $dir_proj . '.db/', $server, $client, $proj);
			// re-check siteurl (the first one was for the initial database)
			$siteurl = wp_siteurl($wp_db_creds);
		    $wp_db_creds['siteurl'] = $siteurl;
			// find and replace a database
			db_far($wp_db_creds, $server, $client, $proj);
		}
	}
/*temp*/shell_exec('echo "index    . . .    . . .    . . .    . . ." >> webhook.log');
} catch (Exception $e) {
	//output the log
	error_log(sprintf("%s >> %s", date('Y-m-d H:i:s'), $e));
}