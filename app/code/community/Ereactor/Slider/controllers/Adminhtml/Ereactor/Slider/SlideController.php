<?php

class Ereactor_Slider_Adminhtml_Ereactor_Slider_SlideController
	extends Mage_Adminhtml_Controller_Action
{
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
	
	protected function _initSlide()
	{
		$slideId = (int)$this->getRequest()->getParam('slide_id');
		if (empty($slideId)) {
			return;
		}
		
		$slide = Mage::getModel('ereactor_slider/slide')->load($slideId);
		if (empty($slide) || !$slide->getId()) {
			return;
		}
		
		Mage::register('slide', $slide);
		$this->getRequest()->setParam('slideshow_id', $slide->slideshow_id);
		$this->_initSlideshow();
	}
	
	public function batchAddImagesFormAction()
	{
		$this->_initSlideshow();
		$this->loadLayout('popup');
		$this->renderLayout();
	}
	
	public function batchAddAction() {
		$data = $this->getRequest()->getParam('generate');
		$this->getRequest()->setParam('slideshow_id', $data['slideshow_id']);
		$this->_initSlideshow();
		$slides = array();
		$slides = Ereactor_Slider_Model_Slide::generateImageSlides($data['path']);
		$totalSlides = count($slides);
		$goodSlides = 0;
		$slideNames = array();
		foreach ($slides as $slide) {
			$success = true;
			try {
				$slide->save();
			} catch ( Exception $e ) {
				$success = false;
			}
			if ($success) {
				++$goodSlides;
				$slideNames[] = $slide->getName();
			}
		}
		
		$slideNames = implode(', ', $slideNames);
		if ($goodSlides === 0) {
			$this->_getSession()->addError(Mage::helper('slider')->__(
				'Found %d %s but could not generate any slides.', $totalSlides, $source
			));
		} elseif( $goodSlides !== $totalSlides ) {
			$this->_getSession()->addWarning(Mage::helper('slider')->__(
				'Found %d %s but only %d slides could be created: %s.', $totalSlides, $source, $goodSlides, $slideNames
			));
		} else {
			$this->_getSession()->addSuccess(Mage::helper('slider')->__(
				'Successfully generated slides from %d %s: %s.', $totalSlides, $source, $slideNames
			));
		}
	}
	
	public function previewAction() {
		$data = $this->getRequest()->getPost();
		if (empty($data)) {
			return;
		}
		$data = $this->_filterPostData($data);
		
		$slide = Mage::getModel('ereactor_slider/slide')->addData( $data );
		Mage::register('slide',  $slide);
		$slideshow = Mage::getModel('ereactor_slider/slideshow')->load((int)$slide->slideshow_id);
		Mage::register('slideshow', $slideshow);
		
		$this->loadLayout('popup')->renderLayout();
	}
	
	public function editAction() {
		$this->_initSlide();
		$this->loadLayout('popup')->renderLayout();
	}
	
	public function saveAction(){
		$helper = Mage::helper('slider');
		$redirectPath = '*/*';
		$redirectParams = array();
		
		$data = $this->getRequest()->getPost();
		if (empty($data)) {
			return;
		}
		$data = $this->_filterPostData($data);
		$model = Mage::getModel('ereactor_slider/slide');
		
		$slideId = isset($data['slide_id']) ? (int)$data['slide_id'] : null;
		if ($slideId) {
			$model->load($slideId);
		}
		$model->addData($data);
		
		$success = true;
		try {
			$model->save();
			$this->_getSession()->addSuccess($helper->__('The slide has been saved.'));
			
			if ($this->getRequest()->getParam('back')) {
				$redirectPath = '*/*/edit';
				$redirectParams = array('slide_id' => $model->getId());
			}
		} catch (Mage_Core_Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		} catch (Exception $e) {
			$this->_getSession()->addException($e, $helper->__('An error occured while saving the slide.'));
		}
	}
	
	public function deleteAction()
	{
		$this->_slideCallbackOperation('delete', null, 'The slide has been deleted.', 'An error occured while deleting the slide.');
	}
	
	public function moveUpAction()
	{
		$this->_slideMoveOperation(-1);
	}
	
	public function moveDownAction()
	{
		$this->_slideMoveOperation(1);
	}
	
	protected function _slideMoveOperation($offset)
	{
		return $this->_slideCallbackOperation('move', $offset, 'The slide has been moved.', 'An error occured while moving the slide.');
	}
	
	protected function _slideCallbackOperation($callback, $callbackParam, $successMessage, $exceptionMessage, $invalidMessage = 'Unable to find the slide.')
	{
		$helper = Mage::helper('slider');
		$slideId = (int)$this->getRequest()->getParam('slide_id');
		
		try {
			if (empty($slideId)) {
				Mage::throwException($helper->__($invalidMessage));
			}
		
			$model = Mage::getModel('ereactor_slider/slide')->load($slideId);
			if (!$model->getId()) {
				Mage::throwException($helper->__($invalidMessage));
			}
			
			// Originally there was an anonymous function here as a callback
			// I switched to call_user_func for compatibility reasons
			if (isset($callbackParam)) {
				call_user_func(array($model, $callback), $callbackParam);
			} else {
				call_user_func(array($model, $callback));
			}
			
			$this->_getSession()->addSuccess($helper->__($successMessage));
		} catch (Mage_Core_Exception $e) {
			$this->_getSession()->addError($e->getMessage());
			return false;
		} catch (Exception $e) {
			$this->_getSession()->addException($e, $helper->__($exceptionMessage));
			return false;
		}
		
		return true;
	}
	
	public function gridAction()
	{
		$this->_initSlideshow();
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
		// Slide fields are inside a slide[ ] array
		$data = $data['slide'];
		
		// Make sure this is a valid slide type
		if (!isset(Ereactor_Slider_Model_Slide::$slideTypes[$data['type']])) {
			$data['type'] = Ereactor_Slider_Model_Slide::TYPE_IMAGE;
		}
		
		// Build the JSON content
		$data['content'] = Mage::helper('slider')->extractJsonFields($data, array(
			'image_url', 'image_link', 'image_target', 'image_follow', 'html_html',
		));
		return $data;
	}
}