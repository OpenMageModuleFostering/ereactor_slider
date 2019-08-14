<?php
$installer = $this;
$db = $installer->getConnection();

$slideshowTableName = $installer->getTable('ereactor_slider/slideshow');
$slideshowTableExists = $db->showTableStatus($slideshowTableName);

if ($slideshowTableExists === false) {
	$slideshowTable = $db->newTable($slideshowTableName)
		->addColumn('slideshow_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'auto_increment' => true,
			'unsigned' => true,
			'identity' => true,
			'nullable' => false,
			'primary' => true,
		), 'Slideshow id')
		->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
			'nullable' => true,
			'default' => null,
		), 'Name')
		->addColumn('is_published', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
			'unsigned' => true,
			'nullable' => false,
			'default' => 1,
		), 'Is published')
		->addColumn('type', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
			'unsigned' => true,
			'nullable' => false,
			'default' => 0,
		), 'Type')
		->addColumn('theme', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
			'nullable' => false,
			'default' => 'default',
		), 'Theme')
		->addColumn('width', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
			'unsigned' => true,
			'nullable' => false,
			'default' => 600,
		), 'Width')
		->addColumn('width_unit', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
			'unsigned' => true,
			'nullable' => false,
			'default' => 0,
		), 'Width unit')
		->addColumn('height', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
			'unsigned' => true,
			'nullable' => false,
			'default' => 100,
		), 'Height')
		->addColumn('height_unit', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
			'unsigned' => true,
			'nullable' => false,
			'default' => 0,
		), 'Height unit')
		->addColumn('javascript', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
			'nullable' => true,
			'default' => null,
		), 'Javascript parameters')
		->addColumn('css', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
			'nullable' => true,
			'default' => null,
		), 'CSS parameters');
	$db->createTable($slideshowTable);

	// Create the sample slideshow
	$slideshow = Mage::getModel('ereactor_slider/slideshow');
	$slideshow->setName('Sample slider');
	$slideshow->setWidth(685);
	$slideshow->setHeight(257);
	$slideshow->setJavascript('{"show_arrows":"1","show_buttons":"1","show_buttons_overlay":"0","pause_on_hover":"1","manual_advance":"0","autoplay_interval":"5000","transition_type":"random","transition_time":"400"}');
	try{
		$slideshow->save();
		$slideshowId = $slideshow->getId();
	} catch (Exception $e) {
		$slideshowId = null;
	}
}

$slideTableName = $installer->getTable('ereactor_slider/slide');
$slideTableExists = $db->showTableStatus($slideTableName);

if ($slideTableExists === false) {
	$slideTable = $db->newTable($slideTableName)
		->addColumn('slide_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'auto_increment' => true,
			'unsigned' => true,
			'identity' => true,
			'nullable' => false,
			'primary' => true,
		), 'Slide id')
		->addColumn('slideshow_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'unsigned' => true,
			'nullable' => false,
		), 'Slideshow id')
		->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
			'nullable' => true,
			'default' => null,
		), 'Name')
		->addColumn('is_published', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
			'unsigned' => true,
			'nullable' => false,
			'default' => 1,
		), 'Is published')
		->addColumn('caption', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
			'nullable' => true,
			'default' => null,
		), 'Caption')
		->addColumn('type', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
			'unsigned' => true,
			'nullable' => false,
			'default' => 0,
		), 'Type')
		->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
			'nullable' => true,
			'default' => null,
		), 'Content')
		->addColumn('icon', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
			'nullable' => true,
			'default' => null,
		), 'Icon')
		->addColumn('slide_order', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
			'unsigned' => true,
			'nullable' => false,
			'default' => 0
		), 'Order')
		->addForeignKey(
			'FK_SLIDE_RELATION_SLIDESHOW',
			'slideshow_id',
			$installer->getTable('ereactor_slider/slideshow'),
			'slideshow_id',
			'cascade',
			'cascade')
		->addIndex(
			'IDX_ORDER',
			array('slideshow_id', 'slide_order')
		);
	$db->createTable($slideTable);

	// Create the sample slides if possible
	if (isset($slideshowId)) {
		$db->insertArray($slideTableName, array('slide_id', 'slideshow_id', 'type', 'slide_order', 'name', 'caption', 'content'), array(
			array(
				1, $slideshowId, Ereactor_Slider_Model_Slide::TYPE_IMAGE, 1,
				'Woman on beach',
				'Display large, stunning images anywhere on your website.',
				'{"image_url":"\/media\/wysiwyg\/\/sample-slideshow\/womanonbeach.jpg","image_link":"","image_target":"_blank","image_follow":"follow","html_html":"","product_id":""}',
			),
			array(
				2, $slideshowId, Ereactor_Slider_Model_Slide::TYPE_IMAGE, 2,
				'Coffee cup',
				'You can make the images link to other pages.',
				'{"image_url":"\/media\/wysiwyg\/sample-slideshow\/coffeecup.jpg","image_link":"https:\/\/en.wikipedia.org\/wiki\/Coffee","image_target":"_blank","image_follow":"nofollow","html_html":"","product_id":""}',
			),
			array(
				3, $slideshowId, Ereactor_Slider_Model_Slide::TYPE_HTML, 3,
				'Padlock',
				'And you can still include a caption if you need one.',
				'{"image_url":"","image_link":"","image_target":"_blank","image_follow":"follow","html_html":"<img src=\"http:\/\/magentoslider.dk\/media\/wysiwyg\/sample-slideshow\/lock.jpg\" alt=\"Padlock\" \/>\n<div style=\"bottom:50px; left:20px; width: 200px; background:rgb(0,0,0); background:rgba(0,0,0,0.8); color:#fff; padding:20px; font-size:20px; line-height: 24px;\">With HTML slides, you can position content in any way you want!<\/div>","product_id":""}',
			),
			array(
				4, $slideshowId, Ereactor_Slider_Model_Slide::TYPE_HTML, 4,
				'More HTML',
				'',
				'{"image_url":"","image_link":"","image_target":"_blank","image_follow":"follow","html_html":"<div style=\"width: 100%; height: 100%; background: #6af;\nbackground: -moz-linear-gradient(-45deg, #6af 0%, #fff 100%);\nbackground: -webkit-gradient(linear, left top, right bottom, color-stop(0%,#6af), color-stop(100%,#fff));\nbackground: -webkit-linear-gradient(-45deg, #6af 0%,#fff 100%);\nbackground: -o-linear-gradient(-45deg, #6af 0%,#fff 100%);\nbackground: -ms-linear-gradient(-45deg, #6af 0%,#fff 100%);\nbackground: linear-gradient(135deg, #6af 0%,#fff 100%);\nfilter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'#6af\', endColorstr=\'#fff\',GradientType=1 );\">\n<div style=\"top:15%; left:50%; margin-left:-300px; width:600px; font-size:20px; line-height:24px;\">\nYou can include any HTML elements you want, such as <a href=\"#\">a link<\/a>, <button type=\"button\">a button<\/button>, <input type=\"text\" value=\"Editable form elements\" style=\"width:300px;\" \/>, or even more images.<\/div>\n<div style=\"top: 45%; width:100%; text-align:center;\">\n<img src=\"http:\/\/lorempixel.com\/g\/180\/100\/abstract\" alt=\"Sample image\" style=\"border: 1px solid #000974;\" \/>&nbsp;&nbsp;\n<img src=\"http:\/\/lorempixel.com\/230\/100\/abstract\" alt=\"Sample image\" style=\"border: 1px solid #000974;\" \/>&nbsp;&nbsp;\n<img src=\"http:\/\/lorempixel.com\/g\/200\/100\/abstract\" alt=\"Sample image\" style=\"border: 1px solid #000974;\" \/><\/div>\n<\/div>","product_id":""}',
			),
		));
	}
}