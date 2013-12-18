 <?php
/*/////////////////////////////////////////////////////////////////Set Up Error Logging
Set Up Error Logging */
ini_set("log_errors", 1);
ini_set("error_log", "webhook.log");
error_reporting(E_ALL);
ignore_user_abort(true);
date_default_timezone_set('America/Denver');
/*temp*/shell_exec('echo "****    ****    ****    ****    ****    ****    ****    ****    " > webhook.log');

/*/////////////////////////////////////////////////////////////////Initialize Data
Initialize Data */
try {
///*temp*/$data = file_get_contents('data.json', true); //temp data
///*temp*/$gitlab = json_decode($data); //temp data
	$gitlab = json_decode(file_get_contents('php://input')); //data from gitlab
	$client = $_GET['client'];
	$proj = $_GET['project'];
	$proj_type = $_GET['type'];

	$branch_parts = explode('/', $gitlab->ref);
	$branch = array_pop($branch_parts); //the last item is the branch
///*temp*/$branch = 'dev_db';//temp
	$branch_base_parts = explode('_', $branch);

	if(!isset($branch)){
		throw new Exception("Branch [$branch] not set");
	}

	$server = $branch_base_parts[0];

	$dir_root = '/YOUR_SERVER_ADDRESS/zen_dev2/zenpository/';
///*TEMP*$dir_base = $dir_root . 'xen_'.$server.'2/'; //psudo live
	$dir_base = '/YOUR_SERVER_ADDRESS/zen_'.$server.'2/sites/'; //psudo live
	$dir_client = $dir_base . $client . '/';
	$dir_proj = $dir_client . $proj . '/';

	$repo = $gitlab->repository->url;

	// for wordpress sites
	if($proj_type == 'wp'){
		include_once 'lib/helpers/db.php';
		include_once 'lib/helpers/wp_db.php';
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
			db_import($wp_db_creds, $dir_proj . '.db/', $server, $client, $proj);
// /*TEMP*/$wp_db_creds = array('name' => 'l1_p', 'user' => 'l1_p', 'pass' => 'passward', 'host' => 'localhost', 'char' => 'utf8');
			// re-check siteurl (the first one was for the initial database)
			$siteurl = wp_siteurl($wp_db_creds);
		    $wp_db_creds['siteurl'] = $siteurl;
			// find and replace a database
// /*temp*/shell_exec('echo " [just before far'.print_r($wp_db_creds,1).'] " > webhook.log');
			db_far($wp_db_creds, $server, $client, $proj);
		}
	}

///*TEMP*/echo "<br>No Errors"; //if we've made it all the way through with no errors thrown

} catch (Exception $e) {
	//output the log
	error_log(sprintf("%s >> %s", date('Y-m-d H:i:s'), $e));
}