<?php

class Thomas_Mails_Model_Resource_Mails_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    
    protected function _construct(){

        $this->_init('mails/mails');
    }
}