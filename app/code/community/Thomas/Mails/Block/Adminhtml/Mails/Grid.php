<?php
class Thomas_Mails_Block_Adminhtml_Mails_Grid extends Mage_Adminhtml_Block_Widget_Grid{

    public function __construct(){
    
        parent::__construct();
        $this->setId('mails_id');
        $this->setDefaultSort('mails_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        // $this->_removeButton('add');
    }

    protected function _prepareCollection(){

        $collection = Mage::getModel('mails/mails')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns(){

        $this->addColumn('mails_id', array(
            'header'    => 'id',
            'index'     => 'mails_id',
        ));

        $this->addColumn('name', array(
            'header'    => 'name',
            'index'     => 'name',
        ));

        $this->addColumn('telephone', array(
            'header'    => 'Contact Number',
            'index'     => 'telephone',
        ));

        $this->addColumn('email', array(
            'header'    => 'Email',
            'index'     => 'email',
        ));

        $this->addColumn('comment', array(
            'header'    => 'Message',
            'index'     => 'comment',
        ));

        $this->addColumn('created_at', array(
            'header'    => 'created_at',
            'index'     => 'created_at',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row){

        return false;
    }

    public function getGridUrl(){

      return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    protected function _prepareMassaction(){

        $this->setMassactionIdField('ids');
        $this->getMassactionBlock()->setFormFieldName('ids');
         
        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> 'Delete',
            'url'  => $this->getUrl('*/*/delete', array('' => '')),       
            'confirm' => 'Are you sure you want to delete these?'
        ));

        $this->getMassactionBlock()->addItem('resend', array(
            'label'=> 'Resend',
            'url'  => $this->getUrl('*/*/resend', array('' => '')),       
            'confirm' => 'Are you sure resend these?'
        ));
         
        return $this;
    }
}