<?php

class Ereactor_Slider_Block_Slider
	extends Mage_Core_Block_Template
	implements Mage_Widget_Block_Interface
{
	private static $_includedScripts = false;
	
	protected function _toHtml()
	{
		$this->setTemplate('ereactor/slider/slideshow.phtml');
		
		$slideshow = Mage::getModel('ereactor_slider/slideshow')->load($this->getData('slideshowId'));
		$this->setSlideshow($slideshow);
		
		$slides = Mage::getModel('ereactor_slider/slide')->getCollection()
			->addFilter('slideshow_id', $slideshow->getId())
			->setOrder('slide_order', 'ASC');
		
		$slidesHtml = array();
		foreach ($slides as $slide) {
			$block = Mage::app()->getLayout()->createBlock(
				'Ereactor_Slider_Block_Slide',
				'slide',
				array('slide' => $slide)
			);
			$slidesHtml[] = $block->toHtml();
		}
		
		$this->setSlides($slidesHtml);
		
		if (!self::$_includedScripts) {
			$this->setIncludeScripts(true);
			self::$_includedScripts = true;
		}
		
		return parent::_toHtml();
	}
}