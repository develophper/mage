<?php
class Thomas_Mails_Block_Adminhtml_Mails extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct(){

        $this->_controller = 'adminhtml_mails';
        $this->_blockGroup = 'mails';
        $this->_headerText = 'Customer Contact Mails';
        $this->_removeButton('add');
        parent::__construct();
    }

    protected function _toHtml(){
        
        return parent::_toHtml();
    }
}