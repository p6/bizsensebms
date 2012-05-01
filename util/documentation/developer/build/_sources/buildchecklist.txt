Build Checklist
#########################

Files and directories to be removed

* .htaccess
* database.ini
* general.log
* error_log
* tests
* util

After removing the files, perform these steps

* The application environment has to be set to production
* The version has to be updated in installer and updater
* .svn directories must be moved
* The update process from previous version has to be thoroughly tested
* Tag the release version in Subversion
* Generate .tar.gz archive
* Upload release tarball to binaryvibes.co.in
* Create a news post at projects.binaryvibes.co.in 
* Make a blog post at blog.binaryvibes.co.in. Mention changelog, highlights and plans for future releases.
