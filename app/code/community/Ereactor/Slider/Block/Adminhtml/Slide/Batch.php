<?php
class Ereactor_Slider_Block_Adminhtml_Slide_Batch
	extends Mage_Adminhtml_Block_Template
{
	protected function _prepareLayout()
	{
		$slideshow = Mage::helper('slider')->getSlideshowInstance();
		$this->setImagesUrl($this->getUrl('*/ereactor_slider_slide/batchAddImagesForm', array('slideshow_id' => $slideshow->getId())));
	}
}