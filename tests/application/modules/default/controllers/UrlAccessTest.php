<?php
class UrlAccessTest extends PHPUnit_Framework_TestCase
{
        
    public function setUp()
    {   
        parent::setUp();
    }

    public function testDefaultModelUrlsAreAccessControlled()
    {
        $defaultModuleControllers =  APPLICATION_PATH . '/modules/default/controllers';

        $iterator = new DirectoryIterator($defaultModuleControllers);
        $urlsInInstaller = $this->_getUrlsInInstaller();
        $publicUrls = array(
            'default/error/error',
            'default/error/access',
            'default/error/db',
            'default/error/fournotfound',
            'default/user/login',
            'default/user/forgotpass',
            'default/user/resetpass',
            'default/install/index',
        );

        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDot())  {
                continue;
            }
            $fileName = $fileInfo->getFilename();    
            if (strpos($fileName, "Controller")) {
                $className = rtrim($fileName, ".php");
                if ($className == 'TestController') {
                    $className = 'IndexController';
                }
                require_once APPLICATION_PATH . '/modules/default/controllers/' . $fileName;
                $methods = (get_class_methods($className));
                if (!$methods)  {
                    $methods = array();
                }
                foreach ($methods as $method) {
                   if (strpos($method, "Action")) {
                        $controllerWithoutSuffix = strtolower(substr_replace($className, "", -10));
                        //$actionWithoutSuffix = strtolower(substr_replace($method, "", -6));
                        $actionWithoutSuffix = explode("Action", $method);
                        $urlFound =  'default/' . $controllerWithoutSuffix . "/" . $actionWithoutSuffix[0];
                        $test = in_array($urlFound, $urlsInInstaller); 
                        $testCondition = ($test or (in_array($urlFound, $publicUrls)) );
                        $message = '';
                        if (!$testCondition) {
                            $message = $urlFound . " is not access controlled";
                        }
                        $this->assertTrue($testCondition, $message);
                   }
                }
            }

        }
    }

    protected function _getUrlsInInstaller()
    {
        $urlAccessFile = file_get_contents(APPLICATION_PATH . '/modules/default/services/Install/TableFill/UrlAccess.json');
        $urlAccessContent = json_decode($urlAccessFile);
        $allUrls = array();
        foreach ($urlAccessContent as $record) {
            $allUrls[] = $record->url;
        }
        return $allUrls;

    }
 
}

