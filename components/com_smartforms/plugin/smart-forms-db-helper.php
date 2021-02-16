<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
class SmartFormsDBHelper
{
    public static function Insert($table,$args)
    {
        $db=JFactory::getDBO();

        $columnNames='';
        $values='';

        foreach($args as $columnName=>$value)
        {
            if($columnNames!='')
                $columnNames.=",";

            if($values!='')
                $values.=',';

            $columnNames.=$columnName;
            if(!is_numeric($value))
                $value=$db->quote($value);

            $values.=$value;
        }

        $query="insert into ".$table.'('.$columnNames.')'.' values ('.$values.')';

        $db->setQuery($query);
        $db->execute();
        return $db;


    }

    public static function Update($table,$args,$where)
    {
        $db=JFactory::getDBO();

        $setString='';

        foreach($args as $columnName=>$value)
        {
            if($setString!='')
                $setString.=",";
            if(!is_numeric($value))
                $value=$db->quote($value);

            $setString.=$columnName.'='.$value;
        }

        $whereString='';
        foreach($where as $columnName=>$value)
        {
            if($whereString!='')
                $whereString.=" and ";

            if(!is_numeric($value))
                $value=$db->quote($value);

            $whereString.=$columnName.'='.$value;
        }

        $query="update ".$table." set ".$setString." where ".$whereString;

        $db->setQuery($query);
        $db->execute();
        return $db->getAffectedRows();


    }



}