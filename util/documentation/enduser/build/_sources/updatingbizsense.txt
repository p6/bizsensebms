Updating Bizsense
====================

Important points to remember:

* BizSense supports updating from the last version only. For example, if the latest version is 0.2.4 you can only update from 0.2.3. You can repeat the steps below to update from version to version
* It is highly recommended to stay up to date
* Update command should not be run more than once

Steps to update Bizsense
############################

* Take a backup of all the files
* Take a backup of database. We will need the files from backup in later steps
* Remove the Bizsense files used in production
* Download the newer version of Bizsense
* Upload the files to the web server
* Extract the archive
* Copy and overwrite the directories - `application/data`, `application/configs`, `public_html/files` and `public_html/.htaccess` from the backup
* Run the updater command::

    php application/modules/default/services/cli.php --service=Core_Service_Update

* Manually verify the updated software




