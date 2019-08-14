<?php
class Ereactor_Slider_Block_Adminhtml_Slide_Quickadd
	extends	Mage_Adminhtml_Block_Widget_Form
{	
	protected static $_text = array(
		'add' => array(
			'caption' => 'Add new slide',
			'save_button' => 'Add slide',
			'cancel_button' => 'Cancel',
		),
		
		'edit' => array(
			'caption' => 'Edit slide',
			'save_button' => 'Save changes',
			'cancel_button' => 'Cancel',
		),
	);
	
	protected $_mode = 'add';
	
	protected function _text( $text ) {
		return self::$_text[ $this->_mode ][ $text ];
	}
	
	protected function _prepareForm()
	{
		$helper = Mage::helper('slider');
		$slideshow = $helper->getSlideshowInstance();
		$slide = $helper->getSlideInstance();
		$isEditing = isset($slide) && $slide->getId();
		if ($isEditing) {
			$this->_mode = 'edit';
		}
		$isElementDisabled = ! Mage::helper('slider/Admin')->isActionAllowed('save');
		
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('slideshow_quick' . $this->_mode . '_');
		
		$fieldset = $form->addFieldset('base_fieldset', array(
			'legend' => $this->__($this->_text('caption'))
		));
		
		// General fields
		
		if ($slideshow->getId()) {
			$fieldset->addField('slideshow_id', 'hidden', array(
				'name' => 'slideshow_id',
				'value' => $slideshow->getId(),
			));
		}
		
		if ($isEditing) {
			$fieldset->addField('slide_id', 'hidden', array(
				'name' => 'slide_id',
				'value' => $slide->getId(),
			));
		}
		
		$fieldset->addField('name', 'text', array(
			'name'		=> 'name',
			'label'		=> $this->__('Title'),
			'title'		=> $this->__('Title'),
			//'required'	=> true,
			'disabled'	=> $isElementDisabled,
		));
		
		$fieldset->addField('caption', 'text', array(
			'name'		=> 'caption',
			'label'		=> $this->__('Caption'),
			'title'		=> $this->__('Caption'),
			'disabled'	=> $isElementDisabled,
		));
		
		$slideTypeImages = '';
		foreach (Ereactor_Slider_Model_Slide::$slideTypes as $typeId => $typeLabel) {
			$slideTypeImages .= '<div class="ereactor-slide-type ereactor-slide-t' . $typeId . '" data-value="' . $typeId . '">' . $this->__($typeLabel) . '</div>';
		}
		$slideTypeImages .= '<div class="ereactor-slide-disabled ereactor-slide-t2">' . $this->__('Product') . '</div>
		<p class="note">' . $this->__('Product slides are only available in the PRO version') . '</p>';
		
		$fieldset->addField('type', 'hidden', array(
			'name' => 'type',
			'value' => Ereactor_Slider_Model_Slide::TYPE_IMAGE,
		));
		
		$fieldset->addField('type_images', 'note', array(
			'label'		=> $this->__('Slide layout'),
			'title'		=> $this->__('Slide layout'),
			'text'		=> $slideTypeImages,
		));
		
		$this->_addImageFields($fieldset, $isElementDisabled);
		$this->_addHtmlFields($fieldset, $isElementDisabled);
		
		// Buttons - save and cancel (only if we're editing)
		$fieldset->addField('save', 'note', array(
			'text' =>
				$this->getButtonHtml(Mage::helper('catalog')->__($this->_text('save_button')), '', 'save')
				. ( $isEditing ? '&nbsp;' . $this->getButtonHtml(Mage::helper('catalog')->__($this->_text('cancel_button')), '', 'cancel') : '' )
		));
		
		$form->addFieldNameSuffix('slide');
		Mage::dispatchEvent('adminhtml_slideshow_edit_tab_slides_quick' . $this->_mode . '_prepare_form', array('form' => $form));
		
		if ($isEditing) {
			$data = $slide->getData();
			$form->setValues($data);
		}
		$this->setForm($form);
		
		return parent::_prepareForm();
	}
	
	protected function _addImageFields($fieldset, $isElementDisabled) {
		$imageField = 'field-t' . Ereactor_Slider_Model_Slide::TYPE_IMAGE;
		$chooseButton = $this->getLayout()->createBlock('adminhtml/widget_button')
			->setType('button')
			->setClass('scalable btn-chooser ereactor-image-chooser')
			->setLabel($this->__('Choose...'));
		
		$fieldset->addField('image_url', 'text', array(
			'name'		=> 'image_url',
			'label'		=> $this->__('Image'),
			'title'		=> $this->__('Image'),
			'disabled'	=> $isElementDisabled,
			'class'		=> $imageField,
			'style'		=> 'width: 200px;',
			'after_element_html' => $chooseButton->toHtml(),
		));
		
		$fieldset->addField('image_link', 'text', array(
			'name'		=> 'image_link',
			'label'		=> $this->__('Image link'),
			'title'		=> $this->__('Image link'),
			'disabled'	=> $isElementDisabled,
			'class'		=> $imageField,
		));
		
		$fieldset->addField('image_target', 'select', array(
			'name'		=> 'image_target',
			'label'		=> $this->__('Where to open the link'),
			'title'		=> $this->__('Where to open the link'),
			'values'	=> array(
				'_blank'	=> $this->__('In a new window'),
				'self'		=> $this->__('In the current window'),
			),
			'disabled'	=> $isElementDisabled,
			'class'		=> $imageField,
		));
		
		$fieldset->addField('image_follow', 'select', array(
			'name'		=> 'image_follow',
			'label'		=> $this->__('Link follow'),
			'title'		=> $this->__('Link follow'),
			'style'		=> 'width: 50px;',
			'values'	=> array(
				'follow'	=> $this->__('Yes'),
				'nofollow'	=> $this->__('No'),
			),
			'after_element_html' => '<p class="note">' . $this->__('Should search engines follow the link?') . '</p>',
			'disabled'	=> $isElementDisabled,
			'class'		=> $imageField,
		));
	}
	
	protected function _addHtmlFields($fieldset, $isElementDisabled) {
		$fieldset->addField('html_html', 'textarea', array(
			'name'		=> 'html_html',
			'label'		=> $this->__('Content'),
			'title'		=> $this->__('Content'),
			'disabled'	=> $isElementDisabled,
			'class'		=> 'field-t' . Ereactor_Slider_Model_Slide::TYPE_HTML,
		));
	}
}