<?php
log_status("\n\n:: init_repo included");
// ensure we're working from a base directory
if(file_exists($dir_base)){
	log_status('base directory is '.$dir_base);
	// if the client directory doesn't exist
	if(!file_exists($dir_client)){
		log_status('client directory does not exists');
		// create the client directory
		log_status('create client directory '.$dir_client);
		mkdir($dir_client);
		log_status('create archive directory');
		mkdir($dir_client.'archive');
		log_status('copy client_template/index.php');
		copy($dir_base.'client_template/index.php',$dir_client.'index.php');
		// report request to create client directory
		log_status('new client directory creation requested');
	} else {
		log_status('client directory not created');
	}
	// if the project directory doesn't exist
	if(!file_exists($dir_proj)){
		log_status('project directory does not exists');
		// create the proj directory
		log_status('create project directory '.$dir_proj);
		mkdir($dir_proj);
		log_status('new project directory creation requested');
	} else {
		log_status('project directory not created');
	}
	// if the project isn't a git repo
	if(!file_exists($dir_proj . '.git')){
		log_status('not a git repository but project directory is present');
		// change into the project directory
		chdir($dir_proj);
		// set up git
		echo exec('git init');
		// establish credentials
		echo exec('git config user.email "dev@zenman.com"');
		echo exec('git config user.name "YOUR_USERNAME"');
		// set up remote
		echo exec("git remote add gitlab $repo");
		// run init commit in order to rename the branch from master
		echo exec('echo "gitlab_preview" >> gitlab_preview');
		echo exec('git add gitlab_preview');
		echo exec('git commit -m "Initial commit"');
		echo exec('git branch -m gitlab_preview');
		// change back to the root directory
		chdir($dir_root);
		// report true to signify that initialization took place
		log_status('git init ran');
		return true;
	} else {
		log_status('git init not run');
	}
// if the base directory doesn't exist (also true for non-supported branches)
} else {
	throw new Exception("Branch [$branch] does not match server $dir_base");
}