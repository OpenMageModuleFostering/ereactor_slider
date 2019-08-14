<?php

class Ereactor_Slider_Adminhtml_Ereactor_SliderController
	extends Mage_adminhtml_Controller_Action
{
	protected function _initAction()
	{
		$helper = Mage::helper('slider');
		
		// Check if the license key is missing
		$licenseKey = Mage::getStoreConfig('ereactor_slider/activation/license_key');
		if (empty($licenseKey)) {
			$helper->noLicenseKey();
		}
		
		$this->loadLayout()
			->_setActiveMenu('cms/ereactor_slider')
			->_addBreadcrumb($helper->__('E-reactor Slider'), $helper->__('E-reactor Slider'))
			->_addBreadcrumb($helper->__('Slideshows'), $helper->__('Slideshows'));
		return $this;
	}
	
	protected function _initSlideshow()
	{
		$slideshowId = (int)$this->getRequest()->getParam('slideshow_id');
		if (empty($slideshowId)) {
			return;
		}
		
		$slideshow = Mage::getModel('ereactor_slider/slideshow')->load($slideshowId);
		if (empty($slideshow) || !$slideshow->getId()) {
			return;
		}
		
		Mage::register('slideshow', $slideshow);
	}
	
	public function indexAction()
	{
		$this->_title($this->__('Slideshows'))
			->_title($this->__('E-reactor slider'));
		
		$this->_initAction();
		$this->renderLayout();
	}
	
	public function newAction()
	{
		$this->_forward('edit');
	}
	
	public function editAction()
	{
		$helper = Mage::helper('slider');
		$this->_title($this->__('Slideshows'))
			->_title($this->__('E-reactor slider'));
		
		$model = Mage::getModel('ereactor_slider/slideshow');
		$slideshowId = $this->getRequest()->getParam('slideshow_id');
		if ($slideshowId) {
			$model->load($slideshowId);
			
			if (!$model->getId()) {
				$this->_getSession()->addError($helper->__('The slideshow does not exist.'));
				return $this->_redirect('*/*/');
			}
			
			$this->_title($model->getName());
			$breadCrumb = $helper->__('Edit slideshow');
		} else {
			$this->_title($helper->__('New slideshow'));
			$breadCrumb = $helper->__('New slideshow');
		}
		
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if (!empty($data)) {
			$model->addData(true);
		}
		
		Mage::register('slideshow', $model);
		
		$this->_initAction()->_addBreadcrumb($breadCrumb, $breadCrumb);
		$this->renderLayout();
	}
	
	public function saveAction()
	{
		$helper = Mage::helper('slider');
		$redirectPath = '*/*';
		$redirectParams = array();
		
		$data = $this->getRequest()->getPost();
		if ($data) {
			$data = $this->_filterPostData($data);
			$model = Mage::getModel('ereactor_slider/slideshow');
			
			$slideshowId = $this->getRequest()->getParam('slideshow_id');
			if ($slideshowId) {
				$model->load($slideshowId);
			}
			$model->addData($data);
			
			$hasError = false;
			try {
				$model->save();
				$this->_getSession()->addSuccess($helper->__('The slideshow has been saved.'));
				
				if ($this->getRequest()->getParam('back')) {
					$redirectPath = '*/*/edit';
					$redirectParams = array('slideshow_id' => $model->getId());
				}
			} catch (Mage_Core_Exception $e) {
				$hasError = true;
				$this->_getSession()->addError($e->getMessage());
			} catch (Exception $e) {
				$hasError = true;
				$this->_getSession()->addException($e, $helper->__('An error occured while saving the slideshow.'));
			}
			
			if ($hasError) {
				$this->_getSession()->setFormData($data);
				$redirectPath = '*/*/edit';
				$redirectParams = array('slideshow_id' => $this->getRequest()->getParam('slideshow_id'));
			}
		}
		
		$this->_redirect($redirectPath, $redirectParams);
	}
	
	public function deleteAction()
	{
		$helper = Mage::helper('slider');
		$itemId = $this->getRequest()->getParam('slideshow_id');
		if ($itemId) {
			try {
				$model = Mage::getModel('ereactor_slider/slideshow');
				$model->load($itemId);
				if (!$model->getId()) {
					Mage::throwException($helper->__('Unable to find the slideshow.'));
				}
				$model->delete();
				
				$this->_getSession()->addSuccess($helper->__('The slideshow has been deleted.'));
			} catch (Mage_Core_Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			} catch (Exception $e) {
				$this->_getSession()->addException($e, $helper->__('An error occured while deleting the slideshow.'));
			}
		}
		
		$this->redirect('*/*/');
	}
	
	public function gridAction()
	{
		$this->loadLayout()->renderLayout();
	}
	
	protected function _isAllowed()
	{
		$permissionName = '';
		switch ($this->getRequest()->getActionName()) {
			case 'new':
			case 'save':
				$permissionName = 'save';
				break;
			case 'delete':
				$permissionName = 'delete';
				break;
		}
		
		return Mage::getSingleton('Admin/session')->isAllowed('slideshow/manage' . ( $permissionName ? '/' . $permissionName : '' ));
	}
	
	protected function _filterPostData($data)
	{
		// The transition type must be one of the supported values
		if (!isset(Ereactor_Slider_Model_Slideshow::$transitionTypes[$data['transition_type']])) {
			$data['transition_type'] = 'slideInLeft';
		}
		$data['javascript'] = Mage::helper('slider')->extractJsonFields($data, array(
			'show_arrows', 'show_buttons', 'show_buttons_overlay', 'mouse_scroll', 'keyboard_scroll', 'pause_on_hover',
			'manual_advance', 'autoplay_interval', 'transition_type', 'transition_time', /*'transition_easing'*/
		));
		
		return $data;
	}
}