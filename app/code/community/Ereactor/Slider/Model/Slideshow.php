<?php

class Ereactor_Slider_Model_Slideshow extends Mage_Core_Model_Abstract
{
	public static $units = array(
		0 => 'px',
		1 => '%',
	);
	
	public static $transitionTypes = array (
		'slideInLeft'		=> 'Slide left',
		'slideInRight'		=> 'Slide right',
		'slideInTop'		=> 'Slide top',
		'slideInBottom'		=> 'Slide bottom',
		'fade'				=> 'Fade',
		'fold'				=> 'Fold',
		'sliceDownRight'	=> 'Slice down right',
		'sliceDownLeft'		=> 'Slice down left',
		'sliceUpRight'		=> 'Slice up right',
		'sliceUpLeft'		=> 'Slice up left',
		'sliceUpDown'		=> 'Slice up down',
		'sliceUpDownLeft'	=> 'Slice up down left',
		'boxRandom'			=> 'Random boxes',
		'boxRain'			=> 'Box rain',
		'boxRainReverse'	=> 'Box rain reverse',
		'boxRainGrow'		=> 'Box rain grow',
		'boxRainGrowReverse'=> 'Box rain grow reverse',
		'random'			=> 'Random',
	);
	
	public static $slideshowTypes = array(
		0 => 'Manual',
		1 => 'Automatic',
	);

	protected function _construct()
	{
		$this->_init( 'ereactor_slider/slideshow' );
	}
	
	protected function _beforeSave()
	{
		parent::_beforeSave();
		
		return $this;
	}
	
	public function getTypeName()
	{
		return Mage::helper('slider')->__(self::$slideshowTypes[$this->type]);
	}
	
	public function getSlideshowTypes()
	{
		return self::$slideshowTypes;
	}
	
	public function toOptionArray()
	{
		$results = array();
		foreach ($this->getCollection() as $slideshow)
		{
			$results[$slideshow->slideshow_id] = $slideshow->name;
		}
		return $results;
	}
	
	// An override that extracts the Json fields present in 'javascript'
	public function getData($key = '', $index = null)
	{
		$data = parent::getData($key, $index);
		if (is_array($data) && !empty($data['javascript'])) {
			$data = array_merge($data, json_decode($data['javascript'], true));
		}
		return $data;
	}
	
	public function getNivoParams()
	{
		$params = json_decode($this->javascript, true);
		$nivoParams = array(
			'effect' => $params['transition_type'],
			'animSpeed' => $params['transition_time'] >= 50 ? $params['transition_time'] : 400,
			'pauseTime' => $params['autoplay_interval'] >= 50 ? $params['autoplay_interval'] : 5000,
			'directionNav' => (bool)$params['show_arrows'],
			'controlNav' => (bool)$params['show_buttons'],
			'pauseOnHover' => (bool)$params['pause_on_hover'],
			'manualAdvance' => (bool)$params['manual_advance'],
		);
		if ($params['show_buttons_overlay']) {
			$nivoParams['controlNavClass'] = 'nivo-controlNav-hover';
		}
		return json_encode($nivoParams);
	}
	
	public function getFullWidth()
	{
		if (!isset(self::$units[ $this->width_unit ])) {
			return '';
		}
		return $this->width . self::$units[ $this->width_unit ];
	}
	
	public function getFullHeight()
	{
		if (!isset(self::$units[ $this->height_unit ])) {
			return '';
		}
		return $this->height . self::$units[ $this->height_unit ];
	}
	
	public function getDimensions()
	{
		return 'width:' . $this->getFullWidth() . ';height:' . $this->getFullHeight() . ';';
	}
}