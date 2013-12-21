<?php
/*temp*/shell_exec('echo " [ init_repo.php running ] " >> webhook.log');
// ensure we're working from a base directory
/*temp*/shell_exec('echo " [ dir base is '.$dir_base.' ] " >> webhook.log');
if(file_exists($dir_base)){
/*temp*/shell_exec('echo " [ there is a server for this branch ] " >> webhook.log');
	// if the project directory doesn't exist
	if(!file_exists($dir_proj)){
/*temp*/shell_exec('echo " [ no proj dir ] " >> webhook.log');
		// if the client directory doesn't exist
		if(!file_exists($dir_client)){
			// create the client directory
			mkdir($dir_client);
/*temp*/shell_exec('echo " [ no client dir ] " >> webhook.log');
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
/*temp*/shell_exec('echo " [ true: init of repo (clone) ] " >> webhook.log');
		// report true to signify that initialization took place
		return true;
	// if the project isn't a git repo
	} elseif(!file_exists($dir_proj . '.git')){
/*temp*/shell_exec('echo " [ .git does not exist ] " >> webhook.log');
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
/*temp*/shell_exec('echo " [ true: init of repo (.git) ] " >> webhook.log');
		return true;
	// if the base directory doesn't exist (also true for non-supported branches)
	}
} else {
	throw new Exception("Branch [$branch] does not match server $dir_base");
}
/*temp*/shell_exec('echo " [ init_repo.php ran ] " >> webhook.log');