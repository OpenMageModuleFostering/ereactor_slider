<?php

class Ereactor_Slider_Block_Adminhtml_Slideshow_Edit_Tab_General
	extends Mage_adminhtml_Block_Widget_Form
	implements Mage_adminhtml_Block_Widget_Tab_Interface
{
	private static $defaultValues = array(
	// Base default values
		'type'					=> 0,
		'theme'					=> 'default',
		'width'					=> 600,
		'width_unit'			=> 'px',
		'height'				=> 200,
		'height_unit'			=> 'px',
	// Javascript default values
		'show_arrows'			=> 1,
		'show_buttons'			=> 1,
		'show_buttons_overlay'	=> 0,
		'mouse_scroll'			=> 1,
		'keyboard_scroll'		=> 1,
		'pause_on_hover'		=> 0,
		'manual_advance'		=> 0,
		//'autoplay_mode'			=> 3,
		'autoplay_interval'		=> 5000,
		'transition_type'		=> 'slideInLeft',
		'transition_time'		=> 400,
		'transition_easing'		=> 0,
	);
	
	private static $units = array(
		0 => 'px',
		1 => '%',
	);

	protected function _prepareForm()
	{
		$this->getParentBlock()->setActiveTab('general');
		$model = Mage::helper('slider')->getSlideshowInstance();
		$isElementDisabled = ! Mage::helper('slider/Admin')->isActionAllowed('save');
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('slideshow_general_');
		$formData = $model->getData();
		$formData = array_merge(self::$defaultValues, $formData);
		
		$fieldset = $form->addFieldset('base_fieldset', array(
			'legend' => $this->__('Slideshow info')
		));
		
		if ($model->getId()) {
			$fieldset->addField('slideshow_id', 'hidden', array(
				'name' => 'slideshow_id'
			));
		}
		
		$fieldset->addField('name', 'text', array(
			'name'		=> 'name',
			'label'		=> $this->__('Title'),
			'title'		=> $this->__('Title'),
			'required'	=> true,
			'disabled'	=> $isElementDisabled,
		));
		
		/*$fieldset->addField('type', 'select', array(
			'name'		=> 'type',
			'label'		=> $this->__('Type'),
			'title'		=> $this->__('Type'),
			'values'	=> $model->getSlideshowTypes(),
		));*/
		
		/*$fieldset->addField('theme', 'text', array(
			'name'		=> 'theme',
			'label'		=> $this->__('Theme'),
			'title'		=> $this->__('Theme'),
		));*/
		
		$stretchButton = $this->getLayout()->createBlock('adminhtml/widget_button')
			->setType('button')
			->setId('slideshow_general_stretch_width')
			->setLabel($this->__('Stretch'));
		
		$fieldset->addField('width', 'text', array(
			'name'		=> 'width',
			'label'		=> $this->__('Width'),
			'title'		=> $this->__('Width'),
			'style'		=> 'width: 50px;',
			'after_element_html' => $this->_prepareUnitSelect('width_unit', 'Width unit', $formData['width_unit']) . ' ' . $stretchButton->toHtml(),
		));
		
		$fieldset->addField('height', 'text', array(
			'name'		=> 'height',
			'label'		=> $this->__('Height'),
			'title'		=> $this->__('Height'),
			'style'		=> 'width: 50px;','after_element_html' => $this->_prepareUnitSelect('height_unit', 'Height unit', $formData['height_unit']),
		));
		
		$javascript = json_decode($model->getJavascript(), true);
		$this->_prepareControlFields($form, $javascript);
		$this->_prepareTransitionFields($form, $javascript);
		
		Mage::dispatchEvent('adminhtml_slideshow_edit_tab_main_prepare_form', array('form' => $form));
		
		$form->setValues($formData);
		$this->setForm($form);
		
		return parent::_prepareForm();
	}
	
	protected function _prepareUnitSelect($name, $title, $value)
	{
		$html = '<select id="slideshow_general_' . $name . '" name="' . $name . '" title="' . $title . '" class="select" style="width:40px;">';
		foreach (self::$units as $key => $unit) {
			$html .= '<option value="' . $key . '" ' . ($key == $value ? 'selected="selected"' : '') . '>' . $unit . '</option>';
		}
		$html .= '</select>';
		return $html;
	}
	
	protected function _prepareControlFields($form, $js)
	{
		$fieldset = $form->addFieldset('javascript_fieldset', array(
			'legend' => $this->__('Slideshow controls')
		));
		
		$fieldset->addField('show_arrows', 'radios', array(
			'name'		=> 'show_arrows',
			'label'		=> $this->__('Show navigation arrows?'),
			'values'	=> array(
				array('value' => 1, 'label' => $this->__('Yes')),
				array('value' => 0, 'label' => $this->__('No')),
			),
			'after_element_html' => '<p class="note">' . $this->__('These are the arrows you use to move left/right between slides') . '</p>',
		));
		
		$fieldset->addField('show_buttons', 'radios', array(
			'name'		=> 'show_buttons',
			'label'		=> $this->__('Show navigation buttons?'),
			'values'	=> array(
				array('value' => 1, 'label' => $this->__('Yes')),
				array('value' => 0, 'label' => $this->__('No')),
			),
			'after_element_html' => '<p class="note">' . $this->__('These are the buttons you use to jump between slides') . '</p>',
		));
		
		$fieldset->addField('show_buttons_overlay', 'radios', array(
			'name'		=> 'show_buttons_overlay',
			'label'		=> $this->__('Show navigation buttons as an overlay?'),
			'values'	=> array(
				array('value' => 1, 'label' => $this->__('Yes')),
				array('value' => 0, 'label' => $this->__('No')),
			),
			'after_element_html' => '<p class="note">' . $this->__('When enabled, the buttons will appear over the slideshow; when disabled, they will appear below.') . '</p>',
		));
		
		/*$fieldset->addField('mouse_scroll', 'radios', array(
			'name'		=> 'mouse_scroll',
			'label'		=> $this->__('Enable mouse wheel scroll?'),
			'values'	=> array(
				array('value' => 1, 'label' => $this->__('Yes')),
				array('value' => 0, 'label' => $this->__('No')),
			),
		));
		
		$fieldset->addField('keyboard_scroll', 'radios', array(
			'name'		=> 'keyboard_scroll',
			'label'		=> $this->__('Enable keyboard scroll?'),
			'values'	=> array(
				array('value' => 1, 'label' => $this->__('Yes')),
				array('value' => 0, 'label' => $this->__('No')),
			),
		));*/
		
		$fieldset->addField('pause_on_hover', 'radios', array(
			'name'		=> 'pause_on_hover',
			'label'		=> $this->__('Pause slideshow on mouseover'),
			'values'	=> array(
				array('value' => 1, 'label' => $this->__('Yes')),
				array('value' => 0, 'label' => $this->__('No')),
			),
		));
	}
	
	protected function _prepareTransitionFields($form, $js) {
		$fieldset = $form->addFieldset('transition_fieldset', array(
			'legend' => $this->__('Slide transitions')
		));
		
		$fieldset->addField('manual_advance', 'radios', array(
			'name'		=> 'manual_advance',
			'label'		=> $this->__('Play automatically?'),
			'values'	=> array(
				array('value' => 0, 'label' => $this->__('Yes')),
				array('value' => 1, 'label' => $this->__('No')),
			),
		));
		
		$fieldset->addField('autoplay_interval', 'text', array(
			'name'		=> 'autoplay_interval',
			'label'		=> $this->__('Time between slides'),
			'style'		=> 'width: 50px;',
			'after_element_html' => $this->__('milliseconds') . ' <p class="note">' . $this->__('Not available if autoplay is disabled.') . '<br />' . $this->__('Values under 50 miliseconds are ignored.') . '</p>',
		));
		
		$fieldset->addField('transition_type', 'select', array(
			'name'		=> 'transition_type',
			'label'		=> $this->__('Slide transition'),
			'values'	=> Ereactor_Slider_Model_Slideshow::$transitionTypes,
			'after_element_html' => '<p class="note">' . $this->__('For Product and HTML slides, only Slide and Fade are available.') . '</p>',
		));
		
		$fieldset->addField('transition_time', 'text', array(
			'name'		=> 'transition_time',
			'label'		=> $this->__('Transition time'),
			'style'		=> 'width: 50px;',
			'after_element_html' => $this->__('milliseconds') . ' <p class="note">' . $this->__('Values under 50 miliseconds are ignored.') . '</p>',
		));
		
		/*$fieldset->addField('transition_easing', 'select', array(
			'name'		=> 'transition_easing',
			'label'		=> $this->__('Transition easing'),
			'values'	=> array(
				0 => $this->__('Linear'),
				1 => $this->__('Cubic'),
				2 => $this->__('Sine'),
			),
		));*/
	}
	
	public function getTabLabel()
	{
		return $this->__('General');
	}
	
	public function getTabTitle()
	{
		return $this->__('General');
	}
	
	public function canShowTab()
	{
		return true;
	}
	
	public function isHidden()
	{
		return false;
	}
}