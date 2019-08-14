<?php
class Ereactor_Slider_Block_Adminhtml_Widget_Grid_Column_Renderer_Slideaction
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
	/**
     * Renders column
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $actions = $this->getColumn()->getActions();
        if (empty($actions) || !is_array($actions)) {
            return '&nbsp;';
        }

		$out = '';
		
        foreach ($actions as $action) {
			if (is_array($action)) {
				$icon = '';
				if (isset($action['icon'])) {
					$icon = '<img src="' . $action['icon'] . '" />';
					$action['caption'] = '{ICON}' . ( empty( $action['caption'] ) ? '' : '&nbsp;' . $action['caption'] );
					unset($action['icon']);
				}
				$extraClasses = array();
				if (isset($action['popup']) && $action['popup'] === true) {
					$extraClasses[] = 'ereactor-ajax-modal';
					unset($action['popup']);
				} elseif (isset($action['ajax']) && $action['ajax'] === true) {
					$extraClasses[] = 'ereactor-ajax-link';
					unset($action['ajax']);
				}
				if (!empty($action['confirm'])) {
					$extraClasses[] = 'ereactor-confirm';
					$action['data-confirm'] = $action['confirm'];
					unset($action['confirm']);
				}
				$action['class'] = (empty($action['class']) ? '' : $action['class'] . ' ') . implode(' ', $extraClasses);
				$renderedAction = str_replace( '{ICON}', $icon, $this->_toLinkHtml($action, $row) );
				$out .= $renderedAction . '&nbsp;';
			}
		}

        return $out;
    }
}