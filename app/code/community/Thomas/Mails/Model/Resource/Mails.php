<?php

class Thomas_Mails_Model_Resource_Mails extends Mage_Core_Model_Resource_Db_Abstract{
    
    protected function _construct(){
    	
        $this->_init('mails/mails', 'mails_id');
    }
}