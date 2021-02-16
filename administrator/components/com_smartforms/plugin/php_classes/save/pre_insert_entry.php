<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
class PreInsertEntry extends InsertEntryBase{

    Public $ContinueInsertion=true;

    public function __construct(&$formId,&$formEntryData,&$formOptions,&$elementOptions,&$additionalData)
    {
        parent::__construct($formId,$formEntryData,$formOptions,$elementOptions,$additionalData);
    }






}