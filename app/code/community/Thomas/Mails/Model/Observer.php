<?php
class Thomas_Mails_Model_Observer{

	public function addEvent($observer){
		if($observer->getEvent()->getControllerAction()->getFullActionName() == 'contacts_index_post'){
			Mage::dispatchEvent('contacts_post_save', $observer);
		}
	}
}
