<?php
class Ereactor_Slider_Block_Adminhtml_Slide_Grid
	extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
	
		$this->setId('slideGrid');
		$this->setUseAjax(true); // We need updating through AJAX
		$this->setDefaultSort('slide_order'); // Show the slides in order
		$this->setDefaultDir('ASC');
		$this->setFilterVisibility(false); // No filtering
		$this->setPagerVisibility(false); // No pagination
		$this->setSaveParametersInSession(false);  //Don't save paramters in session or else it creates problems
		
		// Javascript parameters
		$this->setRowClickCallback(false);
	}
	
	protected function _prepareCollection()
	{
		$slideshow = Mage::registry('slideshow');
		$collection = Mage::getModel('ereactor_slider/slide')->getCollection()->addFilter('slideshow_id', $slideshow->getId());
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	protected function _prepareColumns()
	{
		$this->addColumn('position', array(
            'header'	=> $this->__('ID'),
            'index'		=> 'slide_id',
			'width'		=> '50px',
        ));
		
		$this->addColumn('name', array(
			'header'	=> $this->__('Name'),
			'index'		=> 'name',
		));
		
		$this->addColumn('caption', array(
			'header'	=> $this->__('Caption'),
			'index'		=> 'caption',
		));
		
		$this->addColumn('slide_order', array(
			'header'	=> $this->__('Order'),
			'index'		=> 'slide_order',
			'width'		=> '50px',
		));
		
		$this->addColumn('order_actions', array(
			'header'	=> $this->__('Move'),
			'width'		=> '50px',
			'type'		=> 'action',
			'getter'	=> 'getId',
			'actions'	=> array(
				array(
					'caption' => '',
					'icon' => '/skin/adminhtml/default/default/images/sort-arrow-up.png',
					'url' => array( 'base' => '*/ereactor_slider_slide/moveUp' ),
					'ajax' => true,
					'field' => 'slide_id',
				),
				array(
					'caption' => '',
					'icon' => '/skin/adminhtml/default/default/images/sort-arrow-down.png',
					'url' => array( 'base' => '*/ereactor_slider_slide/moveDown' ),
					'ajax' => true,
					'field' => 'slide_id',
				),
			),
			'sortable'	=> false,
			'renderer'	=> 'Ereactor_Slider_Block_Adminhtml_Widget_Grid_Column_Renderer_Slideaction',
		));
		
		$this->addColumn('action', array(
			'header'	=> $this->__('Actions'),
			'width'		=> '80px',
			'type'		=> 'action',
			'getter'	=> 'getId',
			'actions'	=> array(
				array(
					'caption' => $this->__('Edit'),
					'url' => array( 'base' => '*/ereactor_slider_slide/edit' ),
					'popup' => true,
					'field' => 'slide_id',
				),
				array(
					'caption' => $this->__('Delete'),
					'url' => array( 'base' => '*/ereactor_slider_slide/delete' ),
					'ajax' => true,
					'confirm' => $this->__('Are you sure you want to delete this slide?'),
					'field' => 'slide_id',
				),
			),
			'sortable'	=> false,
			'renderer'	=> 'Ereactor_Slider_Block_Adminhtml_Widget_Grid_Column_Renderer_Slideaction',
		));
		
		return parent::_prepareColumns();
	}
	
	public function getRowUrl($row)
	{
		return $this->getUrl('*/ereactor_slider_slide/edit', array('slide_id' => $row->getId()));
	}
	
	public function getGridUrl()
	{
		return $this->getUrl('*/ereactor_slider_slide/grid', array('_current' => true));
	}
}