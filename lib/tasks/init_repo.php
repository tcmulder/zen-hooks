<?php
log_status('init_repo: included');
// ensure we're working from a base directory
if(file_exists($dir_base)){
	log_status('init_repo: base directory is '.$dir_base);
	// if the project directory doesn't exist
	if(!file_exists($dir_proj)){
		log_status('init_repo: project directory does not exists '.$dir_proj);
		// if the client directory doesn't exist
		if(!file_exists($dir_client)){
			log_status('init_repo: client directory does not exists '.$dir_client);
			// create the client directory
			log_status('init_repo: create client directory');
			mkdir($dir_client);
			log_status('init_repo: create archive directory');
			mkdir($dir_client.'archive');
			log_status('init_repo: copy client_template/index.php');
			copy($dir_base.'client_template/index.php',$dir_client.'index.php');
		}
		// change into the client directory
		chdir($dir_client);
		// clone in the repo
		log_status('init_repo: clone repo '.$repo);
		exec("git clone --origin gitlab $repo");
		// cd into it
		chdir($dir_proj);
		// establish credentials
		exec('git config user.email "dev@zenman.com"');
		exec('git config user.name "YOUR_USERNAME"');
		// change back to the root directory
		chdir($dir_root);
		// report true to signify that initialization took place
		log_status('init_repo: initial clone requested');
		return true;
	// if the project isn't a git repo
	} elseif(!file_exists($dir_proj . '.git')){
		log_status('init_repo: not a git repository but project directory is present');
		// change into the project directory
		chdir($dir_proj);
		// set up git
		exec('git init');
		// establish credentials
		exec('git config user.email "dev@zenman.com"');
		exec('git config user.name "YOUR_USERNAME"');
		// set up remote
		exec("git remote add gitlab $repo");
		// change back to the root directory
		chdir($dir_root);
		// report true to signify that initialization took place
		log_status('init_repo: git initialized with existing files');
		return true;
	} else {
		log_status('init_repo: initialization not run');
	}
// if the base directory doesn't exist (also true for non-supported branches)
} else {
	throw new Exception("Branch [$branch] does not match server $dir_base");
}