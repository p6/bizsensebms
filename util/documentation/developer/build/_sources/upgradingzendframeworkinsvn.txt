Upgrading Zend Framework In Subversion
#########################################
* cd library
* svn up
* svn propedit svn:externals .
* Change the URL of the Zend Framework version to which you want to upgrade. Example, Zend http://framework.zend.com/svn/framework/standard/tags/release-1.9.7/library/Zend
* Save the file and quite the editor
* svn up
* svn commit -m "changed ZF version to x.xx"

