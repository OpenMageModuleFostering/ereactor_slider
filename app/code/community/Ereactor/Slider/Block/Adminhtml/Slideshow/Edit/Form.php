<?php

class Ereactor_Slider_Block_Adminhtml_Slideshow_Edit_Form extends
	Mage_adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
			'id'		=> 'edit_form',
			'action'	=> $this->getUrl('*/*/save'),
			'method'	=> 'post',
			'enctype'	=> 'multipart/form-data',
		));
		
		$form->setUseContainer(true);
		$this->setForm($form);
		return parent::_prepareForm();
	}
}