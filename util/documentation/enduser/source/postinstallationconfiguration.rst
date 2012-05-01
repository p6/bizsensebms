Post Installation Configuration
=================================

* Navigate to Administer->BizSense Status->File Permissions. Verify the file permissions
* Configure email settings at Administer->Email Settings
* Set up cron. Have the command::

         php application/modules/default/services/cli.php --service=Core_Service_Cron

    in your crontab entry
* Set your organization details
* Add the site logo. This logo appears in the top left corner of the application
* Add the Document logo. This logo appears in the PDF and other documents
* Save newsletter settings at Marketing->Settings->Message Queue Settings
* Improve performance by setting Expires header in your Apache virutal host directive or .htaccess. Example::

    <FilesMatch "\.(ico|pdf|flv|jpg|jpeg|png|gif|js|css|swf)$">
        Header set Expires "Fri, 31 Dec 2011 20:00:00 GMT"
    </FilesMatch>



