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
		// change back to the root directory
		chdir($dir_root);
	} else {
		// if the project directory already exists
		echo "directory exists: $dir_proj";
		return false;
	}
} else {
	throw new Exception("Base directory [$dir_base] does not exist");
}


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
