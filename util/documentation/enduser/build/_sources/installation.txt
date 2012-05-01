Installation
=================

Make sure the System Requirement is met.

Follow these steps to install Bizsense:

* Make sure memcache daemon is running
* Enable Apache mod_rewrite, if not already. Set AllowOverride to All
* Download Bizsense
* Extract the archive and upload the files to your web server
* Rename public_html/.htaccess.dist to public_html/.htaccess. Alternatively set the rules in Apache configuration file.
* Make the `application/configs`, `application/data`, `application/data/cache`, `public_html/files/logo` directories writeable
* Point DocumentRoot to `public_html`
* Create a MySQL database and user
* Access the hostname, for example bizsense.example.com, from your web browser
* Follow the on screen instructions to complete the installation






