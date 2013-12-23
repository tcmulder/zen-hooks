<?php
/*/////////////////////////////////////////////////////////////////Set Up Error Logging
Set Up Error Logging */
ini_set("log_errors", 1);
ini_set("error_log", "webhook.log");
error_reporting(E_ALL);
ignore_user_abort(true);
date_default_timezone_set('America/Denver');

$dir_root = '/YOUR_SERVER_ADDRESS/zen_dev2/zenpository/';

try {
/*/////////////////////////////////////////////////////////////////Set Up Status Logging
Set Up Status Logging */
	function log_status($status){
		if(isset($_GET['log'])){
			global $dir_root;
			$file = $dir_root.'webhook.log';
			file_put_contents($file, "$status\n", FILE_APPEND | LOCK_EX);
			$truncated = shell_exec("tail -n 5000 $file");
			file_put_contents($file, $truncated, LOCK_EX);
		} else {
			return false;
		}
	}
	log_status('zenpository start ........................ [ '.date('Y-m-d H:i:s').' ]');
/*/////////////////////////////////////////////////////////////////Initialize Data
Initialize Data */
	$gitlab = json_decode(file_get_contents('php://input')); //data from gitlab
	log_status('gitlab data: '.($gitlab ? 'true' : 'false'));
	$client = $_GET['client'];
	log_status('client: '.$client);
	$proj = $_GET['project'];
	log_status('project: '.$proj);
	$proj_type = $_GET['type'];
	log_status('project type: '.$proj_type);

	$branch_parts = explode('/', $gitlab->ref);
	$branch = array_pop($branch_parts); //the last item is the branch
	log_status('branch: '.$branch);
	$branch_base_parts = explode('_', $branch);

	$server = $branch_base_parts[0];
	log_status('server: '.$server);

	$dir_base = '/YOUR_SERVER_ADDRESS/zen_'.$server.'2/sites/';
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
		include_once 'lib/helpers/db.php';
		// get the wordpress database credentials
		include_once 'lib/helpers/wp_db.php';
		$wp_db_creds = wp_db($branch, $dir_proj);
		log_status('wordpress database credentials: ');
		log_status(print_r($wp_db_creds,1));
	}

/*/////////////////////////////////////////////////////////////////Run All the Commands
Run All the Commands */

	// try to initialize the repo
	include_once 'lib/tasks/init_repo.php';
	// update the branch
	include_once 'lib/tasks/update_repo.php';

	// for wordpress sites
	if($proj_type == 'wp'){
		log_status('is type wordpress');
		// grab all the database helper functions
		include_once 'lib/helpers/db.php';
		// get the wordpress database credentials
		include_once 'lib/helpers/wp_db.php';
		$wp_db_creds = wp_db($branch, $dir_proj);
		log_status('wordpress database credentials: ');
		log_status(print_r($wp_db_creds,1));
		// if the database credentials are established
		if($wp_db_creds){
			log_status('database credentials exist');
			// create a database (returns false if it's already there)
			db_create($wp_db_creds);
			// import a database
			db_import($wp_db_creds, $dir_proj . '.db/', $server, $client, $proj);
			// re-check siteurl (the first one was for the initial database)
			$siteurl = wp_siteurl($wp_db_creds);
		    $wp_db_creds['siteurl'] = $siteurl;
		    log_status('siteurl: '.$wp_db_creds['siteurl']);
			// find and replace a database
			db_far($wp_db_creds, $server, $client, $proj);
		}
	}
	log_status('zenpository end .......................... [ '.date('Y-m-d H:i:s').' ]');
} catch (Exception $e) {
	//output the log
	error_log(sprintf("%s >> %s", date('Y-m-d H:i:s'), $e));
	log_status('zenpository end .......................... [ '.date('Y-m-d H:i:s').' ]');
}