<?php
require_once( 'Mage/Adminhtml/controllers/Cms/Wysiwyg/ImagesController.php' );

/**
 * This class is an override of the standard images controller
 * because we need to get the static URL of the selected images
 * instead of the adminhtml URL which is normally returned.
 * This method requires the least amount of core overrides.
 */
class Ereactor_Slider_Adminhtml_Ereactor_Slider_ImagesController
	extends Mage_Adminhtml_Cms_Wysiwyg_ImagesController
{
	/**
	 * Fired when an image is selected
	 */
	public function onInsertAction()
	{
		$helper = Mage::helper('cms/wysiwyg_images');
		$storeId = $this->getRequest()->getParam('store');

		$filename = $this->getRequest()->getParam('filename');
		$filename = $helper->idDecode($filename);
		//$asIs = $this->getRequest()->getParam('as_is');

		Mage::helper('catalog')->setStoreId($storeId);
		$helper->setStoreId($storeId);

		// The actual override - get the static URL of the image instead of the directive URL
		$image = $helper->getCurrentUrl() . $filename;
		$this->getResponse()->setBody($image);
	}
}