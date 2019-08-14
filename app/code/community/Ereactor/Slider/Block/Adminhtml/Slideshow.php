<?php

class Ereactor_Slider_Block_Adminhtml_Slideshow extends
	Mage_adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_blockGroup = 'slider';
		$this->_controller = 'adminhtml_slideshow';
		$this->_headerText = 'Manage slideshows';
		
		parent::__construct();
	}
}