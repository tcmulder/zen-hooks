# Zenpository Local Setup
## Description
This branch explains how to set up your local repository to play nice with the remote zenpository script.

## Setup
To set up a local copy of a repository using the zenpository script, you can basically follow the instructions provided by gitlab, but here's the process I've found to work best for me.

1. Create a ``sites/client`` directory to match the Zenman web server setup.
2. ``cd`` into the client folder.
3. Run ``git clone git@git.zenman.com:owner_name/project_name.git``.
4. ``cd`` into the new project repository and set up any brances you'd like to use.
    1. Creat new brances with the command ``git checkout -b branch_name``.
    2. You can track existing branches by running ``git checkout --track -b branch_name origin/branch_name``.

### New Projects
For new projects, there are a couple additional steps you should complete in most cases *before starting to make commits*.

1. Download the ``.gitattributes`` and ``.gitignore`` files from this ``local`` branch of the zempository repository.
2. Commit these files on the ``master`` branch before committing any other files.
3. Push these changes up to gitlab.

Git behaves pretty erratically without a ``master`` branch that has at least one file committed to it, so make sure to push a change to the ``master`` branch to gitlab before anything else.

The ``.gitignore`` file needs to be tracked for it to do it's job. So if, for instance, you start tracking the ``.sass-cache`` directory and then add the ``.gitignore`` later, your repository will continue to track changes to that directory.

The ``.gitattributes`` file ensures git doesn't attempt line ending normalization which makes it hard to keep things synchronized, especially between multiple OS's that handle line endings differently.

### WordPress Type Projects
*Word of warning for WordPress type projects:* You should make sure your ``wp-config.php`` file is up-to-date for all servers you expect to push to and that a database dump you'd like to use is in the ``/.db/db.sql`` file. The zenpository script is generally pretty trusting, so it will happily create a database named ``d1_enter_database_name_here`` if that's what the ``wp-config.php`` file instructs it to do.

You should also grab the hooks located in this branch of the zenpository script. These scripts add a mysql dump of your database to your commits so you'll have a complete snapshot of the site at its current state. This dump is also used by the remote zenpository script to update the remote database.

1. Download the ``local`` branch of the zenpository script.
2. Replace the ``.git/hooks`` directory with the ``hooks`` directory you downloaded (this removes all the ``*.sample`` files that git creates by default).
3. Edit the values in the ``.git/hooks/config`` file to match your project.

## Usage
While it's possible to work on a branch like ``dev``, it's better practice to use more meaningful branches based on features you're working on.

For instance, if you're creating a mobile nav, you could work on a branch called ``dev_mobile_nav``. When your changes are complete, merge them into the ``dev`` branch, push it up, and then checkout your next feature branch to work on. Any branch with the ``dev`` prefix will be pulled into ``YOUR_SERVER_ADDRESS`` when you push to a zenpository-script-enabled project.

Similiarly, anything pushed with the ``test`` prefix will get pulled into ``YOUR_SERVER_ADDRESS``. We'll want to nail down our process for this, but you could potentially push a branch like ``test_qa`` to gitlab in order to make a branch available for quality assurance testing.

Feel free also to push branches up that don't need to be visuallized. You can push up the ``master`` branch with changes, for instance; in this case, gitlab will store your changes and the zenpository script will essentially ignore them.

### Usage for WordPress Sites
Using the hooks provided in this ``local`` branch of the zenpository repository is highly encouraged as it will ensure the database gets version controlled along with your files.

If you choose not to (or if the hooks aren't working for some reason), you can alternatively do a manual dump of the ``db.sql`` file into the ``/.db/`` directory, add and commit it, then push it up. The zenpository script will use this file to update the remote database, doing a find and replace based on the ``siteurl`` value in the database.

## To Do:
Feel free to contribute enhancements to the local setup if you'd like.

- Create local script to setup projects via command line.
- Automate wp-config.php creation.
- Automate creation of a safer [wp-config.php](https://basecamp.com/1781478/projects/432754-zenman-tech-forum/messages/18451467-idea-for-a-new).