<?php

class Ereactor_Slider_Block_Adminhtml_Slideshow_Edit extends
	Mage_adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		$helper = Mage::helper('slider/Admin');
		$this->_objectId = 'slideshow_id';
		$this->_blockGroup = 'slider';
		$this->_controller = 'adminhtml_slideshow';
		
		parent::__construct();
		
		if ($helper->isActionAllowed('save')) {
			$this->_updateButton('save', 'label', $this->__('Save'));
			$this->_addButton('saveandcontinue', array(
				'label'		=> $this->__('Save and continue'),
				'onclick'	=> 'saveAndContinueEdit()',
				'class'		=> 'save',
			), -100);
		} else {
			$this->_removeButton('save');
		}
		
		if ($helper->isActionAllowed('delete')) {
			$this->_updateButton('delete', 'label', $this->__('Delete'));
		} else {
			$this->_removeButton('delete');
		}
		
		$this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('page_content') == null) {
					tinyMCE.execCommand('mceAddControl', false, 'page_content');
				} else {
					tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
				}
			}
			
			function saveAndContinueEdit()
			{
				editForm.submit($('edit_form').action+'back/edit');
			}
		";
	}
	
	public function getHeaderText()
	{
		$model = Mage::helper('slider')->getSlideshowInstance();
		if ($model->getId()) {
			return $this->__("Edit '%s'", $this->escapeHtml($model->getName()));
		}
		return $this->__('New slideshow');
	}
}