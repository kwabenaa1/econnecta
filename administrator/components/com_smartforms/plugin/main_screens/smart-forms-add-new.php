<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

if ( !JFactory::getUser()->authorise('core.manage', 'com_smartforms'))
{
    return JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
}

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );


if(JFactory::getApplication()->input->get('formid','','raw')!='')
{
    $formId=JFactory::getApplication()->input->get('formid','','raw');
    $db = JFactory::getDBO();
    $db->setQuery("SELECT form_options,element_options,client_form_options,form_id FROM #__smartforms where form_id= ".$db->quote(JFactory::getApplication()->input->get('formid','','raw')));
    $row = $db->loadAssoc();
    if($row==null)
    {
        echo "Form does not exists!, please select another valid form";
        return;
    }

    JFactory::getDocument()->addScriptDeclaration('var smartFormId="'.$row['form_id'].'";var smartFormsOptions='.$row['form_options'].';var smartFormsElementOptions='.$row['element_options'].';var smartFormClientOptions='.$row['client_form_options'].';');
}





require_once(SMART_FORMS_DIR.'filter_listeners/fixed-field-listeners.php');
require_once(SMART_FORMS_PUBLIC_DIR.'smart-forms-bootstrap.php');
require_once(SMART_FORMS_PUBLIC_DIR.'additional_fields/smart-forms-additional-fields-list.php');

JFactory::getDocument()->addScriptDeclaration('var RedNaoSmartFormLicenseErrorMessage="";var RedNaoSmartFormLicenseIsValid='.SMART_FORMS_IS_PR.';var RedNaoSmartFormLicenseErrorMessage="";var SmartFormsElementsTranslation={};var ajaxurl="index.php?option=com_smartforms&task=execute_ajax&format=raw";var RedNaoSmartFormLicenseIsValid ='.SMART_FORMS_IS_PR.';');
JFactory::getDocument()->addScriptDeclaration('var smartFormsTranslation='.json_encode(array(
        "SelectAField"=>"Type an email or select an email field (pro)",
        "ClickInAnElementToEditIt"=>"Click in an element to edit it",
        "Default"=>"Default",
        "typeOrSelectFieldsToBeShown"=>"Type or select the fields to be shown",
        "Next"=>"Next",
        "Previous"=>"Previous",
        "Finish"=>"Finish",
        "WhenDoYouWantToDisplay"=>"When do you want to display them?",
        "SelectAtLeastOneField"=>"Please select at least one field",
        "PleaseFillAllFields"=>"Please fill all fields",
        "YouAreClosingOneParenthesis"=>"You are closing one parenthesis when there is no open parenthesis",
        "ParenthesisDontMatch"=>"The open parenthesis count doesn't match the close parenthesis count",
        "HowDoYouWantToName"=>"How do you want to name your new condition?",
        "SelectTheStepsToBeShow"=>"Select the steps to be shown",
        "MyNewCondition"=>"My new condition",
        "The title can't be empty"=>"The title can't be empty",
        "AddConditionalLogic"=>"Add Conditional Logic",
        "AreYouSureYouWantToDelete"=>"Are you sure you want to delete",
        "Default"=>"Default",
        "ClickHereToCreateSnippet"=>"Click here to create a new javascript snippet",
        "SelectTheFieldsRedirectPage"=>"Select the fields you want to send as parameters to the redirect page",
        "SelectCampaignBeforeSaving"=>"If you are going to use a donation button, please select a campaign before saving",
        "SelectPaypalEmailBeforeSaving"=>"Please select a paypal donation email before saving",
        "SetupDonationFormulaBeforeSaving"=>"Please setup a donation formula before saving",
        "ConfigureEmailIsGoingToBeSent"=>"Please before saving, configure the email that is going to be sent.",
        "AnErrorOccurred"=>"An error occurred",
        "CodeTestedSuccessfully"=>"Code tested successfully!!",
        "AddParametersToUrl"=>"Add Parameters to Url",
        "AddEditConditionalLogic"=>"Add/Edit conditional logic",
        "DeleteRow"=>"Delete row",
        "DeletingRow"=>"Deleting row",
        "AreYouSureDeleteRow?"=>"Are you sure you want to delete the row?",
        "AddAnotherRedirectToUrl"=>"Add another redirect to url",
        "whichFieldYouWantToMakeInvalid"=>"Which field(s) you want to make invalid depending on a condition",
        "WhenDoYouWantToMakeInvalid"=>"When do you want to make it/them invalid?",
        "WhatMessageWhenInvalid"=>"Please type the message you want to display when the field(s) is/are invalid",
        "AreYouSureDeleteRow"=>"Are you sure you want to delete the row?",
        "DefaultCountry"=>"Default Country"
    )).';</script>');

JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/react/react.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/react/react-dom.js');

JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/licensing/rednao-licensing-manager.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/fuelux/wizard.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/velocityAsync/velocityAsync.js');


$formElementDependencies=array('isolated-slider','smart-forms-form-elements-container');
$formElementDependencies=SmartFormsFilterManager::GetInstance()->ApplyFilter('smart_forms_add_form_elements_dependencies',$formElementDependencies);

JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/bootstrap-slider/bootstrap-slider.min.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/es6-promise/dist/es6-promise.min.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/eventmanager.js');







JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/container/Container.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/container/ContainerResizer.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/container/ContainerDesigner.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/formelements.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/tutorials/rnTutorials.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/tinymce/tinymce.min.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/popup-wizard/wizard-steps.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/popup-wizard/popup-wizard.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/wizards/email-enabled-wizard-steps.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/wizards/paypal-wizard-steps.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/wizards/redirect-to-wizard-steps.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/rnListManager.js');

JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/formula/customActions.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/formula/fixedValues.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/properties/manipulators.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/formbuilder.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/dragManager/dragmanager.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/dragManager/dragitembehaviors.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/conditional_manager/condition-designer.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/bundle/conditionalHandlers_bundle.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/bundle/conditionalManager_bundle.js');

JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/multiple_steps/multiple_steps_base.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/multiple_steps/multiple_steps_designer.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/codeMirror/codemirror.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/codeMirror/mode/javascript/javascript.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/codeMirror/addon/display/placeholder.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/codeMirror/addon/hint/show-hint.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/formula/autoComplete.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/codeMirror/addon/lint/eslint.min.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/codeMirror/addon/lint/eslint-lint.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/codeMirror/addon/lint/lint.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/formula/formulawindow.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/bundle/addnewtutorial_bundle.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/subscriber_interfaces/ismart-forms-add-new.js');








$additionalJS=SmartFormsFilterManager::GetInstance()->ApplyFilter("sf_form_configuration_on_load_js",array());
$addNewDependencies= array('smart-forms-add-new-tutorial','smart-forms-list-manager','ismart-forms-add-new','isolated-slider','smart-forms-formula-window','smart-forms-formBuilder','smart-forms-select2','smart-forms-event-manager','smart-forms-conditional-manager');
for($i=0;$i<count($additionalJS);$i++){

    if(!isset($additionalJS[$i]['dependencies']))
        $additionalJS[$i]['dependencies']=array('ismart-forms-add-new');
    JFactory::getDocument()->addScript( JUri::root( true ) .$additionalJS[$i]["path"]);


}
//JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/main_screens/smart-forms-add-new-loader.js');

JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/select2/select2.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/spectrum/spectrum.js');




echo "<div class='bootstrap-wrapper' style='position: absolute;width:100%;'><div id='smart-forms-notification'></div></div>";


$fieldsDependencies=array();
$additionalFields=SmartFormsFilterManager::GetInstance()->ApplyFilter('smart_forms_af_names',$fieldsDependencies);
foreach($additionalFields as $field)
{
    SmartFormsActionManager::GetInstance()->DoAction('smart_forms_af_'.$field['id']);
}



JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/editors/style_editor/element-styler.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/editors/style_editor/style-editor.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/editors/style_editor/style-properties.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/editors/style_editor/styler-set.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/bootstrap/material.min.js');

JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/formula/formula.js');
JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/dist/AddNew_bundle.js');
JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/bootstrap-slider/bootstrap-slider.min.css');
JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/css/mainStyle.css');
JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/spectrum/spectrum.css');
JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/css/smartFormsSlider/jquery-ui-1.10.2.custom.min.css');
JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/css/formBuilder/bootstrap.min.css');
JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/css/formBuilder/custom.css');
JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/select2/select2.css');
JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/fuelux/fuelux.css');
JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/codeMirror/codemirror.css');
JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/codeMirror/addon/hint/show-hint.css');
JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/codeMirror/addon/lint/lint.css');



JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/css/bootstrap/bootstrap-material-scoped.css');

SmartFormsActionManager::GetInstance()->DoAction('smart_forms_load_designer_scripts');

?>


<script type="text/javascript">
    var smartFormsDesignMode=true;
    var SmartFormsEnableGDPR='n';
    var smartFormsRootPath="<?php echo JUri::root( false ).'components/com_smartforms/plugin/'?>";
    var rntinyMCEPreInit={
        baseURL: smartFormsRootPath+'js/utilities/tinymce',
        suffix: ".min"
    };
	<?php

	$emailFixedFieldListeners=array();
	$emailFixedFieldListeners=SmartFormsFilterManager::GetInstance()->ApplyFilter('smart-forms-get-email-fixed-field-listener',$emailFixedFieldListeners);
    echo "var smartFormsAdditionalFields0=".json_encode($additionalFields).";";
	echo "var smartFormsFixedFields=".json_encode($emailFixedFieldListeners).";";
	 ?>

    var smartFormsPreviewUrl='<?php echo JUri::root( false ).'index.php?option=com_smartforms&task=preview'?>';
    var smartForms_arrow_closed="<?php echo SMART_FORMS_DIR_URL?>images/arrow_right.png";
    var smartForms_arrow_open="<?php echo SMART_FORMS_DIR_URL?>images/arrow_down.png";
    var smartFormsPath="<?php echo JUri::root( false ) . 'components/com_smartforms/plugin/'?>";

    var smartFormsEmailDoctorUrl="index.php?option=com_smartforms&act=emaildoctor";

    <?php

        $customVars=array();
        $customVars=SmartFormsFilterManager::GetInstance()->ApplyFilter('smart-forms-add-new-js-vars',$customVars);

        foreach($customVars as $var)
        {
            echo "var ".$var["name"]." = ".$var["value"].'; ';
        }



     ?>


</script>
<style type="text/css">
    .OpHidden{
        opacity: 0.0;
        filter: alpha(opacity=0);"
    }
</style>



    <div style="position:fixed;top:0;left:0;overflow: auto;z-index: 999999;width:100%;height:100%;background-color: #efefef;" id="sfMainContainer">
        <div id="loadingScreen" style="background-color: white;width: 100%;height: 100%;top:0;left:0;z-index: 100000; position: absolute;text-align: center;">
            <div style="top:200px;position: absolute;width: 100%;text-align: center;" id="smartFormsLoadingLogo">
                <img  src="<?php echo JUri::root( false )?>components/com_smartforms/plugin/images/ProgressBar2.gif" height="211;" alt="" />
                <label  style="font-size: 31px; line-height: 31px; font-family:Verdana, Geneva, sans-serif;padding:0;margin:0; display: block;">Loading important stuffs, please wait a bit =)</label>
            </div>


        </div>

        <div id="rootContentDiv" class="OpHidden" style="height: 100%;">
            <?php

            if(SmartFormsOptionManager::GetInstance()->GetOption('sf_dont_show_again')===false)
            {
                ?>
                <div style="margin-bottom: 5px; border-style: dashed;border-color: black;border-width: 2px;padding:5px; margin-left: 5px; background-color;background-color: #ffffff" class="bootstrap-wrapper sfSignUpForm" >

                    <span style=" vertical-align: middle; font-size:30px;" class="glyphicon glyphicon-envelope"></span>  <p style="vertical-align: middle; display: inline; margin-top: 5px;margin-bottom:5px; font-size: 15px;"><?php echo "Get exclusive content, news and tips directly in your email" ?> <a data-toggle="modal" data-target="#signUpModal" style="cursor:hand;cursor:pointer;"><?php echo "Subscribe to the Smart Forms mailing list here" ?></a></p>
                    <div style="float: right">

                        <a style="clear: both;cursor: pointer;cursor:hand;" onclick="DontShowSignUpAgain()"><?php echo "Don't show this again" ?></a>
                        <span>|</span>
                        <a style="clear: both;cursor: pointer;cursor:hand;" onclick="rnJQuery('.sfSignUpForm').hide();"><?php echo "Close" ?></a>
                    </div>

                </div>

                <script>
                    function DontShowSignUpAgain()
                    {
                        var data={};
                        data.action="rednao_smart_forms_dont_show_again";
                        rnJQuery.post(ajaxurl,data,function(result){
                            rnJQuery('.sfSignUpForm').hide();
                        });
                    }
                </script>



                <div class="bootstrap-wrapper">
                    <div class="modal fade" id="signUpModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog">
                            <form class="modal-content" method="post" target="_blank" action="https://rednao.us5.list-manage.com/subscribe/post?u=b9238e9ea9&amp;id=fdc8bf5485">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel"><?php echo "Almost done =). Submit your email to register to the newsletter." ?></h4>
                                </div>
                                <div class="modal-body">
                                    <div style="display: inline-block;width:29%"><label><?php echo "Email" ?></label></div>
                                    <input style="display:inline-block; width: 70%;margin-bottom: 5px;" name="EMAIL" type="text" placeholder="your@email.com" class="form-control redNaoInputText " value="">

                                    <div style="display: inline-block;width:29%"><label><?php echo "First Name:" ?></label></div>
                                    <input style="display:inline-block; width: 70%;margin-bottom: 5px;" name="FNAME" type="text" placeholder="your@email.com" class="form-control redNaoInputText " value="">

                                    <div style="display: inline-block;width:29%"><label><?php echo "Last Name:" ?></label></div>
                                    <input style="display:inline-block; width: 70%;" name="LNAME" type="text" placeholder="your@email.com" class="form-control redNaoInputText " value="">

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo "Close" ?></button>
                                    <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-ok"></span><?php echo "Subscribe" ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <?php
            }
            ?>


            <div style="background: #fbfbfb;height: 100px;display: flex;flex-direction: column;justify-content: space-between">
                <div style="text-align: left;height: 55px;" class="bootstrap-wrapper">

                    <table style="z-index: 10000;;position: fixed;top:50px;right:0px;height: calc(100% - 100px);display: none;" class="sfHelper">
                        <tr>
                            <td style="vertical-align: top">
                                <div style="background-color: white" class="sfHelpIconContainer" data-toggle="popover" data-placement="left" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus."  >
                                    <span title="Tutorials" style="font-size: 30px;" class="glyphicon glyphicon-question-sign"></span>
                                </div>
                            </td>
                            <td>
                                <div class="sfHelpContent" style="width:0px;" >
                                    <table>
                                        <tr>
                                            <td>
                                                <div class="redNaoControls col-sm-9 has-feedback-left" style="width:300px;margin:12px 10px 10px 10px;">
                                                    <input style="" id="tbHelpSearch" name="Search_f" type="text" placeholder="Search for specific topic" class="form-control redNaoInputText " value="">
                                                    <span class="sfPlaceHolderIcon glyphicon glyphicon-search form-control-feedback"></span>
                                                </div>
                                            </td>
                                            <td><button id="btnHelpSearch" class="btn btn-success">Search</button></td>
                                        </tr>
                                    </table>



                                    <div  style="clear:both;">

                                        <div style="margin:10px;display: none;" class="waitPanel">
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                                    <span><?php echo "Loading tutorials" ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="list-group videoList" style="display: none;">
                                            <!-- <span class="list-group-item">Dapibus ac facilisis in</span>
                                           <a href="#" class="list-group-item">Morbi leo risus</a>
                                            <a href="#" class="list-group-item">Porta ac consectetur ac</a>
                                            <a href="#" class="list-group-item">Vestibulum at eros</a>-->
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>



                    <div style="padding: 10px;display: flex;justify-content: space-between;align-items: center;">
                        <div style="display: inline-block;">
                            <button style="min-width:100px;cursor: hand;cursor: pointer;" class="btn btn-success ladda-button" id="smartFormsSaveButton"  data-style="expand-left" onclick="return false;" >
                                <span class="glyphicon glyphicon-floppy-disk"></span><span class="ladda-label"><?php echo "Save" ?></span>
                            </button>

                            <button style="min-width:100px;cursor: hand;cursor: pointer;" class="btn btn-primary" id="smartFormsPreview">
                                <span class="glyphicon glyphicon-search"></span><span class="ladda-label"><?php echo "Preview" ?></span>
                            </button>
                        </div>
                        <div style="display: inline-flex;flex-grow:1;align-items: center;margin:0 10px;">
                            <h4 style="margin:0 0 0 15px;display:inline-block">Editing: </h4>
                            <input id="smartFormName" style="width:400px;cursor: pointer;background-color:transparent;flex-grow: 1;padding:3px 5px;" value="New Form"/>
                        </div>
                        <span onclick="location.href='<?php echo JUri::root( false ).'/administrator/index.php?option=com_smartforms&act=manageforms' ?>'" id="ExitButton" title="Exit" class="fa fa-times" style="font-size: 30px;float: right;"></span>
                    </div>
                </div>
                <ul class="nav nav-tabs" id="smartFormsTopTab" style="margin-bottom: 0;">
                    <li class="active" style="margin-left: 10px;"><a style="cursor: hand;cursor: pointer;" class='nav-tab' id="smartFormsGeneralTab"  onclick="SmartFormsAddNewVar.GoToGeneral();"><?php echo "General Info" ?></a></li>
                    <li style="cursor: hand;cursor: pointer;"><a style="cursor: hand;cursor: pointer;" class='nav-tab' id="smartFormsSettingsTab"  onclick="SmartFormsAddNewVar.GoToSettings();"><?php echo "Settings" ?></a></li>
                    <li><a style="cursor: hand;cursor: pointer;" class='nav-tab' id="smartFormsJavascriptTab" onclick="SmartFormsAddNewVar.GoToJavascript();"><?php echo "Javascript" ?></a></li>
                    <li><a style="cursor: hand;cursor: pointer;" class='nav-tab' id="smartFormsCSSTab" onclick="SmartFormsAddNewVar.GoToCSS();"><?php echo "CSS" ?></a></li>
                    <li><a style="cursor: hand;cursor: pointer;" class='nav-tab' id="smartFormsAfterSubmitTab" onclick="SmartFormsAddNewVar.GoToAfterSubmit();"><?php echo "After Submit" ?></a></li>


                    <?php
                    $tabs=array();
                    $tabs=SmartFormsFilterManager::GetInstance()->ApplyFilter("sf_form_configuration_on_load_tabs",$tabs);
                    for($i=0;$i<count($tabs);$i++)
                    {
                        echo '<li><a id="smartFormsCustom'.$i.'Tab" data-tab-id="'.$tabs[$i]["id"].'" class="nav-tab sfcustomtab" onclick="SmartFormsAddNewVar.GoToCustomTab('.$i.');" >'.$tabs[$i]["name"].'</a></li>';
                    }
                    ?>



                    <?php
                    $db = JFactory::getDBO();
                    $db->setQuery("SELECT count(*) form_name FROM #__smart_forms_plugins where plugin_name='paypal' ");
                    $hasPaypalAddOn = $db->loadResult();
                    if($hasPaypalAddOn>0)
                    {
                        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/integration/smart-donations-integration.js');
                        ?>
                        <li><a class='nav-tab' id="smartDonationsTab" onclick="SmartFormsAddNewVar.GoToSmartDonations();"><?php echo "PayPal" ?></a></li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
            <div id="redNaoGeneralInfo" style="height: calc(100% - 100px)">



                <!--
        <div id="redNaoEmailEditor" title="Email" style="display: none;">
            <table id="emailControls">
                <tr>
                    <td style="text-align: right">From email address</td><td> <select  multiple="multiple"  id="redNaoFromEmail" style="width:300px"></td>
                    <td rowspan="5">
                        <a target="_blank" style="color:red; margin-right: 10px;margin-top: 10px;cursor:hand;cursor:pointer;" id="sfNotReceivingEmail"><?php echo "Not receiving the email? check the email doctor." ?></a>
                        <div class="bootstrap-wrapper" style="height: 150px;overflow-y: scroll;width: 340px;">
                            <div id="emailList"></div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="text-align: right"><?php echo "From name" ?></td><td> <input placeholder="Default (Wordpress)" type="text" id="redNaoFromName" style="width:300px"></td>
                </tr>

                <tr>
                    <td style="text-align: right"><?php echo "Send to email address(es)" ?></td><td> <select multiple="multiple" id="redNaoToEmail" style="width:300px"></select></td>
                </tr>

                <tr>
                    <td style="text-align: right"><?php echo "Email subject" ?></td><td> <input placeholder="Default (Form Submitted)" type="text" id="redNaoEmailSubject" style="width:300px"></td>
                </tr>
            </table>


            <div id="redNaoEmailEditorComponent">
            <div id="tinyMCEContainer" style="width:600px;float:left;">
                <button type="button" class="button" id="rnAddMedia"><span class="wp-media-buttons-icon"></span> Add Media</button>
                <textarea id="redNaoTinyMCEEditor"></textarea>
            </div>

            <div id="redNaoAccordion" class="smartFormsSlider" style="float:right;">
                <h3>Form Fields</h3>
                <div>
                    <ul id="redNaoEmailFormFields">

                    </ul>
                </div>
                <h3><?php //echo __("Fixed Values") ?></h3>
                <div>
                    <ul id="redNaoEmailFormFixedFields">

                    </ul>
                </div>
            </div>
            </div>
            <div style="text-align: right;clear: both;">
                <button onclick="RedNaoEmailEditorVar.CloseEmailEditor();"><?php echo "Close" ?></button>
                <button onclick="SmartFormsAddNewVar.SendTestEmail();"><?php echo "Send Test Email" ?></button>
            </div>
        </div>-->
                <div id="redNaoStyleEditor" title="<?php echo "Style Editor"?>" style="display: none;margin:0;padding:0;">
                    <table style="width: 100%;height: 100%;">

                        <tr>
                            <td style="width: 550px;">
                                <div id="styleEditorPreview" class="rednaoFormContainer bootstrap-wrapper" style="width: 100%;height: 100%;">
                                    <table style="width: 100%;height: 100%;">
                                        <tr>
                                            <td style="vertical-align: middle;" id="smartFormStyleEditorContainer">

                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                            <td>
                                <div style="width: 100%;height: 100%;" class="bootstrap-wrapper">
                                    <div style="text-align: right" class="rnEditorContainer">
                                        <label><?php echo "Apply to:" ?></label>
                                        <select  id="rnStyleApplyTo">
                                            <option value="1"><?php echo "This field" ?></option>
                                            <option id="allOfTypeOption" value="2"><?php echo "All fields of the same type" ?></option>
                                            <option value="3"><?php echo "All fields" ?></option>
                                        </select>
                                    </div>


                                    <ul class="nav nav-tabs rnEditorContainer" >
                                        <li role="presentation" class="active"><a id="rnStyleEditorAttribute" href="#styleEditorAttributes" data-toggle="tab"><?php echo "Styles" ?></a></li>
                                        <li role="presentation"><a href="#styleCustomRules" data-toggle="tab"><?php echo "Custom CSS (Advanced)" ?></a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="styleEditorAttributes" >
                                        </div>
                                        <div class="tab-pane" id="styleCustomRules" >
                                    <textarea style="width: 100%;height: 555px;" id="rnCustomStyleContent" placeholder="<?php echo "Here you can put only style rules" ?> (<?php echo "e.g." ?> background-color:red;), <?php echo "not selectors" ?> (<?php echo "e.g." ?> .mybutton{background-color:red;}.
        <?php echo "If you want to add your own selectors and rules please add them in the CSS tab of your form." ?>
        <?php echo "Tip:If your rule is not working try adding !important (e.g. background-color:red !important;)" ?>"></textarea>
                                            <button id="rnApplyCustomRule" style="margin-left: auto;display: block;"><?php echo "Apply Custom Rules" ?></button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>

                </div>


                <div id="smartFormsJavascriptDiv" class="bootstrap-wrapper" style="display: none;padding: 10px;height: 100%;background-color:#6b6b6b;padding:10px;">
                    <table style="width:100%;">
                        <tr>
                            <td style="width:80%;">
                                <textarea id="smartFormsJavascriptText" class="form-control" disabled="disabled"></textarea>
                                <button onclick="SmartFormsAddNewVar.Validate()"><?php echo "Validate" ?></button>
                                <button onclick="SmartFormsAddNewVar.RestoreDefault()"><?php echo "Restore default" ?></button>
                            </td>
                            <td style="width:20%;vertical-align: top;">
                                <div id="javascriptList"></div>
                            </td>
                        </tr>
                    </table>


                </div>

                <?php
                for($i=0; $i<count($tabs);$i++)
                {
                    echo "<div style='display:none;height: 100%;background-color:#6b6b6b;' class='smartFormsCustomTab'  id='smartFormsCustom".$i."Div'><div style='width:100%;padding:10px;background-color: white;'>";
                    echo $tabs[$i]["content"];
                    echo "</div></div>";
                }
                ?>



                <div id="smartDonationsDiv" style="display: none;height: 100%;background-color:#6b6b6b;padding:10px;">
                    <table style="width: 100%;background-color: white;">
                        <tr style="display: none;">
                            <td style="text-align: right;width: 200px;"><?php echo "Campaign" ?></td><td>
                                <select id="redNaoCampaign"></select></td>
                        </tr>
                        <tr >
                            <td style="text-align: right" ><span class="smartDonationsConfigurationInfo"><?php echo "PayPal email" ?></span></td><td class="smartDonationsConfigurationInfo"> <input type="text" id="smartDonationsEmail" />  <span  class="description smartDonationsConfigurationInfoDesc" style="margin-bottom:5px;display: inline;"> <?php echo "*The email of your paypal account"; ?></span></td>
                        </tr>
                        <tr >
                            <td style="text-align: right" ><span class="smartDonationsConfigurationInfo"><?php echo "Type" ?></span></td><td class="smartDonationsConfigurationInfo">
                                <select id="redNaoPaypalType">
                                    <option value="payment">Payment</option>
                                    <option value="donation" selected="selected">Donation</option>
                                </select>

                            </td>
                        </tr>
                        <tr >
                            <td style="text-align: right"><span class="smartDonationsConfigurationInfo"><?php echo "Donation/Payment description" ?></span></td><td class="smartDonationsConfigurationInfo"> <input type="text" id="smartDonationsDescription"/><span class="description smartDonationsConfigurationInfoDesc" style="margin-bottom:5px;display: inline;"> <?php echo "*This description is going to be shown in the Paypal transaction page "; ?><a href="<?php echo SMART_FORMS_DIR_URL?>images/paypal_transaction_page.png" target="_blank"><?php echo "(Screenshot)"?></a></span></td>
                        </tr>



                        <tr >
                            <td style="text-align: right"><span class="smartDonationsConfigurationInfo"><?php echo "Currency" ?></span></td><td> <select class="smartDonationsConfigurationInfo" id="smartDonationsCurrencyDropDown" name="donation_currency"></select></td>
                        </tr>


                        <tr >
                            <?php /*     <td style="text-align: right"><span class="smartDonationsConfigurationInfo">Send thank you email</span></td><td class="smartDonationsConfigurationInfo"> <input  type="checkbox" id="redNaoSendThankYouEmail" ><span  class="description smartDonationsConfigurationInfoDesc" style="margin-bottom:5px;display: inline;"> <?php echo __("*If you check this box the thank you email is going to be send to the donators "); ?> <a href="<?php echo SMART_FORMS_DIR_URL?>images/campaign.png" target="_blank"><?php echo __("(Screenshot)")?></a></span></td> */?>
                        </tr>
                        <tr>
                            <td>
                            </td>
                            <td class="bootstrap-wrapper">
                                <button style="display: inline;" class="smartDonationsConfigurationInfo" id="setUpDonationFormulaButton"><?php echo "Setup donation formula" ?></button>
                                <span  class="addConditionLogic glyphicon glyphicon glyphicon-link sfConditionLogicPayPal" style="cursor: pointer; cursor:hand;margin-left:5px;display: inline;"></span>
                            </td>
                        </tr>


                    </table>
                </div>
                <div id="gdprDiv" style="display: none" class="bootstrap-wrapper">
                    <table style="max-width: 700px; margin-top:10px;" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Activate</th>
                            <th>Action</th>
                        </tr>
                        <tr>

                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style="text-align: center;">
                                <input style="margin:0;" type="checkbox" id="smartFormsDisableDBStorage"/>
                            </td>
                            <td>
                                <label style="margin:0;font-weight: normal" for="smartFormsDisableDBStorage">Do not save the entry in the database (you still can receive all the form information in your email)</label>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">
                                <input style="margin:0;" type="checkbox" id="smartFormsDisableIPStorage"/>
                            </td>
                            <td>
                                <label style="margin:0;font-weight: normal" for="smartFormsDisableIPStorage">Do not save user's device information (like IP address)</label>
                            </td>
                        </tr>

                        </tbody>
                    </table>
                </div>


                <div id="smartFormsAfterSubmitDiv" class="bootstrap-wrapper" style="display: none;border:none;width:auto;padding:10px;height: 100%;background-color:#6b6b6b;padding:10px;" >
                    <table class="table table-bordered table-striped" style="background-color: white;">
                        <thead>
                        <tr>
                            <th><?php echo "Activate" ?></th>
                            <th><?php echo "Action" ?></th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr class="sfAfterSubmitAction">
                            <td style="text-align: center"> <input type="checkbox" checked="checked"  id="smartFormsSendNotificationEmail"/></td>
                            <td>
                                <span><?php echo "Send notification email"; ?></span>
                                <button id="redNaoEditEmailButton" class="btn btn-default" disabled="disabled"><?php echo "Edit Email"; ?></button>
                            </td>
                        </tr>

                        <tr class="sfAfterSubmitAction">
                            <td style="text-align: center"><input  type="checkbox"   id="redNaoRedirectToCB"/></td>
                            <td>
                                <span ><?php echo "Redirect to"; ?></span>
                                <table id="redirectToOptionsItems">

                                </table>


                            </td>
                        </tr>

                        <tr class="sfAfterSubmitAction">
                            <td style="text-align: center"><input style="vertical-align: top"  type="checkbox"  id="redNaoAlertMessageCB"/></td>
                            <td>
                                <span style="vertical-align: top"><?php echo "Show alert message"; ?></span>
                                <textarea style="width:250px;height: 70px;" id="alertMessageInput" disabled="disabled" ></textarea>
                            </td>
                        </tr>

                        <tr class="sfAfterSubmitAction">
                            <td style="text-align: center"><input style="vertical-align: top"  type="checkbox"  id="rednaoDontClearForm"/></td>
                            <td>
                                <span style="vertical-align: top"><?php echo "Don't clear the form after submission."; ?></span>
                            </td>
                        </tr>


                        </tbody>
                    </table>
                </div>




                <div id="smartFormsCSSDiv" style="display: none;padding: 10px;height: 100%;background-color:#6b6b6b;padding:10px;" class="form-horizontal bootstrap-wrapper">

            <textarea id="smartFormsCSSText" placeholder="<?php echo "You can put your custom css rules here, example:" ?>
        button{
            background-color:red;
        }
        <?php echo "TIP: if the rule is not working try adding" ?> !important, <?php echo "e.g." ?> background-color:red !important;
        "></textarea>
                    <button id="sfApplyCss">Apply</button>

                </div>


                <div id="smartFormsSettingsDiv" style="display: none;height: 100%;background-color:#6b6b6b;padding:10px;" class="bootstrap-wrapper">
                    <div id="rednaoSmartForms" class="bootstrap-wrapper">

                        <input type="hidden" id="smartFormsId" value=""/>


                        <div  id="smartFormsBasicDetail" class="tab-content" style="background-color: white;">
                            <div class="tab-pane active">
                                <table class="table table-bordered table-striped">
                                    <tbody>


                                    <tr>
                                        <td>
                                            <span><?php echo "Description"; ?></span>
                                            <span style="margin-left: 2px;cursor:hand;cursor:pointer;" data-toggle="tooltip" data-placement="right" title="" class="glyphicon glyphicon-question-sign" data-original-title="<?php echo "The form description, this is displayed in the form list" ?>"></span>
                                        </td>
                                        <td>
                                            <input type="text"  id="smartFormDescription" style="width: 400px;display: inline-block;" class="form-control"/>

                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <div style="width: 200px;display: inline;">
                                                <div style="display: inline-block" class="sfLabelLayoutContainer">
                                                    <span><?php echo "Labels Layout"; ?></span>

                                                    <span class="sfLabelLayout glyphicon glyphicon-question-sign"style="margin-left: 2px;cursor:hand;cursor:pointer;" data-toggle="tooltip" data-placement="right" title="" class="glyphicon glyphicon-question-sign" data-original-title="<?php echo "<strong>Left</strong>:Always in the left of the field"."<img src='".SMART_FORMS_DIR_URL."images/labelsLeft.png'/>"."\r\n"."<strong>Top</strong>: Labels always in the top of the field."."<img src='".SMART_FORMS_DIR_URL."images/labelsTop.png'/>\r\n"."<strong>Auto</strong>:If enough space put labels on the left, otherwise on the top."."<img src='".SMART_FORMS_DIR_URL?>images/labelsAuto.gif'/>"  ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <select class="form-control rnLabelLayout" style="margin-right: 5px;width: 200px;display: inline;">
                                                <option  value="auto"><?php echo "Auto"; ?></option>
                                                <option selected="selected" value="top"><?php echo "Top"; ?></option>
                                                <option value="left"><?php echo "left"; ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div style="width: 200px;display: inline;">
                                                <span><?php echo "Theme"; ?></span>

                                            </div>
                                        </td>
                                        <td>
                                            <select class="form-control rnTheme" style="margin-right: 5px;width: 200px;display: inline;">
                                                <option selected="selected" value="basic"><?php echo "Basic"; ?></option>
                                                <option value="material"><?php echo "Material"; ?></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span><?php echo "Invalid field message"; ?></span>
                                            <span style="margin-left: 2px;cursor:hand;cursor:pointer;" data-toggle="tooltip" data-placement="right" title="" class="glyphicon glyphicon-question-sign" data-original-title="<?php echo "The message that is displayed when a required field is empty"; ?>"></span>
                                        </td>
                                        <td>
                                            <input style="width: 400px;display: inline-block;" class="form-control" type="text"  id="smartFormsInvalidFieldMessage" value="*Please fill all the required fields"/>

                                            <div style="display: inline-block;">
                                                <span class="sfToolTipPosition" style="display: none;margin-left: 10px;"><?php echo "Position" ?></span>
                                                <span class="sfToolTipPosition glyphicon glyphicon-question-sign"style="display: none; margin-left: 2px;cursor:hand;cursor:pointer;" data-toggle="tooltip" data-placement="right" title="" class="glyphicon glyphicon-question-sign" data-original-title="<?php echo "The invalid message is displayed in a tooltip, select the position of the tooltip" ?>"></span>
                                                <div class="sfToolTipPosition" style="display: none;" role="toolbar" id="tooltipPositionList">
                                                    <button id="toolTipPosition_none" type="button"  style="outline: invert none medium;" title="<?php echo "Don't display tooltip" ?>" class="btn btn-default"><span class="glyphicon glyphicon-remove-sign"></span></button>
                                                    <button id="toolTipPosition_left" type="button" style="outline: invert none medium;" title="<?php echo "Left" ?>" class="btn btn-default"><span class="glyphicon glyphicon-hand-left"></span></button>
                                                    <button id="toolTipPosition_top" type="button"  style="outline: invert none medium;" title="<?php echo "Up" ?>" class="btn btn-default"><span class="glyphicon glyphicon-hand-up"></span></button>
                                                    <button id="toolTipPosition_right" type="button" style="outline: invert none medium;" title="<?php echo "Right" ?>" class="btn btn-default"><span class="glyphicon glyphicon-hand-right"></span></button>
                                                    <button id="toolTipPosition_bottom" type="button" style="outline: invert none medium;" title="<?php echo "Down" ?>" class="btn btn-default"><span class="glyphicon glyphicon-hand-down"></span></button>
                                                </div>
                                            </div>

                                        </td>
                                    </tr>



                                    <tr>
                                        <td>
                                            <span><?php echo "Form Type"; ?></span>
                                            <span style="margin-left: 2px;cursor:hand;cursor:pointer;" data-toggle="tooltip" data-placement="right" title="" class="glyphicon glyphicon-question-sign" data-original-title="Normal: The normal form <br>Multiple Steps: A form that is divided in multiple sections, perfect for big forms"></span>
                                        </td>
                                        <td>
                                            <div>
                                                <select id="rnFormType" class="form-control" style="width: 400px;display: inline-block;">
                                                    <option value="nor">Normal</option>
                                                    <option value="sec"><?php echo "Multiple Steps Form (pro)" ?></option>
                                                </select>
                                            </div>
                                            <div style="width:100px;float:left;display: none;" class="msfText" >
                                                <span>Previous</span><input type="text" class="form-control" id="prevText"/>
                                            </div>
                                            <div style="width:100px;float:left;display: none;" class="msfText" >
                                                <span>Next</span><input type="text" class="form-control" id="nextText"/>
                                            </div>
                                            <div style="width:100px;float:left;display: none;" class="msfText" >
                                                <span><?php echo "Complete"?></span><input type="text" class="form-control"  id="completeText"/>
                                            </div>

                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <br/>
                    </div>
                </div>

                <div id="smartFormsGeneralDiv" style="height: 100%;">
                    <form  style="height: 100%;margin: 0;">



                        <div id="redNaoFormBackground" class="bootstrap-wrapper" style="height: 100%;">
                            <div class="rednaoformbuilder container rednaoFormContainer" style="margin:0;width:100%;height: 100%;padding:0 0 0 5px;">

                                <div style="border-collapse: collapse;background-color: #efefef;width: 100%;height: 100%;">


                                    <div style=" vertical-align: top;max-width: 570px;display: inline-block;width: 570px;height: 100%;height:100%;overflow: auto;">
                                        <div id="formSettingsScrollArea" style="height: 100%;box-sizing: border-box;display: block;width:100%;padding:0;">
                                            <div id="formSettings" style="height: 100%;overflow: auto;display: flex;flex-direction: column;">
                                                <ul class="nav nav-tabs" id="sfSettingTabs" role="tablist" style="margin: 0;margin-top: 5px;">
                                                    <li class="active"><a style="cursor:pointer" id="formRadio1"
                                                                          href="#formBuilderComponents" data-toggle="tab"><span
                                                                    class="glyphicon glyphicon-list-alt"></span><?php echo "Fields" ?>
                                                        </a></li>
                                                    <li><a style="cursor:pointer" id="formRadio2" href="#formPropertiesContainer"
                                                           data-toggle="tab"><span
                                                                    class="glyphicon glyphicon glyphicon-cog"></span><?php echo "Field Settings" ?>
                                                        </a></li>
                                                    <li><a style="cursor:pointer" id="formRadio4" href="#formStylesContainer"
                                                           data-toggle="tab"><span
                                                                    class="fa fa-paint-brush"></span><?php echo "Styles" ?></a>
                                                    </li>
                                                    <li><a style="cursor:pointer" id="formRadio3"
                                                           href="#formConditionalLogicContainer" data-toggle="tab"><span
                                                                    class="glyphicon glyphicon glyphicon-link"></span><?php echo "Conditional Logic" ?>
                                                        </a></li>
                                                </ul>
                                                <!--
                                    <div id="formBuilderButtonSet" class="smartFormsSlider">
                                        <input type="radio" id="formRadio1" value="Fields"  name="smartFormsFormEditStyle"  checked="checked" style="display:inline-block;"/><label style="margin:0;width:150px;display:inline-block;" for="formRadio1"><?php echo "Fields" ?></label>
                                        <input type="radio" id="formRadio2"  value="Settings" name="smartFormsFormEditStyle" style="display:inline-block;"/><label style="width:150px;margin: 0 0 0 -5px;display:inline-block;" for="formRadio2"><?php echo "Field Settings" ?></label>
                                        <input type="radio" id="formRadio3"  value="ConditionalLogic" name="smartFormsFormEditStyle" style="display:inline-block;"/><label style="width:170px;margin: 0 0 0 -5px;display:inline-block;" for="formRadio3"><?php echo "Conditional Logic" ?></label>
                                    </div>-->

                                                <div id="formBuilderContainer" class="tab-content" style="overflow: auto;padding:5px;">


                                                    <div class="span6 tab-pane active" style="width:100%" id="formBuilderComponents">
                                                        <h2 class="redNaoFormContainerHeading"><?php echo "Fields List" ?></h2>
                                                        <hr style="margin:0;">
                                                        <div class="tabbable">
                                                            <ul class="nav nav-tabs" id="navtab">
                                                                <li><a id="alayout" class="formtab"><?php echo "Layout" ?></a>
                                                                </li>
                                                                <li><a id="atabinput"
                                                                       class="formtab selectedTab"><?php echo "Basic Input" ?></a>
                                                                </li>
                                                                <li><a id="atabselect"
                                                                       class="formtab"><?php echo "Advanced" ?></a></li>
                                                                <li><a id="atabradioscheckboxes"
                                                                       class="formtab"><?php echo "Multiple Choices" ?></a></li>


                                                                <li><a id="atabbuttons" class="formtab" <?php echo (SMART_FORMS_IS_PR?"":'style="display: none"');?> ><?php echo "Paypal" ?></a></li>
                                                                <li><a id="atabpro" class="formtab"><?php echo "Pro" ?></a></li>
                                                            </ul>
                                                            <div class="form-horizontal" id="components">
                                                                <fieldset>
                                                                    <div class="tab-content">
                                                                        <div class="tab-pane active rednaotablist smartFormFieldTabLayout"
                                                                             id="layout" style="display: none;width:100%;">
                                                                            <div class="component">
                                                                                <div class="control-group rednaotitle">
                                                                                </div>
                                                                            </div>
                                                                            <div class="component">
                                                                                <div class="control-group rednaolineseparator">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="tab-pane active rednaotablist smartFormFieldTabBasic"
                                                                             id="tabinput">
                                                                            <div class="component">
                                                                                <div class="control-group rednaotextinput">
                                                                                </div>
                                                                            </div>
                                                                            <div class="component">
                                                                                <div class="control-group rednaoprependedtext">
                                                                                </div>
                                                                            </div>
                                                                            <div class="component">
                                                                                <div class="control-group rednaoappendedtext">
                                                                                </div>
                                                                            </div>
                                                                            <div class="component">
                                                                                <div class="control-group rednaoprependedcheckbox">
                                                                                </div>
                                                                            </div>
                                                                            <div class="component">
                                                                                <div class="control-group rednaoappendedcheckbox">
                                                                                </div>
                                                                            </div>
                                                                            <div class="component">
                                                                                <div class="control-group rednaotextarea">
                                                                                </div>
                                                                            </div>

                                                                            <div class="component">
                                                                                <div class="control-group rednaodatepicker">
                                                                                </div>
                                                                            </div>

                                                                            <div class="component rednaosubmitbuttoncontainer">
                                                                                <div class="control-group rednaosubmissionbutton">
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="tab-pane rednaotablist smartFormFieldTabAdvanced"
                                                                             id="tabselect" style="display: none;">
                                                                            <div class="component">
                                                                                <div class="control-group rednaoname">
                                                                                </div>
                                                                            </div>
                                                                            <div class="component">
                                                                                <div class="control-group rednaophone">
                                                                                </div>
                                                                            </div>
                                                                            <div class="component">
                                                                                <div class="control-group rednaoemail">
                                                                                </div>
                                                                            </div>
                                                                            <div class="component">
                                                                                <div class="control-group rednaonumber">
                                                                                </div>
                                                                            </div>

                                                                            <div class="component">
                                                                                <div class="control-group rednaoaddress">
                                                                                </div>
                                                                            </div>

                                                                            <div class="component">
                                                                                <div class="control-group rednaohtml">
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                        <div class="tab-pane rednaotablist smartFormFieldTabMultiple"
                                                                             id="tabradioscheckboxes" style="display: none;">
                                                                            <div class="component">
                                                                                <div class="control-group rednaomultipleradios"></div>
                                                                            </div>

                                                                            <div class="component">
                                                                                <div class="control-group rednaomultiplecheckboxes">
                                                                                </div>
                                                                            </div>

                                                                            <div class="component">
                                                                                <div class="control-group rednaoselectbasic">
                                                                                </div>
                                                                            </div>

                                                                            <div class="component">
                                                                                <div class="control-group rednaosearchablelist">
                                                                                </div>
                                                                            </div>

                                                                            <div class="component">
                                                                                <div class="control-group rednaosurveytable"></div>
                                                                            </div>
                                                                            <div class="component">
                                                                                <div class="control-group rednaorating"></div>
                                                                            </div>

                                                                        </div>



                                                                        <div class="tab-pane rednaotablist" id="tabbuttons"
                                                                             style="display: none;">
                                                                            <div class="component">
                                                                                <div class="control-group rednaodonationrecurrence">
                                                                                </div>
                                                                            </div>

                                                                            <div class="component">
                                                                                <div class="control-group rednaodonationbutton">
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="tab-pane rednaotablist smartFormFieldTabPro" id="tabpro"
                                                                             style="display: none;">
                                                                            <h4 id="smartFormsProWarning" style="margin-top: 0;">
                                                                                <span style="color: red;"><?php echo "Warning" ?></span> <?php echo "These fields require a license of smart forms, you can get one " ?>
                                                                                <a target="_blank"
                                                                                   href="http://smartforms.rednao.com/getit"><?php echo "here" ?>
                                                                                    .</a> <?php echo "If you already have a license please" ?>
                                                                                <a href="javascript:RedNaoLicensingManagerVar.ActivateLicense();"><?php echo "activate it here" ?></a>
                                                                            </h4>

                                                                            <div class="component">
                                                                                <div class="control-group sfFileUpload"></div>
                                                                            </div>




                                                                        </div>
                                                                    </div>
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="formPropertiesContainer">
                                                        <div id="smartFormPropertiesContainer" style="width:100%">

                                                        </div>
                                                    </div>

                                                    <div class="tab-pane" id="formStylesContainer" style="padding:5px;">
                                                        &nbsp;
                                                    </div>

                                                    <div class="tab-pane" id="formConditionalLogicContainer"
                                                         style="padding: 0px; overflow-x: hidden;">
                                                        <table id="sfPanelContainer" cellpadding="0"
                                                               style="position: relative; width: 100%;">
                                                            <tr>
                                                                <td style="vertical-align: top;">
                                                                    <table id="sfSavedConditionList"
                                                                           style="width:570px;padding: 5px;">

                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div style="background-color: #6b6b6b;padding:20px;border-left: 1px solid #dfdfdf;border-right:1px solid #dfdfdf;vertical-align: top; height: 100%;display: inline-block;overflow: auto;
                                width: calc(100% - 575px)"
                                         class="smartFormsSelectedElementContainer">

                                        <div class="span6 " id="newFormContainer"
                                             style="margin:0;cursor: e-resize;width: 100%;padding: 10px;background-color: white;">
                                            <div class="clearfix" style="text-align:left;cursor: auto;">


                                                <div id="build">
                                                    <div id="target" class="form-horizontal" style="background-color:white;">
                                                        <div id="redNaoElementlist"
                                                             class="formelements bootstrap-wrapper SfFormElementContainer">
                                                            <div class="formelement last"
                                                                 style="clear:both;height:77px;width:100%; ">

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
    <div class="bootstrap-wrapper">
        <div class="modal fade" data-backdrop="static" data-keyboard="false" id="redNaoEmailEditor" style="display: none;">
            <div class="modal-dialog" style=";width:900px;max-width: 90%; max-height: 85%;overflow-y:auto;">
                <div class="modal-content" style="overflow-x: auto;max-height: 80%"  >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title"><span class="glyphicon glyphicon-envelope" style="line-height: 18px;vertical-align: middle;font-size: 13px;"></span> <span style="line-height: 18px;vertical-align: middle;">Email Builder</span></h4>
                    </div>
                    <div class="modal-body" style="overflow:visible;padding:5px 5px 5px 5px;background-color: #fafafa;">
                        <ul id="emailTabs" class="nav nav-tabs">

                        </ul>
                        <div id="emailContainer" style="border-left:1px solid #7f7f7f;border-right:1px solid #7f7f7f;border-bottom:1px solid #7f7f7f;padding:10px;background-color:white;">
                            <table id="emailControls" style="width:100%">
                                <tr>
                                    <td style="text-align: left;width:50%;">
                                        <label style="width: 100%"><?php echo "Send to email address(es)" ?></label>
                                        <select multiple="multiple" id="redNaoToEmail" style="width:100%"></select>
                                        <span class="sfEmailShowAdvancedOptions" style="color:blue;text-decoration: underline;cursor: pointer;">Show advanced options</span>
                                    </td>
                                    <td style="text-align: left; padding-left: 10px;vertical-align: top;width:50%;">
                                        <label style="width:100%;text-align: left;"><?php echo "From name" ?></label>
                                        <input class="form-control" style="width:100%" placeholder="Default (Wordpress)" type="text" id="redNaoFromName" style="width:300px">
                                    </td>


                                    <!-- <td rowspan="5">
                                                <a target="_blank" style="color:red; margin-right: 10px;margin-top: 10px;cursor:hand;cursor:pointer;" id="sfNotReceivingEmail"><?php echo "Not receiving the email? check the email doctor." ?></a>
                                                <div class="bootstrap-wrapper" style="height: 150px;overflow-y: scroll;width: 340px;">
                                                    <div id="emailList"></div>
                                                </div>
                                            </td>-->
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div style="width:100%;display: none;overflow: hidden;" class="sfEmailAdvancedOptions" >
                                            <div class="row" style="margin:0;">
                                                <div class="col-sm-6" style="text-align: left;width:50%;padding:0;">
                                                    <label style="width: 100%;"><?php echo "Reply To" ?> <span id="replyToTooltip" style="margin-left: 2px;cursor:hand;cursor:pointer;" data-toggle="tooltip" data-placement="right" title="" class="glyphicon glyphicon-question-sign" data-original-title=" <?php echo "When you receive the email and hit reply your reply will be send to this email"; ?>"></span></label>
                                                    <select  placeholder="Default (admin email)" multiple="multiple"  id="redNaoReplyTo" style="width:100%"></select>
                                                </div>
                                                <div class="col-sm-6"  style="text-align: left;width:50%;padding-left: 10px;">
                                                    <label style="width:100%;text-align: left;">From email address <span id="fromEmailAddressTooltip" style="margin-left: 2px;cursor:hand;cursor:pointer;" data-toggle="tooltip" data-placement="right" title="" class="glyphicon glyphicon-question-sign" data-original-title=" <?php echo "Important: if you use an email address different than the default one some email providers will flag your email as spam and block it"; ?>"></span></label>
                                                    <select placeholder="Default (recommended)" style="width:100%"  multiple="multiple"  id="redNaoFromEmail"></select>
                                                </div>
                                            </div>
                                            <div class="row" style="margin:0;">
                                                <div class="col-sm-6" style="overflow: hidden;padding:0;" >
                                                    <label style="width: 100%"><?php echo "Bcc address(es)" ?></label>
                                                    <select multiple="multiple" id="redNaoBccEmail" style="width:100%"></select>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                </tr>

                                <tr>
                                    <td colspan="2" style="text-align: left;padding:0;">
                                        <label><?php echo "Email subject" ?></label>
                                        <input placeholder="Default (Form Submitted)" type="text" id="redNaoEmailSubject" style="width:100%" class="form-control"></td>
                                </tr>
                            </table>


                            <div id="redNaoEmailEditorComponent" style="min-width: 760px;margin-top:15px;">

                            </div>
                            <div style="text-align: right;clear: both;">
                                <div style="display:inline;float:left;">
                                    <input  checked="checked" class="sfEmailElementToShow"  style="margin:0;padding:0;outline:none;" id="showEmailName" type="radio" value="label" name="emailElementToShow">
                                    <label style="margin:0;padding:0" for="showEmailName">&nbsp;Show Label</label>
                                    <input class="sfEmailElementToShow" id="showEmailId" style="margin:0;padding:0;margin-left:10px;outline:none;" value="id" type="radio" name="emailElementToShow">
                                    <label style="margin:0;padding:0" for="showEmailId">&nbsp;Show Id</label>
                                </div>
                                <button onclick="RedNaoEmailEditorVar.CloseEmailEditor();"><?php echo "Close" ?></button>
                                <button onclick="SmartFormsAddNewVar.SendTestEmail();"><?php echo "Send Test Email" ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bootstrap-wrapper">
        <div class="modal fade" data-backdrop="static" data-keyboard="false" id="templateManager" style="display: none;">
            <div class="modal-dialog" style=";width:90%;max-width: 1200px; height: 85%;overflow-y:auto;">
                <div class="modal-content" style="overflow-x: auto;max-height: 100%;display: flex;flex-direction: column;height: 100%;"  >
                    <div class="modal-header" style="height: 55px;min-height: auto;">
                        <button type="button" onclick="event.preventDefault();window.location.href='<?php echo JUri::root( false ).'/administrator/index.php?option=com_smartforms&act=manageforms'?>'" class="close" aria-hidden="true">×</button>
                        <h4 class="modal-title"><span class="fa fa-paint-brush" style="line-height: 18px;vertical-align: middle;font-size: 13px;"></span> <span style="line-height: 18px;vertical-align: middle;">Select a template</span></h4>
                    </div>
                    <div class="modal-body" style="overflow:visible;padding:0;background-color: #fafafa;height: calc(100% - 55px)">

                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
SmartFormsActionManager::GetInstance()->DoAction('smart_forms_pr_add_new_extension');

