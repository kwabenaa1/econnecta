<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
if ( !JFactory::getUser()->authorise('core.manage', 'com_smartforms'))
{
    return JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'));
}

if(get_magic_quotes_gpc())
{

}else{
    $data= JFactory::getApplication()->input->get("exportdata",'','raw');
}
$json=json_decode($data,true);
$rows=$json["rowsInfo"];
$headers=$json["headers"];
header('Content-Encoding: UTF-8');
header('Content-type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename=Export.csv');

if(count($rows)<=0)
    die();
$firstColumn=true;
$numberOfColumns=count($rows[0]);
$count=0;
echo '"date"';
foreach($rows[0] as $key=>$value)
{
	$count++;
	if($numberOfColumns==$count)
		break;
    if($firstColumn)
    {
        $firstColumn=false;
        continue;//skip date column
    }
    echo ",";
    $label="";
    if(isset($headers[$key]))
        $label=$headers[$key];
    echo '"'.$label.'"';

}
foreach($rows as $row)
{
    $firstColumn=true;
    echo "\r\n";
	$count=0;
    foreach($row as $column)
    {
		$count++;
		if($numberOfColumns==$count)
			break;
        if(!$firstColumn)
            echo ",";
        $firstColumn=false;
        echo '"'.$column.'"';
    }

}

echo "\xEF\xBB\xBF";

die();



