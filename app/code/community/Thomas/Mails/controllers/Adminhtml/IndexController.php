<?php
class Thomas_Mails_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{

	const XML_PATH_EMAIL_RECIPIENT  = 'contacts/email/recipient_email';
    const XML_PATH_EMAIL_SENDER     = 'contacts/email/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE   = 'contacts/email/email_template';

    public function indexAction(){

		$this->loadLayout();
		$this->renderLayout();
	}

	public function gridAction(){

	    $this->loadLayout();
	    $this->renderLayout();
	}

	public function deleteAction(){
	
		$mails = $this->getRequest()->getParam('ids');      
		if(!is_array($mails)) {
			Mage::getSingleton('adminhtml/session')->addError('Please select mail(s).');
		} else {
			try {
				$model = Mage::getModel('mails/mails');
				foreach ($mails as $mail) {
					$model->load($mail)->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess('Total of '.count($mails).' record(s) were deleted.');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
	    $this->_redirect('*/*/index');
	}

	public function resendAction(){

        $mails = $this->getRequest()->getParam('ids');      
        if(!is_array($mails)) {
			Mage::getSingleton('adminhtml/session')->addError('Please select mail(s).');
		} else {
			try {
				$model = Mage::getModel('mails/mails');
				$mailTemplate = Mage::getModel('core/email_template');
				foreach ($mails as $mail) {
					$mail1 = $model->load($mail)->getData();
					$mail = new Varien_Object();
                	$mail->setData($mail1);

			        $mailTemplate->setDesignConfig(array('area' => 'frontend'))
			            ->setReplyTo($mail['email'])
			            ->sendTransactional(
			                Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
			                Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
			                Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
			                null,
			                array('data' => $mail)
			            );
			            if (!$mailTemplate->getSentSuccess()) {
		                    throw new Exception();
		                }
				}
				Mage::getSingleton('adminhtml/session')->addSuccess('Total of '.count($mails).' record(s) were sent.');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
        $this->_redirect('*/*/index');
	}
}