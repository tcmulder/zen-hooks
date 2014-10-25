<?php
log_status("\n\n:: update_repo included");
// ensure this is a git project
if(file_exists($dir_proj . '.git')){
    log_status('git repo found in '.$dir_proj);
    // run git commands in working directory
    $git = "git --git-dir=$dir_proj.git --work-tree=$dir_proj";
    // get the current status
    $status = log_exec("$git status");
    // if this is not a clean working directory
    if(strpos($status, "working directory clean") == false){
        log_status('working directory is not clean');
        // for wordpress sites
        if($proj_type == 'wp'){
            log_status('is type wordpress');
            // include the database scripts
            include_once 'lib/functions/db.php';
            // dump the database so it will be saved
            db_export($wp_db_creds, $dir_proj . '.db/');
        }
        // create a dated branch and automate a commit to it
        $prefixed_branch = 'gitlab_autosave_at_'.date(date('Y_m_d_H_i_s'));
        log_status('automated branch name is '.$prefixed_branch);
        log_exec("$git checkout -b $prefixed_branch");
        log_exec("$git add --all .");
        log_exec("$git commit -m 'Automate commit to save working directory'");
        log_status('requested automated commit');
    }
    log_status('fetch new commit script running');
    // create the gitlab_preview branch (doesn't to do so if it exists already)
    log_exec("$git branch gitlab_preview");
    // checkout the gitlab_preview branch (even if it is already)
    log_exec("$git checkout gitlab_preview");
    // get rid of untracked files and directories
    log_exec("$git clean -f -d");
    // fetch the branch
    log_exec("$git fetch -f --depth=1 gitlab $branch:refs/remotes/gitlab/$branch");
    // reset hard to the branch: no need to preserve history in the gitlab_preview
    log_exec("$git reset --hard gitlab/$branch");
    log_status('reset hard requested on gitlab_preview branch');
// if the .git directory can't be found in the project
} else {
    // talk about it
    throw new Exception($dir_proj . '.git could not be found. Is the deployment key added?');
}