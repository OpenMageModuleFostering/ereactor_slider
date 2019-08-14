<?php
class Ereactor_Slider_Block_Slide
	extends Mage_Core_Block_Template
{
	public function __construct()
	{
		parent::__construct();
		
		// This is always a frontend block, regardless of where it's called from
		// This affects the paths that are generated for the templates
		$this->setData('area', 'frontend');
	}

	protected function _prepareLayout()
	{
		$slide = $this->getSlide();
		$this->setCaption($slide->getCaption());
		
		$slideParams = json_decode($slide->getContent());
		switch ($slide->getType()) {
			case Ereactor_Slider_Model_Slide::TYPE_IMAGE:
				$this->_prepareImage($slideParams);
				break;
			case Ereactor_Slider_Model_Slide::TYPE_HTML:
				$this->_prepareHtml($slideParams);
				break;
		}
		
		return parent::_prepareLayout();
	}
	
	protected function _prepareImage($params)
	{
		$this->setTemplate('ereactor/slider/slide/image.phtml');
		$this->setImageUrl($params->image_url);
		if (!empty($params->image_link)) {
			$this->setImageLink($params->image_link);
			$this->setTarget($params->image_target);
			$this->setFollow($params->image_follow);
		}
	}
	
	protected function _prepareHtml($params)
	{
		$this->setTemplate('ereactor/slider/slide/html.phtml');
		$this->setInnerHtml($params->html_html);
	}
}