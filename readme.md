# zen-hooks Local Setup
## Description
This branch's readme explains how to set up your local repository to play nice with the remote zen-hooks script.

## Setup
To set up a local copy of a repository using the zen-hooks script, you can basically follow the instructions provided by gitlab, but here's the process I've found to work best for me.

1. Create a ``sites/client`` directory to match the Zenman web server setup.
2. ``cd`` into the client folder.
3. Run ``git clone git@github.com:tcmulder/zen-hooks.git``.
4. ``cd`` into the new project repository and set up any brances you'd like to use.
    1. Creat new brances with the command ``git checkout -b branch_name``.
    2. You can track existing branches by running ``git checkout --track -b branch_name origin/branch_name``.

### New Projects
For new projects, there are a few additional steps you must complete in most cases *before starting to make commits*.

1. Download the ``.gitattributes``, ``.gitignore``, ``wp-config.php``, ``zen-config.php``, and ``changelog.md`` files from this ``local`` branch of the zen-hooks repository.
2. Update the ``changelog.md`` file with the project name.
4. Add line ``17`` (``include...``) line in the downloaded ``wp-config.php`` file to your site's ``wp-config.php`` file.
5. Update the ``zen-config.php`` file to match your project.
6. Commit just these files on the ``master`` branch before committing any other files.
7. Push these changes up to gitlab.

Git sometimes behaves erratically without a ``master`` branch that has at least one change committed to it, so make sure to push a change to the ``master`` branch to gitlab before anything else.

The ``.gitignore`` file needs to be tracked for it to do it's job. So if, for instance, you start tracking the ``.sass-cache`` directory and then add the ``.gitignore`` later, your repository will continue to track changes to that directory.

The ``.gitattributes`` file ensures git doesn't attempt line ending normalization which makes it hard to keep things synchronized, especially between multiple OS's that handle line endings differently.

You don't need to replace the ``wp-config.php`` file WordPress gives you during installation with the one you download from this repo; the only line you need to add is the ``include`` line so WordPress knows to look for the ``zen-config.php`` file (and add the line to stop auto core updates, but that's not needed for zen-hooks to work). You can add the live WordPress database credentials to the ``wp-config.php`` at this time also if you know them.

You can use the variables at the top of ``zen-config.php`` so you're not repeating the same information multiple times, or you can just use strings instead.

In the ``changelog.md`` file, you can add an initial ``* Created repo.`` entry with today's date if you'd like.

### WordPress Type Projects
*Word of warning for WordPress type projects:* You should make sure your ``zen-config.php`` file is up-to-date for all servers you expect to push to and that a database dump you'd like to use is in the ``/.db/db.sql`` file. The zen-hooks script is generally pretty trusting, so it will happily create a database named ``d1_enter_database_name_here`` if that's what the ``zen-config.php`` file instructs it to do.

You can also grab the hooks located in this branch of the zen-hooks script. These scripts add a mysql dump of your database to your commits so you'll have a complete snapshot of the site at its current state. This dump is also used by the server side zen-hooks script to update the remote database.

1. Download the ``local`` branch of the zen-hooks script.
2. Replace the ``.git/hooks`` directory with the ``hooks`` directory you downloaded (this removes all the ``*.sample`` files that git creates by default).
3. Edit the values in the ``.git/hooks/config`` file to match your project.

If you choose not to add these hooks, you can alternatively just do a manual dump of the ``db.sql`` file into the ``/.db/`` directory, add and commit it, then push it up. The zen-hooks script will use this file to update the remote database, doing a find and replace by figuring out the database's ``siteurl`` on it's own.

## To Do:
Feel free to contribute enhancements to the local setup if you'd like.

- Create local script to setup projects via command line.
- Automate zen-config.php creation.
