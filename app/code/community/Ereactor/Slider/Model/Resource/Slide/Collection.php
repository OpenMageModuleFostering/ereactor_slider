<?php

class Ereactor_Slider_Model_Resource_Slide_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	protected function _construct()
	{
		$this->_init('ereactor_slider/slide');
	}
	
	public function getNextOrder($slideshow_id)
	{
		$this->getSelect()
			->reset(Zend_Db_Select::COLUMNS)
			->columns('MAX(slide_order) AS max_order')
			->where('slideshow_id = ?', $slideshow_id);
		return $this->fetchItem()->max_order + 1;
	}
}