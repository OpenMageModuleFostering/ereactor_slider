<?php

class Ereactor_Slider_Block_Adminhtml_Slideshow_Edit_Tabs
	extends Mage_adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('page_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('slider')->__('Slideshow info'));
	}
	
	protected function _prepareLayout() {
		$url = array(
			'quickAddSave'		=> $this->getUrl('*/ereactor_slider_slide/save'),
			'productChooser'	=> $this->getUrl('*/ereactor_slider_chooser/product'),
			'productsSchooser'	=> $this->getUrl('*/ereactor_slider_chooser/products'),
			'imageChooser'		=> $this->getUrl('*/cms_wysiwyg_images/index'),
			'imageInsert'		=> $this->getUrl('*/ereactor_slider_images/onInsert'),
			'batchAdd'			=> $this->getUrl('*/ereactor_slider_slide/batchAdd'),
			'slidePreview'		=> $this->getUrl('*/ereactor_slider_slide/preview'),
		);
		$jsBlock = $this->getLayout()
			->createBlock('core/text', 'js_urls')
			->setText('<script type="text/javascript">EREACTOR.SLIDER.url = ' . json_encode( $url ) . ';</script>');
		$this->getLayout()->getBlock('content')->append($jsBlock);
		return parent::_prepareLayout();
    }
}