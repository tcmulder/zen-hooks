<?php
/*/////////////////////////////////////////////////////////////////Set Up Error Logging
Set Up Error Logging */
ini_set("log_errors", 1);
ini_set("error_log", "webhook.log");
error_reporting(E_ALL);
ignore_user_abort(true);
date_default_timezone_set('America/Denver');

/*/////////////////////////////////////////////////////////////////Initialize Data
Initialize Data */
try {
/*temp*/$data = file_get_contents('data.json', true); //temp data
/*temp*/$gitlab = json_decode($data); //temp data
	// $gitlab = json_decode(file_get_contents('php://input')); //data from gitlab
	$client = $_GET['client'];
	$proj = $_GET['project'];
	$proj_type = $_GET['type'];

	$branch_parts = explode('/', $gitlab->ref);
	// $branch = array_pop($branch_parts); //the last item is the branch
/*temp*/$branch = 'dev_db';//temp
	$branch_base_parts = explode('_', $branch);

	if(!isset($branch)){
		throw new Exception("Branch [$branch] not set");
	}

	$server = $branch_base_parts[0];

	$dir_root = '/YOUR_SERVER_ADDRESS/zen_dev2/zenpository/';
	$dir_base = $dir_root . 'xen_'.$server.'2/'; //psudo live
	$dir_client = $dir_base . $client . '/';
	$dir_proj = $dir_client . $proj . '/';

	$repo = $gitlab->repository->url;

	// for wordpress sites
	$wp_db_creds = array();
	if($proj_type == 'wp'){
		include_once 'lib/helpers/wp_db.php';
		// set up the db prefix
		$db_prefix = substr($branch, 0, 1) . '2_';
		// try to get the db creds
/*TEMP*/$dir_proj = '/Applications/MAMP/htdocs/sites/zenpository/xen_dev2/c/p/';
/*TEMP*/$db_prefix= 'l1_';
		$wp_db_creds = read_wp_file($dir_proj, $db_prefix);
	}


/*/////////////////////////////////////////////////////////////////Run All the Commands
Run All the Commands */

	// try to initialize the repo
	$included = include_once 'lib/tasks/init_repo.php';
	// if the repo's already initialized
	if($included){
		// update the repo
		$included = include_once 'lib/tasks/update_repo.php';
	}

	// for wordpress sites
	if($proj_type == 'wp'){
		// if the database credentials are established
		if($wp_db_creds){
			// grab all the database helper functions
			include_once 'lib/helpers/db.php';
			// create a database (returns false if it's already there)
			db_create($wp_db_creds);
			// import a database
			db_import($wp_db_creds, $dir_proj . '.db/');
			// find and replace a database
// /*TEMP*/$wp_db_creds = array('name' => 'l1_p', 'user' => 'l1_p', 'pass' => 'passward', 'host' => 'localhost', 'char' => 'utf8');
			db_far($wp_db_creds, $server, $client, $proj);
		}
	}

// if we've made it all the way through with no errors thrown
/*TEMP*/echo "<br>No Errors"; //fake

} catch (Exception $e) {
	//output the log
	error_log(sprintf("%s >> %s", date('Y-m-d H:i:s'), $e));
}