<?php
// recieve, init vars, call scripts
// if !init_repo
// 	update_repo
// if type == wp
// 	wp_db
// 	if !init_db
// 		update_db

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
// $data = file_get_contents('data.json', true); //fake data
// $gitlab = json_decode($data); //fake data
	$gitlab = json_decode(file_get_contents('php://input')); //data from gitlab
	$client = $_GET['client'];
	$proj = $_GET['project'];
	$proj_type = $_GET['type'];

	$branch_parts = explode('/', $gitlab->ref);
	$branch = array_pop($branch_parts); //the last item is the branch
// /*FAKE*/$branch = 'dev_db';//fake
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
	include_once 'lib/helpers/wp_db.php';
	// set up the db prefix
	$db_prefix = substr($branch, 0, 1) . '2_';
	// try to get the db creds
	$wp_db_creds = read_wp_file($dir_proj, $db_prefix);
	// if there's a functioning wp-config.php file
	if($wp_db_creds){
		// grab all the database helper functions
		include_once 'lib/helpers/db.php';
		// create a database (returns false if it's already there)
		db_create($wp_db_creds);
		// import a database
		db_import($wp_db_creds, $dir_proj . '.db/');
	}

// if we've made it all the way through with no errors thrown
/*TEMP*/echo "<br>No Errors"; //fake

} catch (Exception $e) {
	//output the log
	error_log(sprintf("%s >> %s", date('Y-m-d H:i:s'), $e));
}































//**** **** **** **** **** **** **** **** ****
// $the_log = file_get_contents("webhook.log");
// echo "<div style='width:100%;font-size:.5em;font-family:monospace;'>";
// if(isset($the_log)){
// 	print_r($the_log);
// 	return true;
// }
// echo "</div>";
//**** **** **** **** **** **** **** **** ****




















//shell_exec('git clone git@git.zenman.com:tcmulder/p.git');
// shell_exec('bash test');
// echo '<br>end update ' . date('i:s');

// $str = print_r($gitlab, true);

// chdir('my_repo');
// echo 'test: ' . shell_exec("git pull origin master");
// shell_exec("cd xen_dev2/my_repo && git fetch");
// shell_exec("cd xen_dev2/my_repo && git pull master");

// //run shell commands
// function syscall ($cmd, $cwd) {

// 	$descriptorspec = array(1 => array('pipe', 'w')); // stdout is a pipe that the child will write to
// 	$resource = proc_open($cmd, $descriptorspec, $pipes, $cwd);

// 	if (is_resource($resource)) {
// 		$output = stream_get_contents($pipes[1]);
// 		fclose($pipes[1]);
// 		proc_close($resource);

// 		return $output;
// 	}

// }

// syscall('git clone git@git.zenman.com:tcmulder/p.git','.');

// require_once 'lib/tasks/init_repo.php';

// <?php
// 	echo date('i:s');


// 	// Get Data
// 	$data = file_get_contents('./data.json', true);
// 	$client = $_GET['client'];
// 	$proj = $_GET['project'];
// 	$proj_type = $_GET['type'];

// 	// Parse Data
// 	// $gitlab = json_decode($data);
// 	// for actual data:
// 	$gitlab = json_encode(file_get_contents('php://input'));

// 	$branch_parts = explode('/', $gitlab->ref);
// 	$branch = array_pop($branch_parts);
// 	$branch_base_parts = explode('_', $branch);

// 	$server = $branch_base_parts[0];

// 	$dir_base = '/YOUR_SERVER_ADDRESS/zen_dev2/zenpository/xen_'.$server.'1/';
// 	$dir_client = $dir_base . $client . '/';
// 	$dir_proj = $dir_client . $proj . '/';

// 	// for actual repo:
// 	$repo = $gitlab->repository->url;

// 	// $repo = '/YOUR_SERVER_ADDRESS/zen_dev2/zenpository/gitlab/my_repo/';

// 	// Commands
// 	require_once 'lib/tasks/init_repo.php';

////////////////////////////////////////////////
// Output
////////////////////////////////////////////////
	// echo date('i:s');
	// echo '.<pre>';
	// print_r($dir_proj);
	// echo '</pre>.';

	//CHANGE FOR LIVE SERVER
	// $git_clone = '/usr/local/git/bin/git clone --mirror ' . $gitlab->{'repository'}->{'url'} . '; git fetch origin/master';
	// $git_fetch = '/usr/local/git/bin/git fetch origin';
	// $git_reset = '/usr/local/git/bin/git reset --hard origin/master';

	// if(!file_exists($client_dir)){
	// 	mkdir($client_dir);
	// }
	// if(!file_exists($project_dir)){
	// 	chdir($client_dir);
	// 	shell_exec($git_clone);
	// } else {
	// 	// shell_exec('')
	// }


	// shell_exec('/usr/local/git/bin/git clone git@git.zenman.com:tcmulder/test-project-to-delete.git');


//////////////////////////////////////////////////////////// FROM INIT_REPO

// try{
// 	// ensure we're working from a base directory
// 	if(file_exists($dir_base)){
// 		// if the project directory doesn't exist
// 		if(!file_exists($dir_proj)){
// 			// if the client directory doesn't exist
// 			if(!file_exists($dir_client)){
// 				// create the client directory
// 				mkdir($dir_client);
// 			}
// 			// change into the client directory
// 			chdir($dir_client);
// 			// clone in the repo
// 			shell_exec("git clone $repo");
// 		} else {
// 			// if the project directory already exists
// 			return true;
// 		}
// 	} else {
// 		throw new Exception("Base directory '$dir_base' does not exist");
// 	}
// } catch (Exception $e) {
// 	error_log(sprintf("%s >> %s", date('Y-m-d H:i:s'), $e));
// }

// shell_exec('git clone git@git.zenman.com:tcmulder/p.git');

// if($branch && $branch != "heads"){
// 	if(is_dir("$dir_base/$branch")){   //lets check if branch dir exists
// 		//hey look, the branch directory already exists, so lets use it as our working directory and just run the pull command -- obviously we want to pull from the remote origin &amp; branch name
// 		$result = syscall("git pull origin $branch", "$dir_base/$branch");
// 	} else {
// 		//the repos name
// 		$repo = $gitlab->repository->url;
// 		//if branch dir doesn't exist, create it with a clone
// 		$result = syscall("git clone $repo $branch", $dir_base);
// 		//change dir to the clone directory, and checkout the branch
// 		$result = syscall("git checkout $branch", "$dir_base/$branch");
// 		throw new Exception("git clone $repo $branch in $dir_base");
// 	}
// } else {
	//throw new Exception("branch variable is not set or == to 'heads'");
	//throw new Exception("repo is $repo");
// }
// 	// ensure we're working from a base directory
// 	if(file_exists($dir_base)){
// 		// if the project directory doesn't exist
// 		if(!file_exists($dir_proj)){
// 			// if the client directory doesn't exist
// 			if(!file_exists($dir_client)){
// 				// create the client directory
// 				mkdir($dir_client);
// 			}
// 			// change into the client directory
// 			chdir($dir_client);
// 			// clone in the repo
// 			$cmd_clone = "git clone git@git.zenman.com:tcmulder\/test-project-to-delete.git";
// 			$testing = shell_exec($cmd_clone);
// 		}
// 	}


// ////////////////////////////////////////////////
// // Output
// ////////////////////////////////////////////////
// 	echo '<pre>';
// 	print_r($repo);
// 	echo '</pre>';
// 	// require 'lib/tasks/init_repo.php';




// 	// shell_exec('/usr/local/git/bin/git clone git@git.zenman.com:tcmulder/test-project-to-delete.git');
