<?php
class Core_Form_User_Permission_ResourceFilter extends Zend_Form
{
    protected $_resource_id;

    public function __construct($role_id = null, $resource_id = null)
    {
        if (is_numeric($resource_id)) {
            $this->_resource_id = $resource_id;        
        }
        parent::__construct();
    }
    
    public function init()
    {
        $this->setMethod('get');
        $this->addElement('select', 'resource_id', array(
            'label'         =>  'Filter by resource',
        ));
        $resourceElement = $this->getElement('resource_id');
        $resourceModel = new Core_Model_Resource;
        $resources = $resourceModel->fetchAll();
        $resourceElement->addMultiOption('', 'All');
        foreach ($resources as $resource) {
            $resourceElement->addMultiOption($resource['resource_id'], $resource['name']);
        }
        $resourceElement->setValue($this->_resource_id);
        $this->addElement('submit', 'Go');
    }
     
}
