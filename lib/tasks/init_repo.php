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
		shell_exec("git clone $repo");
		// cd into it
		chdir($dir_proj);
		// establish credentials
		shell_exec('git config user.email "dev@zenman.com"');
		shell_exec('git config user.name "YOUR_USERNAME"');
		// create the view branch and check it out
		shell_exec("git checkout -b view");
		// grab the branch
		shell_exec("git fetch origin $branch:refs/remotes/origin/$branch");
		// hard reset to the branch. we don't care about versioning
		// this branch as it's only a view.
		shell_exec("git reset --hard origin/$branch");
		// change back to the root directory
		chdir($dir_root);
	// if the project directory already exists
	} else {
		// report false to signify init_repo is unnecessary
		return false;
	}
// if the base directory doesn't exist (also true for non-supported branches)
} else {
	throw new Exception("Base directory [$dir_base] does not exist");
}