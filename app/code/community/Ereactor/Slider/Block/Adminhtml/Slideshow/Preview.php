<?php
class Ereactor_Slider_Block_Adminhtml_Slideshow_Preview
	extends Mage_Adminhtml_Block_Template
{
	public function __construct()
	{
		$nivoParams = json_encode(array(
			'effect' => 'slideInLeft',
			'animSpeed' => 400,
			'pauseTime' => 5000,
			'directionNav' => true,
			'controlNav' => true,
			'pauseOnHover' => false,
			'manualAdvance' => false,
		));
		$slideshow = Mage::helper('slider')->getSlideshowInstance();
		if (isset($slideshow) && $slideshow->getId()) {
			$nivoParams = $slideshow->getNivoParams();
		}
		$this->setNivoParams($nivoParams);
	}
}