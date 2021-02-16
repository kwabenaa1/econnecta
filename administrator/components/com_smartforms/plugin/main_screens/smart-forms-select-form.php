<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
require_once(SMART_FORMS_PUBLIC_DIR.'smart-forms-bootstrap.php');
if ( !JFactory::getUser()->authorise('core.manage', 'com_smartforms'))
{
    return JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'));
}

echo '<div class="bootstrap-wrapper">';


if(JFactory::getApplication()->input->get('act','','raw')=='deleteform'&&JFactory::getApplication()->input->get('formid','','raw')!='')
{
    $formId=JFactory::getApplication()->input->get('formid','','raw');
    $db=JFactory::getDBO();
    $db->setQuery('delete from #__smartforms where form_id='.$db->quote($formId));
    $db->execute();

}
$bar= JToolBar::getInstance( 'toolbar' );
$bar->appendButton('Custom','<a href="index.php?option=com_smartforms&act=createform" class="btn btn-default btn-success" ><span class="glyphicon glyphicon-plus" ></span>Create New Form</a>', 'custom');

$db = JFactory::getDBO();
$db->setQuery("SELECT form_id,form_name FROM #__smartforms ");
$rows = $db->loadAssocList();
if ($db->getErrorNum())	{echo $db->stderr();return false;}

?>
<table class="table table-striped">
    <thead>
        <th class="jtable-column-header jtable-column-header-sortable">
            <span class="jtable-column-header-text">ID</span>
        </th>
        <th class="jtable-column-header jtable-column-header-sortable">
            <span class="jtable-column-header-text">Form Name</span>
        </th>
    </thead>
    <tbody>
    <?php
        if(count($rows)==0)
        {
            echo '<tr><td colspan="3">There are no forms yet! Please create one.</td></tr>';
        }else{
            foreach($rows as $formItem)
            {
                echo '<tr>';
                echo '<td>';
                echo '<a href="javascript:window.parent.jSelectChart('.$formItem['form_id'].',\''.htmlspecialchars($formItem['form_name']).'\')">'. htmlspecialchars($formItem['form_id']).'</a>';
                echo '</td>';
                echo '<td>';
                echo '<a href="javascript:window.parent.jSelectChart('.$formItem['form_id'].',\''.htmlspecialchars($formItem['form_name']).'\')">'. htmlspecialchars($formItem['form_name']).'</a>';
                echo '</td>';
                echo '</tr>';

            }
        }


    ?>
    </tbody>
</table>

</div>

<style type="text/css">
    .deleteForm:hover{
        color:red;
    }
</style>


<script type="application/javascript">
    rnJQuery(function(){
        rnJQuery('.deleteForm').click(function(){
            if(confirm('Are you sure you want to delete this form?'))
            {
                var formId=rnJQuery(this).data('formid');
                document.location='index.php?option=com_smartforms&act=deleteform&formid='+formId;
            }
        })
    });
</script>