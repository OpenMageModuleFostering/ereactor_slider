<?php

class Ereactor_Slider_Block_Adminhtml_Slideshow_Grid extends
	Mage_adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('slideshow_list_grid');
		$this->setDefaultSort('slideshow_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
	
	protected function _prepareCollection()
	{
		$collection = Mage::getModel('ereactor_slider/slideshow')->getCollection();
		$collection->getSelect()
			->joinLeft(array('slide' => 'ereactor_slider_slide'),'main_table.slideshow_id=slide.slideshow_id', null)
			->columns('COUNT(*) AS nr_slides')
			->group('main_table.slideshow_id');
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	protected function _prepareColumns()
	{
		$this->addColumn('slideshow_id', array(
			'header'	=> $this->__('ID'),
			'width'		=> '50px',
			'index'		=> 'slideshow_id',
		));
		
		$this->addColumn('name', array(
			'header'	=> $this->__('Name'),
			'index'		=> 'name',
		));
		
		/*$this->addColumn('type', array(
			'header'	=> $this->__('Type'),
			'index'		=> 'type',
		));
		
		$this->addColumn('theme', array(
			'header'	=> $this->__('Theme'),
			'index'		=> 'theme',
		));*/
		
		$this->addColumn('nr_slides', array(
			'header'	=> $this->__('Number of slides'),
			'index'		=> 'nr_slides',
		));
		
		$this->addColumn('action', array(
			'header'	=> $this->__('Action'),
			'width'		=> '100px',
			'type'		=> 'action',
			'getter'	=> 'getId',
			'actions'	=> array(
				array(
					'caption' => $this->__('Edit'),
					'url' => array( 'base' => '*/*/edit' ),
					'field' => 'id'
				),
			),
			'filter'	=> false,
			'sortable'	=> false,
		));
		
		return parent::_prepareColumns();
	}
	
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('slideshow_id' => $row->getId()));
	}
	
	public function getGridUrl()
	{
		return $this->getUrl('*/*/grid', array('_current' => true));
	}
}