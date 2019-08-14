<?php
class Ereactor_Slider_Block_Adminhtml_Slideshow_Edit_Tab_Slides
	extends Mage_Adminhtml_Block_Text_List
	implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	public function __construct()
	{
	}
	
	public function getTabLabel()
	{
		return $this->__('Slides');
	}
	
	public function getTabTitle()
	{
		return $this->__('Slides');
	}
	
	public function canShowTab()
	{
		// Only show this tab if the slideshow is already created
		// This is because we need to give a slideshow_id to the slides that we create
		// We can't do that if the slideshow is not saved
		$slideshow = Mage::helper('slider')->getSlideshowInstance();
		return isset($slideshow) && $slideshow->getId();
	}
	
	public function isHidden()
	{
		return false;
	}
}