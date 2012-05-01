<?php
class BV_View_Helper_BaseUrl
{
    public $view;
    function setView(Zend_View_Interface $view) 
    {
        $this->view = $view;
    }

    public function scriptPath($script) {

        return $this->view->getScriptPath($script);
    }

	
    protected $_baseUrl;
    
    function __construct()
    {
        $fc = Zend_Controller_Front::getInstance();
        $request = $fc->getRequest();
        $this->_baseUrl =  $request->getBaseUrl();
    }
    
    function baseUrl()
    {
        return $this->_baseUrl;
    }

}


