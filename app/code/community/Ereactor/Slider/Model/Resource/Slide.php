<?php

class Ereactor_Slider_Model_Resource_Slide extends Mage_Core_Model_Resource_Db_Abstract
{
	protected function _construct()
	{
		$this->_init('ereactor_slider/slide', 'slide_id');
	}
}