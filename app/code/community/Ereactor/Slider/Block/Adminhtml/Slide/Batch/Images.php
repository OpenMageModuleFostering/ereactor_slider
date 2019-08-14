<?php
class Ereactor_Slider_Block_Adminhtml_Slide_Batch_Images
	extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm() {
		$slideshow = Mage::helper('slider')->getSlideshowInstance();
		$isElementDisabled = ! Mage::helper('slider/Admin')->isActionAllowed('save');
		
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('slideshow_batch_add_images');
		
		$fieldset = $form->addFieldset('base_fieldset', array(
			'legend' => $this->__('Generate slides from images')
		));
		
		if ($slideshow->getId()) {
			$fieldset->addField('slideshow_id', 'hidden', array(
				'name' => 'slideshow_id',
				'value' => $slideshow->getId(),
			));
		}
		
		$fieldset->addField('source', 'hidden', array(
			'name' => 'source',
			'value' => 'images',
		));
		
		$fieldset->addField('path', 'text', array(
			'name'		=> 'path',
			'label'		=> $this->__('Folder path'),
			'title'		=> $this->__('Folder path'),
			'disabled'	=> $isElementDisabled,
			'style'		=> 'width: 200px;',
			'after_element_html' => '<p class="note">' . $this->__('The path to the folder that contains the images') . '</p>',
		));
		
		// Buttons - save and cancel
		$fieldset->addField('save', 'note', array(
			'text' =>
				$this->getButtonHtml($this->__('Generate'), '', 'save') . '&nbsp;'
				. $this->getButtonHtml($this->__('Cancel'), '', 'cancel')
		));
		
		$form->addFieldNameSuffix('generate');
		$this->setForm($form);
		return parent::_prepareForm();
	}
}