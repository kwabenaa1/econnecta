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
$bar->appendButton('Custom','<a href="index.php?option=com_smartforms&act=createform" class="btn btn-info btnImport" ><span class="glyphicon glyphicon-plus" ></span>Import</a>', 'custom');

$db = JFactory::getDBO();
$db->setQuery("SELECT form_id,form_name FROM #__smartforms ");
$rows = $db->loadAssocList();
if ($db->getErrorNum())	{echo $db->stderr();return false;}

?>
<table class="table table-striped">
    <thead>
        <th style="width: 50px;">
            <span class="jtable-column-header-text">Actions</span>
        </th>
        <th class="jtable-column-header jtable-column-header-sortable">
            <span class="jtable-column-header-text">Form Name</span>
        </th>
         <th class="jtable-column-header jtable-column-header-sortable">
            <span class="jtable-column-header-text">Plugin Code (put this in the article)</span>
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
                echo '<td style="text-align: center">';
                echo '<span style="cursor:pointer;" data-formid="'.$formItem['form_id'].'" title="delete" class="glyphicon glyphicon-trash deleteForm"></span>';
                echo '<span style="cursor:pointer;margin-left:5px" data-formid="'.$formItem['form_id'].'" title="export" class="glyphicon gglyphicon glyphicon-export exportForm"></span>';
                echo '</td>';
                echo '<td>';
                echo '<a href="index.php?option=com_smartforms&act=createform&formid='.$formItem['form_id'].'">'. htmlspecialchars($formItem['form_name']).'</a>';
                echo '</td>';
                echo '<td>';
                echo '{smartform '.$formItem['form_id'].'}';
                echo '</td>';
                echo '</tr>';

            }
        }


    ?>
    </tbody>
</table>

</div>;

<style type="text/css">
    .deleteForm:hover{
        color:red;
    }

    .exportForm:hover{
        color:red;
    }
</style>


<form class="formImport"  method="post"  target="_self" action="index.php?option=com_smartforms&act=emaildoctor" enctype="multipart/form-data" style="display: none">
    <input type="hidden" name="act" value="import"/>
    <input type="file" name="file_uploaded" class="rnFileImport" />

</form>

<script type="application/javascript">
    rnJQuery(function(){
        rnJQuery('.deleteForm').click(function(){
            if(confirm('Are you sure you want to delete this form?'))
            {
                var formId=rnJQuery(this).data('formid');
                document.location='index.php?option=com_smartforms&act=deleteform&formid='+formId;
            }
        });


        rnJQuery('.exportForm').click(function(){
            var formId=rnJQuery(this).data('formid');
            document.location='index.php?option=com_smartforms&act=exportform&formid='+formId;
        });
        rnJQuery('.btnImport').click(function(e){
            e.preventDefault();
            rnJQuery('.rnFileImport').click();
        });


        rnJQuery('.rnFileImport').change(function () {
            if(rnJQuery('.rnFileImport')[0].files.length>0)
            {
                rnJQuery('.formImport').submit();
            }
        })
    });





</script>