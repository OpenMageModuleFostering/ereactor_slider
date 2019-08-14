<?php

class Ereactor_Slider_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $_slideshowInstance;
	protected $_slideInstance;

	public function getSlideshowInstance() {
		if (!$this->_slideshowInstance) {
			$this->_slideshowInstance = Mage::registry('slideshow');
		}
		
		return $this->_slideshowInstance;
	}
	
	public function getSlideInstance() {
		if (!$this->_slideInstance) {
			$this->_slideInstance = Mage::registry('slide');
		}
		
		return $this->_slideInstance;
	}
	
	public function noLicenseKey($errorLevel = 'warning') {
		$url = $this->_getUrl('*/system_config/edit', array( 'section' => 'ereactor_slider' ));
		$errorText = $this->__('You have not yet entered a license key for e-reactor Slider. You must do this before you are able to save any slides.') .
			' <a href="' . $url . '" target="_blank">' . $this->__('Click here') . '</a>' . $this->__(' to enter a license key now.') . '<br />' .
			$this->__('If you do not have a license key yet, you can go to %s and create one.', '<a href="http://shop.e-reactor.dk/licensemanager/license/" target="_blank">http://shop.e-reactor.dk</a>');
		
		if ($errorLevel === 'warning') {
			Mage::getSingleton('core/session')->addWarning($errorText);
		} else {
			Mage::throwException($errorText);
		}
	}
	
	/**
	 * Some form fields don't go straight to the database
	 * Instead, they are placed into a JSON-encoded field
	 * This method extracts those fields from the $data array
	 * and returns them encoded together as JSON
	 * Params:
	 * $data	An array containing the form data (from $_POST)
	 *			$data IS MODIFIED by this method
	 * $fields	An array of fields to extract
	 */
	public function extractJsonFields(&$data, $fields) {
		$result = array();
		foreach ($fields as $field) {
			if (!array_key_exists($field, $data)) {
				continue;
			}
			$result[$field] = $data[$field];
			unset($data[$field]);
		}
		
		return json_encode( $result );
	}
}