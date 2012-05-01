<?php
class Finance_UrlAccessTest extends PHPUnit_Framework_TestCase
{
        
    public function setUp()
    {   
        parent::setUp();
    }

    public function testFinanceModelUrlsAreAccessControlled()
    {
        $defaultModuleControllers =  APPLICATION_PATH . '/modules/finance/controllers';

        $iterator = new DirectoryIterator($defaultModuleControllers);
        $urlsInInstaller = $this->_getUrlsInInstaller();
        $publicUrls = array(
            'default/install/index',
        );

        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDot())  {
                continue;
            }
            $fileName = $fileInfo->getFilename();    
            if (strpos($fileName, "Controller")) {
                $className = "Finance_" . rtrim($fileName, ".php");
                require_once APPLICATION_PATH . '/modules/finance/controllers/' . $fileName;
                $methods = (get_class_methods($className));
                foreach ($methods as $method) {
                   if (strpos($method, "Action")) {
                        $controllerWithoutSuffix = strtolower(substr_replace($className, "", -10));
                        $controllerWithoutSuffix = strtolower(substr_replace($controllerWithoutSuffix, "", 0, 8));
                       # echo "\nController without suffix is $controllerWithoutSuffix";
                        //$actionWithoutSuffix = strtolower(substr_replace($method, "", -6));
                        $actionWithoutSuffix = explode("Action", $method);
                        $urlFound =  'finance/' . $controllerWithoutSuffix . "/" . $actionWithoutSuffix[0];
                        $test = in_array($urlFound, $urlsInInstaller); 
                        $condition = ($test or (in_array($urlFound, $publicUrls)));
                        $message = '';
                        if (!$condition) {
                            $message = $urlFound . " is not access controlled \n";
                        }
                        $this->assertTrue($condition, $message);
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

