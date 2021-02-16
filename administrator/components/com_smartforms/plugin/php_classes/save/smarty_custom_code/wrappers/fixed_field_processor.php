<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class fixed_field_processor
{
    public $match;
    public $entryData;
    public $elementOptions;
    public $useTestData;
    function __construct($match,$entryData,$elementOptions,$useTestData)
    {
        $this->match=$match;
        $this->entryData=$entryData;
        $this->elementOptions=$elementOptions;
        $this->useTestData=$useTestData;
    }

    public function __toString()
    {
        require_once(SMART_FORMS_DIR.'filter_listeners/fixed-field-listeners.php');
        $match=str_replace("'","\"",$this->match);
        $fixedFieldParameters=json_decode($match,true);
        if($fixedFieldParameters==null)
        {

            return '';
        }

        try{
            $value=SmartFormsFilterManager::GetInstance()->ApplyFilter("smart-forms-fixed-field-value-".$fixedFieldParameters["Op"],$fixedFieldParameters,$this->entryData,$this->elementOptions,$this->useTestData);
            if($value==$fixedFieldParameters)
                return "";
            if($value==null)
                $value='';
            return strval($value);
        }catch(Exception $e)
        {

        }

        return "";
    }


}