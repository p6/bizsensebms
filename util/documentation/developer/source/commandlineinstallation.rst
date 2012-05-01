Command Line Installation
############################

To quickly reinstall the application, you can use the command line installation utility script. The script removes the database and creates the database again.

* The directories `application/data`, `application/configs` `public_html/files` must be writeable
* cp util/dev/scripts/Install/myInstallerValues.php.dist util/dev/scripts/Install/myInstallerValues.php
* Edit the file `util/dev/scripts/Install/myInstallerValues.php`
* All fields are mandatory. Pay attention to the fields
      URL - the install URL of the application
      privilegedDbUsername - the MySQL database user that can create and delete databases. Usually root
      privilegedDbUserPassword - the password for the above MySQL user
* Execute the command
      php util/dev/scripts/Install/Install.php
* To install demo data execute the command
      php util/dev/scripts/Install/DemoData.php

