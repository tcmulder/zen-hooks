# zen-hooks
## Description
The zen-hooks script enable gitlab, the zenman webservers, and local machines to talk to each other.

This ``master`` branch contains instructions pertinent to gitlab and the zenman webservers.

If you're trying to set up your local machine, check out the [``local`` branch](http://git.zenman.com/tcmulder/zen-hooks/tree/local).




## Setup
After creating a project in gitlab:

1. Add the web hook ``http://YOUR_SERVER_ADDRESS/zen-hooks/?client=client_folder_name&project=project_folder_name`` under Settings > Web Hooks (replacing client and project folder names appropriately).
2. Enable the ``Zenman Web Servers`` deployment key in Settings > Deployment Keys.

Your gitlab project is now connected to the Zenman web servers.

## Usage
The zen-hooks script is pretty flexible. Most interaction after setup happens locally, and you can [find instructions here](http://git.zenman.com/tcmulder/zen-hooks/tree/local).

Basically, when pushed up to gitlab, branches similar to the following will get pulled into ``YOUR_SERVER_ADDRESS`` via the zen-hooks script:

- ``dev``
- ``dev_feature``
- ``dev_slider``

Similarly, you can push to any other Zenman web server by prefixing your branch name appropriately:

- ``test_qa`` will get pulled into ``YOUR_SERVER_ADDRESS``
- ``test`` will get pulled into ``YOUR_SERVER_ADDRESS``
- ``stage`` will get pulled into ``YOUR_SERVER_ADDRESS``

### Usage for WordPress Sites
The zen-hooks script can handle the database for WordPress sites. (If however you choose to handle updating the database manually, there's no need to implement the following instructions.)

To tell the zen-hooks script to handle the WordPress database for your project, pass in the additional query ``&type=wp`` with the url. For example:

``http://YOUR_SERVER_ADDRESS/zen-hooks/?client=client_folder_name&project=project_folder_name&type=wp``

You should have a few things in place before pushing up changes for WordPress sites:

1. Your ``zen-config.php`` should have the correct database credentials for all servers you intend to pull into.

2. Dump a database you'd like the zen-hooks script to use into ``/.db/db.sql``. No find and replace is necessary as the script will replace the value of ``siteurl`` with the appropriate url based on the server name.

## Extended Description
The zen-hooks script is flexible enough to handle a variety of situations. The script always uses a branch called ``view``. Don't actually use this branch in your projects as the history gets really messed up (the script uses reset --hard to avoid merge conflicts).

### Logging
You can turn on logging by passing in the ``&log=true`` query. You can SSH into the server and tail this file like this:

``tail -f /YOUR_SERVER_ADDRESS/zen_dev1/zen-hooks/webhook.log``

The script uses a pretty primitive logging system but is better than nothing. PHP errors will also be logged in this file. The file will get truncated to 1000 lines when it reaches 100000 lines to ensure it doesn't get unmanageable. Therefore, don't wait too long to check the log or your results will get overwritten, and when the truncation occurs on occasion you might need to rerun your tail.

### Server Check
The script checks to ensure the branch matches an available server. If you push a branch named ``dev_feature_name``, it will pull changes into YOUR_SERVER_ADDRESS. However, if you push ``blah_feature`` or ``master``, the script will cease execution. It's important to note that gitlab is unaware of the zen-hooks script's activities, so you can certainly push such brances to gitlab and it will track your changes just fine.

This adds quite a bit of flexibility as you can pull changes into any Zenman web server that follows the ``zen_servername1`` format. If we add a server (as we did with ``discovery1.zenman.com`` a while ago), the script will automatically work with it.

### Nonexistent Directories
If the client directory doesn't yet exist, the script creates it and copies the ``client_template`` setup. If the project directory doesn't exist, the script clones the branch pushed to it. Note that it might take a while for your changes to appear the first time if this clone is large.

### Project Not in git
If the script detects an existing project that is not version controlled with git, it initializes git and commits the current state (including the database if you've set the type to be WordPress). This commit happens on ``master`` and then your pushed changes are imported to the ``view`` branch so you can see them.

### Dirty Working Directory
If the script detects a dirty working directory (e.g. if you've been playing around on dev1), the script makes an automated commit (including the database if you've set the type to be WordPress) to preserve your changes, then switches over to the ``view`` branch and imports your changes. The commit message for these will be "Automate commit to save working directory (switching to view)."

### WordPress Sites
If you tell the script your project is a WordPress site, it employs some special database handling capabilities. It relies on the details you feed it through the ``zen-config.php`` file, so ensure these are as you want them to be before pushing to gitlab. Then, a couple of things can happen:

- If the database doesn't exist, the script creates it with a same-named user.
- For existing databases, the script will drop tables, import the ``/.db/db.sql`` file, and do a find and replace based on the imported database ``siteurl``.

## To Do:
The zen-hooks script can still use improvement. If you have time to implement any of these (or to make overall improvements to existing scripts), feel free to contribute. Here's the future enhancement list:

- Create local setup script/file set.