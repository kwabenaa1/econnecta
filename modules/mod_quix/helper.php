<?php
/**
 * @package		Quix
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
use Joomla\Registry\Registry;
/**
 * Helper for mod_breadcrumbs
 *
 * @since  1.5
 */
class ModQuixHelper
{
	/**
	 * renderShortCode
	 *
	 * @param   \Joomla\Registry\Registry  &$params  module parameters
	 *
	 * @return html
	 */
	public static function renderShortCode(&$params)
	{
		$id = $params->get('id');
		if (!$id) {
		  return '<p>'.JText::_('MOD_QUIX_INVALID_ID').'</p >';
		}

		// Include dependencies
        jimport('quix.vendor.autoload');

        jimport('quix.app.bootstrap');

        $collection = qxGetCollectionInfoById($id);
        if (!$collection) {
          return '<p>invalid quix collection shortcode!</p >';
        }

        $decodedData = json_decode($collection->data, true);

        if( array_key_exists("data", $decodedData) ) {
          if($decodedData["type"] == "layout") $collection->data = json_decode($collection->data, true)['data'];
          else $collection->data = [ json_decode($collection->data, true)['data'] ];
        }

    	JHtml::_('jquery.framework');
        JHtml::_('bootstrap.framework');

        // rander main item
        return quixRenderItem($collection);		    
	}
}
