<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class basic_field_wrapper
{
    public $stringBuilder;
    public $fieldOptions;
    public $entryData;
    public $useTestData;
    function __construct($stringBuilder,$fieldOptions,$entryData,$useTestData)
    {
        $this->StringBuilder=$stringBuilder;
        $this->fieldOptions=$fieldOptions;
        $this->entryData=$entryData;
        $this->useTestData=$useTestData;
    }

    public function __toString()
    {
        if($this->useTestData)
            return 'sample data';
        return $this->StringBuilder->GetStringFromColumn($this->fieldOptions,$this->entryData);
    }


}