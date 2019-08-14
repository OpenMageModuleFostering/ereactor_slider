<?php
class Ereactor_Slider_Block_Adminhtml_Slide_Preview
	extends Mage_Adminhtml_Block_Template
{
	protected function _prepareLayout()
	{
		$helper = Mage::helper('slider');
		$slide = $helper->getSlideInstance();
		if (!isset($slide)) {
			return '';
		}
		$slideshow = $helper->getSlideshowInstance();
		if (!isset($slideshow)) {
			return '';
		}
		
		$this->setWidth($slideshow->getFullWidth());
		$this->setHeight($slideshow->getFullHeight());
		
		$block = $this->getLayout()->createBlock(
			'Ereactor_Slider_Block_Slide',
			'slide',
			array('slide' => $slide)
		);
		$this->setSlide($block->toHtml());
	}
}