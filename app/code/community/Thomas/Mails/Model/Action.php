<?php
class Thomas_Mails_Model_Action{

	public function contactsPostsSave($observer){
		
		$post = Mage::app()->getRequest()->getPost();
    	Mage::getModel('mails/mails')->setData(
				array('name'=>$post['name'],
	  			'email'=>$post['email'], 
	  			'telephone'=>$post['telephone'], 
	  			'comment'=>$post['comment'] )
	  		)->save();
    }
}