<?php

class Ereactor_Slider_Helper_Admin extends Mage_Core_Helper_Abstract
{
	public function isActionAllowed($action)
	{
		return Mage::getSingleton('Admin/session')->isAllowed('slideshow/manage/' . $action);
	}
}