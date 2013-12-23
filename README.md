# Zenpository
## Description
In short, the zenpository script connects gitlab to Zenman's various web servers.

## Setup
It's easy to set up you project to work with the zenpository script.

1. Create/access your project in gitlab.
2. In you project, go to Settings>Web Hooks.
3. Enter the the url 
``http://YOUR_SERVER_ADDRESS/zenpository/?client=client_folder_name&project=project_folder_name`` and add the web 
hook.
4. Now, go to Deployment Keys.
5. Enable the dev1 deployment key.

Your gitlab project is now connected to the Zenman web servers.

## Usage
The zenpository script is pretty flexible. Simply clone the gitlab project to your local machine to get 
started. You can then create branches based on the Zenman web server you'd like to see changes on.

For example, when pushed up to gitlab, the following branches will get pulled into ``YOUR_SERVER_ADDRESS`` via the 
zenpository script:

- ``dev_feature``
- ``dev_slider``
- ``dev``

Similarly, you can push to any other Zenman web server by prefixing your branch name appropriately:

- ``test_qa`` will get pulled into ``YOUR_SERVER_ADDRESS``
- ``test`` will get pulled into ``YOUR_SERVER_ADDRESS``
- ``stage`` will get pulled into ``YOUR_SERVER_ADDRESS``

### WordPress Sites
The zenpository script can handle the database for WordPress sites. (If however you choose to handle updating 
the database manually, there's no need to implement the following instructions.)

To tell the zenpository script to handle the WordPress database for your project, you'll need to pass in the 
extra query ``&type=wp`` with the url. For example: 

``http://YOUR_SERVER_ADDRESS/zenpository/?client=client_folder_name&project=project_folder_name&type=wp``

You should have a few things in place before pushing up changes for WordPress sites:

1. Your ``wp-config.php`` should have the correct database credentials for all servers you intend to pull into 
(use Zenman's case-style version which relies on database prefixes to determine the server).

2. Dump a database you'd like the zenpository script to use into ``/.db/db.sql``. No find and replace is 
necessary as the script will replace the value of ``siteurl`` with the appropriate url.

## Extended Description
The zenpository script is flexible enough to handle a variety of situations. The script always uses a branch 
called ``view``. Don't use this branch in your projects as the history gets really messed up (the script uses 
reset --hard to avoid merge conflicts).

### Logging
You can turn on logging by passing in the ``&log=true`` query. The script will output all logs in the 
following file:

``/YOUR_SERVER_ADDRESS/zen_dev1/zenpository/webhook.log``

The script uses a pretty primitive logging system but is better than nothing. PHP errors will also be logged 
in this file. The file will get truncated to a few thousand lines to ensure it doesn't get unmanageable. 
Therefore, don't wait too long to check the log or your results will get overwritten.

### Server Check
The script checks to ensure the branch matches an available server. If you push a branch named 
``dev_feature_name``, it will pull changes into YOUR_SERVER_ADDRESS. However, if you push ``blah_feature`` or 
``master``, the script will cease execution.

This adds quite a bit of flexibility as you can pull changes into any Zenman web server that follows the 
``zen_servername1`` format. If we add a server (as we did with ``discovery1.zenman.com`` a while ago), the 
script will automatically work with it.

### Directory Creation
If the client directory doesn't exist yet, the script creates it and copies the ``client_template`` setup. If 
the project directory doesn't exist, the script clones the branch pushed to it. Note that it might take a 
while for your changes to appear if this clone is large.

### Project Not in git
If the script detects an existing project that is not version controlled with git, it initializes git, commits 
current changes (including the database if you've set the type to be WordPress). Changes are committed on 
``master`` and then changes are reset on the ``view`` branch.

### Dirty Working Directory
If the script detects a dirty working directory (e.g. if you've been playing around on dev1), the script makes 
an automated commit (including the database if you've set the type to be WordPress) to preserve your changes, 
then switches over to the ``view`` branch, and imports your changes. The commit message for these will be 
"Automate commit to save working directory (switching to view)."

### WordPress Sites
If you tell the script your project is a WordPress site, it employs some special database handling 
capabilities. It relies on the details you feed it through the ``wp-config.php`` file, so ensure these are as 
you want them to be before pushing to gitlab. Then, a few things can happen:

- If the database doesn't exist, the script creates it (with the corresponding user).
- For existing databases, the script will drop tables, import the ``/.db/db.sql`` file, and do a find and 
replace based on the imported database ``siteurl``.

## To Do:
The zenpository script can still use some improvements. If any of you have time to implement any of these (or 
to make overall improvements to existing scripts), feel free to contribute. Here's the future enhancement 
list:

- Test missing ``/.db/db.sql`` files.
- Test wrong credentials in ``wp-config.php`` files
- Create local setup script/file set.
- Switch to dev1.
- Copy client_template files properly.
