<?php
log_status('update_repo: included');
// ensure this is a git project
if(file_exists($dir_proj . '/.git')){
	log_status('update_repo: git repo found in '.$dir_proj);
	// run git commands in working directory
	$git = "git --git-dir=$dir_proj/.git --work-tree=$dir_proj";
	// get the current status
	$status = shell_exec("$git status");
	//get the current sha and show the after sha for comparison
	$sha_cur = shell_exec("$git rev-parse --verify HEAD");
	log_status('update_repo: the current sha is ' . $sha_cur);
	log_status('update_repo: the after sha is ' . $sha_after);


	log_status('update_repo: the comparison equals ' . ('zenman' != 'zenman'));


	log_status('update_repo: the comparison equals ' . ("$sha_cur" != "$sha_after"));



	log_status('update_repo: which is equivalent to ' . "$sha_cur" . ' != ' . "$sha_after");


	// if this is not a clean working directory
	if(strpos($status, "working directory clean") == false){
		log_status('update_repo: working directory is not clean');
		// for wordpress sites
		if($proj_type == 'wp'){
			log_status('update_repo: is type wordpress');
			// include the database scripts
			include_once 'lib/helpers/db.php';
			// dump the database so it will be saved
			db_export($wp_db_creds, $dir_proj . '.db/');
		}
		exec("$git add --all .");
		exec("$git commit -m 'Automate commit to save working directory (switching to view)'");
		log_status('update_repo: requested automated commit');
	// if this is a new commit
	} elseif($sha_cur != $sha_after) {
		// create the view branch (doesn't to do so if it exists already)
		exec("$git branch view");
		// checkout the view branch (even if it is already)
		exec("$git checkout view");
		// get rid of untracked files and directories
		exec("$git clean -f -d");
		// fetch the branch
		exec("$git fetch gitlab $branch:refs/remotes/gitlab/$branch");
		// reset hard to the branch: no need to preserve history in the view
		exec("$git reset --hard gitlab/$branch");
		log_status('update_repo: reset hard requested on view branch');
	// if the current and after commit are the same
	} else {
		throw new Exception('Current and requested commits are identical');
	}
// if the .git directory can't be found in the project
} else {
	// talk about it
	throw new Exception($dir_proj . '.git could not be found. Is the deployment key added?');
}