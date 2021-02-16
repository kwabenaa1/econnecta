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




require_once(SMART_FORMS_PUBLIC_DIR.'smart-forms-bootstrap.php');
require_once(SMART_FORMS_PUBLIC_DIR.'additional_fields/smart-forms-additional-fields-list.php');



$formElementDependencies=array('isolated-slider','smart-forms-form-elements-container');
$formElementDependencies=$additionalFields=SmartFormsFilterManager::GetInstance()->ApplyFilter('smart_forms_add_form_elements_dependencies',$formElementDependencies);
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/licensing/rednao-licensing-manager.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/smart-forms-global-vars.js?path='.JUri::root( false ).'components/com_smartforms/plugin/');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/grid_chart/excanvas.min.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/grid_chart/jquery.jqplot.min.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/grid_chart/jqplot.highlighter.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/grid_chart/jqplot.cursor.min.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/grid_chart/jqplot.dateAxisRenderer.min.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/grid_chart/jqplot.canvasAxisTickRenderer.min.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/grid_chart/jqplot.pointLabels.min.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/container/Container.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/main_screens/smart-forms-entries.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/formelements.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/eventmanager.js');

$fieldsDependencies=array();
$additionalFields=SmartFormsFilterManager::GetInstance()->ApplyFilter('smart_forms_af_names',$fieldsDependencies);
foreach($additionalFields as $field)
{
    SmartFormsActionManager::GetInstance()->DoAction('smart_forms_af_'.$field['id']);
}

SmartFormsActionManager::GetInstance()->DoAction('smart_formsa_include_systemjs');
SmartFormsActionManager::GetInstance()->DoAction('smart_forms_include_form_elemeents_scripts');
SmartFormsActionManager::GetInstance()->DoAction('smart_forms_pr_add_form_elements_extensions');

JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/grid_chart/grid.locale-en.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/grid_chart/jquery.jqGrid.min.js');


JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/css/formBuilder/custom.css');
JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/css/grid_chart/ui.jqgrid.css');
JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/css/grid_chart/jquery.jqplot.css');
JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/css/smartFormsSlider/jquery-ui-1.10.2.custom.min.css');




?>

<script type="text/javascript">
    var RedNaoSmartFormLicenseIsValid=<?php echo SMART_FORMS_IS_PR?>;
    var RedNaoSmartFormLicenseErrorMessage="";
    var ajaxurl="index.php?option=com_smartforms&task=execute_ajax&format=raw";
    var smartFormsPath="<?php echo SMART_FORMS_DIR_URL?>";
    var smartFormsAdditionalFields0=<?php echo json_encode($additionalFields)?>;
</script>

<style  type="text/css">
    .ui-jqgrid tr.jqgrow td {
        white-space: normal !important;
    }

    .editButton,.deleteButton{
        cursor:hand;
        cursor:pointer;
        padding:2px;
    }

    .bootstrap-wrapper .modal-body{
        float:left;
    }

    .bootstrap-wrapper .row{
        margin:0;
    }

    .editButton:hover,.deleteButton:hover{
        color:red;
    }

    .bootstrap-wrapper .form-control {
        color:black !important;
    }



    .bootstrap-wrapper .modal-content{
        overflow:auto;
        overflow-x:hidden;
    }

    .bootstrap-wrapper .modal-footer{
        clear:both;
        border:none !important;
    }

    .bootstrap-wrapper .modal-body{
        width:100% !important;
    }


    .bootstrap-wrapper [class*="span"] {
        float: none !important;
        min-height: auto !important;
        margin-left: auto !important;
    }

    .bootstrap-wrapper div.modal{
        width:100% !important;
        margin-left:0 !important;
        background-color: transparent !important;


    }
    .modal-backdrop, .modal-backdrop.fade.in{
        opacity: .5 !important;
    }

    .bootstrap-wrapper .modal-body{
        max-height: none !important;
    }

    .bootstrap-wrapper .CodeMirror{
        min-height: 0 !important;
    }

    .bootstrap-wrapper .close{
        width: auto !important;
        margin-top: 0;
        margin-right: 0 !important;
        font-size: 21px !important;
        line-height: 21px !important;
        border-left: none !important;
    }

    .bootstrap-wrapper select{
        width: auto;
    }
</style>



<hr/>


<div id="smartDonationRadio" class="smartFormsSlider" style="margin-bottom: 20px;">
    <strong >Start Date</strong>
    <input type="text" class="datePicker smartFormsSlider" id="dpStartDate"/>
    <strong style="margin-left: 15px">End Date</strong>
    <input type="text" class="datePicker smartFormsSlider" id="dpEndDate"/>
    <strong style="margin-left: 15px" >Form</strong>
    <select id="cbForm">
        <?php
        $db = JFactory::getDBO();
        $db->setQuery("SELECT form_id,form_name FROM #__smartforms");
        $results = $db->loadObjectList();


        foreach($results as $result)
        {
            echo "<option value='$result->form_id' >$result->form_name</option>";
        }

        ?>
    </select>

    <script type="text/javascript" language="javascript">
        var smartFormsRootPath="<?php echo SMART_FORMS_DIR_URL?>";
        var RedNaoCampaignList="";
        var smartFormsDesignMode=false;
        <?php
            echo "RedNaoCampaignList='";
            foreach($results as $result)
            {

                echo ";$result->form_id:$result->form_name";
            }
            echo "'";
        ?>
    </script>




    <Button style="margin-left:35px" id="btnExecute">
        Execute
    </Button>

</div>
<div style="width:80%;overflow-x: scroll;padding:25px;display: none">
    <div id="Chart"></div>
</div>

<div id="editDialog"></div>

<div>
    <div class="smartFormsSlider" style="margin-right:10px;">
        <table id='grid' class="ui-jqdialogasdf" style="width:100%;height:100%;"></table><div id='pager'></div>
    </div>

    <form method="post" action="index.php?option=com_smartforms&act=export" id="exporterForm" target="_blank">
        <input type="hidden" value="rednao_smartformsexport" name="action">
        <input type="hidden" id="smartFormsExportData" name="exportdata"/>
    </form>