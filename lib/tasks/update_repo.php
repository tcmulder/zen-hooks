<?php
// ensure this is a git project
if(file_exists($dir_proj . '/.git')){
	// run git commands in working directory
	$git = "git --git-dir=$dir_proj/.git --work-tree=$dir_proj";
	// get the current status
	$status = shell_exec("$git status");
	// if this is not a clean working directory
	if(strpos($status, "working directory clean") == false){
		//
		// DUMP DB IF NEEDED (added below)
		//
		//
		shell_exec("$git add --all .");
		shell_exec("$git commit -m 'Automate commit to save working directory (switching to view)'");
	}
	// create the view branch (doesn't to do so if it exists already)
	shell_exec("$git branch view");
	// checkout the view branch (even if it is already)
	shell_exec("$git checkout view");
	// fetch the branch
	shell_exec("$git fetch gitlab $branch:refs/remotes/gitlab/$branch");
	// reset hard to the branch: no need to preserve history in the view
	shell_exec("$git reset --hard gitlab/$branch");

	//
	//
	// UPDATE DB IF NEEDED
	//
	//

// if the .git directory can't be found in the project
} else {
	// talk about it
	throw new Exception($dir_proj . '/.git could not be found');
}