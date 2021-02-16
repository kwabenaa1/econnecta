<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class selectable_list_wrapper extends basic_field_wrapper
{
    public function RNWasOptionSelected($optionName)
    {
        if(isset($this->entryData['selectedValues']))
        {
            foreach ($this->entryData['selectedValues'] as $selectedValue)
            {
                if ($selectedValue['label'] == html_entity_decode($optionName))
                    return true;
            }
            return false;
        }
        return $this->entryData['value']==html_entity_decode($optionName);


    }



}