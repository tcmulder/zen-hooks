<?php
// ensure we're working from a base directory
if(file_exists($dir_base)){
	// if the project directory doesn't exist
	if(!file_exists($dir_proj)){
		// if the client directory doesn't exist
		if(!file_exists($dir_client)){
			// create the client directory
			mkdir($dir_client);
		}
		// change into the client directory
		chdir($dir_client);
		// clone in the repo
		shell_exec("git clone --origin gitlab $repo");
		// cd into it
		chdir($dir_proj);
		// establish credentials
		shell_exec('git config user.email "dev@zenman.com"');
		shell_exec('git config user.name "YOUR_USERNAME"');
		// change back to the root directory
		chdir($dir_root);
		// report true to signify that initialization took place
		return true;
	// if the project isn't a git repo
	} elseif(!file_exists($dir_proj . '.git')){
		// change into the project directory
		chdir($dir_proj);
		// set up git
		shell_exec('git init');
		// establish credentials
		shell_exec('git config user.email "dev@zenman.com"');
		shell_exec('git config user.name "YOUR_USERNAME"');
		// set up remote
		shell_exec("git remote add gitlab $repo");
		// change back to the root directory
		chdir($dir_root);
		// report true to signify that initialization took place
		return true;
	}
// if the base directory doesn't exist (also true for non-supported branches)
} else {
	// throw an error
	throw new Exception("Base directory [$dir_base] does not exist");
}