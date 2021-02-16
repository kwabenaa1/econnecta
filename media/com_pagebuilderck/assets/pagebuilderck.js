/**
 * @name		Page Builder CK
 * @package		com_pagebuilderck
 * @copyright	Copyright (C) 2015. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - https://www.template-creator.com - https://www.joomlack.fr
 */


var $ck = $ck.noConflict();
var CKUNIQUEIDLIST = new Array();
var workspace;
var accordionckOptions = {
			collapsible: true,
			heightStyle: "content",
			scrollToActive: false
		};

$ck(document).ready(function(){
	workspace = ckGetWorkspace();
//	ckCheckNestedRowsBC();
//	$ck(document.body).append('<div id="ck_overlay"></div>');
//	$ck(document.body).append('<div id="popup_editionck" class="ckpopup"></div>');
//	$ck(document.body).append($ck('#menuck'));
//	if ($ck('#workspaceck').length) ckDoActionsList[0]=document.getElementById('workspaceck').innerHTML; // save code for undo and redo
	ckInitWorkspace();
	// ckInitDndForImageUpload();
//	ckInlineEditor();
});

function ckInitDndForImageUpload(workspace) {
	if (!workspace) workspace = ckGetWorkspace();
	$ck('.cktype[data-type="image"], [data-identifier="image"]', workspace).each(function(i, holder) {
		ckAddDndForImageUpload(holder);
	});
}

function ckGetWorkspace() {
	return $ck('.workspaceck');
}

function ckCleanInterfaceBeforeSave(workspace) {
	if (!workspace) workspace = ckGetWorkspace();
	workspace.find('.addcontent').remove();
	workspace.find('.ui-resizable-handle').remove();
	workspace.find('.blockck_width').remove();
	workspace.find('.addrow').remove();
	workspace.find('.editorck').remove();
	workspace.find('.editorckresponsive').remove();
	workspace.find('.ui-sortable').removeClass('ui-sortable');
	workspace.find('.ui-resizable').removeClass('ui-resizable');
	workspace.find('.editfocus').removeClass('editfocus');
	workspace.find('.cssfocus').removeClass('cssfocus');
	workspace.find('.ckfocus').removeClass('ckfocus');
	workspace.find('.animateck').removeClass('animateck');
	workspace.find('.ui-accordion-header').removeClass('ui-accordion-header-active').removeClass('ui-state-active').removeClass('ui-corner-top');
	try {
		workspace.find('.accordionsck').accordionck('destroy');
		workspace.find('.tabsck').tabsck('destroy');
	} catch(error) {
		console.error('PBCK LOG : ' + error);
	}
	workspace.find('> #system-readmore').removeAttr('style');
	workspace.find('.ckcolwidthedition').remove();
	workspace.find('.ckcolwidthediting').removeClass('ckcolwidthediting');
	workspace.find('.mce-content-body').removeClass('mce-content-body');
	workspace.find('.ckinlineeditable').removeClass('ckinlineeditable');
	workspace.find('.ckfakehover').removeClass('ckfakehover');
	workspace.removeClass('pagebuilderck');
	ckShowResponsiveSettings('1');
	ckCheckHtml('1');
	workspace.find('[id^="mce_"]').removeAttr('id');
	workspace.find('input[name^="mce_"]').remove();
	workspace.find('[contenteditable="true"]').removeAttr('contenteditable');
	ckFixBC();
	workspace.find('.ckimagedata').remove();
	workspace.find('.chzn-container').parent().find('select').css('style', '');
	workspace.find('.chzn-container').remove();
	workspace.find('sec').remove();
	ckMergeGooglefontscall();
//	ckBackupStyleTags(workspace);
}

function ckCleanContenttypeInterfaceBeforeSave(workspace) {
	if (!workspace) workspace = ckGetWorkspace();
	workspace.find('.uick-draggable').removeClass('uick-draggable');
	workspace.find('[data-original-title]').removeAttr('data-original-title');
	workspace.find('div.ckcontenttype[style]').removeAttr('style').removeAttr('title');
}

function ckInitWorkspace(workspace) {
	if (!workspace) workspace = ckGetWorkspace();

	ckCheckNestedRowsBC();
	$ck(document.body).append('<div id="ck_overlay"></div>');
	$ck(document.body).append('<div id="popup_editionck" class="ckpopup"></div>');
	$ck(document.body).append($ck('#menuck'));
	if ($ck('#workspaceck').length) ckDoActionsList[0]=document.getElementById('workspaceck').innerHTML; // save code for undo and redo
	// ckInitDndForImageUpload();

	if (! workspace.length 
			// iframe for the frontedition
			&& !$ck('#tckeditioniframe').length
			) {
		console.log('PBCK JS MESSAGE : no workspace found in the page. ckInitWorkspace aborted.');
		return;
	}

	console.log('PBCK JS MESSAGE : workspace found in the page. Everything is OK.');

	if (PAGEBUILDERCK.ISCONTENTTYPE != '1') { 
		ckInitInterface(workspace); 
	}
	ckMakeTooltip(workspace);
	ckInitContents(workspace);
	ckRemoveLinkRedirect();
	ckFixBC();
	ckAddDataOnImages();
	if (! $ck('.pagebuilderckparams').length && !workspace.hasClass('ckelementedition')) workspace.prepend('<div class="pagebuilderckparams" />');
	ckSetColorPalettes();
	if (! $ck('.googlefontscall').length && !workspace.hasClass('ckelementedition')) workspace.prepend('<div class="googlefontscall" />');
//	ckRestoreStyleTags(workspace);
	if (PAGEBUILDERCK.ISCONTENTTYPE == '1') { 
		ckInitContentTypes();
	}
	// clean the select list made by Bootstrap
	workspace.find('.chzn-container').parent().find('select.ckcontactfield').css('display', '');
	workspace.find('.chzn-container').remove();
	ckAddFakeLinkEvent();
	ckInlineEditor();
}

function ckAddFakeLinkEvent() {
	// add event to simulate link hover for preview of styles
	$ck('.pbck-has-link-wrap > .inner').on('mouseover', function() {
		$ck(this).addClass('ckfakehover');
	}).on('mouseleave', function() {
		$ck(this).removeClass('ckfakehover');
	});
}

function ckInitContentTypes(workspace) {
	if (!workspace) workspace = ckGetWorkspace();
	// only edit in normal page, nor in the content type edition page
	if (workspace.hasClass('ckcontenttypeedition')) return;
	workspace.find('.ckcontenttype').each(function() {
		var bloc = $ck(this);
		ckInitContentType(bloc)
	});
}

function ckInitContentType(bloc) {
	var myurl = PAGEBUILDERCK.URIPBCK + "&task=contenttype.ajaxLoadFields&" + PAGEBUILDERCK.TOKEN;
	var type = bloc.attr('data-type');
	$ck.ajax({
		type: "POST",
		url: myurl,
		data: {
			type: type
		}
	}).done(function(code) {
		var result = JSON.parse(code);
		if (result.status == '1') {
			var identifiers = result.identifiers.split('|');
			for (var i=0; i<identifiers.length; i++) {
				var identifier = identifiers[i];
				if (! bloc.find('[data-identifier="' + identifier + '"]').length) {
					console.log('Missing field : ' + identifier);
					ckUpdateContentType(bloc, type, identifier);
				}
			}
			bloc.find('[data-identifier]').each(function() {
				var identifier = $ck(this).attr('data-identifier');
				if (! identifiers.includes(identifier)) {
					$ck(this).hide().attr('data-enabled', '0');
				} else {
					$ck(this).show().attr('data-enabled', '1');
				}
			});
			ckInitDndForImageUpload();
			var returnFunc = 'ckInitContentType' + type;
			if (typeof(window[returnFunc]) == 'function') window[returnFunc](bloc);
		} else {
			alert(Joomla.JText._('CK_FAILED_TO_UPDATE_CONTENTTYPE', 'Failed'));
		}
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

function ckUpdateContentType(bloc, type, identifier) {
	var myurl = PAGEBUILDERCK.URIPBCK + "&task=contenttype.ajaxAddField&" + PAGEBUILDERCK.TOKEN;
	var blocid = bloc.attr('id');
	$ck.ajax({
		type: "POST",
		url: myurl,
		data: {
			type: type,
			identifier: identifier,
			blocid: blocid
		}
	}).done(function(code) {
//		bloc.append(code);
		ckUpdateContentTypeFieldPosition(bloc, type, identifier, code);
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

function ckUpdateContentTypeFieldPosition(bloc, type, identifier, newcode) {
	var myurl = PAGEBUILDERCK.URIPBCK + "&task=contenttype.ajaxGetFieldPosition&" + PAGEBUILDERCK.TOKEN;
	$ck.ajax({
		type: "POST",
		url: myurl,
		data: {
			type: type,
			identifier: identifier
		}
	}).done(function(code) {
		var field = bloc.find('[data-identifier="' + identifier + '"]');
		var result = JSON.parse(code);
		if (result.status == '1') {
			var position = result.position;
			if (position == 'first') {
				bloc.find('> .inner').prepend(newcode);
			} else if (position == 'last') {
				bloc.find('> .inner').append(newcode);
			} else {
				bloc.find('[data-identifier="' + position + '"]').after(newcode);
			}
			var identifierinitfunc = 'ckInit' + ckCapitalize(type) + ckCapitalize(identifier);
			if (typeof(window[identifierinitfunc]) == 'function') window[identifierinitfunc](bloc);
		} else {
			alert(Joomla.JText._('CK_FAILED_TO_UPDATE_CONTENTTYPE', 'Failed'));
		}
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

function ckSetColorPalettes() {
	var params = $ck('.pagebuilderckparams');
	params.attr('data-colorpalettefromtemplate', PAGEBUILDERCK.COLORSFROMTEMPLATE);
	params.attr('data-colorpalettefromsettings', PAGEBUILDERCK.COLORSFROMSETTINGS);
}

function ckBackupStyleTags(workspace) {
	workspace.find('style').each(function() {
		var $s = $ck(this);
		var styles = this.innerHTML;
//		styles = escapeHtml(styles);
		var $sClass = $s.attr('class') ? $s.attr('class') : '';
		$s.after('<div class="ckstylebackup ' + $sClass + '" style="display:none;">' + styles + '</div>');
		$s.remove();
	});
}

function ckRestoreStyleTags(workspace) {
	workspace.find('.ckstylebackup').each(function() {
		var $s = $ck(this).removeClass('ckstylebackup');
		var styles = this.innerHTML;
//		styles = unescapeHtml(styles);
		var $sClass = $s.attr('class') ? $s.attr('class') : '';
		$s.after('<style class="' + $sClass + '" style="display:none;">' + styles + '</style>');
		$s.remove();
	});
}

function escapeHtml(text) {
	var map = {
	  '&': '|amp|',
	  '<': '|lt|',
	  '>': '|gt|',
	  '"': '|quot|',
	  "'": '|039|'
	};

	return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

function unescapeHtml(text) {

	return text
		.replace(/\|amp\|/g, "&amp;")
		.replace(/\|lt\|/g, "&lt;")
		.replace(/\|gt\|/g, '>')
		.replace(/\|quot\|/g, "&quot;")
		.replace(/\|039\|/g, "&#039;");
}

function ckRemoveLinkRedirect() {
	// stop links redirection whithin the interface
	$ck('.workspaceck').find('a[href]').click(function(ev){ev.preventDefault();return false;})
}
/**
* Insert image from com_media
*/
function jInsertFieldValue(value, id) {
	$ck('#'+id).val(value);
	$ck('#'+id).trigger('change');
}

/**
 * Override the options if the nested rows has already been used before the option implementation
 */
function ckCheckNestedRowsBC() {
	if ($ck('.rowck .rowck').length > 0) {
		PAGEBUILDERCK.NESTEDROWS = '1';
	}
}

/**
* Backward Compatibility : Update the elements to keep the behavior though the versions
*/
function ckFixBC() {
	// for V2.0.3
	// add automatic stack alignement to columns in small resolution
	$ck('.rowck:not([class*="ckstack"])').each(function() {
		$row = $ck(this);
		if (! $row.hasClass('ckhide')) {
			// if no block width from boostrap, then remove useless css class
			if ($ck('.blockck', $row).length && ! $ck('.blockck[class*="span"]', $row).length) {
				$row.removeClass('row-fluid');
			}
			ckFixBCRow($row);
		}
		// fix B/C for old responsive css classes
		$row.find('.ckhidedesktop').addClass('ckhide5').removeClass('ckhidedesktop');
		$row.find('.ckhidephone').addClass('ckhide4').removeClass('ckhidephone');
	});
}

function ckFixBCRow(row) {
	if (! row.length) return;
//	row.removeClass('row-fluid');
	if (! row.attr('class').match(/ckstack/g) && ! row.attr('class').match(/ckhide/g) && row.hasClass('row-fluid')) {
		row.addClass('ckstack1').addClass('ckstack2').addClass('ckstack3');
	}
}

function ckInitContents(workspace) {
	if (!workspace) workspace = ckGetWorkspace();
	var activeTab = {active: parseInt($ck(this).attr('activetab'))};
	var options = Object.assign(accordionckOptions, activeTab);

	workspace.find('.accordionsck').each(function() {
		$ck(this).accordionck(options);
	});
	workspace.find('.tabsck').each(function() {
		$ck(this).tabsck({
			active: parseInt($ck(this).attr('activetab'))
		});
	});
}

function ckInitInterface(workspace) {
	if (!workspace) workspace = ckGetWorkspace();

	// add animate class to make all items visible
	workspace.find('.rowck, .blockck').addClass('animateck');

	// init the wrappers
	workspace.find('> .wrapperck').each(function(i, wrapper) {
		wrapper = $ck(wrapper);
		if (! wrapper.find('> .ckstyle').length) { // for beta version retrocompatibility
			wrapper.prepend('<div class="ckstyle"></div>');
		}
		ckAddWrapperEdition(wrapper);
//		ckMakeRowSortableInWrapper(wrapper);
	});
	// init the rows in the wrappers
//	$ck('.wrapperck', workspace).each(function() {
//		ckMakeRowSortableInWrapper($ck(this));
//	});

	ckMakeRowsSortable(workspace);
	workspace.find('.rowck').each(function(i, row) {
		row = $ck(row);
		// check user rights
		var acl = row.attr('data-acl-edit') ? row.attr('data-acl-edit') : '';
		if (! ckCheckUserRightsFromAcl(acl))
			return true;

		if (! row.find('> .ckstyle').length) { // for beta version retrocompatibility
			row.prepend('<div class="ckstyle"></div>');
		}
		ckAddRowEditionEvents(row);
		ckMakeBlocksSortable(row);
		row.find('> .inner > .blockck').each(function() {
			block = $ck(this);
			ckAddBlockEditionEvents(block);
			ckMakeItemsSortable(block);
			block.find('> .inner > .innercontent > .cktype').each(function() {
				// item = $ck(this);
				ckAddItemEditionEvents($ck(this));
			});
		});
		ckInitDndForImageUpload(row);
		ckInlineEditor(row);
	});

	workspace.find('> #system-readmore').each(function() {
		block = $ck(this);
		ckAddBlockEditionEvents(block, 'readmore');
	});

	if (! workspace.find('.rowck').length && !workspace.hasClass('ckelementedition')) {
		ckAddRow(false, workspace);
	}
	// for my elements edition only
	if (workspace.hasClass('ckelementedition')) {
		workspace.find('.cktype').each(function() {
			ckAddItemEditionEvents($ck(this));
		});
	}

	// make the menu items draggable
	ckMakeItemsDraggable();

//	var connectToSortable = PAGEBUILDERCK.NESTEDROWS === '1' ? ".workspaceck, .wrapperck > .inner, .innercontent" : ".workspaceck";
	// make the menu items draggable
	$ck('.menuitemck[data-type="row"]').draggable({
		connectToSortable: ".workspaceck",
		// iframeFix: true,
//		connectToSortable: connectToSortable,
		helper: "clone",
		// appendTo: ".workspaceck",
		forcePlaceholderSize: true,
		zIndex: "999999",
		tolerance: "pointer",
		start: function( event, ui ){
			$ck('#menuck').css('overflow', 'visible');
			$ck('.workspaceck .rowck').css('margin-top', '10px').css('margin-bottom', '10px').addClass('ckfocus');
		},
		stop: function( event, ui ){
			$ck('#menuck').css('overflow', '');
			$ck('.workspaceck .rowck').css('margin-top', '').css('margin-bottom', '').removeClass('ckfocus');;
		}
	});
	$ck('.menuitemck[data-type="rowinrow"]').draggable({
		connectToSortable: ".innercontent",
		helper: "clone",
		// appendTo: ".workspaceck",
		forcePlaceholderSize: true,
		zIndex: "999999",
		tolerance: "pointer",
		start: function( event, ui ){
			$ck('#menuck').css('overflow', 'visible');
			$ck('.workspaceck .rowck').css('margin-top', '10px').css('margin-bottom', '10px').addClass('ckfocus');
		},
		stop: function( event, ui ){
			$ck('#menuck').css('overflow', '');
			$ck('.workspaceck .rowck').css('margin-top', '').css('margin-bottom', '').removeClass('ckfocus');;
		}
	});
	$ck('.menuitemck[data-type="readmore"], .menuitemck[data-group="layout"]:not([data-type*="row"])').draggable({
		connectToSortable: ".workspaceck",
		helper: "clone",
		// appendTo: ".workspaceck",
		forcePlaceholderSize: true,
		zIndex: "999999",
		tolerance: "pointer",
		start: function( event, ui ){
			$ck('#menuck').css('overflow', 'visible');
		},
		stop: function( event, ui ){
			$ck('#menuck').css('overflow', '');
		}
	});
	ckConnectRowWithWorkspace();
	$ck('.menuitemck[data-type="readmore"]').on('mousedown', function() {
		$ck('.workspaceck').sortable( "option", "connectWith", "" );
	});
}

function ckConnectRowWithWorkspace() {
	if (PAGEBUILDERCK.NESTEDROWS === '1') {
		// fix to make the rows connected to the existing wrappers
		$ck('.rowck > .editorck .controlMove').on('mousedown', function() {
//			$ck('.workspaceck').sortable( "option", "connectWith", ".innercontent" );
		});
	}
}
/*
function ckMakeRowSortableInWrapper(wrapper) {
	wrapper.find('> .inner').sortable({
		items: ".rowck",
		helper: "clone",
		handle: "> .editorck > .ckfields  > .controlMove",
		forcePlaceholderSize: true,
		// forceHelperSize: true,
//		axis: "y",
		tolerance: "pointer",
		placeholder: "placeholderck",
		connectWith: ".wrapperck > .inner, .workspaceck, .innercontent",
//		zIndex: 9999,
		activate: function (event, ui) {
			if (ui != undefined && !$ck(ui.item).hasClass('menuitemck')) {
				$ck(ui.helper).css('width', '250px').css('height', '100px').css('overflow', 'hidden');
			}
		},
		out: function (event, ui) {
			if ($ck(this).data().uickSortable.currentContainer){
				var receiver = $ck(this).data().uickSortable.currentContainer.bindings;
				receiver.parent().removeClass('ckfocus');
			}
		},
		start: function (event, ui) {
			if ($ck(this).data().uickSortable.currentContainer){
				var receiver = $ck(this).data().uickSortable.currentContainer.bindings;
				receiver.parent().addClass('ckfocus');
			}
		},
		stop: function( event, ui ){
			if ($ck(this).data().uickSortable.currentContainer){
				var receiver = $ck(this).data().uickSortable.currentContainer.bindings;
				receiver.parent().removeClass('ckfocus');
			}
			if (ui != undefined) {
					$ck(ui.item).css('width', '').css('height', '').css('overflow', '');
			}
			if (! $ck(ui.item).hasClass('menuitemck')) {
					ckSaveAction('ckMakeRowsSortable'); // only save action if not from left menu
			}
		},
		receive: function( event, ui ) {
			// need to init the connectWith option to avoid wrappers in wrappers
			$ck('.workspaceck').sortable( "option", "connectWith", "" );

			if (ui.sender.hasClass('menuitemck') && ui.sender.hasClass('ckmyelement')) {
					var newblock = $ck(this).find('.menuitemck');
					newblock.css('float', 'none').empty().addClass('ckwait');
					ckAddElementItem(ui.sender.attr('data-type'), newblock);
			} else if (ui.sender.hasClass('menuitemck')) {
					var newblock = $ck(this).find('.menuitemck');
					newblock.css('float', 'none').empty().addClass('ckwait');
					ckAddItem(ui.sender.attr('data-type'), newblock)
			} else {

			}
		}
	});
}
*/

function ckMakeItemsDraggable() {
	// make the menu items draggable
	$ck('.menuitemck:not([data-type="row"]):not([data-type="wrapper"])').draggable({
		connectToSortable: ".blockck .innercontent",
		helper: "clone",
//		appendTo: "body",
		forcePlaceholderSize: true,
		zIndex: "999999",
		tolerance: "pointer",
		start: function( event, ui ){
			$ck('#menuck').css('overflow', 'visible');
		},
		stop: function( event, ui ){
			$ck('#menuck').css('overflow', '');
		}
	});
}

function ckInitOptionsTabs() {
	$ck('#elementscontainer div.tab:not(.current)').hide();
	$ck('#elementscontainer .menulink').each(function(i, tab) {
		tab = $ck(tab);
		tab.click(function() {
			if (!$ck(this).hasClass('open') && !$ck(this).hasClass('current')) {
				// $ck(this).removeClass('current');
				// $ck('#' + tab.attr('tab')).removeClass('current');
				$ck(this).addClass('open');
				$ck('#elementscontainer .tab.tab_fullscreen').fadeOut('fast');
				$ck('#' + tab.attr('tab')).slideDown('fast');
			} else {
				// $ck(this).removeClass('current');
				$ck('#' + tab.attr('tab')).slideUp('fast');
				$ck(this).removeClass('open');
			}
			$ck(this).removeClass('current');
			$ck('#' + tab.attr('tab')).removeClass('current');
		});
	});
}

function ckInitColorPickers(container) {
	if (! container) container = $ck(document.body);
	var startcolor = '';
	$ck('.colorPicker', container).each(function(i, picker) {
		picker = $ck(picker);
		picker.mousedown(function() {
			if (picker.val()) {
				startcolor = picker.val().replace('#','');
			} else {
				startcolor = 'fff000';
			}
			new ColpickCK(picker, {
//			picker.colpick({
				layout:'full',
				color: startcolor,
				livePreview: true,
				onChange:function(hsb,hex,rgb,el,bySetColor) {
					$ck(el).css('background-color','#'+hex);
					setpickercolor(picker);
					// force the # character
					if (picker.val().indexOf("#") == -1) {
						picker.val('#'+picker.val());
					}
					// Fill the text box just if the color was set using the picker, and not the colpickSetColor function.
					if(!bySetColor) $ck(el).val('#' + hex);
				},
				onSubmit:function(hsb,hex,rgb,el,bySetColor) {
//					picker.trigger('blur');console.log('chang');
				},
				onClean: function(button, cal) {
					picker.val('');
					picker.css('background', 'none');
//					picker.trigger('blur');console.log('onClean');
				},
				onCopy: function(color, cal) {
					CLIPBOARDCOLORCK = picker.val();
				},
				onPaste: function(color, cal) {
					picker.val(CLIPBOARDCOLORCK);
					picker.css('background', CLIPBOARDCOLORCK);
//					picker.trigger('blur');console.log('onPaste');
					setpickercolor(picker);
				},
				onPaletteColor: function(hsb,hex,rgb,el,bySetColor) {
					picker.val('#'+hex);
					picker.css('background','#'+hex);
//					picker.trigger('blur');console.log('onPaletteColor');
					setpickercolor(picker);
				},
			});
		}).keyup(function(){
			$ck(this).colpickSetColor(this.value);
//				picker.trigger('blur');console.log('keyup');
		});
	});
}

/**
 * Method to give a black or white color to have a good contrast
 */
function setpickercolor(picker) {
	pickercolor =
			0.213 * hexToR(picker.val()) / 100 +
			0.715 * hexToG(picker.val()) / 100 +
			0.072 * hexToB(picker.val()) / 100
			< 1.5 ? '#FFF' : '#000';
	picker.css('color', pickercolor);
	return pickercolor;
}

/*
 * Functions to manage colors conversion
 *
 */
function hexToR(h) {
	return parseInt((cutHex(h)).substring(0, 2), 16)
}
function hexToG(h) {
	return parseInt((cutHex(h)).substring(2, 4), 16)
}
function hexToB(h) {
	return parseInt((cutHex(h)).substring(4, 6), 16)
}
function cutHex(h) {
	return (h.charAt(0) == "#") ? h.substring(1, 7) : h
}
function hexToRGB(h) {
	return 'rgb(' + hexToR(h) + ',' + hexToG(h) + ',' + hexToB(h) + ')';
}

function ckInitAccordions() {
	$ck('.menustylesblockaccordion').hide();
	$ck('.ckproperty').each(function(i, tab) {
		tab = $ck(tab);
		// $ck('.menustylesblockaccordion', tab).first().show();
		// $ck('.menustylesblocktitle', tab).first().addClass('open');
		$ck('.menustylesblocktitle', tab).click(function() {
			if (!$ck(this).hasClass('open')) {
				$ck('.menustylesblockaccordion', tab).slideUp('fast');
				blocstyle = $ck(this).next('.menustylesblockaccordion');
				$ck('.menustylesblocktitle', tab).removeClass('open');
				$ck(this).addClass('open');
				blocstyle.slideDown('fast');
			} else {
				blocstyle = $ck(this).next('.menustylesblockaccordion');
				blocstyle.slideUp('fast');
				$ck(this).removeClass('open');
			}
		});
	});
}

function ckInitMenustylesAccordion(tab) {
	$ck('.menustylesblockaccordion', tab).first().show();
	$ck('.menustylesblocktitle', tab).first().addClass('open');
	$ck('.menustylesblocktitle', tab).click(function() {
		if (!$ck(this).hasClass('open')) {
			$ck('.menustylesblockaccordion', tab).slideUp('fast');
			blocstyle = $ck(this).next('.menustylesblockaccordion');
			$ck('.menustylesblocktitle', tab).removeClass('open');
			$ck(this).addClass('open');
			blocstyle.slideDown('fast');
		} else {
			blocstyle = $ck(this).next('.menustylesblockaccordion');
			blocstyle.slideUp('fast');
			$ck(this).removeClass('open');
		}
	});
}

function ckAddRowEditionEvents(row) {
		ckAddRowEdition(row);
}

function ckAddBlockEditionEvents(block, type) {
	if (!type) type = '';
	block.mouseenter(function() {
		var i = block.parents('.rowck').length;
		ckAddEdition(this, i, type);
	}).mouseleave(function() {
		var t = setTimeout( function() {
			block.removeClass('highlight_delete');
			ckRemoveEdition(block);
		}, 200);
		
	});
}

function ckAddItemEditionEvents(el) {
	el.mouseenter(function() {
		ckAddEdition(this);
	}).mouseleave(function() {
		$ck(this).removeClass('highlight_delete');
		ckRemoveEdition(this);
	});
}

function ckHtmlRow(newrowid) {
	var row = 
	$ck('<div class="rowck ckstack3 ckstack2 ckstack1" id="'+ckGetUniqueId('row_')+'">'
			+'<div class="inner animate clearfix"></div>'
			+'<div class="ckstyle"></div>'
	+'</div>');
	return row;
}

function ckAddRow(cur_row, workspace) {
	if (!workspace) workspace = ckGetWorkspace();
	var newrowid = ckGetUniqueId('row_');
	var row = ckHtmlRow(newrowid);
	// $ck('.workspaceck .addrow').before(row);
	if (cur_row == false) {
		workspace.append(row);
	} else {
		cur_row.after(row);
	}
	ckAddBlock(row);
	ckAddRowEditionEvents(row);
	ckMakeBlocksSortable(row);
	ckMakeTooltip(row);
	ckConnectRowWithWorkspace();
	return row;
}

function ckAddWrapper(cur_row, workspace) {
	if (!workspace) workspace = ckGetWorkspace();
	var wrapper = 
	$ck('<div class="wrapperck" id="'+ckGetUniqueId('wrapper_')+'">'
			+'<div class="inner animate clearfix"></div>'
			+'<div class="ckstyle"></div>'
	+'</div>');
	
	// $ck('.workspaceck .addrow').before(row);
	if (cur_row == false) {
		workspace.append(wrapper);
	} else {
		cur_row.after(wrapper);
	}
//	ckAddBlock(row);
	ckAddWrapperEdition(wrapper);
//	ckMakeRowSortableInWrapper(wrapper);
	ckMakeTooltip(wrapper);
}

function ckHtmlBlock(newblockid) {
	var newblock = 
	$ck('<div class="blockck" id="'+newblockid+'">'
		+ '<div class="ckstyle"></div>'
		+ '<div class="inner animate resizable">'
			+ '<div class="innercontent">'
				// + response
			+ '</div>'
		+ '</div>'
	+ '</div>');
	return newblock;
}

function ckAddBlock(row) {
	var newblockid = ckGetUniqueId('block_');
	newblock = ckHtmlBlock(newblockid);
	$ck('> .inner', row).append(newblock);
	ckAddBlockEditionEvents(newblock);
	ckInitBlocksSize(row);
	ckMakeItemsSortable(newblock);
	ckMakeTooltip(newblock);
	ckAddColumnsSuggestions();
	return newblockid;
}

function ckAddItem(type, currentbloc) {
	ckHideContentList();
	var myurl = "index.php?option=com_pagebuilderck&view=content";
	var id = ckGetUniqueId();
	$ck.ajax({
	type: "POST",
	url: myurl,
	// dataType : 'json',
	data: {
		cktype: type,
		ckid: id
		}
	}).done(function(result) {
		if (type == 'row' || type == 'rowinrow') {
			ckAddRow(currentbloc);
			$ck(currentbloc).remove();
			ckSaveAction();
		} else if (type == 'wrapper') {
			ckAddWrapper(currentbloc);
			$ck(currentbloc).remove();
			ckSaveAction();
		} else {
			el = $ck(result);
			if (currentbloc) {
				$ck(currentbloc).fadeOut(500, function() {
					$ck(currentbloc).before(el);
					$ck(currentbloc).remove();
	//				el.trigger('show');
					if (el.attr('onshow')) {
						$ck(document.body).append('<script id="cktempscript">function cktempscript() {' + el.attr('onshow').replace('$ck(this)', '$ck("#' + el.attr('id') + '")') + '}</script>');
						cktempscript();
						$ck('#cktempscript').remove();
					}
					ckSaveAction();
					ckInlineEditor();
				});
			} else {
				$ck('.ckfocus').append(el).removeClass('ckfocus');
				ckSaveAction();
			}
			if (PAGEBUILDERCK.ISCONTENTTYPE !== '1') {
				ckAddItemEditionEvents(el);
				ckMakeItemsSortable($ck($ck('#'+id).parents('.blockck')[0]));
			}
			ckTriggerAfterAdditem(id);
			if (el.hasClass('ckcontenttype')) ckInitContentType(el);
			ckInlineEditor();
		}
	}).fail(function() {
		alert('A problem occured when trying to load the content. Please retry.');
		$ck(currentbloc).remove();
	});
}

function ckAddRowItem(type, currentbloc) {
	ckHideContentList();
	// var block_inner = $ck('.ckfocus');
	var myurl = "index.php?option=com_pagebuilderck&view=content";
	var id = ckGetUniqueId();
	$ck.ajax({
	type: "POST",
	url: myurl,
	// dataType : 'json',
	data: {
		cktype: type,
		ckid: id
		}
	}).done(function(result) {
		if (type == 'row') {
			ckAddRow(currentbloc);
			$ck(currentbloc).remove();
			ckSaveAction();
		} else if (type == 'wrapper') {
			ckAddWrapper(currentbloc);
			$ck(currentbloc).remove();
			ckSaveAction();
		} else {
			item = $ck(result);
			if (currentbloc) {
				$ck(currentbloc).fadeOut(500, function() {
					$ck(currentbloc).before(item);
					$ck(currentbloc).remove();
//					item.trigger('show');
					if (item.attr('onshow')) {
					$ck(document.body).append('<script id="cktempscript">function cktempscript() {' + item.attr('onshow') + '}</script>');
					cktempscript();
					$ck('#cktempscript').remove();
				}
					ckSaveAction();
				});
			} else {
				$ck('.ckfocus').append(item).removeClass('ckfocus');
				ckSaveAction();
			}
			item.mouseenter(function() {
				ckAddEdition(this, 0 , type);
			}).mouseleave(function() {
				$ck(this).removeClass('highlight_delete');
				ckRemoveEdition(this);
			});
			ckTriggerAfterAdditem(id);
		}
	}).fail(function() {
		alert('A problem occured when trying to load the content. Please retry.');
		$ck(currentbloc).remove();
	});
}

function ckAddContentTypeItem(type, currentbloc) {
	$ck(currentbloc).removeClass('menuitemck').addClass('cktype');
	var id = ckGetUniqueId();
	$ck(currentbloc).attr('id', id);
	$ck(currentbloc).find('> div').addClass('ckcontenttype-infos');
	var group = $ck(currentbloc).attr('data-group');
	$ck(currentbloc).append('<div class="ckstyle"></div><div class="' + group + 'ck-group inner"><div class="' + group + 'ck-label">[label]</div><div class="' + group + 'ck-field">[value]</div></div>')
	ckMakeContentTypeSortable();
	
	ckAddItemEditionEvents(currentbloc);
//		ckMakeItemsSortable($ck($ck('#'+id).parents('.blockck')[0]));
//		ckTriggerAfterAdditem(id);
}

function ckMakeContentTypeSortable() {
	$ck('.innercontent').sortable( "option", "items", ".ckcontenttype" );
//	$ck('.innercontent').sortable( "option", "handle", "" );
}

function ckMergeGooglefontscall() {
	var workspace = ckGetWorkspace();
	if (!workspace.hasClass('ckelementedition')) {
		$ck('.googlefontscall').remove();
		workspace.prepend('<div class="googlefontscall"></div>');
	}
	var gfontnames = new Array();
	workspace.find('.rowck, .blockck, .cktype').each(function() {
		var bloc = $ck(this);
		$ck('> .ckprops', bloc).each(function(i, ckprops) {
			ckprops = $ck(ckprops);
			fieldslist = ckprops.attr('fieldslist') ? ckprops.attr('fieldslist').split(',') : Array();
			for (j=0;j<fieldslist.length;j++) {
				fieldname = fieldslist[j];
				cssvalue = ckprops.attr(fieldname);
				field = $ck('#' + fieldname);
				if (fieldname.indexOf('googlefont') > -1) {
					fontname = ckCapitalize(cssvalue).trim("'");
					if (gfontnames.indexOf(fontname) == -1) gfontnames.push(fontname);
				}
			}
		});
	});
	for (var i=0;i<gfontnames.length;i++) {
		fonturl = "https://fonts.googleapis.com/css?family="+gfontnames[i].replace(' ', '+');
		ckAddGooglefontStylesheet(fonturl);
	}
}

function ckAddElementItem(type, currentbloc) {
	ckHideContentList();
	var myurl = PAGEBUILDERCK.URIBASE + '/index.php?option=com_pagebuilderck&task=ajaxAddElementItem&' + PAGEBUILDERCK.TOKEN;
	var id = ckGetUniqueId();
	$ck.ajax({
	type: "POST",
	url: myurl,
	// dataType : 'json',
	data: {
		id: currentbloc.attr('data-id')
		}
	}).done(function(result) {
		if (type == 'row' || type == 'wrapper') {
			el = $ck(result);
			if (currentbloc) {
				// $ck(currentbloc).fadeOut(500, function() {
					$ck(currentbloc).before(el);
					$ck(currentbloc).remove();

					if (el.attr('onshow')) {
						$ck(document.body).append('<script id="cktempscript">function cktempscript() {' + el.attr('onshow') + '}</script>');
						cktempscript();
						$ck('#cktempscript').remove();
					}
					// ckSaveAction();
				// });
			} else {
				$ck('.ckfocus').append(el).removeClass('ckfocus');
				// ckSaveAction();
			}

			ckMergeGooglefontscall();
			if (type == 'wrapper') {
				var wrappercopyid = ckGetUniqueId('wrapper_');
				var elcopy = el;
				elcopy.removeClass('editfocus');
				elcopy.find('> .editorck').remove();
				ckReplaceId(elcopy, wrappercopyid);
//				ckMakeRowSortableInWrapper(elcopy);
				ckAddWrapperEdition(elcopy);

				elcopy.find('.rowck').each(function() {
					$row = $ck(this);
					$row.removeClass('editfocus');
					$row.find('> .editorck').remove();
					var copyid = ckGetUniqueId('row_');
					// copy the styles
					ckReplaceId($row, copyid);
					ckMakeBlocksSortable($row);
					ckAddRowEditionEvents($row);
				});
			} else {
				// manage the new ids
				var rowcopyid = ckGetUniqueId('row_');
				var elcopy = el;
				elcopy.removeClass('editfocus');
				elcopy.find('> .editorck').remove();
				ckReplaceId(elcopy, rowcopyid);

				ckAddRowEditionEvents(elcopy);
				ckMakeBlocksSortable(elcopy);
				
			}

			// for tiny inline editing
			if (elcopy.find('[id^="mce_"]').length) {
				elcopy.find('[id^="mce_"]').removeAttr('id');
			}

			elcopy.find('.blockck, .cktype').each(function() {
				$this = $ck(this);
				$this.removeClass('editfocus');
				// init the effect if needed
				if ($this.hasClass('cktype') && $this.find('.tabsck').length) {
					$this.find('.tabsck').tabsck();
				}
				if ($this.hasClass('cktype') && $this.find('.accordionsck').length) {
					$this.find('.accordionsck').accordionck();
				}
				
				var prefix = '';
				if ($this.hasClass('blockck')) {
					prefix = 'block_';
					ckMakeItemsSortable(elcopy);
					ckAddBlockEditionEvents($this);
				} else {
					ckAddItemEditionEvents($this);
				}

				// add dnd for image
				if ($this.attr('data-type') == 'image') ckAddDndForImageUpload($this[0]);

				var copyid = ckGetUniqueId(prefix);
				// copy the styles
				ckReplaceId($this, copyid);
			});
			
			ckMakeTooltip(elcopy);
			ckTriggerAfterAdditem(id);
			ckSaveAction();
			ckInlineEditor();
			
		} else {
			el = $ck(result);
			if (currentbloc) {
				$ck(currentbloc).fadeOut(500, function() {
					$ck(currentbloc).before(el);
					$ck(currentbloc).remove();
	//				el.trigger('show');
					if (el.attr('onshow')) {
						$ck(document.body).append('<script id="cktempscript">function cktempscript() {' + el.attr('onshow').replace('$ck(this)', '$ck("#' + el.attr('id') + '")') + '}</script>');
						cktempscript();
						$ck('#cktempscript').remove();
					}
					ckSaveAction();
					ckInlineEditor();
				});
			} else {
				$ck('.ckfocus').append(el).removeClass('ckfocus');
				ckSaveAction();
			}
			ckMergeGooglefontscall();
			
			var copy = el;
			copyid = ckGetUniqueId();
			// copy the styles
			ckReplaceId(copy, copyid);
			// copy.attr('id', copyid);

			copy.removeClass('editfocus');
			ckAddItemEditionEvents(copy);

			// init the effect if needed
			if (copy.find('.tabsck').length) {
				copy.find('.tabsck').tabsck();
			}
			if (copy.find('.accordionsck').length) {
				copy.find('.accordionsck').accordionck();
			}
			// for tiny inline editing
			if (copy.find('[id^="mce_"]').length) {
				copy.find('[id^="mce_"]').removeAttr('id');
			}

			// add dnd for image
			if (copy.attr('data-type') == 'image') ckAddDndForImageUpload(copy[0]);

			// copy the styles
			
			// var re = new RegExp(blocid, 'g');
			// copy.find('.ckstyle').html(bloc.find('.ckstyle').html().replace(re,copyid));
			ckSaveAction();
			ckInlineEditor();
			
			
			
			
			
			// ckAddItemEditionEvents(el);
			// ckMakeItemsSortable($ck($ck('#'+id).parents('.blockck')[0]));
			ckTriggerAfterAdditem(id);
		}
	}).fail(function() {
		alert('A problem occured when trying to load the content. Please retry.');
		$ck(currentbloc).remove();
	});
}

// empty function to override in each layout if needed
function ckTriggerAfterAdditem(id) {
	return;
}

function ckMakeRowsSortable(workspace) {
	if (!workspace) workspace = ckGetWorkspace();
//	var items = PAGEBUILDERCK.NESTEDROWS === '1' ? ".rowck, > #system-readmore, > .wrapperck" : "> .rowck, > #system-readmore";
	workspace.sortable({
		items: "> .rowck, > #system-readmore",
		helper: "clone",
//		axis: "y",
		handle: "> .editorck > .ckfields  > .controlMove",
		connectWith: ".workspaceck",
		forcePlaceholderSize: true,
//		forceHelperSize: true,
		tolerance: "pointer",
		placeholder: "placeholderck",
		// zIndex: 9999,
		activate: function (event, ui) {
			$ck(this).sortable("refreshPositions");
			if (ui != undefined && !$ck(ui.item).hasClass('menuitemck') && !$ck(ui.item).hasClass('ckcontenttype') && !$ck(ui.item).hasClass('ckpageitem')) {
				$ck(ui.helper).css('width', '250px').css('height', '100px').css('overflow', 'hidden');
			}
		},
		over: function( event, ui ) {

		},
		start: function( event, ui ) {

		},
		receive: function( event, ui ) {

			if (ui.sender.hasClass('menuitemck') && ui.sender.attr('data-type') == 'readmore') {
				if (workspace.find('#system-readmore').length) {
					alert('There is already a Readmore in your content. You can only have one readmore.');
					return false;
				}
			}
			if (ui.sender.hasClass('menuitemck') && ui.sender.hasClass('ckmyelement')) {
				var newblock = $ck(this).find('.menuitemck');
				newblock.css('float', 'none').empty().addClass('ckwait');
				ckAddElementItem(ui.sender.attr('data-type'), newblock);
			} else if (ui.sender.hasClass('menuitemck')) {
				var newblock = $ck(this).find('.menuitemck');
				newblock.css('float', 'none').empty().addClass('ckwait');
				ckAddRowItem(ui.sender.attr('data-type'), newblock);
			} else if (ui.sender.hasClass('ckgalleryitem')) {
				var newblock = $ck(this).find('.ckgalleryitem');
				var name = ui.sender.attr('data-name');
				var cat = ui.sender.attr('data-category');
				ckLoadPageFromMediaLibrary(cat + '/' + name, newblock)
			} else if (ui.sender.hasClass('ckpageitem')) {
				var newblock = $ck(this).find('.ckpageitem');
				var id = ui.sender.attr('data-id');
				ckLoadPageFromPagebuilder(id, newblock)
			}
		}
	});
//	workspace.sortable( "option", "axis", "y" );
}

function ckMakeBlocksSortable(row) {
	row.sortable({
		items: ".blockck",
		helper: "clone",
		// axis: "x",
		handle: "> .editorck > .ckfields  > .controlMove",
		forcePlaceholderSize: true,
		// forceHelperSize: true,
		tolerance: "pointer",
		placeholder: "placeholderchild",
//		zIndex: 9999,
		activate: function (event, ui) {
			$ck(this).sortable("refreshPositions");
		},
		sort: function( event, ui ) {
			ui.helper.find('.editorck').hide();
		},
		start: function( event, ui ){
			ui.placeholder.width(parseInt($ck('> .inner',ui.helper).width()));
			ui.placeholder.append('<div class="inner" />')
		},
		stop: function( event, ui ) {
			ckSaveAction();
			ui.item.css('display', '');
		}
	});
}

function ckMakeItemsSortable(block) {
	$ck('.innercontent', block).sortable({
		connectWith: ".innercontent",
		items: '.cktype, .rowck',
		helper: "clone",
		// dropOnEmpty: true,
		handle: ".controlMoveItem, > .roweditor > .ckfields > .controlMove",
		tolerance: "pointer",
		// forcePlaceholderSize: true,
		placeholder: "placeholderck",
		// cancel: 'div',
		activate: function (event, ui) {
			if (ui != undefined && !$ck(ui.item).hasClass('menuitemck') && !$ck(ui.item).hasClass('ckcontenttype') && !$ck(ui.item).hasClass('ckpageitem')) {
				$ck(ui.helper).css('width', '250px').css('height', '100px').css('overflow', 'hidden');
			}
		},
		stop: function( event, ui ){
			if (ui != undefined) {
				$ck(ui.item).css('width', '').css('height', '').css('overflow', '');
			}
			if (! $ck(ui.item).hasClass('menuitemck')) {
				ckSaveAction('ckMakeItemsSortable'); // only save action if not from left menu
			}
			// ui.placeholder.width(parseInt($ck('> .inner',ui.helper).width()));
			// ui.placeholder.append('<div class="inner" />')
		},
		receive: function( event, ui ) {
			if (ui.sender.hasClass('menuitemck') && ui.sender.hasClass('ckmyelement')) {
				var newblock = $ck(this).find('.menuitemck');
				newblock.css('float', 'none').empty().addClass('ckwait');
				ckAddElementItem(ui.sender.attr('data-type'), newblock);
			} else if (ui.sender.hasClass('menuitemck') && ui.sender.hasClass('ckcontenttype')) {
				var newblock = $ck(this).find('.menuitemck');
//				newblock.css('float', 'none').empty().addClass('ckwait');
				ckAddContentTypeItem(ui.sender.attr('data-type'), newblock);
			} else if (ui.sender.hasClass('menuitemck')) {
				var newblock = $ck(this).find('.menuitemck');
				newblock.css('float', 'none').empty().addClass('ckwait');
				ckAddItem(ui.sender.attr('data-type'), newblock)
				// createBloc(newblock, ui.sender.attr('data-type'));
				// makeRowcontainerSortable($ck('ckrowcontainer'));
			} else {
				// newblock.remove();
			}
		}
	});
}

function ckInitBlocksSize(row) {
	// check if we don't want to calculate automatically, then return
	if (row.hasClass('ckadvancedlayout')) {
		ckSetColumnWidth(row.find('> .inner > .blockck').last(), row.find('> .inner > .blockck').last().prev().attr('data-width'));
		if (row.find('.ckcolwidthselect').length) ckEditColumns(row, true);
		return;
	}
	var number_blocks = row.find('> .inner > .blockck').length;
	var gutter = ckGetRowGutterValue(row);
	var default_data_width = 100 / number_blocks;
	var default_real_width = ( 100 - ( (number_blocks - 1) * parseFloat(gutter) ) ) / number_blocks;
	row.find('> .inner > .blockck').each(function() {
		$ck(this).attr('class', function(i, c) {
			return c.replace(/(^|\s)span\S+/g, ''); // backward compat to remove old bootstrap styles
		});
		$ck(this).attr('data-real-width', default_real_width + '%').attr('data-width', default_data_width);
		if ($ck(this).find('.ckcolwidthselect').length) $ck(this).find('.ckcolwidthselect').val(default_data_width);
	});
	ckFixBCRow(row);
	row.removeClass('row-fluid');
	ckSetColumnsWidth(row);
	if (row.find('.ckcolwidthselect').length) ckEditColumns(row, true);
	ckSaveAction();
}

function ckGetObjectAnyway(foobar) {
	if (! (foobar instanceof $ck)) {
		if (! foobar.id && (typeof foobar == 'string' && foobar.indexOf('#') == -1)) {
			foobar = $ck('#' + foobar);
		} else {
			foobar = $ck(foobar);
		}
	}
	return foobar;
}

function ckRemoveBlock(block) {
	block = ckGetObjectAnyway(block);
	var row = $ck($ck(block).parents('.rowck')[0]);
	if (!confirm(Joomla.JText._('CK_CONFIRM_DELETE','CK_CONFIRM_DELETE'))) return;
	$ck(block).remove();
	$ck('.cktooltip').remove();
	// check if the last block is resizable, disable it
	if (row.find('.blockck').last().is('.ui-resizable')) {
		row.find('.blockck').last().resizable('destroy');
	}
	// check if there is just one block left
	if (! row.find('.blockck').length) {
		ckAddBlock(row);
	}
	// give the correct width to the elements
	ckInitBlocksSize(row);
	ckAddColumnsSuggestions();
}

function ckRemoveWrapper(wrapper) {
	ckRemoveRow(wrapper);
	$ck('.cktooltip').remove();
}

function ckRemoveRow(row) {
	row = ckGetObjectAnyway(row);
	if (!confirm(Joomla.JText._('CK_CONFIRM_DELETE','CK_CONFIRM_DELETE'))) return;
	row.remove();
	$ck('.cktooltip').remove();
	// if we delete the last row, then add a new empty one
	if (! $ck('.workspaceck .rowck').length) {
		ckAddRow(false);
	}
}

function ckRemoveItem(el) {
	el = ckGetObjectAnyway(el);
	if (!confirm(Joomla.JText._('CK_CONFIRM_DELETE','CK_CONFIRM_DELETE'))) return;
	$ck(el).remove();
	$ck('.cktooltip').remove();
}

function ckAddWrapperEdition(bloc) {
	bloc = $ck(bloc);
	if (bloc.hasClass('ui-sortable-helper')) 
		return;
	if ($ck('> .editorck', bloc).length)
		return;
	bloc.css('position','relative');
	var editor = '<div class="editorck wrappereditor" id="' + bloc.attr('id') + '-edition"></div>';
	editor = $ck(editor);
	editor.css({
		'left': '-29px',
		'top': '0px',
		'position': 'absolute',
		'z-index': 99,
		'height': '30px'
	});
	ckAddWrapperEditionControls(editor, bloc);
	bloc.append(editor);
	ckMakeTooltip(editor);
	editor.css('display', 'none').fadeIn('fast');
}

function ckAddRowEdition(bloc) {
	bloc = $ck(bloc);
	if (bloc.hasClass('ui-sortable-helper')) 
		return;
	if ($ck('> .editorck', bloc).length)
		return;
	bloc.css('position','relative');
	var editor = '<div class="editorck roweditor" id="' + bloc.attr('id') + '-edition"></div>';
	editor = $ck(editor);
	editor.css({
		'left': '-30px',
		'top': '0',
		'position': 'absolute',
		'z-index': 999,
		'height': '100%'
	});
	ckAddRowEditionControls(editor, bloc);
	bloc.append(editor);
	ckMakeTooltip(editor);
	editor.css('display', 'none').fadeIn('fast');
}

function ckAddEdition(bloc, i, type) {
	if (!i)
		i = 0;
	if (!type)
		type = '';
	bloc = $ck(bloc);
	if (bloc.hasClass('ui-sortable-helper')) return;
	if ($ck('> .editorck', bloc).length && i == 0)
		return;
	var leftpos = bloc.position().left;
	var toppos = bloc.position().top;
	bloc.css('position','relative');
	var editorclass = '';
	var editor = '<div class="editorck' + editorclass + '" id="' + bloc.attr('id') + '-edition" contenteditable="false"></div>';
	editor = $ck(editor);
	editor.css({
		'left': 0,
		'top': 0,
		'position': 'absolute',
		'z-index': 99 + 1,
		'width': bloc.outerWidth()
	});
	if (bloc.hasClass('cktype')) {
		ckAddItemEditionControls(editor, bloc);
		editor = $ck(editor);
		editor.css({
			'left': '10px',
			'z-index': 999 + i
		});
	} else {
		switch (type) {
			case 'readmore':
				ckAddEditionControlsReadmore(editor, bloc);
			break;
			default:
				ckAddEditionControls(editor, bloc);
			break;
		}
	}
	bloc.append(editor);
	ckMakeTooltip(editor);
	editor.css('display', 'none').fadeIn('fast');
	if (bloc.hasClass('blockck')) editor.css('top', -(editor.find('> .ckfields').height()-30));
}

function ckMakeTooltip(el) {
	if (! el) el = '.cktip';
	if (! $ck(el).attr('title')) el = $ck(el).find('.cktip, .isControl')
	if (PAGEBUILDERCK.TOOLTIPS !== '0') CKApi.Tooltip(el);
	/*if (! el) el = $ck('.hastoolTip');
	el.tooltipck({
		// items: ".infotip",
		content: function() {
			return $ck(this).attr('title');
		},
		close: function( event, ui ) {
			ui.tooltipck.hide();
		},
		position: {
			my: "center top",
			at: "center top-40",
			using: function( position, feedback ) {
				$ck( this ).css( position );
			}
		},
		track: false,
		tooltipClass: "cktooltipinfo",
		container: "body"
	});*/
}

function ckAddEditionControls(editor, bloc) {

	var blocclass = bloc.attr('ckclass') ? bloc.attr('ckclass') : '';
	var controls = '<div class="ckfields">'
			+ '<div class="controlDel isControl" title="'+Joomla.JText._('CK_REMOVE_BLOCK')+'" onclick="ckRemoveBlock($ck(this).parents(\'.blockck\')[0]);" onmouseover="$ck($ck(this).parents(\'.blockck\')[0]).addClass(\'highlight_delete\');" onmouseleave="$ck($ck(this).parents(\'.blockck\')[0]).removeClass(\'highlight_delete\');"></div>'
			+ '<div class="controlMove isControl" title="'+Joomla.JText._('CK_MOVE_BLOCK')+'"></div>'
			+ '<div class="controlCopy isControl" title="'+Joomla.JText._('CK_DUPLICATE_COLUMN')+'" onclick="ckDuplicateColumn(\'' + bloc.attr('id') + '\');"></div>'
			+ '<div class="controlCss isControl" title="'+Joomla.JText._('CK_EDIT_STYLES')+'" onclick="ckShowCssPopup(\'' + bloc.attr('id') + '\');"></div>'
			+ '<div class="controlFavorite isControl" title="'+Joomla.JText._('CK_DESIGN_SUGGESTIONS')+'" onclick="ckShowFavoritePopup(\'' + bloc.attr('id') + '\');"></div>'
			+ "<div class=\"controlValignDefault isControl ckhastip" + (blocclass == '' ? ' active' : '') + "\" title=\"" + Joomla.JText._('CK_VALIGN_DEFAULT', 'Default vertical alignment') + "\" onclick=\"ckToggleVerticalAlign('" + bloc.attr('id') + "', 'default', this);\"></div>"
			+ "<div class=\"controlValignTop isControl ckhastip" + (blocclass == 'valign-top' ? ' active' : '') + "\" title=\"" + Joomla.JText._('CK_VALIGN_TOP', 'Top vertical alignment') + "\" onclick=\"ckToggleVerticalAlign('" + bloc.attr('id') + "', 'top', this);\"></div>"
			+ "<div class=\"controlValignCenter isControl ckhastip" + (blocclass == 'valign-center' ? ' active' : '') + "\" title=\"" + Joomla.JText._('CK_VALIGN_CENTER', 'Center vertical alignment') + "\" onclick=\"ckToggleVerticalAlign('" + bloc.attr('id') + "', 'center', this);\"></div>"
			+ "<div class=\"controlValignBottom isControl ckhastip" + (blocclass == 'valign-bottom' ? ' active' : '') + "\" title=\"" + Joomla.JText._('CK_VALIGN_BOTTOM', 'Bottom vertical alignment') + "\" onclick=\"ckToggleVerticalAlign('" + bloc.attr('id') + "', 'bottom', this);\"></div>"
			+ "</div>";

	editor.append(controls);
}

function ckToggleVerticalAlign(blocid, pos, btn) {
	var focus = $ck('#' + blocid);
	focus.removeClass('valign-top').removeClass('valign-center').removeClass('valign-bottom');
	if (pos != 'default') {
		focus.addClass('valign-' + pos);
		focus.attr('ckclass', 'valign-' + pos);
	} else {
		focus.attr('ckclass', '');
	}
	$ck(btn).parent().find('[class*="controlValign"]').removeClass('active');
	$ck(btn).addClass('active');
}

function ckAddEditionControlsReadmore(editor, bloc) {
	var controls = '<div class="ckfields">'
			+ '<div class="controlDel isControl" title="'+Joomla.JText._('CK_REMOVE_BLOCK')+'" onclick="ckRemoveRow($ck(this).parents(\'#system-readmore\')[0].id);" onmouseover="$ck($ck(this).parents(\'.blockck\')[0]).addClass(\'highlight_delete\');" onmouseleave="$ck($ck(this).parents(\'.blockck\')[0]).removeClass(\'highlight_delete\');"></div>'
			+ '<div class="controlMove isControl moverow" title="'+Joomla.JText._('CK_MOVE_BLOCK')+'"></div>'
			+ '</div>';

	editor.append(controls);
}

function ckAddWrapperEditionControls(editor, bloc) {
	var controls = '<div class="ckfields">'
			+ '<div class="controlResponsiveShown isControlResponsive isControl" data-class="ckshow" title="'+Joomla.JText._('CK_RESPONSIVE_SETTINGS_SHOWN')+'" onclick="ckToggleResponsiveWrapper(this);" ><span class="fa fa-eye"></span></div>'
//			+ '<div class="controlResponsiveStacked isControlResponsive isControl" data-class="ckstack" title="'+Joomla.JText._('CK_RESPONSIVE_SETTINGS_STACKED')+'" onclick="ckToggleResponsiveRow(this);" ></div>'
			+ '<div class="controlResponsiveHidden isControlResponsive isControl" data-class="ckhide" title="'+Joomla.JText._('CK_RESPONSIVE_SETTINGS_HIDDEN')+'" onclick="ckToggleResponsiveWrapper(this);" ><span class="fa fa-eye-slash"></span></div>'
//			+ '<div class="controlCss isControlResponsive isControl" title="'+Joomla.JText._('CK_EDIT_STYLES')+'" onclick="ckShowResponsiveCssEdition(\'' + bloc.attr('id') + '\');" ></div>'
			+ '<div class="controlMore isControl" title="'+Joomla.JText._('CK_MORE_MENU_ELEMENTS')+'" onclick="$ck(this).toggleClass(\'ckhover\').next().toggle();" >...</div>'
			+ '<div style="display:none;" class="controlMoreChildren">'
					+ '<div class="controlMove isControl" title="'+Joomla.JText._('CK_MOVE_WRAPPER')+'"></div>'
//					+ '<div class="controlSize isControl" title="'+Joomla.JText._('CK_EDIT_COLUMNS')+'" onclick="ckShowColumnsEdition($ck(this).parents(\'.rowck\')[0]);" ></div>'
					+ '<div class="controlCss isControl" title="'+Joomla.JText._('CK_EDIT_STYLES')+'" onclick="ckShowCssPopup(\'' + bloc.attr('id') + '\');"></div>'
//					+ '<div class="controlFavorite isControl" title="'+Joomla.JText._('CK_DESIGN_SUGGESTIONS')+'" onclick="ckShowFavoritePopup(\'' + bloc.attr('id') + '\');"></div>'
					+ '<div class="controlCopy isControl" title="'+Joomla.JText._('CK_DUPLICATE_WRAPPER')+'" onclick="ckDuplicateWrapper(\'' + bloc.attr('id') + '\');"></div>'
					+ '<div class="controlFullwidth isControl' + (bloc.hasClass('wrapperckfullwidth') ? ' ckactive' : '') + '" title="'+Joomla.JText._('CK_FULLWIDTH')+'" onclick="ckShowFullwidthRowEdition(\'' + bloc.attr('id') + '\');"></div>'
					+ '<div class="controlSave isControl" title="'+Joomla.JText._('CK_SAVE')+'" onclick="ckSaveItem(\'' + bloc.attr('id') + '\');"></div>'
				+ '<div class="controlDel isControl" title="'+Joomla.JText._('CK_REMOVE_WRAPPER')+'" onclick="ckRemoveWrapper($ck(this).parents(\'.wrapperck\')[0]);" onmouseover="$ck($ck(this).parents(\'.wrapperck\')[0]).addClass(\'highlight_delete\');" onmouseleave="$ck($ck(this).parents(\'.wrapperck\')[0]).removeClass(\'highlight_delete\');" ></div>'
			+ '</div>'
			+ '</div>';

	editor.append(controls);
}

function ckAddRowEditionControls(editor, bloc) {
	var controls = '<div class="ckfields">'
			+ '<div class="editorckresponsiverow">'
				+ '<div class="controlResponsiveAligned isControlResponsive isControl" data-class="ckalign" title="'+Joomla.JText._('CK_RESPONSIVE_SETTINGS_ALIGNED')+'" onclick="ckToggleResponsiveRow(this);" ></div>'
				+ '<div class="controlResponsiveStacked isControlResponsive isControl" data-class="ckstack" title="'+Joomla.JText._('CK_RESPONSIVE_SETTINGS_STACKED')+'" onclick="ckToggleResponsiveRow(this);" ></div>'
				+ '<div class="controlResponsiveHidden isControlResponsive isControl" data-class="ckhide" title="'+Joomla.JText._('CK_RESPONSIVE_SETTINGS_HIDDEN')+'" onclick="ckToggleResponsiveRow(this);" ><span class="fa fa-eye-slash"></span></div>'
				+ '<div class="controlCss isControlResponsive isControl" title="'+Joomla.JText._('CK_EDIT_STYLES')+'" onclick="ckShowResponsiveCssEdition(\'' + bloc.attr('id') + '\');" ></div>'
			+ '</div>'
			+ '<div class="controlMove isControl moverow" title="'+Joomla.JText._('CK_MOVE_ROW')+'"></div>'
			+ '<div class="controlMore isControl" title="'+Joomla.JText._('CK_MORE_MENU_ELEMENTS')+'" onclick="$ck(this).toggleClass(\'ckhover\').next().toggle();" >...</div>'
			+ '<div style="display:none;" class="controlMoreChildren">'
					+ '<div class="controlSize isControl" title="'+Joomla.JText._('CK_EDIT_COLUMNS')+'" onclick="ckShowColumnsEdition($ck(this).parents(\'.rowck\')[0]);" ></div>'
					+ '<div class="controlCss isControl" title="'+Joomla.JText._('CK_EDIT_STYLES')+'" onclick="ckShowCssPopup(\'' + bloc.attr('id') + '\');"></div>'
					+ '<div class="controlFavorite isControl" title="'+Joomla.JText._('CK_DESIGN_SUGGESTIONS')+'" onclick="ckShowFavoritePopup(\'' + bloc.attr('id') + '\');"></div>'
					+ '<div class="controlCopy isControl" title="'+Joomla.JText._('CK_DUPLICATE_ROW')+'" onclick="ckDuplicateRow(\'' + bloc.attr('id') + '\');"></div>'
					+ '<div class="controlFullwidth isControl' + (bloc.hasClass('rowckfullwidth') ? ' ckactive' : '') + '" title="'+Joomla.JText._('CK_FULLWIDTH')+'" onclick="ckShowFullwidthRowEdition(\'' + bloc.attr('id') + '\');"></div>'
					+ '<div class="controlSave isControl" title="'+Joomla.JText._('CK_SAVE')+'" onclick="ckSaveItem(\'' + bloc.attr('id') + '\');"></div>'
					+ (PAGEBUILDERCK.ITEMACL == '1' ? '<div class="controlAcl isControl" title="'+Joomla.JText._('CK_ACCESS_RIGHTS')+'" onclick="ckShowAclEdition(\'' + bloc.attr('id') + '\');"><span class="fa fa-key"></span></div>' : '')
				+ '<div class="controlDel isControl" title="'+Joomla.JText._('CK_REMOVE_ROW')+'" onclick="ckRemoveRow(\'' + bloc.attr('id') + '\');" onmouseover="$ck($ck(this).parents(\'.rowck\')[0]).addClass(\'highlight_delete\');" onmouseleave="$ck($ck(this).parents(\'.rowck\')[0]).removeClass(\'highlight_delete\');" ></div>'
			+ '</div>'
			+ '</div>';

	editor.append(controls);
}

function ckAddItemEditionControls(editor, bloc) {

	var isContentType = bloc.hasClass('ckcontenttype');
	var controls = '<div class="ckfields">'
			+ '<div class="controlDel isControl" title="'+Joomla.JText._('CK_REMOVE_ITEM')+'" onclick="ckRemoveItem($ck(this).parents(\'.cktype\')[0]);" onmouseover="$ck($ck(this).parents(\'.cktype\')[0]).addClass(\'highlight_delete\');" onmouseleave="$ck($ck(this).parents(\'.cktype\')[0]).removeClass(\'highlight_delete\');" ></div>'
			+ '<div class="controlMoveItem isControl" title="'+Joomla.JText._('CK_MOVE_ITEM')+'"></div>'
			+ (!isContentType ? '<div class="controlCopy isControl" title="'+Joomla.JText._('CK_DUPLICATE_ITEM')+'" onclick="ckDuplicateItem(\'' + bloc.attr('id') + '\');"></div>' : '')
			+ '<div class="controlEdit isControl" title="'+Joomla.JText._('CK_EDIT_ITEM')+'" onclick="ckShowEditionPopup(\'' + bloc.attr('id') + '\');"></div>'
			+ (!isContentType ? '<div class="controlFavorite isControl" title="'+Joomla.JText._('CK_DESIGN_SUGGESTIONS')+'" onclick="ckShowFavoritePopup(\'' + bloc.attr('id') + '\');"></div>' : '')
			+ (!isContentType ? '<div class="controlSave isControl" title="'+Joomla.JText._('CK_SAVE')+'" onclick="ckSaveItem(\'' + bloc.attr('id') + '\');"></div>' : '')
			+ '</div>';

	editor.append(controls);
}

function ckRemoveEdition(bloc, all) {
	if (!all)
		all = false;
	if (all == true) {
			$ck('.editorck', bloc).remove();
		} else {
			$ck('> .editorck', bloc).remove();
		}
}

function ckShowLeftPanel(panel) {
	$ck('#menuck > .inner').fadeOut();
	$ck(panel).fadeIn();
	ckMakeTooltip($ck('.ckcolumnsedition'));
}

function ckCloseLeftPanel(panel) {
	$ck('.ckfocus').removeClass('ckfocus');
	$ck(panel).fadeOut();
	$ck('#menuck > .inner').fadeIn();
	$ck('.cktooltip').remove();
}

function ckShowColumnsEdition(row) {
	row = ckGetObjectAnyway(row);
	ckCloseEdition();
	ckEditColumns($ck('.ckfocus'), false, true);
	$ck('.ckfocus').removeClass('ckfocus');
	$ck('.editfocus').removeClass('editfocus');
	row = $ck(row);
	row.addClass('ckfocus');
	ckShowLeftPanel('.ckcolumnsedition');
	ckAddColumnsSuggestions();
	$ck('#menuck .ckguttervalue').val(ckGetRowGutterValue(row));
	var autowidth = row.hasClass('ckadvancedlayout') ? '0' : '1';
	$ck('#menuck [name="autowidth"]').removeAttr('checked');
	$ck('#menuck [name="autowidth"][value="' + autowidth + '"]').prop('checked','checked');
	if (! autowidth) {
		$ck('#ckcolumnsuggestions').hide();
	} else {
		$ck('#ckcolumnsuggestions').show();
	}
	var columnsspacebetween = row.attr('data-columns-space-between') == '0' ? '0' : '1';
	row.attr('data-columns-space-between', columnsspacebetween);
	$ck('#menuck [name="columns-space-between"]').removeAttr('checked');
	$ck('#menuck [name="columns-space-between"][value="' + columnsspacebetween + '"]').prop('checked','checked');
	ckEditColumns(row, true);
	for (var i=1;i<5;i++) {
		$ck('.ckresponsiveoptions [data-range="' + i + '"] .ckbutton').removeClass('active');
		if (row.hasClass('ckhide' + i)) {
			$ck('.ckresponsiveoptions [data-range="' + i + '"] [data-class="ckhide"]').addClass('active');
		} else if (row.hasClass('ckstack' + i)) {
			$ck('.ckresponsiveoptions [data-range="' + i + '"] [data-class="ckstack"]').addClass('active');
		} else {
			$ck('.ckresponsiveoptions [data-range="' + i + '"] [data-class="ckalign"]').addClass('active');
		}
	}
}

function ckUpdateAutowidth(row, autowidth) {
	if (autowidth == '1') {
		$ck(row).removeClass('ckadvancedlayout');
		$ck('#ckcolumnsuggestions, .ckcolwidthlocker, #ckgutteroptions').show();
	} else {
		$ck(row).addClass('ckadvancedlayout');
		$ck('#ckcolumnsuggestions, .ckcolwidthlocker, #ckgutteroptions').hide();
	}
}

function ckUpdateSpacebetween(row, value) {
	$ck(row).attr('data-columns-space-between', value);
}

function ckHideColumnsEdition() {
	var row = $ck('.rowck.ckfocus');
	ckEditColumns(row, false, true);
	ckCloseLeftPanel('.ckcolumnsedition');
}

function ckAddColumnsSuggestions() {
	var row = $ck('.rowck.ckfocus');
	var nb_blocks = row.find('.blockck').length;
	if (nb_blocks == 0) return;
	var buttons = ckCalculateColumnSuggestion(row, nb_blocks);

	$ck('#menuck #ckcolumnsuggestions').empty();
	if (buttons) {
		$ck('#menuck #ckcolumnsuggestions').append('<div>' + Joomla.JText._('CK_SUGGESTIONS') + '</div>');
		$ck('#menuck #ckcolumnsuggestions').append(buttons);
	}
}

function ckEditColumns(row, force, forcehide) {
	if (! force) force = false;
	if (! forcehide) forcehide = false;
	var responsiverange = ckGetResponsiveRange();
	if (row.find('.ckcolwidthedition').length && ! force || forcehide) {
		row.find('.ckcolwidthedition').remove();
		row.find('.ckcolwidthediting').removeClass('ckcolwidthediting');
	} else {
		var number_blocks = row.find('> .inner > .blockck').length;
		if (responsiverange == '1' || responsiverange == '2') {
			var default_data_width = 100;
		} else {
			var default_data_width = 100 / number_blocks;
		}
		row.find('> .inner > .blockck > .inner').each(function(i, blockinner) {
			var blockinner = $ck(blockinner);
			var block = blockinner.parent();
			blockinner.addClass('ckcolwidthediting');
			var responsiverangeattrib = ckGetResponsiveRangeAttrib(responsiverange);
			var block_data_width = block.attr('data-width' + responsiverangeattrib) ? block.attr('data-width' + responsiverangeattrib) : default_data_width;
			block.attr('data-width' + responsiverangeattrib, block_data_width);
			if (! blockinner.find('.ckcolwidthedition').length) blockinner.append('<div class="ckcolwidthedition"><div class="ckcolwidthlocker" title="Click to lock / unlock the width" onclick="ckToggleColWidthState(this);"></div><input id="' + row.attr('id') + '_w' + i + '" class="ckcolwidthselect inputbox" value="' + block_data_width + '" onchange="ckCalculateBlocsWidth(this);" type="text" /> %</div>')
		});
	}
}

function ckGetResponsiveRange() {
	var responsiverange = $ck('.workspaceck').attr('ckresponsiverange') ? $ck('.workspaceck').attr('ckresponsiverange') : '';
	return responsiverange;
}

function ckGetResponsiveRangeNumber() {
	var range = ckGetResponsiveRange();
	var rangeNumber = range.charAt(range.length-1);
	return rangeNumber;
}

function ckGetResponsiveRangeAttrib(responsiverange) {
	var responsiverangeattrib = responsiverange ? '-' +responsiverange : '';
	return responsiverangeattrib;
}

function ckToggleColWidthState(locker) {
	var input = $ck(locker).parent().find('input.ckcolwidthselect');
	var enableamount = $ck('.ckcolwidthselect:not(.disabled)', $ck(locker).parents('.rowck')).length;
	var loackedamount = $ck('.ckcolwidthedition.locked', $ck(locker).parents('.rowck')).length;

	if (!input.hasClass('locked')) {
		input.addClass('locked');
		$ck(locker).addClass('locked');
		$ck(locker).parent().addClass('locked');
	} else {
		input.removeClass('locked');
		$ck(locker).removeClass('locked');
		$ck(locker).parent().removeClass('locked');
	}
}

function ckCalculateBlocsWidth(field) {
	// if advanced layout selected, no calculation
	var row = $ck('.rowck.ckfocus');
	if (! row.length) {
		row = $ck($ck(field).parents('.rowck')[0]);
	}
	if (row.hasClass('ckadvancedlayout') || $ck('.workspaceck.ckresponsiveactive').length) {
		ckSetColumnsWidth(row);
		ckSaveAction();
		return;
	}
	var responsiverange = ckGetResponsiveRange();

	var enabledfields = $ck('.ckcolwidthedition:not(.disabled) .ckcolwidthselect:not(.disabled,.locked,#' + $ck(field).attr('id') + ')', row);
	var amount = enabledfields.length;
	var lockedvalue = 0;
	$ck('.ckcolwidthselect.locked', row).each(function(i, modulefield) {
		modulefield = $ck(modulefield);
		if (modulefield.val() == '') {
			modulefield.removeClass('locked').next('input').prop('checked', false);
			ckCalculateBlocsWidth(field);
		}
		if (modulefield.attr('id') != $ck(field).attr('id')) {
			lockedvalue = parseFloat(modulefield.val()) + parseFloat(lockedvalue);
		}
	});
	var mw = parseFloat($ck(field).val());
	// $ck(field).val(mw+'%');
//	if (responsiverange && parseInt(responsiverange) > 2) {
	var percent = (100 - mw - lockedvalue) / amount;
//	} else {
//		var percent = 100;
//	}
	enabledfields.each(function(i, modulefield) {
		if ($ck(modulefield).attr('id') != $ck(field).attr('id')
				&& !$ck(modulefield).hasClass('locked')) {
				
			$ck(modulefield).val(parseFloat(percent));
		}
	});
	ckSetColumnsWidth(row);
	ckSaveAction();
}

function ckCalculateColumnSuggestion(row, nb_blocks) {
	var suggestions = [];
	switch(nb_blocks) {
		case 2:
			suggestions = 	[ 	[ '1/4', '3/4' ],
								[ '1/2', '1/2' ],
								[ '3/4', '1/4' ],
								[ '2/3', '1/3' ],
								[ '1/3', '2/3' ],
								[ '5/6', '1/6' ],
								[ '1/6', '5/6' ]
							]
			break;
		case 3:
			suggestions = 	[ 	[ '1/3', '1/3', '1/3' ],
								[ '1/4', '1/2', '1/4' ],
								[ '1/6', '2/3', '1/6' ]
							]
			break;
		case 4:
			suggestions = 	[ 	[ '1/4', '1/4', '1/4', '1/4' ],
								[ '1/6', '1/3', '1/3', '1/6' ]
							]
			break;
		case 6:
			suggestions = 	[ 	[ '1/6', '1/6', '1/6', '1/6', '1/6', '1/6' ],
								[ '1/12', '1/12', '1/3', '1/3', '1/12', '1/12' ]
							]
			break;
		default:
			break;
	}

	buttons = '';
	for (i=0; i<suggestions.length; i++) {
		cols = '';
		cols_value = [];
		suggestion = suggestions[i];
		for (j=0; j<suggestion.length; j++) {
			cols += '<div class="iscolumnsuggestion" data-width="' + ckFracToDec(suggestion[j])*100 + '" style="width: ' + ckFracToDec(suggestion[j])*100 + '%;"><div></div></div>';
			cols_value.push(suggestion[j]);
		}
		cols_value_txt = cols_value.join(' | ');
		buttons += '<div class="clearfix" title="' + cols_value_txt + '" onclick="ckApplyColumnSuggestion($ck(\'.rowck.ckfocus\'), this);">' + cols + '</div>';
	}
	ckMakeTooltip($ck('#ckcolumnsuggestions'));
	return buttons;
}

/* convert a fraction to decimal */
function ckFracToDec(frac) {
	dec = frac.split('/');
	return (dec[0]/dec[1]);
}

function ckApplyColumnSuggestion(row, selection) {
	if (row.find('.blockck').length != $ck(selection).find('.iscolumnsuggestion').length) {
		alert('Error : the number of columns selected does not match the number of columns in the row');
		return;
	}
	suggestions = $ck(selection).find('.iscolumnsuggestion');
	for (i=0; i<suggestions.length; i++) {
		var col = row.find('.blockck').eq(i);
		data_width = $ck(suggestions[i]).attr('data-width');
		if (col.find('.ckcolwidthselect').length) col.find('.ckcolwidthselect').val(data_width);
		col.attr('data-width', data_width);
	}
	ckSetColumnsWidth(row);
	ckSaveAction();
}

function ckGetRowGutterValue(row) {
	var gutter = row.attr('data-gutter') ? row.attr('data-gutter') : '2%';
	row.attr('data-gutter',gutter);
	return gutter;
}

function ckUpdateGutter(row, gutter) {
	row.attr('data-gutter',parseFloat(gutter)+'%');
	ckSetColumnsWidth(row);
}

function ckSetColumnsWidth(row) {
	var responsiverange = ckGetResponsiveRange();
	var responsiverangeattrib = ckGetResponsiveRangeAttrib(responsiverange);
	if (! row.find('> .ckcolumnwidth' + responsiverange).length) {
		row.prepend('<style class="ckcolumnwidth' + responsiverange + '"></style>');
	}
	var stylewidths = row.find('> .ckcolumnwidth' + responsiverange);
	var gutter = ckGetRowGutterValue(row);
	var nb = row.find('> .inner > .blockck').length;
	row.attr('data-nb', nb);
	stylewidths.empty();
	var prefixselector = responsiverange ? '[ckresponsiverange="' + responsiverange + '"] ' : '';
	row.find('> .inner > .blockck').each(function(i, col) {
		var w = $ck(col).find('.ckcolwidthselect').val();
		if ($ck(col).find('.ckcolwidthselect').length) $ck(col).attr('data-width' + responsiverangeattrib, $ck(col).find('.ckcolwidthselect').val());
		w = $ck(col).attr('data-width' + responsiverangeattrib);
		ckSetColumnWidth($ck(col), w);
		if (responsiverange > 0 && responsiverange < 5) {
			stylewidths.append(prefixselector + '.rowck[data-gutter="' + gutter + '"][data-nb="' + nb + '"] [data-width' + responsiverangeattrib + '="' + w + '"] {width:' + parseFloat($ck(col).attr('data-width' + responsiverangeattrib)) + '%;}');
		} else {
			stylewidths.append(prefixselector + '[data-gutter="' + gutter + '"][data-nb="' + nb + '"]:not(.ckadvancedlayout) [data-width' + responsiverangeattrib + '="' + w + '"] {width:' + $ck(col).attr('data-real-width' + responsiverangeattrib) + ';}');
			stylewidths.append(prefixselector + '[data-gutter="' + gutter + '"][data-nb="' + nb + '"].ckadvancedlayout [data-width' + responsiverangeattrib + '="' + w + '"] {width:' + parseFloat($ck(col).attr('data-width' + responsiverangeattrib)) + '%;}');
		}
	});
	ckFixBCRow(row);
	row.removeClass('row-fluid');
}

function ckSetColumnWidth(col, w) {
	var responsiverange = ckGetResponsiveRange();
	var responsiverangeattrib = ckGetResponsiveRangeAttrib(responsiverange);
	if (! w) w = col.attr('data-width' + responsiverangeattrib) ? col.attr('data-width' + responsiverangeattrib) : '30';
	var row = $ck(col.parents('.rowck')[0]);
	var numberblocks = row.find('.blockck').length;
	var gutter = ckGetRowGutterValue(row);
	var realwidth =  w - (( (numberblocks - 1) * parseFloat(gutter) ) / numberblocks);
	col.attr('class', function(i, c) {
		return c.replace(/(^|\s)span\S+/g, '');
	});
	col.attr('data-real-width' + responsiverangeattrib, realwidth + '%').attr('data-width' + responsiverangeattrib, w).css('width', '');
}

function ckSearchExistingColWidth(row, gutter, nb, w, prefixselector) {
	var stylewidths = row.find('> .ckcolumnwidth');
	var s = prefixselector + '[data-gutter="' + gutter + '"][data-nb="' + nb + '"] [data-width="' + w + '"]';
	// if we don't alreay have the style
	if (stylewidths.html().indexOf(s) == -1) {
		return false;
	}
	return true;
}

function ckSearchExistingGutterWidth(row, gutter, nb, prefixselector) {
	var stylewidths = row.find('> .ckcolumnwidth');
	var s = prefixselector + '[data-gutter="' + gutter + '"][data-nb="' + nb + '"] .blockck';
	// if we don't alreay have the style
	if (stylewidths.html().indexOf(s) == -1) {
		return false;
	}
	return true;
}

function ckDuplicateItem(blocid) {
	bloc = $ck('#' + blocid);
	var copy = bloc.clone();
	copyid = ckGetUniqueId();
	copy.attr('id', copyid);

	bloc.after(copy);
	copy.removeClass('editfocus');
	ckAddItemEditionEvents(copy);

	// init the effect if needed
	if (copy.find('.tabsck').length) {
		copy.find('.tabsck').tabsck();
	}
	if (copy.find('.accordionsck').length) {
		copy.find('.accordionsck').accordionck();
	}
	// for tiny inline editing
	if (copy.find('[id^="mce_"]').length) {
		copy.find('[id^="mce_"]').removeAttr('id');
	}

	// add dnd for image
	if (copy.attr('data-type') == 'image') ckAddDndForImageUpload(copy[0]);

	// copy the styles
	var re = new RegExp(blocid, 'g');
	copy.find('.ckstyle').html(bloc.find('.ckstyle').html().replace(re,copyid));
	ckSaveAction();
	ckInlineEditor();
}

function ckDuplicateColumn(blocid) {
	var col = $ck('#' + blocid);
	var row = $ck(col.parents('.rowck')[0]);
	// add an empty column
	var colcopyid = ckAddBlock(row);
	var colcopy = $ck('#' + colcopyid);
	// copy the styles
//	colcopy.find('> .ckstyle').html(col.find('> .ckstyle').html());
	colcopy.html(col.html());
	colcopy.attr('id', blocid);
	ckReplaceId(colcopy, colcopyid);

	// for tiny inline editing
	if (colcopy.find('[id^="mce_"]').length) {
		colcopy.find('[id^="mce_"]').removeAttr('id');
	}
	ckMakeItemsSortable(row);
//	col.find('.cktype').each(function() {
//		colcopy.find('.innercontent').append($ck(this).clone());
//	});

	colcopy.find('.cktype').each(function() {
		$this = $ck(this);
		$this.removeClass('editfocus');
		// init the effect if needed
		if ($this.hasClass('cktype') && $this.find('.tabsck').length) {
			$this.find('.tabsck').tabsck();
		}
		if ($this.hasClass('cktype') && $this.find('.accordionsck').length) {
			$this.find('.accordionsck').accordionck();
		}

		ckAddItemEditionEvents($this);

		// add dnd for image
		if ($this.attr('data-type') == 'image') ckAddDndForImageUpload($this[0]);

		var copyid = ckGetUniqueId();
		// copy the styles
		ckReplaceId($this, copyid);
	});
	ckSaveAction();
	ckInlineEditor();
}

function ckDuplicateWrapper(blocid) {
	var wrapper = $ck('#' + blocid);
	var wrappercopy = wrapper.clone();
	var wrappercopyid = ckGetUniqueId('wrapper_');
//	wrappercopy.attr('id', wrappercopyid);
	wrapper.after(wrappercopy);
	wrappercopy.removeClass('editfocus');
	wrappercopy.find('> .editorck').remove();
	ckReplaceId(wrappercopy, wrappercopyid);
//	ckMakeRowSortableInWrapper(wrappercopy);
	ckAddWrapperEdition(wrappercopy);

	// for tiny inline editing
	if (wrappercopy.find('[id^="mce_"]').length) {
		wrappercopy.find('[id^="mce_"]').removeAttr('id');
	}

	wrappercopy.find('.rowck').each(function() {
		$row = $ck(this);
		$row.removeClass('editfocus');
		$row.find('> .editorck').remove();
		var copyid = ckGetUniqueId('row_');
		// copy the styles
		ckReplaceId($row, copyid);
		ckMakeBlocksSortable($row);
		ckAddRowEditionEvents($row);
	});

	wrappercopy.find('.blockck, .cktype').each(function() {
		$this = $ck(this);
		$this.removeClass('editfocus');
		// init the effect if needed
		if ($this.hasClass('cktype') && $this.find('.tabsck').length) {
			$this.find('.tabsck').tabsck();
		}
		if ($this.hasClass('cktype') && $this.find('.accordionsck').length) {
			$this.find('.accordionsck').accordionck();
		}
		
		var prefix = '';
		if ($this.hasClass('blockck')) {
			prefix = 'block_';
			ckMakeItemsSortable(wrappercopy);
			ckAddBlockEditionEvents($this);
		} else {
			ckAddItemEditionEvents($this);
		}

		// add dnd for image
		if ($this.attr('data-type') == 'image') ckAddDndForImageUpload($this[0]);

		var copyid = ckGetUniqueId(prefix);
		// copy the styles
		ckReplaceId($this, copyid);
	});
	ckSaveAction();
	ckInlineEditor();
}

function ckDuplicateRow(blocid) {
	var row = $ck('#' + blocid);
	var rowcopy = row.clone();
	var rowcopyid = ckGetUniqueId('row_');
//	rowcopy.attr('id', rowcopyid);
	row.after(rowcopy);
	rowcopy.removeClass('editfocus');
	rowcopy.find('> .editorck').remove();
	ckReplaceId(rowcopy, rowcopyid);
	ckMakeBlocksSortable(rowcopy);
	ckAddRowEditionEvents(rowcopy);

	// for tiny inline editing
	if (rowcopy.find('[id^="mce_"]').length) {
		rowcopy.find('[id^="mce_"]').removeAttr('id');
	}

	rowcopy.find('.rowck').each(function() {
		$row = $ck(this);
		$row.removeClass('editfocus');
		$row.find('> .editorck').remove();
		var copyid = ckGetUniqueId('row_');
		// copy the styles
		ckReplaceId($row, copyid);
		ckMakeBlocksSortable($row);
		ckAddRowEditionEvents($row);
	});

	rowcopy.find('.blockck, .cktype').each(function() {
		$this = $ck(this);
		$this.removeClass('editfocus');
		// init the effect if needed
		if ($this.hasClass('cktype') && $this.find('.tabsck').length) {
			$this.find('.tabsck').tabsck();
		}
		if ($this.hasClass('cktype') && $this.find('.accordionsck').length) {
			$this.find('.accordionsck').accordionck();
		}
		
		var prefix = '';
		if ($this.hasClass('blockck')) {
			prefix = 'block_';
			ckMakeItemsSortable(rowcopy);
			ckAddBlockEditionEvents($this);
		} else {
			ckAddItemEditionEvents($this);
		}

		// add dnd for image
		if ($this.attr('data-type') == 'image') ckAddDndForImageUpload($this[0]);

		var copyid = ckGetUniqueId(prefix);
		// copy the styles
		ckReplaceId($this, copyid);
	});
	ckSaveAction();
	ckInlineEditor();
}

function ckToggleFullwidthRow(blocid, enable) {
	var row = $ck('#' + blocid);
	if (enable == 1) {
		row.addClass('rowckfullwidth').find('.controlFullwidth').addClass('ckactive');
	} else {
		row.removeClass('rowckfullwidth').find('.controlFullwidth').removeClass('ckactive');
	}
}

function ckShowFullwidthRowEdition(blocid) {
	var row = ckGetObjectAnyway(blocid);
	row.addClass('editfocus');
	var myurl = PAGEBUILDERCK.URIPBCK + "&task=interface.load&layout=fullwidth&" + PAGEBUILDERCK.TOKEN;
	$ck.ajax({
		type: "POST",
		url: myurl,
		async: true,
		data: {
		}
	}).done(function(code) {
		$ck('#popup_editionck').empty().append(code).removeClass('ckwait').show();
		$ck('#ckwaitoverlay').remove();
		ckFillEditionPopup(blocid);
		ckLoadPreviewAreaStyles(blocid);
		ckMakeTooltip($ck('#popup_editionck'));
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

function replaceIdsInRow(newrow, addEvents) {
	if (! addEvents) addEvents = false;
	var newrowid = ckGetUniqueId('row_');
	newrow.removeClass('editfocus');
	newrow.find('> .editorck').remove();
	ckReplaceId(newrow, newrowid);

	if (addEvents) ckMakeBlocksSortable(newrow);
	if (addEvents) ckAddRowEditionEvents(newrow);
	newrow.find('.blockck, .cktype').each(function() {
		$this = $ck(this);
		$this.removeClass('editfocus');
		// init the effect if needed
		if ($this.hasClass('cktype') && $this.find('.tabsck').length) {
			if (addEvents) $this.find('.tabsck').tabsck();
		}
		if ($this.hasClass('cktype') && $this.find('.accordionsck').length) {
			if (addEvents) $this.find('.accordionsck').accordionck();
		}
		
		var prefix = '';
		if ($this.hasClass('blockck')) {
			prefix = 'block_';
			if (addEvents) ckMakeItemsSortable(row);
			if (addEvents) ckAddBlockEditionEvents($this);
		} else {
			if (addEvents) ckAddItemEditionEvents($this);
		}
		var copyid = ckGetUniqueId(prefix);
		ckReplaceId($this, copyid);
	});

	return newrow;
}

function ckReplaceId(el, newID) {
	var re = new RegExp(el.attr('id'), 'g');
	if (el.find('> .ckstyle').length) el.find('> .ckstyle').html(el.find('> .ckstyle').html().replace(re,newID));
	if (el.find('> .ckstyleresponsive').length) el.find('> .ckstyleresponsive').html(el.find('> .ckstyleresponsive').html().replace(re,newID));
	el.attr('id', newID);
}

function ckAddContent(block) {
	var id = ckGetUniqueId();
	$ck('.ckfocus').removeClass('ckfocus');
	$ck('.innercontent', block).addClass('ckfocus');
	ckShowContentList();
	// $ck('.innercontent', block).append('<p id="' + id + '">ceci est un test de ced</p>');
}

/*
 * Method to give a random unique ID
 */
function ckGetUniqueId(prefix) {
	if (! prefix) prefix = '';
	var now = new Date().getTime();
	var id = prefix + 'ID' + parseInt(now, 10);

	if ($ck('#' + id).length || CKUNIQUEIDLIST.indexOf(id) != -1)
		id = ckGetUniqueId(prefix);
	CKUNIQUEIDLIST.push(id);

	return id;
}

function ckShowContentList() {
	// $ck(document.body).append('<div id="ck_overlay"></div>');
	$ck('#ck_overlay').fadeIn().click(function() { ckHideContentList() });
	$ck('#ckcontentslist').fadeIn().css('top', $ck(window).scrollTop());
}

function ckHideContentList() {
	$ck('#ck_overlay').fadeOut();
	$ck('#ckcontentslist').fadeOut();
}

function ckShowEditionPopup(blocid, workspace) {
//	if ($ck('#popup_editionck .ckclose').length) {
//		if (! ckConfirmBeforeCloseEditionPopup()) return;
//	}
	ckCloseEdition(1);
	if (!workspace) workspace = ckGetWorkspace();
	blocid = '#' + blocid;
//	$ck(document.body).append('<div id="ckwaitoverlay"></div>');
	bloc = workspace.find(blocid);
	if (! bloc.length) bloc = $ck(blocid);
	$ck('.editfocus').removeClass('editfocus');
	bloc.addClass('editfocus');
	$ck('#popup_editionck').empty().fadeIn().addClass('ckwait');
	if ($ck('#popup_favoriteck').length) {
//		ckCloseFavoritePopup(true);
	}

	var myurl = PAGEBUILDERCK.URIBASE + "/index.php?option=com_pagebuilderck&view=options&layout=edit";
	$ck.ajax({
		type: "POST",
		url: myurl,
		data: {
			cktype: bloc.attr('data-type'),
			ckid: bloc.attr('id')
		}
	}).done(function(code) {
		$ck('#popup_editionck').append(code).removeClass('ckwait');
		$ck('#ckwaitoverlay').remove();

		// manage responsive buttons
		var rangeNumber = ckGetResponsiveRangeNumber();
		if (rangeNumber) {
			var button = $ck('#popup_editionck .ckresponsivebutton[data-range="' + rangeNumber + '"]');
		} else {
			var button = $ck('#popup_editionck .ckresponsivebutton[data-range="5"]');
		}
		$ck('#popup_editionck .cktoolbarResponsive .ckresponsivebutton').removeClass('active').removeClass('ckbutton-warning');
		button.addClass('active').addClass('ckbutton-warning');

		ckInitColorPickers();
		ckInitOptionsTabs();
		ckInitAccordions();
		ckLoadEditionPopup();
		ckLoadPreviewAreaStyles(blocid);
		ckMakeTooltip($ck('#popup_editionck'));
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
		$ck('#ckwaitoverlay').remove();
	});
}

//function ckConfirmBeforeCloseEditionPopup() {
//	var confirmation = confirm(Joomla.JText._('CK_CONFIRM_BEFORE_CLOSE_EDITION_POPUP', 'A popup edition is already in use. Your changes will not be saved. Confirm ?'));
//
//	return confirmation;
//}

function ckCloseEditionPopup(keepopen) {
	// do nothing, removed in 2.0.5. Keep it for B/C
}

function ckCloseEdition(keepopen) {
	if (! keepopen) keepopen = false;
	if (typeof ckBeforeCloseEditionPopup == 'function') { ckBeforeCloseEditionPopup(); }
	if (! keepopen) $ck('#popup_editionck').empty().fadeOut();
	/*$ck('body').animate({'left':'0', complete: function() {$ck('body').css('position', '')}});
	$ck('.workspaceck').css('margin-left', '');*/
	$ck('.editfocus').removeClass('editfocus');
	$ck('.cktooltip').remove();
}

function ckCreateEditItem(i, itemlist, itemtitle, itemcontent) {
	var itemedition = $ck('<div class="item_edition clearfix">'
				+'<div class="item_move"></div>'
				+'<div class="item_title"><input type="text" id="item_title_'+i+'" name="item_title_'+i+'" class="item_title_edition" value="" onchange="ckUpdatePreviewArea()"/></div>'
				+'<div class="item_toggler">'+Joomla.JText._('CK_CLICK_TO_EDIT_CONTENT','Click to edit the content')+'</div>'
				+'<div class="item_content"><textarea id="item_content_'+i+'" name="item_title_'+i+'" class="item_content_edition" onchange="ckUpdatePreviewArea()"></textarea></div>'
				+'<div class="item_delete btn-small btn btn-danger" onclick="ckDeleteEditItem($ck(this).parent())">'+Joomla.JText._('CK_DELETE','Delete')+'</div>'
				+'&nbsp;<div class="item_setdefault btn-small btn" onclick="ckSetDefaultEditItem($ck(this).parent())"><span class="icon icon-star"></span>'+Joomla.JText._('CK_SET_DEFAULT','Set as default')+'</div>'
				+'</div>');
	itemlist.append(itemedition);
	itemedition.find('.item_title_edition').val(itemtitle);
	itemedition.find('.item_content_edition').val(itemcontent);

	return itemedition;
}

function ckCreateEditImageItem(i, itemlist, itemtitle, itemcontent, itemimg) {
	var itemedition = $ck('<div class="item_edition clearfix" data_index="'+i+'">'
				+'<div class="item_move"></div>'
				+'<div class="item_image">'
				+'<a class="item_image_selection" href="javascript:void(0)" onclick="ckCallImageManagerPopup(\'item_imageurl'+i+'\')" >'
				+'<img src="'+itemimg.attr('src')+'" />'
				+'</a>'
				+'</div>'
				+'<div class="item_imageurl"><input type="text" id="item_imageurl'+i+'" name="item_imageurl'+i+'" class="item_imageurl_edition" value="'+getImgPathFromImgSrc(itemimg.attr('src'))+'" style="width: 400px;" onchange="ckUpdatePreviewArea()"/></div>'
				+'<div class="item_title"><input type="text" id="item_title_'+i+'" name="item_title_'+i+'" class="item_title_edition" value="'+itemtitle+'" style="width: 400px;" onchange="ckUpdatePreviewArea()"/></div>'
				+'<div class="item_toggler">'+Joomla.JText._('CK_CLICK_TO_EDIT_CONTENT','Click to edit the content')+'</div>'
				+'<div class="item_content"><textarea id="item_content_'+i+'" name="item_title_'+i+'" class="item_content_edition" onchange="ckUpdatePreviewArea()">'+itemcontent+'</textarea></div>'
				+'<br />'
				+'<div class="item_delete btn-small btn btn-danger" onclick="ckDeleteEditItem($ck(this).parent())">'+Joomla.JText._('CK_DELETE','Delete')+'</div>'
				// +'&nbsp;<div class="item_setdefault btn-small btn" onclick="ckSetDefaultEditItem($ck(this).parent())"><span class="icon icon-star"></span>'+Joomla.JText._('CK_SET_DEFAULT','Set as default')+'</div>'
				+'</div>');
	itemlist.append(itemedition);

	return itemedition;
}

function ckCreateEditAdvancedItem(i, itemlist, itemtitle, itemcontentId) {
	var itemedition = $ck('<div class="item_edition clearfix">'
				+'<div class="item_move"></div>'
				+'<div class="item_title"><input type="text" id="item_title_'+i+'" name="item_title_'+i+'" class="item_title_edition" value="'+itemtitle+'" onchange="ckUpdatePreviewArea()"/></div>'
				+'<div class="item_toggler"><a href="javascript:void(0)" onclick="CKBox.open({handler:\'inline\', fullscreen: true, content:\'\', id: \'ckadvanceditembox\', onCKBoxLoaded: function() {ckInitOnBoxLoaded(\'ckadvanceditembox\', \''+itemcontentId+'\')}})">'+Joomla.JText._('CK_CLICK_TO_EDIT_CONTENT','Click to edit the content')+'</a></div>'
				// +'<div class="item_content"><a href="">'++'</a></div>'
				+'<div class="item_delete btn-small btn btn-danger" onclick="ckDeleteEditItem($ck(this).parent())">'+Joomla.JText._('CK_DELETE','Delete')+'</div>'
				+'&nbsp;<div class="item_setdefault btn-small btn" onclick="ckSetDefaultEditItem($ck(this).parent())"><span class="icon icon-star"></span>'+Joomla.JText._('CK_SET_DEFAULT','Set as default')+'</div>'
				+'</div>');
	itemlist.append(itemedition);

	return itemedition;
}

/* empty function callback to fill in each edition area */
function ckInitOnBoxLoaded(boxid, itemcontentId) {
	return;
}

function getImgPathFromImgSrc(imgsrc, full) {
	if (! imgsrc) return imgsrc;
	if (! full) full = false;

	if (imgsrc.indexOf('http') == 0) return imgsrc;

	if (PAGEBUILDERCK.URIROOT != '/' && PAGEBUILDERCK.URIROOT && imgsrc.substr(0, PAGEBUILDERCK.URIROOT.length) == PAGEBUILDERCK.URIROOT) imgsrc = imgsrc.replace(PAGEBUILDERCK.URIROOT+'/','').replace(PAGEBUILDERCK.URIROOT,'');

	while(imgsrc.charAt(0) === '/')
		imgsrc = imgsrc.substr(1);

	if (full) imgsrc = PAGEBUILDERCK.URIROOT + '/' + imgsrc;

	return imgsrc;
}

function ckMakeEditItemAccordion(el) {
	$ck(el).accordion({
		header: ".item_toggler",
		collapsible: true,
		active: false,
		heightStyle: "content"
	});
}

function ckSetDefaultEditItem(item) {
	alert('ERROR : If you see this message then the function "ckSetDefaultEditItem" is missing from the element edition. Please contact the developer');
}

function ckSaveInlineEditionPopup() {
	alert('ERROR : If you see this message then the function "ckSaveInlineEditionPopup" is missing from the element edition. Please contact the developer');
}

function ckCancelInlineEditionPopup(btn) {
	$ck(btn).parent().fadeOut();
	// can be overridden in the element edition
//	ckCloseEditionPopup();
}

function ckDeleteEditItem(item) {
	if (item.parent().children().length <= 1) {
		alert(Joomla.JText._('CK_CAN_NOT_DELETE_LAST','You can not delete the last item'));
		return;
	}
	if (!confirm(Joomla.JText._('CK_CONFIRM_DELETE','CK_CONFIRM_DELETE'))) return;
	if (typeof ckBeforeDeleteEditItem == 'function') { ckBeforeDeleteEditItem(item); }
	item.remove();
	if (typeof ck_after_delete_edit_item == 'function') { ck_after_delete_edit_item(); }
	ckSaveAction();
}

function ckShowCssPopup(blocid, savefunc) {
//	if ($ck('#popup_editionck .ckclose').length) {
//		if (! ckConfirmBeforeCloseEditionPopup()) return;
//	}
//	if ($ck('.editfocus').length) ckSaveEdition();
//	if (! savefunc) savefunc = 'ckSaveEditionPopup';
	blocid = '#' + blocid;
	// $ck(document.body).append('<div id="ckwaitoverlay"></div>');
	bloc = $ck(blocid);
	$ck('.editfocus').removeClass('editfocus');
	bloc.addClass('editfocus');
	$ck('#popup_editionck').empty().fadeIn().addClass('ckwait');
	/*$ck('body').css('position', 'relative').animate({'left':'310px'});
	$ck('.workspaceck').css('margin-left', '0');*/
	// $ck('html, body').animate({scrollTop: 0}, 'slow');
	if ($ck('#popup_favoriteck').length) {
//		ckCloseFavoritePopup(true);
	}

	var myurl = PAGEBUILDERCK.URIBASE + "/index.php?option=com_pagebuilderck&task=interface.load&layout=stylescss&" + PAGEBUILDERCK.TOKEN;
	$ck.ajax({
		type: "POST",
		url: myurl,
		data: {
			objclass: bloc.prop('class'),
			expertmode: $ck('#body').hasClass('expert'),
			savefunc: savefunc,
			ckobjid: bloc.prop('id')
		}
	}).done(function(code) {
		$ck('#popup_editionck').append(code).removeClass('ckwait');
		$ck('#ckwaitoverlay').remove();
		ckFillEditionPopup(blocid);
		ckLoadPreviewAreaStyles(blocid);
		ckMakeTooltip($ck('#popup_editionck'));
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
		$ck('#ckwaitoverlay').remove();
	});
}

function ckFillEditionPopup(blocid, workspace, responsiverange) {
	// blocid = blocid.test('#') ? blocid : '#' + blocid;
	var patt = new RegExp("#");
	var res = patt.test(blocid);
	blocid = res ? blocid : '#' + blocid;

	if (!workspace) workspace = ckGetWorkspace();
	if (!responsiverange) responsiverange = ckGetResponsiveRangeNumber();

	var bloc = workspace.find(blocid);
	if (! bloc.length) bloc = $ck(blocid);
	var responsivesuffix = responsiverange ? '.ckresponsive' + responsiverange : ':not(.ckresponsive)';

	// clear color fields
	$ck('.colorPicker').each(function() {
		field = $ck(this);
		field.css('background-color', '');
		var prefix = field.attr('id').replace("backgroundcolorend", "");
		if (prefix)
			ckCreateGradientPreview(prefix);
	});

	$ck('> .ckprops' + responsivesuffix, bloc).each(function(i, ckprops) {
		ckprops = $ck(ckprops);
		fieldslist = ckprops.attr('fieldslist') ? ckprops.attr('fieldslist').split(',') : Array();
		// fieldslist.each(function(fieldname) { 
		// for (var fieldname of fieldslist) {
console.log(ckprops);
		for (j=0;j<fieldslist.length;j++) {
			fieldname = fieldslist[j];
			if (!$ck('#' + fieldname).length)
				return;
			cssvalue = ckprops.attr(fieldname);
			field = $ck('#' + fieldname);
			if (field.attr('type') == 'radio' || field.attr('type') == 'checkbox') {
	console.log(fieldname);
	console.log(cssvalue);
				if (cssvalue == 'checked') {
					field.prop('checked', 'checked');
				} else {
					field.removeProp('checked');
				}
			} else if (cssvalue) {
				if (field.attr('multiple')) cssvalue = cssvalue.split(',');
				field.val(cssvalue);
				if (field.hasClass('colorPicker') && field.val()) {
					setpickercolor(field);
					field.css('background-color', field.val());
					if (field.attr('id').indexOf('backgroundcolorend') != -1) {
						prefix = field.attr('id').replace("backgroundcolorend", "");
						if (prefix && $ck('#blocbackgroundcolorstart').val())
							ckCreateGradientPreview(prefix);
					}
					if (field.attr('id').indexOf('backgroundcolorstart') != -1) {
						prefix = field.attr('id').replace("backgroundcolorstart", "");
						if (prefix && $ck('#blocbackgroundcolorstart').val())
							ckCreateGradientPreview(prefix);
					}
				}
			} else {
				field.val('');
			}
		// });
		}
	});
}

function ckLoadPreviewAreaStyles(blocid) {
	bloc = $ck(blocid);
	var blocstyles = $ck('> .ckstyle', bloc).text();
	var replacement = new RegExp(blocid, 'g');
	var previewstyles = blocstyles.replace(replacement, '#previewareabloc'); // /blue/g,"red"
	var editionarea = $ck('#popup_editionck');
	$ck('> .ckstyle', $ck('#previewareabloc')).html('<style type="text/css">'+previewstyles+'</style>');
	ckAddEventOnFields(editionarea, blocid);
}

function ckAddEventOnFields(editionarea, blocid) {
	if (!editionarea) editionarea = $ck('#popup_editionck');
	var rangeNumber = ckGetResponsiveRangeNumber();
	if (rangeNumber == '5' || ! rangeNumber) {
		$ck('.inputbox:not(.colorPicker)', editionarea).off('change').on('change', function() {
			ckGetPreviewAreastylescss('previewareabloc', editionarea, blocid);
		});
		$ck('.colorPicker,.inputbox[type=radio]', editionarea).off('blur').on('blur', function() {
			ckGetPreviewAreastylescss('previewareabloc', editionarea, blocid);
		});
	} else {
		$ck('.inputbox:not(.colorPicker)', editionarea).off('change').on('change', function() {
			ckRenderResponsiveCss();
		});
		$ck('.colorPicker,.inputbox[type=radio]', editionarea).off('blur').on('blur', function() {
			ckRenderResponsiveCss();
		});
	}
}

function ckFocusWorkspaceFrom(focus) {
	if (focus.hasClass('workspaceck')) return;
	$ck('.workspaceck').removeAttr('id');
	$ck(focus.parents('.workspaceck')[0]).attr('id', 'workspaceck');
}

function ckGetPreviewAreastylescss(blocid, editionarea, focus, forpreviewarea, returnFunc, doClose) {
	if (! returnFunc) returnFunc = '';
	if (! doClose) doClose = false;
	ckAddSpinnerIcon($ck('.headerckicon.cksave'));
	if (!editionarea)
		editionarea = document.body;
	if (!focus) {
		focus = $ck('.editfocus');
	} else {
		focus = ckGetObjectAnyway(focus);
	}
	ckFocusWorkspaceFrom(focus);
	if (! forpreviewarea) forpreviewarea = false;
	if (focus.attr('data-previewedition') == '1' || focus.attr('data-type') == 'table') {
		forpreviewarea = true; // needed for preview area edition like in table item
	}
	blocid = forpreviewarea ? blocid : focus.attr('id');

	var fieldslist = new Array();
	fields = new Object();
	$ck('.inputbox', editionarea).each(function(i, el) {
		el = $ck(el);
		fields[el.attr('name')] = el.val();
		if (el.attr('type') == 'radio') {
			fields[el.attr('name')] = $ck('[name="' + el.attr('name') + '"]:checked').val();
			if (el.prop('checked')) {
				fields[el.attr('id')] = 'checked';
			} else {
				fields[el.attr('id')] = '';
			}
		}
	});
	$ck('> .ckprops', focus).each(function(i, ckprops) {
		ckprops = $ck(ckprops);
		fieldslist = ckprops.attr('fieldslist') ? ckprops.attr('fieldslist').split(',') : Array();
		// fieldslist.each(function(fieldname) {
		// for (var fieldname of fieldslist) {
		for (j=0;j<fieldslist.length;j++) {
			fieldname = fieldslist[j];
			if (typeof(fields[fieldname]) == 'null') 
				fields[fieldname] = ckprops.attr(fieldname);
		// });
		}
	});
	fields = JSON.stringify(fields);
	var customstyles = new Object();
	$ck('.menustylescustom').each(function() {
		$this = $ck(this);
		customstyles[$this.attr('data-prefix')] = $this.attr('data-rule');
	});
	customstyles = JSON.stringify(customstyles);
	ckSaveEdition(blocid); // save fields before ajax to keep sequential/logical steps
	var myurl = PAGEBUILDERCK.URIBASE + "/index.php?option=com_pagebuilderck&task=interface.load&layout=rendercss&" + PAGEBUILDERCK.TOKEN;
	$ck.ajax({
		type: "POST",
		url: myurl,
		data: {
			objclass: focus.prop('class'),
			ckobjid: blocid,
			action: 'preview',
			customstyles: customstyles,
			fields: fields
		}
	}).done(function(code) {
		if (forpreviewarea || workspace.hasClass('ckcontenttypeedition')) $ck('> .ckstyle', $ck('#' + blocid)).empty().append(code);
		$ck('> .ckstyle', $ck('.workspaceck #' + blocid + ', #ckelementscontentfavorites #' + blocid)).empty().append(code);
		ckRemoveSpinnerIcon($ck('.headerckicon.cksave'));
		if (typeof(window[returnFunc]) == 'function') window[returnFunc]();
		ckAfterSaveEditionPopup();
		if (doClose == true) ckCloseEdition();
//		ckSaveAction();
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
	ckUpdateShapeDivider();
}

function ckAddSpinnerIcon(btn) {
	if (! btn.attr('data-class')) var icon = btn.find('.fa').attr('class');
	btn.attr('data-class', icon).find('.fa').attr('class', 'fa fa-spinner fa-pulse');
}

function ckRemoveSpinnerIcon(btn) {
	var t = setTimeout( function() {
		btn.find('.fa').attr('class', btn.attr('data-class'));
	}, 500);
}

function ckBeforeSaveEditionPopup() {
//	if (! blocid) blocid = '';
//	if (! returnFunc) returnFunc = '';
//	if (! workspace) workspace = ckGetWorkspace();
//	ckSaveEditionPopup(blocid, workspace, returnFunc)
}

function ckAfterSaveEditionPopup() {
	
}

function ckSaveEditionPopup(blocid, workspace, returnFunc, genCss) {
	// do nothing, function removed in 2.0.5
}

function ckSaveEdition(blocid, workspace, returnFunc, genCss) {
//	if ($ck('.editfocus.cktype').length) 
		ckBeforeSaveEditionPopup();
	if (! returnFunc) returnFunc = '';
	if (! genCss) genCss = ''; // needed for element like table where we have a preview area in the edition
	if (!workspace) workspace = ckGetWorkspace();

	var editionarea = $ck('#popup_editionck');
	var focus = blocid ? workspace.find('#' + blocid) : workspace.find('.editfocus');
	if (! focus.length) focus = blocid ? $ck('#' + blocid) : $ck('.editfocus');

	$ck('> .ckprops:not(.ckresponsive)', focus).remove();
	$ck('.ckproperty', editionarea).each(function(i, tab) {
		tab = $ck(tab);
		tabid = tab.attr('id');
		(!$ck('> .' + tabid, focus).length) ? ckCreateFocusProperty(focus, tabid) : $ck('> .' + tabid, focus).empty();
		
		focusprop = $ck('> .' + tabid, focus);
		ckSavePopupfields(focusprop, tabid);
		fieldslist = ckGetPopupFieldslist(focus, tabid);
		focusprop.attr('fieldslist', fieldslist);
	});
	if (focus.hasClass('wrapper') && $ck('> .tab_blocstyles', focus).attr('blocfullwidth') == 1) {
		$ck('> .inner', focus).removeClass('container').removeClass('container-fluid');
	} else if (focus.hasClass('wrapper')) {
		$ck('> .inner', focus).addClass('container');
	}

	if (genCss) ckGetPreviewstylescss(blocid, editionarea, workspace, returnFunc);
	ckSetAnimations(blocid, editionarea);
	ckAddVideoBackground(bloc);

//	ckCloseEditionPopup();
	if (typeof(window[returnFunc]) == 'function') window[returnFunc]();
	ckSaveAction();
}

//function ckPreviewVideoBackground() {
//	var webmurl = $ck('#blocvideourlwebm').val().replace(PAGEBUILDERCK.URIROOT,'');
//	var mp4url = $ck('#blocvideourlmp4').val().replace(PAGEBUILDERCK.URIROOT,'');
//	var ogvurl = $ck('#blocvideourlogv').val().replace(PAGEBUILDERCK.URIROOT,'');
//	var videocode = ckGetVideoBackgroundCode(webmurl, mp4url, ogvurl);
//
//	var previewarea = $ck('#previewareabloc > .inner');
//	if (previewarea.find('.videockbackground').length) previewarea.find('.videockbackground').remove();
//	previewarea.css('position', 'relative').css('overflow','hidden').prepend(videocode);
//}

function ckAddVideoBackground(bloc) {
	var webmurl = bloc.find('.tab_videobgstyles').attr('blocvideourlwebm');
	var mp4url = bloc.find('.tab_videobgstyles').attr('blocvideourlmp4');
	var ogvurl = bloc.find('.tab_videobgstyles').attr('blocvideourlogv');
	if (bloc.find('> .tab_videobgstyles').length
			&& (
				webmurl
				|| mp4url
				|| ogvurl
			)
		) {
		var videocode = ckGetVideoBackgroundCode(webmurl, mp4url, ogvurl);

		bloc.addClass('hasvideockbackground');
		bloc.find('.videockbackground').remove();
		bloc.find('> .inner').css('position', 'relative').css('overflow','hidden').prepend(videocode);
		return;
	} else {
		bloc.removeClass('hasvideockbackground');
		if (bloc.find('.videockbackground').length) {
			bloc.find('> .inner').css('overflow','').find('.videockbackground').remove();
		}
	}
	ckSaveAction();
	return;
}

function ckGetVideoBackgroundCode(webmurl, mp4url, ogvurl) {
	var videocode = '<video autoplay loop muted poster="" class="videockbackground">'
							+ (webmurl ? '<source src="'+PAGEBUILDERCK.URIROOT+'/'+webmurl+'" type="video/webm">' : '')
							+ (mp4url ? '<source src="'+PAGEBUILDERCK.URIROOT+'/'+mp4url+'" type="video/mp4">' : '')
							+ (ogvurl ? '<source src="'+PAGEBUILDERCK.URIROOT+'/'+ogvurl+'" type="video/ogg">' : '')
						+'</video>';
	return videocode;
}

function ckCheckVideoBackground(bloc) {
	if (bloc.find('> .tab_videobgstyles') 
			&& (
				bloc.find('> .tab_videobgstyles').attr('blocvideourlmp4')
				|| bloc.find('> .tab_videobgstyles').attr('blocvideourlwebm')
				|| bloc.find('> .tab_videobgstyles').attr('blocvideourlogv')
			)
		)
			return true
	return false;
}

function ckSetAnimations(blocid, editionarea) {
	var editionarea = editionarea ? editionarea : $ck('#popup_editionck');
	var focus = blocid ? $ck('#' + blocid) : $ck('.editfocus');
	// replay
	if ($ck('[name="blocanimreplay"]:checked', editionarea).val() == '0') {
		focus.addClass('noreplayck');
	} else {
		focus.removeClass('noreplayck');
	}
}

function ckCreateFocusProperty(focus, tabid) {
	focus.prepend('<div class="' + tabid + ' ckprops" />')
}

function ckSavePopupfields(focusprop, tabid) {
	$ck('.inputbox', $ck('#' + tabid)).each(function(i, field) {
		field = $ck(field);
		if (field.attr('type') != 'radio' && field.attr('type') != 'checkbox') {
			if (field.val() && field.val() != 'default') {
				focusprop.attr(field.attr('id'), field.val());
			} else {
				focusprop.removeAttr(field.attr('id'));
			}
		} else {
			if (field.prop('checked')) {
				focusprop.attr(field.attr('id'), 'checked');
			} else {
				focusprop.removeAttr(field.attr('id'));
			}
		}
		if (field.hasClass('isgooglefont') && field.val() != '') {
			ckSetGoogleFont('', '', field.val(), '');
		}
	});
}

function ckCapitalize(s) {
    return s[0].toUpperCase() + s.slice(1);
}

function ckSetGoogleFont(prefix, fonturl, fontname, fontweight) {
	if (! fontname) return;
	fontname = ckCapitalize(fontname).trim("'");
	if (! fonturl) fonturl = "https://fonts.googleapis.com/css?family="+fontname.replace(' ', '+');
	if (! fontweight) fontweight = $ck('#' + prefix + 'fontweight').val();
	// check if the google font exists
	$ck.ajax({
		url: fonturl,
	})
	.done(function( data ) {
		if (data) {
			if (prefix) {
				$ck('#' + prefix + 'googlefont').removeClass('invalid');
				$ck('#' + prefix + 'googlefont').val(fontname);
				$ck('#' + prefix + 'fontweight').val(fontweight);
				$ck('#' + prefix + 'fontfamily').val('googlefont').trigger('change');
			}
			ckAddGooglefontStylesheet(fonturl);
		} else {
			$ck('#' + prefix + 'googlefont').addClass('invalid');
		}
	})
	.fail(function() {
		$ck('#' + prefix + 'googlefont').addClass('invalid');
	});
}

function ckAddGooglefontStylesheet(fonturl) {
	var exist = false;
	// loop for retrocompatibility before 1.1.13
	while ($ck('#googlefontscall').length) {
		$ck('#googlefontscall').addClass('googlefontscall').removeAttr('id');
	}

	$ck('.googlefontscall link').each(function(i, sheet) {
		if ($ck(sheet).attr('href') == fonturl) exist = true;
	});
	if (exist == false ) {
		$ck('.googlefontscall').append("<link href='"+fonturl+"' rel='stylesheet' type='text/css'>");
	}
}

function ckGetPopupFieldslist(focus, tabid) {
	fieldslist = new Array();
	$ck('.inputbox', $ck('#' + tabid)).each(function(i, el) {
		if ($ck(el).val() && $ck(el).val() != 'default')
			fieldslist.push($ck(el).attr('id'));
	});
	if (tabid == 'tab_blocstyles' && (focus.hasClass('bannerlogo') || focus.hasClass('banner') || focus.hasClass('bannermenu')) )
		fieldslist.push('blocwidth');
	return fieldslist.join(',');
}

function ckGetPreviewstylescss(blocid, editionarea, workspace, returnFunc) {
	if (!editionarea) editionarea = document.body;
	if (!workspace) workspace = ckGetWorkspace();

	var focus = blocid ? workspace.find('#' + blocid) : workspace.find('.editfocus');
	if (! focus.length) focus = blocid ? $ck('#' + blocid) : $ck('.editfocus');

	var fieldslist = new Array();
	$ck('.inputbox', editionarea).each(function(i, el) {
		if ($ck(el).val())
			fieldslist.push($ck(el).attr('id'));
	});
	fields = new Object();
	$ck('> .ckprops', focus).each(function(i, ckprops) {
		ckprops = $ck(ckprops);
		fieldslist = ckprops.attr('fieldslist') ? ckprops.attr('fieldslist').split(',') : Array();
		// fieldslist.each(function(fieldname) {
		// for (var fieldname of fieldslist) {
		for (j=0;j<fieldslist.length;j++) {
			fieldname = fieldslist[j];
			fields[fieldname] = ckprops.attr(fieldname);
		}
		// });
	});
	fields = JSON.stringify(fields);
	customstyles = new Object();
	$ck('.menustylescustom').each(function() {
		$this = $ck(this);
		customstyles[$this.attr('data-prefix')] = $this.attr('data-rule');
	});
	customstyles = JSON.stringify(customstyles);
	var myurl = PAGEBUILDERCK.URIBASE + "/index.php?option=com_pagebuilderck&view=page&layout=ajaxrendercss";
	$ck.ajax({
		type: "POST",
		url: myurl,
		data: {
			objclass: focus.prop('class'),
			ckobjid: focus.prop('id'),
			action: 'preview',
			customstyles: customstyles,
			fields: fields
		}
	}).done(function(code) {
		$ck('> .ckstyle', focus).empty().append(code);
		if (BLOCCKSTYLESBACKUP != 'undefined') {
			BLOCCKSTYLESBACKUP = $ck('> .ckstyle', focus).html();
		}

		if (typeof(window[returnFunc]) == 'function') window[returnFunc]();
		ckSaveAction();
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

function ckCreateGradientPreview(prefix) {
	if (!$ck('#'+prefix + 'gradientpreview'))
		return;
	var area = $ck('#'+prefix + 'gradientpreview');
	if ($ck('#'+prefix + 'backgroundcolorstart') && $ck('#'+prefix + 'backgroundcolorstart').val()) {
		$ck('#'+prefix + 'backgroundcolorend').removeAttr('disabled');
		$ck('#'+prefix + 'backgroundpositionend').removeAttr('disabled');
	} else {
		$ck('#'+prefix + 'backgroundcolorend').attr({'disabled': 'disabled', 'value': ''});
		$ck('#'+prefix + 'backgroundcolorend').css('background-color', '');
		$ck('#'+prefix + 'backgroundpositionend').attr({'disabled': 'disabled', 'value': '100'});
	}
	if ($ck('#'+prefix + 'backgroundcolorend') && $ck('#'+prefix + 'backgroundcolorend').val()) {
		$ck('#'+prefix + 'backgroundcolorstop1').removeAttr('disabled');
		$ck('#'+prefix + 'backgroundpositionstop1').removeAttr('disabled');
		$ck('#'+prefix + 'backgroundopacity').attr({'disabled': 'disabled', 'value': ''});
	} else {
		$ck('#'+prefix + 'backgroundcolorstop1').attr({'disabled': 'disabled', 'value': ''});
		$ck('#'+prefix + 'backgroundcolorstop1').css('background-color', '');
		$ck('#'+prefix + 'backgroundpositionstop1').attr({'disabled': 'disabled', 'value': ''});
		$ck('#'+prefix + 'backgroundopacity').removeAttr('disabled');
	}
	if ($ck('#'+prefix + 'backgroundcolorstop1') && $ck('#'+prefix + 'backgroundcolorstop1').val()) {
		$ck('#'+prefix + 'backgroundcolorstop2').removeAttr('disabled');
		$ck('#'+prefix + 'backgroundpositionstop2').removeAttr('disabled');
	} else {
		$ck('#'+prefix + 'backgroundcolorstop2').attr({'disabled': 'disabled', 'value': ''});
		$ck('#'+prefix + 'backgroundcolorstop2').css('background-color', '');
		$ck('#'+prefix + 'backgroundpositionstop2').attr({'disabled': 'disabled', 'value': ''});
	}

	var gradientstop1 = '';
	var gradientstop2 = '';
	var gradientend = '';
	var gradientpositionstop1 = '';
	var gradientpositionstop2 = '';
	var gradientpositionend = '';
	if ($ck('#'+prefix + 'backgroundpositionstop1') && $ck('#'+prefix + 'backgroundpositionstop1').val())
		gradientpositionstop1 = $ck('#'+prefix + 'backgroundpositionstop1').val() + '%';
	if ($ck('#'+prefix + 'backgroundpositionstop2') && $ck('#'+prefix + 'backgroundpositionstop2').val())
		gradientpositionstop2 = $ck('#'+prefix + 'backgroundpositionstop2').val() + '%';
	if ($ck('#'+prefix + 'backgroundpositionstop3') && $ck('#'+prefix + 'backgroundpositionend').val())
		gradientpositionend = $ck('#'+prefix + 'backgroundpositionend').val() + '%';
	if ($ck('#'+prefix + 'backgroundcolorstop1') && $ck('#'+prefix + 'backgroundcolorstop1').val())
		gradientstop1 = $ck('#'+prefix + 'backgroundcolorstop1').val() + ' ' + gradientpositionstop1 + ',';
	if ($ck('#'+prefix + 'backgroundcolorstop2') && $ck('#'+prefix + 'backgroundcolorstop2').val())
		gradientstop2 = $ck('#'+prefix + 'backgroundcolorstop2').val() + ' ' + gradientpositionstop2 + ',';
	if ($ck('#'+prefix + 'backgroundcolorend') && $ck('#'+prefix + 'backgroundcolorend').val())
		gradientend = $ck('#'+prefix + 'backgroundcolorend').val() + ' ' + gradientpositionend;
	var stylecode = '<style type="text/css">'
			+ '#' + prefix + 'gradientpreview {'
			+ 'background:' + $ck('#'+prefix + 'backgroundcolorstart').val() + ';'
			+ 'background-image: -o-linear-gradient(top,' + $ck('#'+prefix + 'backgroundcolorstart').val() + ',' + gradientstop1 + gradientstop2 + gradientend + ');'
			+ 'background-image: -webkit-linear-gradient(top,' + $ck('#'+prefix + 'backgroundcolorstart').val() + ',' + gradientstop1 + gradientstop2 + gradientend + ');'
			+ 'background-image: -webkit-gradient(linear, left top, left bottom,' + $ck('#'+prefix + 'backgroundcolorstart').val() + ',' + gradientstop1 + gradientstop2 + gradientend + ');'
			+ 'background-image: -moz-linear-gradient(top,' + $ck('#'+prefix + 'backgroundcolorstart').val() + ',' + gradientstop1 + gradientstop2 + gradientend + ');'
			+ 'background-image: -ms-linear-gradient(top,' + $ck('#'+prefix + 'backgroundcolorstart').val() + ',' + gradientstop1 + gradientstop2 + gradientend + ');'
			+ 'background-image: linear-gradient(top,' + $ck('#'+prefix + 'backgroundcolorstart').val() + ',' + gradientstop1 + gradientstop2 + gradientend + ');'
			+ '}'
			+ '</style>';
	area.find('.injectstyles').html(stylecode);
}

function ckInitIconSize(fromicon, iconsizebutton) {
	$ck(iconsizebutton).each(function() {
		$ck(this).click(function() {
			$ck(fromicon).removeClass($ck(iconsizebutton + '.active').attr('data-width')).addClass($ck(this).attr('data-width'));
			$ck(iconsizebutton).removeClass('active');
			$ck(this).addClass('active');
		});
	});
}

function ckGetIconSize(fromicon, iconsizebutton) {
	var iconsize = 'default';
	var icon = $ck(fromicon);
	iconsize = icon.hasClass('fa-lg') ? 'fa-lg' : iconsize;
	iconsize = icon.hasClass('fa-2x') ? 'fa-2x' : iconsize;
	iconsize = icon.hasClass('fa-3x') ? 'fa-3x' : iconsize;
	iconsize = icon.hasClass('fa-4x') ? 'fa-4x' : iconsize;
	iconsize = icon.hasClass('fa-5x') ? 'fa-5x' : iconsize;
	$ck(iconsizebutton).removeClass('active');
	$ck(iconsizebutton + '[data-width="' + iconsize + '"]').addClass('active');
}

function ckInitIconPosition(fromicon, iconpositionbutton) {
	$ck(iconpositionbutton).each(function() {
		$ck(this).click(function() {
			if ($ck(this).attr('data-position') == 'default') {
				$ck(fromicon).css('vertical-align', '');
			} else {
				$ck(fromicon).css('vertical-align', $ck(this).attr('data-position'));
			}
			$ck(iconpositionbutton).removeClass('active');
			$ck(this).addClass('active');
		});
	});
}

function ckGetIconPosition(fromicon, iconpositionbutton) {
	var iconposition = 'default';
	var icon = $ck(fromicon);
	iconposition = icon.css('vertical-align') == 'default' ? 'default' : iconposition;
	iconposition = icon.css('vertical-align') == 'top' ? 'top' : iconposition;
	iconposition = icon.css('vertical-align') == 'middle' ? 'middle' : iconposition;
	iconposition = icon.css('vertical-align') == 'botom' ? 'bottom' : iconposition;

	$ck(iconpositionbutton).removeClass('active');
	$ck(iconpositionbutton + '[data-position="' + iconposition + '"]').addClass('active');
}

function ckGetIconMargin(fromicon, iconmarginfield) {
	$ck(iconmarginfield).val($ck(fromicon).css('margin-right'));
}

function ckSetIconMargin(fromicon, iconmarginfield) {
	if (! $ck(iconmarginfield).length) return;
	var margin = $ck(iconmarginfield).val();
	var pourcent = new RegExp('%',"g");
	var euem = new RegExp('em',"g");
	var pixel = new RegExp('px',"g");

	margin = pourcent.test(margin) ? margin : (euem.test(margin) ? margin : (pixel.test(margin) ? margin : margin + 'px'));
	$ck(fromicon).css('margin-right', margin);
	ckSaveAction();
}

function ckSelectFaIcon(iconclass) {
	alert('ERROR : If you see this message then the function "ckSelectFaIcon" is missing from the element edition. Please contact the developer');
}

function ckSelectModule(module) {
	alert('ERROR : If you see this message then the function "ckSelectModule" is missing from the element edition. Please contact the developer');
}

function ckLoadEditionPopup() {
	alert('ERROR : If you see this message then the function "ckLoadEditionPopup" is missing from the element edition. Please contact the developer');
}

function ckCallImageManagerPopup(id) {
	CKBox.open({handler: 'iframe', url: 'index.php?option=com_pagebuilderck&view=browse&type=image&func=selectimagefile&field='+id+'&tmpl=component'});
}

//function ckCallIconsPopup() {
//	if (! $ck('#pagebuilderckIconsmodalck').length) {
//		var popup = document.createElement('div');
//		popup.id = 'pagebuilderckIconsmodalck';
//		popup.className = 'pagebuilderckIconsmodalck pagebuilderckModalck modal hide fade';
//		document.body.appendChild(popup);
//		popup.innerHTML = '<div class="modal-header">'
//				+'<button type="button" class="close" data-dismiss="modal">×</button>'
//				+'<h3>' + Joomla.JText._('CK_ICON') + '</h3>'
//			+'</div>'
//			+'<div class="modal-body">'
//				+ '<iframe class="iframe" src="' + PAGEBUILDERCK.URIROOT + '/administrator/index.php?option=com_pagebuilderck&view=icons" height="400px" width="800px"></iframe>'
//			+'</div>'
//			+'<div class="modal-footer">'
//				+'<button class="btn fullscreenck" aria-hidden="true" onclick="ckTooglePagebuilderckModalFullscreen(this)"><i class="icon icon-expand-2"></i>' + Joomla.JText._('CK_FULLSCREEN') +'</button>'
//			+'</div>';
//
//		var BSmodal = $ck('#pagebuilderckIconsmodalck');
//		BSmodal.css('z-index', '44444');
//	} else {
//		var BSmodal = $ck('#pagebuilderckIconsmodalck');
//	}
//
//	BSmodal.find('.fullscreenck').removeClass('active');
//	BSmodal.modal().removeClass('pagebuilderckModalFullscreen');
//	BSmodal.modal('show');
//}

function ckCallGoogleFontPopup(prefix) {
	CKBox.open({url: PAGEBUILDERCK.URIROOT + '/administrator/index.php?option=com_pagebuilderck&amp;view=fonts&amp;tmpl=component&amp;prefix='+prefix})
}

function ckOpenModulesPopup() {
	url = PAGEBUILDERCK.URIROOT + '/administrator/index.php?option=com_pagebuilderck&view=modules';
	CKBox.open({id: 'ckmodulespopup', 
				url: url,
				style: {padding: '10px'}
			});
}
/* Toggle the fullscreen */
function ckTooglePagebuilderckModalFullscreen(button) {
	var BSmodal = $ck($ck(button).parents('.modal')[0]);
	if ($ck(button).hasClass('active')) {
		BSmodal.removeClass('pagebuilderckModalFullscreen');
		$ck(button).removeClass('active');
	} else {
		BSmodal.addClass('pagebuilderckModalFullscreen');
		ckResizeModalbodyOnFullscreen();
		$ck(button).addClass('active');
	}
}

/* Resize the fullscreen modal window to get the best space for edition */
function ckResizeModalbodyOnFullscreen() {
	var BSmodal = $ck('.modal.pagebuilderckModalFullscreen');
	var modalBody = BSmodal.find('.modal-body');
	modalBody.css('height', BSmodal.innerHeight() - BSmodal.find('.modal-header').outerHeight() - BSmodal.find('.modal-footer').outerHeight());
}

/* Bind the modal resizing on page resize */
$ck(window).bind('resize',function(){
	ckResizeModalbodyOnFullscreen();
	ckResizeEditor();
});

/* Play the animation in the Preview area */
function ckPlayAnimationPreview() {
//	$ck('.editfocus').hide(0).removeClass('animateck');
//	$ck('.editfocus .blockck').hide(0).removeClass('animateck');
	$ck('.editfocus').removeClass('animateck');
	$ck('.editfocus .blockck').removeClass('animateck');
	$ck('.workspaceck').addClass('pagebuilderck');
	var t = setTimeout( function() {
		$ck('.editfocus').addClass('animateck');
		$ck('.editfocus .blockck').addClass('animateck');
//		$ck('.workspaceck').removeClass('pagebuilderck');
	}, $ck('#blocanimdur').val()*1000);
}

/* remove the root path for the image to be shown in the editor */
function ckContentToEditor(content) {
	if (! content) return '';
	var search = new RegExp('<img(.*?)src="'+PAGEBUILDERCK.URIROOT.replace('/', '\/')+'\/(.*?)"',"g");
	content = content.replace(search, '<img $1src="$2"');

	return content;
}

/* add the root path for the image to be shown in the pagebuilder */
function ckEditorToContent(content) {
	if (! content) return '';
	var search = new RegExp('<img(.*?)src="(.*?)"',"g");
	var images = content.match(search);
	if (images) {
		for (var i = 0; i < images.length; i++) {
			if (images[i].indexOf('src="http') == -1) {
				var image = images[i].replace(search, '<img $1src="'+PAGEBUILDERCK.URIROOT+'/$2"');
				content = content.replace(images[i], image);
			}
		}
	}
//	content = content.replace(search, '<img $1src="'+PAGEBUILDERCK.URIROOT+'/$2"');

	return content;
}

/* show the popup to select a restoration date */
function ckCallRestorePopup() {
	CKBox.open({style: {padding: '10px'}, fullscreen: true, size: {x: '600px', y: '400px'}, handler: 'inline', content: 'pagebuilderckRestoreModalck', id: 'ckboxmodalrestore'});
//	var BSmodal = $ck('#pagebuilderckRestoreModalck');
//	BSmodal.css('z-index', '44444');
//	BSmodal.modal('show');
}

/* load the .pbck backup file and load it in the page */
function ckDoRestoration(id, name, index) {
	$ck('.restoreline' + index + ' .processing').addClass('ckwait');
	var isLocked = parseInt($ck('.restoreline' + index + ' .locked').attr('data-locked'));
	var myurl = PAGEBUILDERCK.URIPBCK + "&task=ajaxDoRestoration&" + PAGEBUILDERCK.TOKEN;
	$ck.ajax({
		type: "POST",
		url: myurl,
		data: {
			id: id,
			name: name,
			isLocked: isLocked
		}
	}).done(function(code) {
		$ck('.workspaceck').html(code);
		CKBox.close('#ckboxmodalrestore .ckboxmodal-button');
		$ck('.restoreline' + index + ' .processing').removeClass('ckwait');
		ckInitWorkspace();
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

/* Lock or unlock the backup to avoid it to be erased */
function ckToggleLockedBackup(id, filename, index) {
	var isLocked = parseInt($ck('.restoreline' + index + ' .locked').attr('data-locked'));
	$ck('.restoreline' + index + ' .locked').addClass('ckwait');
	var myurl = PAGEBUILDERCK.URIBASE + "/index.php?option=com_pagebuilderck&task=ajaxToggleLockBackup&" + PAGEBUILDERCK.TOKEN;
	$ck.ajax({
		type: "POST",
		url: myurl,
		data: {
			id: id,
			filename: filename,
			isLocked: isLocked
		}
	}).done(function(code) {
		if (code == '1') {
			$ck('.restoreline' + index + ' .locked').removeClass('ckwait');
			if (parseInt(isLocked)) {
				$ck('.restoreline' + index + ' .locked').removeClass('active').attr('data-locked', '0');
				$ck('.restoreline' + index + ' .locked .fa').removeClass('fa-lock').addClass('fa-unlock');
			} else {
				$ck('.restoreline' + index + ' .locked').addClass('active').attr('data-locked', '1');
				$ck('.restoreline' + index + ' .locked .fa').removeClass('fa-unlock').addClass('fa-lock');
			}
		} else {
			alert('Failed. Please reload the page.');
		}
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

/* Load an existing page into the interface */
function returnLoadPage(id, type, title) {
	if (! type) type = 'page';
	if (! title) title = '';

	var lefpanelhtml = '<div class="menuckpanelpopup">'
							+'<div class="headerck">'
								+'<span class="headerckicon" onclick="$ck(this).parent().parent().remove()">×</span>'
								+'<span class="headercktext">' + Joomla.JText._('CK_PAGE') + '</span>'
							+'</div>'
							+'<p>' + Joomla.JText._('CK_DRAG_DROP_PAGE') + '</p>'
							+'<div class="ckbutton ckpageitem" data-id="' + id  + '"><i class="fa fa-arrows"></i> '
							+ title
							+'</div>'
						+'</div>';
	$ck('#menuck .inner[data-target="pages"]').append(lefpanelhtml);
	// make the menu items draggable
	$ck('#menuck .menuckpanelpopup .ckpageitem').draggable({
		connectToSortable: ".workspaceck",
		helper: "clone",
		zIndex: "999999",
		tolerance: "pointer",
		start: function( event, ui ){
			$ck('#menuck').css('overflow', 'visible');
		},
		stop: function( event, ui ){
			$ck('#menuck').css('overflow', '');
		}
	});
}

function ckLoadPage(btn, option) {
	var id = $ck(btn).attr('data-id');
	var type = $ck(btn).attr('data-type');
	$ck(btn).addClass('ckwait');
	if (type == 'library') {
		ckLoadPageFromMediaLibrary(id, option);
//		var myurl = PAGEBUILDERCK.URIBASE + "/index.php?option=com_pagebuilderck&task=ajaxLoadLibraryHtml";
	} else {
		ckLoadPageFromPagebuilder(id, option);
//		var myurl = PAGEBUILDERCK.URIBASE + "/index.php?option=com_pagebuilderck&task=ajaxLoadPageHtml";
	}

	/*$ck.ajax({
		type: "POST",
		url: myurl,
		data: {
			id: id
		}
	}).done(function(code) {
		if (code != 'error') {
			var newcode = $ck(code);
			// look for each row and upate the html ID
			newcode.each(function() {
				if ($ck(this).hasClass('rowck')) replaceIdsInRow($ck(this), false);
			});

			if (option == 'replace') {
				$ck('.workspaceck').html(newcode);
			} else if (option == 'bottom') {
				$ck('.workspaceck').append(newcode);
			} else {
				$ck('.workspaceck').prepend(newcode);
			}
			ckInitWorkspace();
			if ($ck(newcode[2]).find('.cktype[data-type="image"]').length) ckAddDndForImageUpload($ck(newcode[2]).find('.cktype[data-type="image"]')[0]);
			ckInlineEditor();
		} else {
			alert(Joomla.JText._('Error : Can not get the page. Please retry and contact the developer.'));
		}
		$ck('#cktoolbarLoadPageOptions .ckwait').removeClass('ckwait');
		CKBox.close();
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
		$ck('#cktoolbarLoadPageOptions .ckwait').removeClass('ckwait');
	});*/
}

function ckLoadPageFromPagebuilder(id, currentblock) {
	var myurl = PAGEBUILDERCK.URIBASE + "/index.php?option=com_pagebuilderck&task=ajaxLoadPageHtml&" + PAGEBUILDERCK.TOKEN;
	$ck.ajax({
		type: "POST",
		url: myurl,
		data: {
			id: id
		}
	}).done(function(code) {
		if (code != 'error') {
			var newcode = code.trim();
			var newpage = $ck(newcode);
			currentblock.after(newpage);
			ckReplaceIdAll(newpage);
			currentblock.remove();
			ckInitWorkspace();
			if ($ck(newcode[2]).find('.cktype[data-type="image"]').length) ckAddDndForImageUpload($ck(newcode[2]).find('.cktype[data-type="image"]')[0]);
			ckInlineEditor();
		} else {
			alert(Joomla.JText._('Error : Can not get the page. Please retry and contact the developer.'));
		}

		$ck('#cktoolbarLoadPageOptions .ckwait').removeClass('ckwait');
		CKBox.close();
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
		$ck('#cktoolbarLoadPageOptions .ckwait').removeClass('ckwait');
	});
}

function ckReplaceIdAll(obj) {
	obj.each(function() {
		$this = $ck(this);
		if ($this.hasClass('rowck')) {
			var copyid = ckGetUniqueId('row_');
			// copy the styles
			ckReplaceId($this, copyid);
		}
	});

	obj.find('.rowck').each(function() {
		$row = $ck(this);
		var copyid = ckGetUniqueId('row_');
		// copy the styles
		ckReplaceId($row, copyid);
	});

	obj.find('.blockck, .cktype').each(function() {
		$this = $ck(this);

		var prefix = '';
		if ($this.hasClass('blockck')) {
			prefix = 'block_';
		} else {
		}

		var copyid = ckGetUniqueId(prefix);
		// copy the styles
		ckReplaceId($this, copyid);
	});
}

function ckLoadPageFromMediaLibrary(id, currentblock) {
//	var myurl = PAGEBUILDERCK.URIBASE + "/index.php?option=com_pagebuilderck&task=ajaxLoadLibraryHtml";
	var myurl = 'https://media.joomlack.fr/api/pagebuilderck/page/' + id;
	$ck.ajax({
		url: myurl,
		dataType: 'jsonp',
		cache: true,
		jsonpCallback: "joomlack_jsonpcallback",
		timeout: 20000,
	}).done(function(code) {
		if (code != 'error') {
			var newcode = $ck(code['htmlcode'].trim());
			currentblock.after(newcode);
			currentblock.remove();
			ckInitWorkspace();
			if ($ck(newcode[2]).find('.cktype[data-type="image"]').length) ckAddDndForImageUpload($ck(newcode[2]).find('.cktype[data-type="image"]')[0]);
			ckInlineEditor();
		} else {
			alert(Joomla.JText._('Error : Can not get the page. Please retry and contact the developer.'));
		}
		$ck('#cktoolbarLoadPageOptions .ckwait').removeClass('ckwait');
		CKBox.close();
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
		$ck('#cktoolbarLoadPageOptions .ckwait').removeClass('ckwait');
	});
}

function ckInjectPage(id, option, newcode) {
	// look for each row and upate the html ID
	newcode.each(function() {
		if ($ck(this).hasClass('rowck')) replaceIdsInRow($ck(this), false);
	});

	if (option == 'replace') {
		$ck('.workspaceck').html(newcode);
	} else if (option == 'bottom') {
		$ck('.workspaceck').append(newcode);
	} else {
		$ck('.workspaceck').prepend(newcode);
	}
	ckInitWorkspace();
	if ($ck(newcode[2]).find('.cktype[data-type="image"]').length) ckAddDndForImageUpload($ck(newcode[2]).find('.cktype[data-type="image"]')[0]);
	ckInlineEditor();
}

function ckSelectFile(file, field) {
		if (! field) {
			alert('ERROR : no field given in the function ckSelectFile');
			return;
		}
		$ck('#'+field).val(file).trigger('change');
		CKBox.close('#ckfilesmodal .ckboxmodal-button')
}

/* for retro compatibility purpose only */
function selectimagefile(file, field) {
	ckSelectFile(file, field);
}

function ckLoadIframeEdition(url, htmlId, taskApply, taskCancel) {
	CKBox.open({id: htmlId, 
				url: url,
				style: {padding: '10px'},
//				url: 'index.php?option=com_content&layout=modal&tmpl=component&task=article.edit&id='+id, 
				onCKBoxLoaded : function(){ckLoadedIframeEdition(htmlId, taskApply, taskCancel);},
				footerHtml: '<a class="ckboxmodal-button" href="javascript:void(0)" onclick="ckSaveIframe(\''+htmlId+'\')">'+Joomla.JText._('CK_SAVE_CLOSE')+'</a>'
			});
}

function ckLoadedIframeEdition(boxid, taskApply, taskCancel) {
	var frame = $ck('#'+boxid).find('iframe');
	frame.load(function() {
		var framehtml = frame.contents();
		framehtml.find('button[onclick^="Joomla.submitbutton"]').remove();
		framehtml.find('form').prepend('<button style="display:none;" id="saveBtn" onclick="Joomla.submitbutton(\''+taskApply+'\');" ></button>')
		framehtml.find('form').prepend('<button style="display:none;" id="cancelBtn" onclick="Joomla.submitbutton(\''+taskCancel+'\');" ></button>')
	});
}

function ckSaveIframe(boxid) {
	var frame = $ck('#'+boxid).find('iframe');
	frame.contents().find('#saveBtn').click();
	CKBox.close($ck('#'+boxid).find('.ckboxmodal-button'), true);
}

function ckTestUnit(value, defaultunit) {
	if (!defaultunit) defaultunit = "px";
	if (value.toLowerCase().indexOf('px') > -1 || value.toLowerCase().indexOf('em') > -1 || value.toLowerCase().indexOf('%') > -1)
		return value;

	return value + defaultunit;
}

/*------------------------------------------------------
 * Editor management 
 *-----------------------------------------------------*/

function ckShowEditor() {
	$ck('#ckeditorcontainer').show().find('.toggle-editor').hide();
	ckResizeEditor();
}

function ckResizeEditor() {
	var ckeditor_ifr_height = $ck('#ckeditorcontainer .ckboxmodal-body').height() - $ck('#ckeditorcontainer .mce-toolbar').height() - $ck('#ckeditorcontainer .mce-toolbar-grp').height() - $ck('#ckeditorcontainer .mce-statusbar').height();
	$ck('#ckeditor_ifr').height(parseInt(ckeditor_ifr_height) - 6);
}

function ckSaveEditorToContent() {
	
}
/*------------------------------------------------------
 * END of Editor management 
 *-----------------------------------------------------*/
 
 function ckSaveAsPage () {
	var title = prompt('This will create a new page with this layout. Please enter a name for this page');
	if (! title) return;
	var myurl = PAGEBUILDERCK.URIBASE + "/index.php?option=com_pagebuilderck&task=page.save&" + PAGEBUILDERCK.TOKEN;
	// CKBox.open({style: {padding: '10px'}, fullscreen: false, size: {x: '500px', y: '200px'}, handler: 'inline', content: 'cktoolbarExportPage'});
	$ck.ajax({
		type: "POST",
		url: myurl,
		data: {
			id: 0,
			title: title,
			method: 'ajax',
			htmlcode: $ck('.workspaceck').html()
		}
	}).done(function(code) {
		alert(Joomla.JText._('CK_PAGE_SAVED', 'Page saved'));
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

function ckShowResponsiveSettings(forcedisable) {
	if (! forcedisable) forcedisable = false;
	var button = $ck('#ckresponsivesettingsbutton');
	if (forcedisable) {
		$ck('.ckelementsedition').show();
		button.removeClass('active');
		$ck('.workspaceck .cktype, .workspaceck .blockck').each(function() {
			$bloc = $ck(this);
			$bloc.removeClass('ckmobileediting');
			$bloc.find('.ckmobileoverlay').remove();
		});
		$ck('.ckresponsiveedition').fadeOut();
		ckRemoveWorkspaceWidth();
		$ck('.ckcolwidthedition').remove();
		$ck('.editorckresponsive').remove();
	} else {
		$ck('.ckelementsedition').hide();
		$ck('.ckcolwidthedition').remove();

		if (! $ck('#ckresponsive4button.active').length) $ck('#ckresponsive4button').trigger('click');
		button.addClass('active');

		var ckresponsiverange = ckGetResponsiveRangeNumber();
		var editor = '<div class="editorckresponsive"></div>';

		$ck('.workspaceck .blockck, .workspaceck .cktype').each(function(i, bloc) {
			bloc = $ck(bloc);
			bloceditor = $ck(editor);
			bloceditor.css({
				'left': 0,
				'top': 0,
				'position': 'absolute',
				'z-index': 99
			});
			if (! bloceditor.find('> .editorckresponsive').length) bloc.append(bloceditor);
			bloceditor.css('display', 'none').fadeIn('fast');
			if (bloc.hasClass('cktype')) {
				var buttons = '<div class="isControl" data-class="ckshow" onclick="ckToggleResponsive(this)"><span class="fa fa-eye"></span></div>'
				+ '<div class="isControl" data-class="ckhide" onclick="ckToggleResponsive(this)"><span class="fa fa-eye-slash"></span></div>'
				+ '<div class="isControl" title="'+Joomla.JText._('CK_EDIT_STYLES')+'" onclick="ckShowEditionPopup(\'' + bloc.attr('id') + '\');" ><span class="fa fa-edit"></span></div>';
			} else {
				var buttons = '<div class="isControl" data-class="ckshow" onclick="ckToggleResponsive(this)"><span class="fa fa-eye"></span></div>'
				+ '<div class="isControl" data-class="ckhide" onclick="ckToggleResponsive(this)"><span class="fa fa-eye-slash"></span></div>'
				+ '<div class="isControl" title="'+Joomla.JText._('CK_EDIT_STYLES')+'" onclick="ckShowResponsiveCssEdition(\'' + bloc.attr('id') + '\');" ><span class="fa fa-edit"></span></div>';
			}
			bloceditor.append(buttons);
			if (bloc.hasClass('ckhide' + ckresponsiverange)) {
				bloc.find('> .editorckresponsive .isControl[data-class="ckhide"]').addClass('active');
			} else {
				bloc.find('> .editorckresponsive .isControl[data-class="ckshow"]').addClass('active');
			}
		});
		// loop through the wrappers
		$ck('.workspaceck .wrapperck').each(function(i, bloc) {
			bloc = $ck(bloc);
			if (bloc.hasClass('ckhide' + ckresponsiverange)) {
				bloc.find('.controlResponsiveHidden').addClass('active');
			} else {
				bloc.find('.controlResponsiveShown').addClass('active');
			}
		});
		$ck('.ckresponsiveedition').fadeIn();
	}
}

function ckSwitchResponsiveEditColumns(btn) {
	var $btn = $ck(btn);
	var state = $btn.attr('data-state');
	if (state == '1') {
		$ck('.workspaceck .rowck').each(function(i, row) {
			ckEditColumns($ck(row), 0, 1);
		});
		$ck('.editorckresponsive').css('display', '');
		$btn.removeClass('active');
	} else {
		$ck('.workspaceck .rowck').each(function(i, row) {
			ckEditColumns($ck(row), 1);
		});
		$ck('.editorckresponsive').css('display', 'none');
		$btn.addClass('active');
	}
	$btn.attr('data-state', 1 - state);
}

function ckSwitchResponsive(responsiverangeNumber, force) {
	if (! force) force = false;
	responsiverangeNumber = responsiverangeNumber ? responsiverangeNumber : ckGetResponsiveRangeNumber();
//	var resolution = parseFloat($ck('#ckresponsive' + responsiverange + 'value').val());
	var button = $ck('#ckresponsive' + responsiverangeNumber + 'button');

	// do nothing if click on the active button
	if (button.hasClass('active')) return;
	if (button.hasClass('active') && !force) {
		ckRemoveWorkspaceWidth();
	} else {
		$ck('#cktoolbarResponsive .ckresponsivebutton').removeClass('active').removeClass('ckbutton-warning');
		button.addClass('active').addClass('ckbutton-warning');
		ckSetWorkspaceWidth(responsiverangeNumber);
	}

	var responsiverangeattrib = ckGetResponsiveRangeAttrib(responsiverangeNumber);
	$ck('.wrapperck').each(function() {
		var $item = $ck(this);
		// set active state for show/hide buttons
		$ck('> .editorck .isControlResponsive', $item).removeClass('active');
		if ($item.hasClass('ckstack' + responsiverangeNumber)) {
			$ck('> .editorck .isControlResponsive[data-class="ckstack"]', $item).addClass('active');
		} else if ($item.hasClass('ckhide' + responsiverangeNumber)) {
			$ck('> .editorck .isControlResponsive[data-class="ckhide"]', $item).addClass('active');
		} else {
			$ck('> .editorck .isControlResponsive[data-class="ckshow"]', $item).addClass('active');
		}
	});
	$ck('.rowck').each(function() {
		var $row = $ck(this);
		// set active state for show/hide buttons
		$ck('> .editorck .isControlResponsive', $row).removeClass('active');
		if ($row.hasClass('ckstack' + responsiverangeNumber)) {
			$ck('> .editorck .isControlResponsive[data-class="ckstack"]', $row).addClass('active');
		} else if ($row.hasClass('ckhide' + responsiverangeNumber)) {
			$ck('> .editorck .isControlResponsive[data-class="ckhide"]', $row).addClass('active');
		} else {
			$ck('> .editorck .isControlResponsive[data-class="ckalign"]', $row).addClass('active');
		}
	});
	$ck('.blockck').each(function() {
		var $bloc = $ck(this);
		var blocdatawidth = $bloc.attr('data-width' + responsiverangeattrib) ? $bloc.attr('data-width' + responsiverangeattrib) : $bloc.attr('data-width');
		$bloc.find('.ckcolwidthselect').val(blocdatawidth);
		// set active state for show/hide buttons
		$ck('> .editorckresponsive .isControl', $bloc).removeClass('active');
		if ($bloc.hasClass('ckhide' + responsiverangeNumber)) {
			$ck('> .editorckresponsive .isControl[data-class="ckhide"]', $bloc).addClass('active');
		} else {
			$ck('> .editorckresponsive .isControl[data-class="ckshow"]', $bloc).addClass('active');
		}
	});
	$ck('.cktype').each(function() {
		var $item = $ck(this);
		// set active state for show/hide buttons
		$ck('> .editorckresponsive .isControl', $item).removeClass('active');
		if ($item.hasClass('ckhide' + responsiverangeNumber)) {
			$ck('> .editorckresponsive .isControl[data-class="ckhide"]', $item).addClass('active');
		} else {
			$ck('> .editorckresponsive .isControl[data-class="ckshow"]', $item).addClass('active');
		}
	});

	// update the css responsive values in the panel
	var range = ckGetResponsiveRange();
	$ck('#popup_editionck .inputbox').val('');
	ckFillEditionPopup($ck('.editfocus').attr('id'), $ck('.workspaceck'), range);
}

function ckGetDefaultDataWidth(row) {
	var number_blocks = row.find('.blockck').length;
	var default_data_width = 100 / number_blocks;

	return default_data_width;
}

function ckSetWorkspaceWidth(range) {
	var resolution = parseFloat($ck('#ckresponsive' + range + 'value').val());
	var workspace = ckGetWorkspace();
	
	var ranges = '';
	var rangeclone = 4;
	if (PAGEBUILDERCK.RESPONSIVERANGE == 'reducing' && range != '5') {
		while (rangeclone >= range) {
			ranges += rangeclone;
			rangeclone--;
		}
	} else {
		ranges = range;
	}

	workspace.css('width', resolution + 'px').attr('ckresponsiverange', ranges).addClass('ckresponsiveactive');
	$ck('.tck-container').css('width', resolution + 'px');
//	workspace.css('width', resolution + 'px');
//	$ck('.tck-container').attr('ckresponsiverange', range).addClass('ckresponsiveactive');
//	workspace.attr('ckresponsiverange', range).addClass('ckresponsiveactive');
	$ck('#menuck').attr('ckresponsiverange', range).addClass('ckresponsiveactive');
	if (range == '5' || range == '0') {workspace.css('width','');}
}

function ckRemoveWorkspaceWidth() {
	$ck('#cktoolbarResponsive .ckbutton').removeClass('active');
	var workspace = ckGetWorkspace();
	workspace.css('width','').attr('ckresponsiverange', '').removeClass('ckresponsiveactive');
	$ck('#menuck').attr('ckresponsiverange', '').removeClass('ckresponsiveactive');
}

function ckToggleResponsive(btn) {
	var btn = $ck(btn);
	var cktype = $ck(btn.parents('.cktype')[0]);
	if (! cktype.length) cktype = $ck(btn.parents('.blockck')[0]);
	var rangeNumber = ckGetResponsiveRangeNumber();
	$ck('> .editorckresponsive .isControl', cktype).removeClass('active');
	btn.addClass('active');
	if (btn.attr('data-class') === 'ckhide') {
			cktype.addClass('ckhide' + rangeNumber);
	} else {
			cktype.removeClass('ckhide' + rangeNumber);
	}
}

function ckToggleResponsiveWrapper(btn) {
	var btn = $ck(btn);
//	var row = $ck('.ckfocus');
	var wrapper = $ck(btn.parents('.wrapperck')[0]);
	var rangeNumber = ckGetResponsiveRangeNumber();
	btn.parent().find('.isControlResponsive').removeClass('active');
	btn.addClass('active');
	if (btn.attr('data-class') === 'ckhide') {
			wrapper.removeClass('ckstack' + rangeNumber).removeClass('ckalign' + rangeNumber);
			wrapper.addClass('ckhide' + rangeNumber);
	} else if (btn.attr('data-class') === 'ckstack') {
			wrapper.removeClass('ckhide' + rangeNumber).removeClass('ckalign' + rangeNumber);
			wrapper.addClass('ckstack' + rangeNumber);
	} else {
			wrapper.removeClass('ckhide' + rangeNumber);
			wrapper.removeClass('ckstack' + rangeNumber);
			wrapper.addClass('ckalign' + rangeNumber);
	}
}

function ckToggleResponsiveRow(btn) {
	var btn = $ck(btn);
//	var row = $ck('.ckfocus');
	var row = $ck(btn.parents('.rowck')[0]);
	var rangeNumber = ckGetResponsiveRangeNumber();
	btn.parent().find('.isControlResponsive').removeClass('active');
	btn.addClass('active');
	if (rangeNumber == '') rangeNumber = '5';
	if (btn.attr('data-class') === 'ckhide') {
		row.removeClass('ckstack' + rangeNumber);
		row.removeClass('ckalign' + rangeNumber);
		row.addClass('ckhide' + rangeNumber);
	} else if (btn.attr('data-class') === 'ckstack') {
		row.removeClass('ckhide' + rangeNumber);
		row.removeClass('ckalign' + rangeNumber);
		row.addClass('ckstack' + rangeNumber);
	} else {
		row.removeClass('ckhide' + rangeNumber);
		row.removeClass('ckstack' + rangeNumber);
		row.addClass('ckalign' + rangeNumber);
	}
}

function ckCheckHtml(forcedisable) {
	if (! forcedisable) forcedisable = false;
	var button = $ck('#ckhtmlchecksettingsbutton');
	if (button.hasClass('active') || forcedisable) {
		button.removeClass('active');
		$ck('.workspaceck .rowck, .workspaceck .blockck, .workspaceck .cktype').each(function() {
			$bloc = $ck(this);
			$bloc.removeClass('ckhtmlinfoediting');
			$bloc.find('.ckhtmlinfos').remove();
		});
	} else {
		button.addClass('active');
		var showmessage = false;
		$ck('.workspaceck .rowck, .workspaceck .blockck, .workspaceck .cktype').each(function() {
			$bloc = $ck(this);
			var customclasses = $bloc.find('> .inner,> .imageck,> .iconck').attr('data-customclass') ? $bloc.find('> .inner,> .imageck,> .iconck').attr('data-customclass') : '';
			$bloc.addClass('ckhtmlinfoediting')
				.prepend('<div class="ckhtmlinfos">'
							+ '<div class="ckhtmlinfosid" onclick="ckChangeBlocId(this)" data-id="'+$bloc.attr('id')+'">'
								+ '<span class="label">ID</span> '
								+ '<span class="ckhtmlinfosidvalue">'
									+ $bloc.attr('id')
								+ '</span>'
							+ '</div>'
							+ '<div class="ckhtmlinfosclass" onclick="ckChangeBlocClassname(this)">'
								+ '<span class="label">Class</span> '
								+ '<span class="ckhtmlinfosclassvalue">'
									+ customclasses 
								+ '</span>'
							+ '</div>'
						+ '</div>');
			// check if duplicated IDs
			if ($ck('[id="'+$bloc.attr('id')+'"]').length > 1) {
				showmessage = true;
				$ck('[id="'+$bloc.attr('id')+'"]').each(function() {
					$ck(this).find('> .ckhtmlinfos .ckhtmlinfosidvalue').addClass('invalid');
				});
			}
		});
		if (showmessage) {
			alert(Joomla.JText._('CHECK_IDS_ALERT_PROBLEM','Some blocks have the same ID. This is a problem that must be fixed. Look at the elements in red and rename them'));
		} else {
			alert(Joomla.JText._('CHECK_IDS_ALERT_OK','Validation finished, all is ok !'));
		}
	}
}

function ckChangeBlocId(btn) {
	// blocid = $ck(btn).attr('data-id');
	// bloc = $ck('#' + blocid);
	bloc = $ck($ck(btn).parents('.rowck, .blockck, .cktype')[0]);
	var result = prompt(Joomla.JText._('CK_ENTER_UNIQUE_ID', 'Please enter a unique ID (must be a text)'), bloc.attr('id'));
	if (!result)
		return;
	result = ckValidateName(result);
	if (ckValidateBlocId(result))
		ckUpdateIdPosition(bloc, result);
}

function ckChangeBlocClassname(btn) {
	bloc = $ck($ck(btn).parents('.rowck, .blockck, .cktype')[0]);
	var blocinner = bloc.find('> .inner,> .imageck,> .iconck');
	var customclasses = blocinner.attr('data-customclass') ? blocinner.attr('data-customclass') : '';
	var result = prompt(Joomla.JText._('CK_ENTER_CLASSNAMES', 'Please enter the class names separated by a space'), customclasses);
	if (result == null)
		return;
	// result = result.replace(/\s/g, "");

	// remove previous classes
	var customclassesFrom = customclasses.split(' ');
	for (var i=0; i<customclassesFrom.length; i++) {
		blocinner.removeClass(customclassesFrom[i]);
	}
	// add new classes
	var customclassesTo = result.split(' ');
	for (var i=0; i<customclassesTo.length; i++) {
		blocinner.addClass(customclassesTo[i]);
	}

	blocinner.attr('data-customclass', result);
	bloc.find('> .ckhtmlinfos .ckhtmlinfosclassvalue').text(result);
}

function ckValidateBlocId(newid) {
	if (newid != null && newid != "" && !$ck('#' + newid).length) {
		return true;
	} else if ($ck('#' + newid).length) {
		alert(Joomla.JText._('CK_INVALID_ID', 'ID invalid or already exist'));
		return false;
	} else if (newid == null || newid == "") {
		alert(Joomla.JText._('CK_ENTER_VALID_ID', 'Please enter a valid ID'));
		return false;
	}
	return true;
}

function ckValidateName(name) {
	var name = name.replace(/\s/g, "");
	name = name.toLowerCase();
	return name;
}

function ckUpdateIdPosition(bloc, newid) {
	// bloc = $ck('#' + blocid);
	ckReplaceId(bloc, newid);
	bloc.find('> .ckhtmlinfos .ckhtmlinfosid').attr('data-id', newid);
	bloc.find('> .ckhtmlinfos .ckhtmlinfosidvalue').removeClass('invalid');
	bloc.find('> .ckhtmlinfos .ckhtmlinfosidvalue').text(newid);
}


/******* Undo and Redo actions *************/

var ckActionsCounter=new Object();
ckActionsCounter=0;
var ckActionsPointer=new Object();
ckActionsPointer=0;
var ckDoActionsList=new Array();

//ckDoActionsList[0] store initial textarea value
//ckDoActionsList[0]="";

function ckSaveAction() {
	if (! document.getElementById('workspaceck')) return; // TODO update for frontend edition
	ckRemoveLinkRedirect(); // on each action we check that the links will not be redirected

	ckActionsCounter++;
	var y=ckActionsCounter;
	var x=document.getElementById('workspaceck').innerHTML;
	ckDoActionsList[y]=x;
	$ck('#ckundo').removeClass('ckdisabled');
}

function ckUndo() {
	if (! document.getElementById('workspaceck')) return;
	if ((ckActionsPointer)<(ckActionsCounter)) {
		ckActionsPointer++;
		$ck('#ckredo').removeClass('ckdisabled');
	} else {
		$ck('#ckundo').addClass('ckdisabled');
		return;
		// alert(Joomla.JText._('CK_NO_MORE_UNDO', 'There is no more Undo action'));
	}
	var z=ckDoActionsList.length;
	z=z-ckActionsPointer-1;
	if (ckDoActionsList[z]) {
		document.getElementById('workspaceck').innerHTML=ckDoActionsList[z];
	} else {
		document.getElementById('workspaceck').innerHTML=ckDoActionsList[0];
	}
	ckInitWorkspace();
}

function ckRedo() {
	if((ckActionsPointer)>=1) {
		ckActionsPointer--;
		$ck('#ckundo').removeClass('ckdisabled');
	} else {
		$ck('#ckredo').addClass('ckdisabled');
		return;
		// alert(Joomla.JText._('CK_NO_MORE_REDO', 'There is no more Redo action'));
	}
	var z=ckDoActionsList.length;
	z=z-ckActionsPointer-1;
	if (ckDoActionsList[z]) {
		document.getElementById('workspaceck').innerHTML=ckDoActionsList[z];
	} else {
		document.getElementById('workspaceck').innerHTML=ckDoActionsList[0];
	}
	ckInitWorkspace();
}

function ckInlineEditor(selector, workspace) {
	// only enable the inline editing if we are using tinymce
	if (PAGEBUILDERCK_EDITOR != 'tinymce') return;
	if (! selector) {
		selector = '.workspaceck [data-type="text"].ckinlineeditable .inner, .workspaceck [data-type="icontext"].ckinlineeditable .textck';
		if (!workspace) workspace = ckGetWorkspace();
		$ck('.cktype[data-type="text"]', workspace).addClass('ckinlineeditable');
		$ck('.cktype[data-type="icontext"]', workspace).addClass('ckinlineeditable');
		$ck('[data-type="text"]', workspace).addClass('ckinlineeditable');
		// .workspaceck [data-type="icontext"].ckinlineeditable .titleck >> attention car code html se retrouve dans textarea
		// add contenteditable for inline edition of all items
		var contenteditables = '.titleck,.buttontextck,.pbck_gallery_item_title,.pbck_gallery_item_desc,.messageck_title,.messageck_text,.separatorck_text,'
		+ '.tableck-cell,.itemcontentck,.itemtitleck,.pbck_testimonial_text, .pbck_testimonial_author_name, .pbck_testimonial_author_status, .pbck_testimonial_author_url'
		+ ',.pbck_contenteditable';
		workspace.find(contenteditables).attr('contenteditable', 'true');
	}
	
	tinymce.init({
		selector: selector,
		inline: true,
		autosave_ask_before_unload: false,
		plugins: [
		'advlist autolink lists link image charmap print preview anchor',
		'searchreplace visualblocks code fullscreen',
		'insertdatetime media table contextmenu paste'
		],
		toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink image',
		menubar: false
	});
	
}

function ckSaveItem(blocid) {
	var name = prompt('Name to save the element');
	if (! name) return;
	// var styleswrapper = ckGetStylesWrapperForBlock(blocid);
	var saveditem = $ck('#' + blocid).clone();
	ckRemoveEdition(saveditem, true);
	if (saveditem.hasClass('rowck')) {
		var type = 'row';
	} else if (saveditem.hasClass('wrapperck')) {
		var type = 'wrapper';
	} else {
		var type = saveditem.attr('data-type');
	}
	var myurl = PAGEBUILDERCK.URIBASE + "/index.php?option=com_pagebuilderck&task=ajaxSaveElement&" + PAGEBUILDERCK.TOKEN;
	$ck.ajax({
		type: "POST",
		url: myurl,
		data: {
			name : name,
			type : type,
			id : saveditem.attr('data-id'),
			html : saveditem[0].outerHTML
		}
	}).done(function(code) {
		var result = JSON.parse(code);
		if (result.status == '1') {
			alert(Joomla.JText._('CK_SAVED', 'Saved'));
			$ck('#ckmyelements').append(result.code);
			ckMakeItemsDraggable();
		} else {
			alert(Joomla.JText._('CK_FAILED', 'Failed'));
		}
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

function ckShowColorsPalette() {
	$boxfooterhtml = '<a class="ckboxmodal-button" href="javascript:void(0);" onclick="ckSetPaletteColors();CKBox.close()">' + Joomla.JText._('CK_SAVE_CLOSE') + '</a>';
	var myurl = PAGEBUILDERCK.URIPBCK + "&task=interface.load&layout=colorspalette&" + PAGEBUILDERCK.TOKEN;
	$ck.ajax({
		type: "POST",
		url: myurl,
		async: true,
		data: {
		}
	}).done(function(code) {
		$ck('#ckcolorspalette').remove();
		var colorspalette = $ck(code);
		$ck(document.body).append(colorspalette);
		colorspalette.hide();
		CKBox.open({handler: 'inline', content: 'ckcolorspalette', footerHtml: $boxfooterhtml, style: {padding: '10px'}, size: {x: '600px', y: '400px'}});
		var colors = $ck('.workspaceck > .pagebuilderckparams').attr('data-colorpalette');
		var colorsFromSettings = $ck('.workspaceck > .pagebuilderckparams').attr('data-colorpalettefromsettings');
		var colorsFromTemplate = $ck('.workspaceck > .pagebuilderckparams').attr('data-colorpalettefromtemplate');

		ckLoadPaletteColors('colorpalette', colors);
		ckLoadPaletteColors('colorpalettefromsettings', colorsFromSettings);
		ckLoadPaletteColors('colorpalettefromtemplate', colorsFromTemplate);
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

function ckLoadPaletteColors(selector, colors) {
	if (colors) {
		colors = colors.split(',');
		for (var i=0; i< colors.length; i++) {
			var field = $ck('#ckcolorspalette [data-selector="' + selector + '"] .inputbox').eq(i);
			if (colors[i]) {
				field.val('#' + colors[i].replace('#', '')).trigger('change');
				field.css('background-color', field.val());
				setpickercolor(field);
			}
		}
	}
}

function ckCopyPaletteFrom(btn) {
	var colorToCopy = $ck(btn).parent().find('.inputbox').val();
	if (! colorToCopy) return;
	var row = $ck($ck(btn).parents('tr')[0]);
	var index = $ck($ck(btn).parents('table')[0]).find(('tr')).index(row);
	$ck('#ckcolorspalette table[data-selector="colorpalette"] tr').eq(index).find('.inputbox')
			.val(colorToCopy)
			.css('background-color', colorToCopy);
}

function ckSetPaletteColors() {
	var colors = new Array();
	$ck('#ckcolorspalette .colorPicker').each(function() {
		colors.push($ck(this).val().replace('#', ''));
	});
	colors = colors.join(',');
	ckSetPaletteOnColorPicker(colors, 'colpick_palette');
}

function ckSetPaletteOnColorPicker(colors, object) {
	CKBox.close();
	colors = colors.split(',');
	$ck('span',$ck('#'+object)).each(function(i, el) {
		$ck(el).css('background-color', '#'+colors[i]);
	});
	$ck('.workspaceck > .pagebuilderckparams').attr('data-colorpalette', colors);
}

function ckUpdateShapeDivider(prefix) {
	// prefix is not used anymore
	// automatically update both dividers
	ckCreateShapeDividerNew('divider');
	ckCreateShapeDividerNew('divider-2');
}

function ckCreateShapeDividerNew(prefix) {
	var focus = $ck('.editfocus');
	switch ($ck('#' + prefix + 'shape').val()) {
		case 'multiclouds' :
			var svgpath = '<svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 1000 86.47" preserveAspectRatio="none"><g style="opacity:0.33"><path d="M823,15.52l.24-.07A27.72,27.72,0,0,0,864.3,30.53a46.9,46.9,0,0,0,51.9,28A55,55,0,0,0,1000,73.07V0H792C795.79,12,809.32,18.85,823,15.52Z"></path><path d="M23.71,83.4A50,50,0,0,0,85.39,48.77v-.05a25.19,25.19,0,0,0,20.89-4.31,32.67,32.67,0,0,0,12.82,7A32.88,32.88,0,0,0,154.31,0H0V68.64A49.74,49.74,0,0,0,23.71,83.4Z"></path></g><g style="opacity:0.66"><path d="M499.63,19.13h.08a8.91,8.91,0,0,0,12.64,6.15A15.07,15.07,0,0,0,528,35.9a17.67,17.67,0,0,0,33.67-9.55v0A8.9,8.9,0,0,0,567.86,22a11.61,11.61,0,0,0,7.48-22H503.08a11.65,11.65,0,0,0-1.71,4.21,9.2,9.2,0,0,0-3.85-.28c-4.65.65-8,4.58-7.37,8.77S495,19.78,499.63,19.13Z"></path><path d="M631.55,20.67c1,7.6,8.68,12.87,17.22,11.78a16.35,16.35,0,0,0,11.45-6.74,16.34,16.34,0,0,0,7.07,2.14A10.86,10.86,0,0,0,686.86,35a10.82,10.82,0,0,0,8.1-1c1.68,6.83,9,11.4,17,10.38a16,16,0,0,0,12.48-8.49,19.56,19.56,0,0,0,10.37,1.45,19.24,19.24,0,0,0,11.72-5.89,10.85,10.85,0,0,0,17.33-.92A10.81,10.81,0,0,0,776,31.2a17.64,17.64,0,0,0,3.38,1,18.52,18.52,0,0,0,16.52,6A18.82,18.82,0,0,0,809.34,30c2.67,10,12.75,17.44,24.8,17.44,9.38,0,17.57-4.5,22-11.2a32,32,0,0,0,16.53,4.5,31.47,31.47,0,0,0,20.23-7.14,17.75,17.75,0,0,0,28.32,2.09,17.74,17.74,0,0,0,22.71,1.75c4.13,10.05,15,17.22,27.72,17.22,13.43,0,24.75-8,28.33-18.88V0H599.32C607.84,23.13,631.55,20.67,631.55,20.67Z"></path><path d="M.74,30.73c0,12.33,11.21,22.33,25.08,22.36,10.84,0,20.08-6.07,23.61-14.62A15.09,15.09,0,0,0,68.74,37a15.1,15.1,0,0,0,24.1-1.74,26.76,26.76,0,0,0,17.2,6.1,27.24,27.24,0,0,0,14.07-3.81,22.33,22.33,0,0,0,18.71,9.56c11.24,0,20.49-7.56,21.62-17.28a14.92,14.92,0,0,0,10.72.18c3.29,7.35,12.1,11.63,21.28,9.81a20.31,20.31,0,0,0,13.62-9.33A20.31,20.31,0,0,0,219,32.56a13.49,13.49,0,0,0,24.86,7.25,13.43,13.43,0,0,0,10-1.91c2.66,8.32,12.06,13.37,21.9,11.42a19.93,19.93,0,0,0,14.75-11.58,24.3,24.3,0,0,0,13,.92,23.88,23.88,0,0,0,14-8.3,13.47,13.47,0,0,0,21.4-2.61,13.46,13.46,0,0,0,17.17-2c4.56,6.88,13.69,10.63,23.18,8.76,12.14-2.4,20.26-13.09,18.13-23.88A73.93,73.93,0,0,0,400.48,0H0V29.49C.24,29.91.48,30.32.74,30.73Z"></path></g><path d="M16.3,13.9c10.2,2.5,20.3-1.1,25.5-8.3a14.66,14.66,0,0,0,18.5,3A14.6,14.6,0,0,0,80,14.9a13.14,13.14,0,0,0,3.4-2.4,25.71,25.71,0,0,0,14.8,9.7,26,26,0,0,0,14.1-.4,21.75,21.75,0,0,0,15.4,13.3c10.6,2.6,21-2.4,24.3-11.3a15,15,0,0,0,10.7,2.6,17.69,17.69,0,0,0,1.6,2.2,14.69,14.69,0,0,0,17.6,3.5,7.46,7.46,0,0,0,1.2-.7,14.54,14.54,0,0,0,6.4-8.9,12.61,12.61,0,0,0,.4-2.8,20.63,20.63,0,0,0,9.8-1.8,11.35,11.35,0,0,0,1.5,2.3A22.35,22.35,0,0,0,214,28.6c11.2,2.8,22.4-3.1,24.8-13.1a24.63,24.63,0,0,0,16.3,11.6c9.8,2.1,19.4-1.7,24.2-8.7a14,14,0,0,0,17.8,2.4,14.07,14.07,0,0,0,19.1,5.4,12.25,12.25,0,0,0,3.1-2.4,22.5,22.5,0,0,0,5.8,5.3,25.42,25.42,0,0,0,16.1,4,30.38,30.38,0,0,0,6-1.2c.2.4.4.9.6,1.3a20.81,20.81,0,0,0,14.6,11c10.2,2.2,20-2.9,22.9-11.5a13.84,13.84,0,0,0,10.3,2.1,14,14,0,0,0,19.3,4.6,14.17,14.17,0,0,0,6.7-11.8,20,20,0,0,0,9.3-2,21.31,21.31,0,0,0,14,9.9c10.6,2.3,20.9-3.4,23.2-12.7a28.46,28.46,0,0,0,37.2,7.1,23.54,23.54,0,0,0,7.3-7.1,15.79,15.79,0,0,0,20.1,2.1,15.69,15.69,0,0,0,21.6,5.5,13.88,13.88,0,0,0,3.5-2.9,26.66,26.66,0,0,0,9.5,7.2,28.5,28.5,0,0,0,7,2.2,29.16,29.16,0,0,0,15.2-1.3c2.8,6.6,9.3,11.8,17.5,13.3,11.4,2.1,22.2-3.8,25.3-13.4,0-.1.1-.2.1-.4.3.2.7.4,1,.6a15.93,15.93,0,0,0,10.7,1.5,15.79,15.79,0,0,0,28.7-6c.1-.4.1-.8.2-1.2a10.87,10.87,0,0,0,.1-1.8,22.26,22.26,0,0,0,10.4-2.6,25,25,0,0,0,3.9,4.7,24.65,24.65,0,0,0,12.2,6A24.5,24.5,0,0,0,715.3,34a19.09,19.09,0,0,0,10.2-13.4h.5a21.68,21.68,0,0,0,21.1,13,13.67,13.67,0,0,0,1.9-.2,22.1,22.1,0,0,0,13.8-7.7,24.79,24.79,0,0,0,11.9,8.5,25.09,25.09,0,0,0,8.1,1.4,25.86,25.86,0,0,0,18.5-6.7,21.77,21.77,0,0,0,5.2-7.2,15,15,0,0,0,19.1-1,15,15,0,0,0,21,2,13.81,13.81,0,0,0,2.8-3.1A26.84,26.84,0,0,0,866.3,26a27.39,27.39,0,0,0,14-3.4,22.36,22.36,0,0,0,18.3,9.9c11.1.3,20.4-7,21.8-16.6a15,15,0,0,0,11.2.2,15,15,0,0,0,21.1,1,15.16,15.16,0,0,0,4.7-13.5A22.32,22.32,0,0,0,966.3,0H0V1.6A25.29,25.29,0,0,0,16.3,13.9Z"></path><path d="M983.6,7.3A22.61,22.61,0,0,0,1000,1.1V0H967.3A22.52,22.52,0,0,0,983.6,7.3Z"></path></svg>';
		break;
		case 'clouds' :
			var svgpath = '<svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 1000 63.67" preserveAspectRatio="none"><path d="M916.2,58.53a46.9,46.9,0,0,1-46.1-17.89,32,32,0,0,1-14-4.4c-4.43,6.7-12.62,11.2-22,11.2-12,0-22.13-7.44-24.8-17.44a18.82,18.82,0,0,1-13.44,8.2,18.51,18.51,0,0,1-12.45-2.59h-.65a25.09,25.09,0,0,1-8.1-1.4,24.79,24.79,0,0,1-3.52-1.48,10.8,10.8,0,0,1-7.32-2.19,10.84,10.84,0,0,1-15.13,2.91,13.67,13.67,0,0,1-1.63.16,21.69,21.69,0,0,1-2.93,0,19.23,19.23,0,0,1-9.36,3.78,19.56,19.56,0,0,1-10.37-1.45A16,16,0,0,1,712,44.38c-7.56,1-14.51-3.07-16.67-9.28q-.71-.27-1.41-.58a10.82,10.82,0,0,1-7,.48,10.85,10.85,0,0,1-16.07,1.54,15.75,15.75,0,0,1-26.69.66,15.93,15.93,0,0,1-10.7-1.5c-.3-.2-.7-.4-1-.6,0,.2-.1.3-.1.4C629.2,45.1,618.4,51,607,48.9c-8.2-1.5-14.7-6.7-17.5-13.3a29.16,29.16,0,0,1-15.2,1.3,28.5,28.5,0,0,1-7-2.2,26.65,26.65,0,0,1-5.65-3.46A17.66,17.66,0,0,1,528,35.9a15.07,15.07,0,0,1-15.65-10.62,8.91,8.91,0,0,1-2.07.72L510,26a23.53,23.53,0,0,1-4.73,3.86,28.46,28.46,0,0,1-37.2-7.1c-2.3,9.3-12.6,15-23.2,12.7a21.31,21.31,0,0,1-14-9.9,20,20,0,0,1-9.3,2,14.17,14.17,0,0,1-6.7,11.8l-.05,0A14,14,0,0,1,395.6,34.8a13.84,13.84,0,0,1-10.3-2.1c-2.9,8.6-12.7,13.7-22.9,11.5a20.81,20.81,0,0,1-14.6-11c-.2-.4-.4-.9-.6-1.3a30.38,30.38,0,0,1-6,1.2,25.39,25.39,0,0,1-7.23-.41,13.46,13.46,0,0,1-16.46-2.33,23.88,23.88,0,0,1-14,8.3,24.3,24.3,0,0,1-13-.92,19.93,19.93,0,0,1-14.75,11.58c-9.84,2-19.24-3.1-21.9-11.42a13.43,13.43,0,0,1-10,1.91A13.49,13.49,0,0,1,219,32.56a20.31,20.31,0,0,1-8.94-2.07,20.31,20.31,0,0,1-13.62,9.33c-7.6,1.51-14.95-1.18-19.11-6.32a14.7,14.7,0,0,1-11.15-3.1,14.89,14.89,0,0,1-1.74-.57c-1,8.55-8.28,15.43-17.67,17a32.85,32.85,0,0,1-40.49-2.38,25.19,25.19,0,0,1-20.89,4.31v.09A50,50,0,0,1,0,68.64V86.47H1000V73.07a55,55,0,0,1-83.8-14.54Z" transform="translate(0 -22.8)"></path></svg>';
		break;
		case 'papertorn' :
			var svgpath = '<svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 1000 80" preserveAspectRatio="none"><path d="M0,0V71.07l.22.05c2.83,1,7.45-4.57,7.45-4.57s13.36,6.34,14.9,6.6S30,76.45,30,76.45L36.7,71.5s5.4,6,8,4.95,16.19-8.88,17.73-3.55S64.2,80,65,80s5.4-5.59,6.42-5.59,11,2.79,11.82,2.79,7.71-5.84,9.25-6.6,18.24.51,22.1,2.66,14.13.13,19.79,0,25.18,1.9,25.18,1.9l8.48-1.9s0-5.46,1.8-5.2,9.25.51,9.25.51L180.34,65s20.81,2,22.1,2,2.31-3.81,4.37-3.81,14.13,3.81,15.42,3.81,9.25-3.55,11.56-3.81,8.48,5.08,10.28,5.58,55-6.6,55-6.6-.26-5.33,4.88-3.55,15.16,1.27,19.53,2,3.34,6.34,7.71,2.54,5.65-7.36,8.22-6.09,10.28,2.28,11,2.28,4.37-8.12,7.19-6.6,21.59,12.5,30.06,13.48,13.1-10.44,13.1-10.44l13.1,2.54s7.71,10.15,16.19,11.17,11,6.34,19.27,3.55,17.73-9.39,20-8.38,17.47-6.6,18.24-6.85,8.74.76,16.44.25,9.51-5.52,9.51-5.52,25.69-1.08,28.78,1.21,3.6,4.31,6.68,4.31,12.33-5.84,22.35-3.55,26,6.34,27.49,7.11,10.28-5.58,10.28-5.58,5.14,4.57,6.42,5.84,6.17.76,9.25,0,3.85-9.14,10-5.08,20.3-5.08,25.44,1S667,64,667,64s6.68-11.42,14.39-9.9S710.16,66.2,710.16,66.2l6.42-5.49,27.24-1.27s-1.28-7.11,6.17-5.33,10-.89,11-1.71,5.14-3.49,9,0,25.44,8.31,32.89,8.31,15.93-6.35,22.61-4.57,13.36-1.52,14.39-2-.77-4.65,6.42-3.47,19-.76,20.58-.25,6.17-.76,11.31-1.78,6.34-11.63,12.25-4.27,8.68,5.28,11.51,6.74,7.45,1.33,9.25,0-1.54-7.93,7.19-5.39,4.17,3.48,10.08,3,14.13,4.06,14.13,4.06,10.79-2,13.11-2.28,9.25-4.57,12.59-2.79,6.17,1.52,9,2.28,10-.51,11.82-1.78,2.57-6.35,7.71-3.55a11.91,11.91,0,0,1,3.14,2.18V0Z" style="opacity:0.66"></path><path d="M0,0V59.17c4.84-3,4.08,1,5.36-.23s0,0,2.57-1.27,3.08.51,7.19,1,2.83,2.54,2.83,2.54,8.74,5.08,10.28,4.57,4.88-9.14,4.37-10.15S40,61.22,40,61.22l4.37,3.3s9.76,2,11,1a59.11,59.11,0,0,1,8-4.57c1.8-.76,4.11,2.28,6.68,2.79s8.74,3.81,8.74,3.81S90.92,60.71,94,60.21s16.7,3.55,17.47,2,11.82-3,13.1-2.79,8.48,10.91,8.48,10.91l30.83-.51s6.68-6.09,7.45-7.61.26-1.78.26-1.78,9-4.57,10-4.57S193.18,61,193.18,61s10.54-4.06,14.39-5.08,6.68,1.52,12.85-2,19.79-2.79,20.56-3.3,7.45-6.34,12.85-9.39,11.31.76,13.1.76,7.71,5.33,10.79,4.57,10.28-5.33,13.62-5.33,2.83,2.28,10.28,1.78,5.91.51,12.59,4.32,8-2.29,12.85-3.55,1.8.25,4.63-.51a19,19,0,0,1,5.65-.76c1.29,0,2.57,1.52,8.74,3.55s5.91-.76,9.25-1.78,8.22-3.3,18.76-10.15,2.57,5.33,6.94,6.85,22.87,3.55,24.41,2,4.37-3,7.2-4.57,2.82-.51,3.59-.51,9,2.54,11.31,4.06,3.85,4.57,6.17,4.57,4.62-2.54,11-3.55-.26,4.82,0,5.58,2.06,0,5.4-.25,4.88-2.79,9.76-2.79,5.14,3.3,8.48,4.06,2.83,0,10.54-3.81,6.68-1.78,14.9-3.55,22.61,6.85,24.41,6.09,4.88-1.27,11.3-2.54,11.56,3,16.19,5.58,5.14-1.52,8.74-5.08,12.08.25,14.9.25,9.25-.25,13.36-.51,5.14-3,13.88-5.08,8,4.57,14.65,3.55,14.13-1.27,28-5.08,6.42,3,10.79,5.58,9,1.78,11.56,1.52S676,39.65,679.84,36.35s9.51,4.31,16.19,8.12,9.25,3.81,14.13,1.78,9.51-4.82,14.9-7.87,5.4,5.84,10,2.79,15.42.76,17.21-.25,8.48.76,15.42-1,1.8,2,7.45,6.85,3.08-2.29,15.42,1,28.52-2.29,32.89-3,4.88,5.33,9.25,5.84,5.4-4.82,9.76-7.87,17-.76,20.56-2a17.22,17.22,0,0,0,6.17-4.06s13.36,0,15.16-1.52,10.28-.76,13.36-.76,26,4.57,35.2,2.79,11.82-7.62,16.44-10.91,23.13,2.54,30.32,2.54,20.3-2.54,20.3-2.54V0Z"></path></svg>';
		break;
		case 'bridge' :
			var svgpath = '<svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 283 20.99" preserveAspectRatio="none"><path d="M81.66,18.4C67.67,6.89,32.57.93,18.57,20.33,14.2,14.75,6.34,11.48,0,9.75V0H0V21H143.75C134.52,9.95,107.42,1.35,81.66,18.4Z" transform="translate(0.02 -0.01)"></path><path d="M283,0V11c-3.82.72-6.67,2.21-13.46,9.21-15.71-21-52.38-5.64-55,.58-12.95-12.92-53.74-17.6-70.74.21H283V0Z" transform="translate(0.02 -0.01)"></path></svg>';
		break;
		case 'rockymoutain' :
			var svgpath = '<svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 1000 90.1" preserveAspectRatio="none"><path d="M999.2,0V89.3c-2.1-.4-3.8.3-5.7.4,0,.1.1.2.1.3l-.3-.1a4.27,4.27,0,0,0-2.5-.1l-.1.1c-3.1-.8-2.6-3.4-8.3-3v-.5c-5.2-.2-10.4-.7-15.7-.5-5-.3-10.3-1.6-15.1-.8-5.6.9-10.9.7-16.4.8a29.31,29.31,0,0,0-7.2-1.2c-6.4-.3-8.2-2-8.6-4.3,0-.5-.2-1.1,1.2-1.5,3.9.1,5.7-1.1,8.4-2.1-4.9-1-9,.5-13.2.1h-2.4c-4.4-.6-.5-2.7-3.7-3.5a40,40,0,0,1-8.4-1.5c.3-2.6-3.8-4.2-8.5-5.5,0-1.1-.4-2.3,3.6-2.5h1.3l4.8-.5c3.3-.4,6.6-.9,9.7-1.5,1.8-.4,4-.6,4.9-1.5a.52.52,0,0,0,.4-.2c.1-.1,0-.2-.4-.3-2.2,0-4.4.1-6.6,0-4.4-.2-8.3,1.4-12.8.5a46.46,46.46,0,0,0-9.7-2l-2.4-1.5c.4-.1.4-.3,0-.5.7-2.2,3.8-3.3,9-3.5a6.54,6.54,0,0,0,3.1-.5c.4-2-2.7-1.1-4.9-1-2.5.8-4.9.2-7.3,0h-1.2c-3.5-2.3,2.3-3,4.6-4.1,4.6-2.2,4.5-2.8-2.1-4v-.5a257.26,257.26,0,0,0-29.1,1l-31.5,3.5a58.57,58.57,0,0,1-14.5,1.5c-1.2.7-3.1.4-4.8.5-1.3.7-3.2.4-4.9.5v.5c-7.2.4-14,1.5-20.7,2.5H779c-1.8-.7-4.2-1.2-5.3-2-5.1-3.7-13.8-5.8-21.2-8.5.1,0,.2-.1.3-.1.3-.3.2-.6-.5-.9-3.2-1.8-7.5-3.2-10.9-5l-1.2-.4a58.37,58.37,0,0,0-17-4.4,16.59,16.59,0,0,1-4.5-1.2c-8.4-2.7-15.5-6.4-27.9-5.9-1.8.1-3.2-.6-5-.8h-.2l-.1-.7a8.16,8.16,0,0,1-3.6-1.5c-8.7-2-18.3-3.1-27.8-4.1l-3.6-1-1.3-1c-5.8-.4-11.6-1.2-17,.5l-10.8.5a8.75,8.75,0,0,0-1.6-.2c-13.2-1.8-25.9-2.1-37.3,2.2-13.8.9-26.7,2.5-36.3,7.1l-2.6,2.5c-5.1,1.4-11.3,1.4-16.9,2-14.1.2-28.4-.3-41.8,2.2-.2,0-.4.1-.6.1-3.5.3-6,0-6-1.8,1.8-.8,1.7-1.7,0-2.5-1.5-2.3-6.3-4-6.2-6.5a16.84,16.84,0,0,1-4.8-2,42.72,42.72,0,0,0-21.8,2.5c-5.5.9-9,3.1-14.5,4l-2.5,1.4-4.8.5a13.86,13.86,0,0,1-5.3,1l-20,1.5a1.71,1.71,0,0,0-1.4,0c-6.9.5-12.2,3-19.9,2.9,3.2-.6,3-2.1,5.4-2.9.6-2.1.6-2.1-1.2-2.5-3.9.2-5.9,2.8-10.9,1.5-.4-.5-.5-1.2-2.4-.9h-.1c-1.3-1.4-2.2-3-7.5-2.2-.8.2-2.3-.5-3.4-.8,1.9-.8,3.3-1.7,4.9-2.5,2.5-.2,4.8-.5,6.1-1.5-.8-1.4-2.9-.7-4.8-.5a63.58,63.58,0,0,1-14.5,1c-1.5-.5-1-1,0-1.5l1.2-.5a11.6,11.6,0,0,0,2.4-2c2.7-.3,6.2.1,7.3-1.5h1.2c.7-.6,2.7-1.1,1.2-2l-6.1-1c-1.6.5-3.2-.7-4.8,0-6.7,1.1-13,2.7-20.6,2.5-.7.3-2,.2-2.6.5-2.4.2-5-.4-7.1.5-1.6,0-2.4.4-2.5,1l-2.3.5a36.43,36.43,0,0,0-13.4,3.5c-3.5.6-5.7,2.1-9.7,2.5-1.3,1.1-4.1,1.3-6.2,1.8-3.3.8-7.5,1.1-9.5,2.7-2.7-.1-3.5,1-4.9,1.6-4,1.8-9.8,3-12.1,5.5h-4.8c-3.4-1-7.3-.3-10.9-.5-1.9-1.3-1.6-3.6-7.9-3-1.5.1-4.2.5-4.7-.5s2.6-1.1,4.1-1.5c1.1-.4,3-.8,3-1.2.2-2.5,4.4-3.9,8.6-5.6-2.2-.3-5.1.3-6.4-.7-1.1-.9,3.7-.3,1.9-1.6-8.8,1.2-15.3,4-22.7,6.1l-3.7,1c-3.4.8-7.6,0-10.9,1-3.4.3-5.4,1.1-6.1,2.5-4.1,1.8-7.8,3.8-12.5,5.3A30.53,30.53,0,0,0,175.5,44c-1.9.4-3.5,1-3.6,2-2.2.6-4.5,1.2-4.8,2.5-3,.6-4,2-6,3-2,.4-3.6,1-3.7,2-3.3,1-4.3,2.4-4.2,4.1.1,2.5-3.7,4.7-9.3,5.5-3.5.5-7.1.9-10.7,1.4-4.5.6-9.4.5-13.6,1.6a8.07,8.07,0,0,1-5.7-.6c-1.6-.9-2.9-2.3-5.8-1.8s-2.7,2.1-2.7,3.3v1c-.1,0-.2.1-.3.2-1.6,1-2.4,2.4-6.6,2.3-3.9-.1-4.9-1.4-6.4-2.5,0-1.1-.4-2.1-3.3-2.8-.1,1.3-1.2,2.4.9,3.3-.1,1.4-3.8,1.9-4.9,3a4,4,0,0,0-2.4.5c-2.9.7-5.2.7-6.2-.9-.7-1.2-2.6-1.9-5.4-2.2-2.4,1.6-.6,3.7-4.1,5.1-6.5,0-4.6,2-4.8,3.4-.3,2.3-1.2,2.6-5.8,1.4-4-1.1-5-.3-5.1,1.2v4.8c-3.7-2.1-7.9-2.6-10.9-3.8l-1.2-2c.2-1.6-1.7-2.7-4.5-3.6-1.6-.5-3.1-.1-2.3.6,1.5,1.3-.5,2.8,2,4v1.5c-.4,2.6-7,4.2-6.1,7-3.6.8-6.8.9-9.1-.9-.7-.5-2.8-1.4-3.5-.4C13.7,89.9,9.1,89,5,89L1.3,85.5A2.07,2.07,0,0,0,0,84.4v5.7H1000V0Z" transform="translate(0 0)"></path></svg>';
		break;
		case 'singlewave' :
			var svgpath = '<svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 1000 99" preserveAspectRatio="none"><path d="M768.06,59.54C687,48.21,607.41,28.42,526.35,17.15,347.45-7.73,155.24,13.87.07,99H1000V68.11A1149.19,1149.19,0,0,1,768.06,59.54Z"></path><rect width="1000" height="0.04"></rect></svg>';
		break;
		case 'multislope' :
			var svgpath = '<svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 1000 100" preserveAspectRatio="none"><path d="M0,22.3V0H1000V100Z" transform="translate(0 0)" style="opacity:0.66"></path><path d="M0,6V0H1000V100Z" transform="translate(0 0)"></path></svg>';
		break;
		case 'slope' :
			var svgpath = '<svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 1000 94" preserveAspectRatio="none"><polygon points="0 94 1000 94 0 0 0 94"></polygon></svg>';
		break;
		case 'waves3' :
			var svgpath = '<svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 1000 84.94" preserveAspectRatio="none"><path d="M0,0V72.94c14.46,5.89,32.38,10.5,54.52.26,110.25-51,120.51,23.71,192.6-4.3,144.73-56.23,154.37,49.44,246.71,4.64C637,4.05,622.19,124.16,757.29,66.21c93-39.91,108.38,54.92,242.71-8.25V0Z" style="fill-rule:evenodd;opacity:0.33"></path><path d="M0,0V52.83c131.11,59.9,147-32.91,239.24,6.65,135.09,58,120.24-62.16,263.46,7.34,92.33,44.8,102-60.88,246.71-4.64,72.1,28,82.35-46.71,192.6,4.3,23.95,11.08,43,4.78,58-1.72V0Z" style="fill-rule:evenodd;opacity:0.66"></path><path d="M0,0V24.26c15.6,6.95,35.77,15.41,61.78,3.38,110.25-51,120.51,23.71,192.6-4.3C399.11-32.89,408.75,72.79,501.08,28,644.3-41.51,629.45,78.6,764.54,20.65,855.87-18.53,872.34,72.12,1000,15.7V0Z" style="fill-rule:evenodd"></path></svg>';
		break;
		case 'drip' :
			var svgpath = '<svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 1000 91.02" preserveAspectRatio="none"><path d="M772,11c-32.4,4-58,9.51-58,9.51C685.3,26.69,659.67,34.32,658,35c-15.34,6.3-25.24,13.11-43,13-27.54-.18-37.37-16.79-56-11-19,5.91-19.53,26.54-35,27-13.47.4-16.5-15.14-36-18-1.32-.19-15.92-2.13-29,6-20.34,12.64-18.82,38.28-28,39-8.62.68-10.8-21.86-26-40-5.44-6.49-24.19-25.34-100-32a429.73,429.73,0,0,0-94,2C165,26.91,96.11,27.3,0,0V91H1000V0C894.78,1.07,813.3,5.92,772,11Z" transform="translate(0 0)"></path></svg>';
		break;
		case 'asymslope' :
			var svgpath = '<svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 1000 89" preserveAspectRatio="none"><polygon points="0 89 741 89 0 0 0 89"></polygon><polygon points="741 89 1000 89 1000 0 741 89"></polygon></svg>';
		break;
		case 'vslope' :
			var svgpath = '<svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 1000 89" preserveAspectRatio="none" style="width: 100%; max-width: 100%;"><polygon points="0 89 500 89 0 0 0 89"></polygon><polygon points="500 89 1000 89 1000 0 500 89"></polygon></svg>';
		break;
		case 'multivslope' :
			var svgpath = '<svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 1000 89" preserveAspectRatio="none" style="width: 100%; max-width: 100%;"><polygon points="0 89 500 89 0 20 0 89"></polygon><polygon points="500 89 1000 89 1000 20 500 89"></polygon><polygon style="opacity: 0.6;" points="0 20 500 89 0 0 0 89"></polygon><polygon style="opacity: 0.6;" points="500 89 1000 20 1000 0 500 89"></polygon></svg>';
		break;
		case 'multiv3slope' :
			var svgpath = '<svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 1000 89" preserveAspectRatio="none" ><polygon points="0 89 500 89 0 40 0 89"></polygon><polygon points="500 89 1000 89 1000 40 500 89"></polygon><polygon style="opacity: 0.6;" points="0 40 500 89 0 20 0 69"></polygon><polygon style="opacity: 0.6;" points="500 89 1000 20 1000 40 500 89"></polygon><polygon style="opacity: 0.3;" points="0 20 500 89 0 0 0 89" ></polygon><polygon style="opacity: 0.3;" points="500 89 1000 20 1000 0 500 89"></polygon></svg>';
		break;
		case 'triangle' :
			var svgpath = '<svg xmlns="https://www.w3.org/2000/svg" preserveAspectRatio="none" viewBox="0 0 1000 100"><polygon points="0 100 1000 100 1000 50 550 50 500 0 450 50 0 50 0 100"></polygon></svg>';
		break;
		case 'trianglesmall' :
			var svgpath = '<svg xmlns="https://www.w3.org/2000/svg" preserveAspectRatio="none" viewBox="0 0 1000 50"><polygon points="0 50 1000 50 1000 25 520 25 500 0 480 25 0 25 0 50"></polygon></svg>';
		break;
		case 'triangle3' :
			var svgpath = '<svg xmlns="https://www.w3.org/2000/svg" preserveAspectRatio="none" viewBox="0 0 1000 50"><polygon points="0 50 1000 50 1000 25 560 25 540 0 520 25 500 0 480 25 460 0 440 25 0 25 0 50"></polygon></svg>';
		break;
		case 'ellipse' :
			var svgpath = '<svg xmlns="https://www.w3.org/2000/svg" preserveAspectRatio="none" viewBox="0 0 1000 50"><path d="M0 50 C 200 0 500 0 1000 50 Z"></path></svg>';
		break;
		case '0' :
		default :
			if (prefix == 'divider') focus.find('.pbck-divider1-container').remove();
			if (prefix == 'divider-2') focus.find('.pbck-divider2-container').remove();
			return;
		break;
	}

	// remove all divider for B/C
	focus.find('.pbck-divider-container:not(.pbck-divider2-container):not(.pbck-divider1-container)').remove();

	if (prefix == 'divider') {
		focus.find('.pbck-divider1-container').remove();
		// focus.find('.pbck-divider-container').remove();
		if (! focus.find('.pbck-divider1-container').length) focus.prepend('<div class="pbck-divider-container pbck-divider1-container">' + svgpath + '</div>');

		var divider = focus.find('.pbck-divider1-container');

		// position
		if ($ck('#' + prefix + 'position').val() == 'top') {
			divider.removeClass('pbck-divider-bottom').addClass('pbck-divider-top');
		} else {
			divider.removeClass('pbck-divider-top').addClass('pbck-divider-bottom');
		}
		// placement
		if ($ck('#' + prefix + 'placement').val() == 'over') {
			divider.removeClass('pbck-divider-under').addClass('pbck-divider-over');
		} else {
			divider.removeClass('pbck-divider-over').addClass('pbck-divider-under');
		}

		// flip
		if ($ck('#' + prefix + 'fliphorizontal').val() == '1') {
			divider.addClass('ckflip-horizontal');
		} else {
			divider.removeClass('ckflip-horizontal');
		}
		if ($ck('#' + prefix + 'flipvertical').val() == '1') {
			divider.addClass('ckflip-vertical');
		} else {
			divider.removeClass('ckflip-vertical');
		}

		divider.find('path, polygon').attr('fill', $ck('#' + prefix + 'color').val());
		divider.css('background-color', $ck('#' + prefix + 'bgcolor').val());
		divider.css('height', ckTestUnit($ck('#' + prefix + 'height').val()));
		divider.find('svg').css('width', ckTestUnit($ck('#' + prefix + 'width').val()));
		divider.find('svg').css('max-width', ckTestUnit($ck('#' + prefix + 'width').val()));

	} else if (prefix == 'divider-2') {
		focus.find('.pbck-divider2-container').remove();
		// focus.find('.pbck-divider-container').remove();
		
		if ($ck('#divider-2shape').val()) {
			if (! focus.find('.pbck-divider2-container').length) focus.prepend('<div class="pbck-divider-container pbck-divider2-container">' + svgpath + '</div>');
		} else {
			return;
		}
		var divider2 = focus.find('.pbck-divider2-container');

		// position
		if ($ck('#' + prefix + 'position').val() == 'top') {
			divider2.removeClass('pbck-divider-bottom').addClass('pbck-divider-top');
		} else {
			divider2.removeClass('pbck-divider-top').addClass('pbck-divider-bottom');
		}
		// placement
		if ($ck('#' + prefix + 'placement').val() == 'over') {
			divider2.removeClass('pbck-divider-under').addClass('pbck-divider-over');
		} else {
			divider2.removeClass('pbck-divider-over').addClass('pbck-divider-under');
		}

		// flip
		if ($ck('#' + prefix + 'fliphorizontal').val() == '1') {
			divider2.addClass('ckflip-horizontal');
		} else {
			divider2.removeClass('ckflip-horizontal');
		}
		if ($ck('#' + prefix + 'flipvertical').val() == '1') {
			divider2.addClass('ckflip-vertical');
		} else {
			divider2.removeClass('ckflip-vertical');
		}

		divider2.find('path, polygon').attr('fill', $ck('#' + prefix + 'color').val());
		divider2.css('background-color', $ck('#' + prefix + 'bgcolor').val());
		divider2.css('height', ckTestUnit($ck('#' + prefix + 'height').val()));
		divider2.find('svg').css('width', ckTestUnit($ck('#' + prefix + 'width').val()));
		divider2.find('svg').css('max-width', ckTestUnit($ck('#' + prefix + 'width').val()));

	}
}

function ckShowResponsiveCssEdition(blocid) {
	blocid = '#' + blocid;
	bloc = $ck(blocid);
	$ck('.editfocus').removeClass('editfocus');
	bloc.addClass('editfocus');
	var focus = $ck('.editfocus');
	$ck('#popup_editionck').empty().fadeIn().addClass('ckwait');
	var range = ckGetResponsiveRange();

	var ckprops = $ck('> .tab_blocstyles', focus);
	var fields = new Object();
	fieldslist = ckprops.attr('fieldslist') ? ckprops.attr('fieldslist').split(',') : Array();
	for (j=0;j<fieldslist.length;j++) {
		fieldname = fieldslist[j];
		fields[fieldname] = ckprops.attr(fieldname);

	}
	fields = JSON.stringify(fields);

	var myurl = PAGEBUILDERCK.URIPBCK + "&task=interface.load&layout=responsivecssedition&" + PAGEBUILDERCK.TOKEN;
	$ck.ajax({
		type: "POST",
		url: myurl,
		data: {
			ckobjid: bloc.prop('id'),
			fields: fields,
			responsiverange: range
		}
	}).done(function(code) {
		$ck('#popup_editionck').append(code).removeClass('ckwait');
		$ck('#ckwaitoverlay').remove();
		ckFillEditionPopup(blocid, $ck('.workspaceck'), range);
		ckAddEventOnResponsiveFields($ck('#popup_editionck'), blocid);
		ckMakeTooltip($ck('#popup_editionck'));
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

function ckAddEventOnResponsiveFields(editionarea, blocid) {
	$ck('.inputbox:not(.colorPicker)', editionarea).change(function() {
		ckRenderResponsiveCss();
	});
	$ck('.colorPicker,.inputbox[type=radio]', editionarea).blur(function() {
		ckRenderResponsiveCss();
	});
}

function ckRenderResponsiveCss() {
	ckAddSpinnerIcon($ck('.headerckicon.cksave'));
	var editionarea = document.body;
	var focus = $ck('.editfocus');
	var blocid = focus.attr('id');
	var fieldslist = new Array();
	var fields = new Object();
	var rangeNumber = ckGetResponsiveRangeNumber();
	$ck('.inputbox', editionarea).each(function(i, el) {
		el = $ck(el);
		fields[el.attr('name')] = el.val();
		if (el.attr('type') == 'radio') {
			fields[el.attr('name')] = $ck('[name="' + el.attr('name') + '"]:checked').val();
			if (el.prop('checked')) {
				fields[el.attr('id')] = 'checked';
			} else {
				fields[el.attr('id')] = '';
			}
		}
	});

	$ck('> .ckprops.ckresponsiverange' + rangeNumber, focus).each(function(i, ckprops) {
		ckprops = $ck(ckprops);
		fieldslist = ckprops.attr('fieldslist') ? ckprops.attr('fieldslist').split(',') : Array();
		// fieldslist.each(function(fieldname) {
		// for (var fieldname of fieldslist) {
		for (j=0;j<fieldslist.length;j++) {
			fieldname = fieldslist[j];
			if (typeof(fields[fieldname]) == 'null') 
				fields[fieldname] = ckprops.attr(fieldname);
		// });
		}
	});
	fields = JSON.stringify(fields);
	var customstyles = new Object();
	$ck('.menustylescustom').each(function() {
		$this = $ck(this);
		customstyles[$this.attr('data-prefix')] = $this.attr('data-rule');
	});
	customstyles = JSON.stringify(customstyles);
	ckSaveResponsiveEdition(); // save fields before ajax to keep sequential/logical steps
	var myurl = PAGEBUILDERCK.URIPBCK + "&task=interface.load&layout=renderresponsivecss&" + PAGEBUILDERCK.TOKEN;
	$ck.ajax({
		type: "POST",
		url: myurl,
		data: {
			objclass: focus.prop('class'),
			ckobjid: blocid,
			responsiverange: rangeNumber,
			customstyles: customstyles,
			fields: fields
		}
	}).done(function(code) {
		if (! $ck('> .ckstyleresponsive.ckresponsiverange' + rangeNumber, $ck('.workspaceck #' + blocid)).length) {
			$ck('.workspaceck #' + blocid).append('<div class="ckstyleresponsive ckresponsiverange' + rangeNumber + '"></div>')
		}
		$ck('> .ckstyleresponsive.ckresponsiverange' + rangeNumber, $ck('.workspaceck #' + blocid)).empty().append(code);
		ckOrderStylesResponsive(blocid);
		ckRemoveSpinnerIcon($ck('.headerckicon.cksave'));
		ckSaveAction();
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

function ckOrderStylesResponsive(blocid) {
	for (var i = 4; i > 0; i--) {
		$ck('.workspaceck #' + blocid).append($ck('.ckstyleresponsive.ckresponsiverange' + i, $ck('.workspaceck #' + blocid)));
	}
}

function ckSaveResponsiveEdition() {
	var focus = $ck('.editfocus');
	var rangeNumber = ckGetResponsiveRangeNumber();
	var editionarea = $ck('#popup_editionck');
	$ck('> .ckprops.ckresponsiverange' + rangeNumber, focus).remove();
	$ck('.ckproperty', editionarea).each(function(i, tab) {
		tab = $ck(tab);
		tabid = tab.attr('id');
		(! $ck('> .' + tabid + '_ckresponsiverange' + rangeNumber, focus).length) ? focus.prepend('<div class="' + tabid + '_ckresponsiverange' + rangeNumber + ' ckprops ckresponsive ckresponsive' + rangeNumber + '" />') : $ck('> .' + tabid + '_ckresponsiverange' + rangeNumber, focus).empty();
		focusprop = $ck('> .' + tabid + '_ckresponsiverange' + rangeNumber, focus);
		ckSavePopupfields(focusprop, tabid);
		fieldslist = ckGetPopupFieldslist(focus, tabid);
		focusprop.attr('fieldslist', fieldslist);
	});
}

function ckSearchAddon() {
	var s = $ck('#ckaddonsearch input').val().toLowerCase();
	$ck('#menuck .menuitemck').each(function() {
		var addon = $ck(this);
		if (addon.attr('data-type').toLowerCase().indexOf(s) != -1
			|| addon.find('.menuitemck_title').text().toLowerCase().indexOf(s) != -1 ) {
			addon.show();
		} else {
			addon.hide();
		}
	});
}

function ckSearchAddonClear() {
	$ck('#menuck .menuitemck').show();
	$ck('#ckaddonsearch input').val('');
}

function ckActivatePanel(target) {
	switch (target) {
		case 'responsive':
			ckShowResponsiveSettings();
		break;
		case 'addons':
		default:
			ckShowResponsiveSettings(1);
		break;
	}

	$ck('.menuckpanel').removeClass('active');
	$ck('.menuckpanel[data-target="' + target + '"]').addClass('active');
	$ck('.menuckpaneltarget').hide();
	$ck('.menuckpaneltarget[data-target="' + target + '"]').show();
}

function ckOpenCustomCssEditor() {
	// article, page, module, element, library
	var customcss = $ck('.ckcustomcssfield', $ck('.workspaceck')).length ? $ck('.ckcustomcssfield', $ck('.workspaceck')).html() : '';
	var myurl = PAGEBUILDERCK.URIPBCK + "&task=interface.load&layout=customcss&" + PAGEBUILDERCK.TOKEN;
	$ck.ajax({
		type: "POST",
		url: myurl,
		data: {
			customcss : customcss
		}
	}).done(function(code) {
		$ck('#ckcustomcssedition').empty().append(code);
		CKBox.open({handler: 'inline', content: 'ckcustomcssedition', style: {padding: '10px'},
			footerHtml: '<a class="ckboxmodal-button" href="javascript:void(0)" onclick="ckSaveCustomCss()">' + Joomla.JText._('CK_SAVE_CLOSE') + '</a>'
		});
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

function ckSaveCustomCss() {
	if (!$ck('#ckcustomcsseditor').length) return;
	ckcustomcsseditor.save(); // save the editor to the textarea
	var customcss = $ck('#ckcustomcsseditor').val();
	if (! $ck('.ckcustomcssfield', $ck('.workspaceck')).length) {
		$ck('.workspaceck').prepend('<div class="ckcustomcssfield" style="display: none;"></div>');
	}
	$ck('.ckcustomcssfield', $ck('.workspaceck')).text(customcss);
	CKBox.close();
}

function ckSetAddonsDisplaytypeState(type) {
	$ck('.ckaddonsdisplaytype').hide();
	$ck('#ckaddonsdisplaytype' + type).show();
	$ck('.headerckdisplaytype').removeClass('active');
	$ck('.headerckdisplaytype[data-type="' + type + '"]').addClass('active');
	ckSetUserState('pagebuilderck.addons.displaytype', type)
}

function ckSetUserState(key, value) {
	var myurl = PAGEBUILDERCK_ADMIN_URL + "&task=ajaxSetUserState&" + PAGEBUILDERCK.TOKEN;
	$ck.ajax({
		type: "POST",
		url: myurl,
		data: {
			key : key,
			value : value
		}
	}).done(function(code) {
		
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

function ckAddDataOnImages() {
	$ck('.imageck').each(function() {
		var imgwrap = $ck(this);
		ckAddDataOnImage(imgwrap);
	});
}

/* imgwrap must be the wrapper .imageck */
function ckAddDataOnImage(imgwrap) {
		var img = imgwrap.find('img');
		if (! imgwrap.find('.ckimagedata').length) {
			imgwrap.append('<div class="ckimagedata"></div>');
		}
		var imgdata = imgwrap.find('.ckimagedata');
		var imglink = imgwrap.find('> a').length ? imgwrap.find('> a') : false;
		var imgtitle = imgwrap.find('img').attr('title');
		if (imgtitle) {
			if (! imgdata.find('.ckimagedata-title').length) {
				imgdata.append('<div class="ckimagedata-title cktip" title="' + imgtitle + '"><span class="fa fa-font"></span></div>');
			} else {
				imgdata.find('.ckimagedata-title').attr('title', imgtitle);
			}
		} else {
			imgdata.find('.ckimagedata-title').remove();
		}
		if (imglink) {
			if (! imgdata.find('.ckimagedata-link').length) {
				imgdata.append('<div class="ckimagedata-link cktip" title="' + imglink.attr('href') + '"><span class="fa fa-link"></span></div>');
			} else {
				imgdata.find('.ckimagedata-link').attr('title', imglink.attr('href'));
			}
			
		} else {
			imgdata.find('.ckimagedata-link').remove();
		}
		ckMakeTooltip(imgdata);
}

function ckApplyParallax() {
	var doParallax = $ck('#elementscontainer #rowbgparallax').val();
	var focus = $ck('.editfocus');
	if (doParallax == '1') {
		var speed = $ck('#elementscontainer #rowbgparallaxspeed').val();
		speed = speed ? speed : '50';
		focus.attr('data-parallax', speed);
	} else {
		focus.removeAttr('data-parallax');
	}
}

function ckApplyLinkWrap() {
	var link = $ck('#elementscontainer #wraplinkurl').val();
	var linktext = $ck('#elementscontainer #wraplinktext').val();
	var linkicon = $ck('#elementscontainer [name="wraplinkicon"]:checked').val();
	var dataAttrs = linktext ? ' data-custom-text="1"' : '';
	dataAttrs += linkicon == '1' ? ' data-link-icon="1"' : '';

	linktext = linktext ? linktext : 'Link to ' + link;
	var focus = $ck('.editfocus');
	focus.removeClass('pbck-has-link-wrap');
	focus.find('> .inner a.pbck-link-wrap').remove();
	if (link) {
		focus.addClass('pbck-has-link-wrap');
		focus.find('> .inner').prepend('<a href="' + link + '" class="pbck-link-wrap"' + dataAttrs + '>' + linktext + '</a>');
		ckAddFakeLinkEvent();
	}
}

function ckMakePagesDraggable(iframe) {
	var frame = $ck(iframe);
	var framehtml = frame.contents();
}

function ckStartDragPage(row) {

}

function ckShowAclEdition(blocid) {
	blocid = '#' + blocid;
	bloc = $ck(blocid);
	$ck('.editfocus').removeClass('editfocus');
	bloc.addClass('editfocus');
	$ck('#popup_editionck').empty().fadeIn().addClass('ckwait');
	var acl = bloc.attr('data-acl-edit') ? bloc.attr('data-acl-edit') : '';

	var myurl = PAGEBUILDERCK.URIPBCK + "&task=interface.load&layout=setacl&" + PAGEBUILDERCK.TOKEN;
	$ck.ajax({
		type: "POST",
		url: myurl,
		data: {
			ckobjid: bloc.prop('id'),
			acl: acl
		}
	}).done(function(code) {
		$ck('#popup_editionck').append(code).removeClass('ckwait');
		$ck('#ckwaitoverlay').remove();
		ckMakeTooltip($ck('#popup_editionck'));
		// set ACL settings
		var aclview = bloc.attr('data-acl-view');
		var acledit = bloc.attr('data-acl-edit');
		if (aclview) {
			aclview = aclview.split(',');
			for (i=0; i<aclview.length; i++) {
				$ck('#popup_editionck').find('.ckaclrow[data-group="' + aclview[i] + '"]').find('.ckaclfieldview').removeAttr('checked');
			}
		}
		if (acledit) {
			acledit = acledit.split(',');
			for (i=0; i<acledit.length; i++) {
				$ck('#popup_editionck').find('.ckaclrow[data-group="' + acledit[i] + '"]').find('.ckaclfieldedit').removeAttr('checked');
			}
		}
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
		$ck('#ckwaitoverlay').remove();
	});
}

function ckSetAcl() {
	ckAddSpinnerIcon($ck('.headerckicon.cksave'));
	var aclview = new Array();
	var acledit = new Array();
	$ck('#popup_editionck').find('.ckaclrow').each(function() {
		$this = $ck(this);
		var groupid = $this.attr('data-group');
		var viewauth = $this.find('.ckaclfieldview:checked').length;
		var editauth = $this.find('.ckaclfieldedit:checked').length;
		if (!viewauth) aclview.push(groupid);
		if (!editauth) acledit.push(groupid);
	});
	var focus = $ck('.editfocus');
	aclview = aclview.join(',');
	acledit = acledit.join(',');
	if (aclview) {
		focus.attr('data-acl-view', aclview);
	} else {
		focus.removeAttr('data-acl-view');
	}
	if (acledit) {
		focus.attr('data-acl-edit', acledit);
	} else {
		focus.removeAttr('data-acl-edit');
	}
	ckRemoveSpinnerIcon($ck('.headerckicon.cksave'));
}

function ckCheckUserRightsFromAcl(restrictedGroups) {
	userGroups = PAGEBUILDERCK.USERGROUPS.split(',');
	// special check to allow super users in any case
	if (userGroups.includes('8')) return true;

	restrictedGroups = restrictedGroups.split(',');

	for (var i=0; i<restrictedGroups.length; i++) {
		if (userGroups.includes(restrictedGroups[i])) {
			return false;
		}
	}
	return true;
}

/*-------------------------------
 * ---		Copy paste styles ---
 --------------------------------*/

function ckCopyStyles(blocid) {
	var item = ckGetObjectAnyway(blocid);

	PAGEBUILDERCK.CLIPBOARD = {"ID" : item.attr('id'), "PROPS" : item.find('> .ckprops').clone(), "STYLE" : item.find('> .ckstyle').html()};

//	alert(TCK.TexJoomla.JText._('CK_COPYTOCLIPBOARD', 'Current styles copied to clipboard !'));
}

function ckPasteStyles(blocid) {
	var item = ckGetObjectAnyway(blocid);

	if (PAGEBUILDERCK.CLIPBOARD) {
		if (!confirm(Joomla.JText._('CK_COPYFROMCLIPBOARD', 'Apply styles from Clipboard ? This will replace all current existing styles.')))
			return;
		item.find('> .ckprops').remove();
		item.prepend(PAGEBUILDERCK.CLIPBOARD.PROPS);
		item.find('> .ckstyle').empty().append(PAGEBUILDERCK.CLIPBOARD.STYLE);
		var re = new RegExp(PAGEBUILDERCK.CLIPBOARD.ID, 'g');
		if (item.find('> .ckstyle').length) item.find('> .ckstyle').html(item.find('> .ckstyle').html().replace(re,item.attr('id')));
	} else {
		alert(Joomla.JText._('CK_CLIPBOARDEMPTY', 'Clipboard is empty'));
	}
}


function ckSwitchResponsiveSmart(responsiverange, force) {
	if (! responsiverange) responsiverange = ckGetResponsiveRangeNumber();
	if (! force) force = false;
	var button = $ck('.ckresponsivebutton[data-range="' + responsiverange + '"]');
	var focus = $ck('.editfocus');
	var blocid = focus.attr('id');

	// do nothing if click on the active button
	if (button.hasClass('active')) return;
	if (button.hasClass('active') && !force) {
		ckRemoveWorkspaceWidth();
	} else {
		$ck('.ckresponsivebutton').removeClass('active').removeClass('ckbutton-warning');
		// $ck('#popup_editionck .ckresponsivebutton').removeClass('active').removeClass('ckbutton-warning');
		button.addClass('active').addClass('ckbutton-warning');
		ckSetWorkspaceWidth(responsiverange);
	}

	var editionarea = $ck('#popup_editionck');
	$ck('#popup_editionck input.ckresponsivable:not([type="radio"])').val('');
	$ck('#popup_editionck textarea.ckresponsivable').val('');
	$ck('#popup_editionck [type="radio"].ckresponsivable').removeProp('checked');

	if (responsiverange == '5' || ! responsiverange) {
		ckRemoveWorkspaceWidth();
		ckFillEditionPopup(blocid, $ck('.workspaceck'));
		ckAddEventOnFields(editionarea, blocid);
	} else {
		// update the css responsive values in the panel
		ckFillEditionPopup($ck('.editfocus').attr('id'), $ck('.workspaceck'), responsiverange);
		ckAddEventOnFields(editionarea, blocid);
	}
}

function ckSaveEditionPanel(close) {
	if (! close) close = false;
	var rangeNumber = ckGetResponsiveRangeNumber();

	if (! rangeNumber || rangeNumber == '5') {
		if (close) {
			ckGetPreviewAreastylescss(false, false, false, false, false, true);
			$ck('.menuckpanel[data-target="addons"]').trigger('click');
		} else {
			ckGetPreviewAreastylescss();
		}
	} else {
		ckRenderResponsiveCss();
		ckBeforeSaveEditionPopup();
		if (close) {
			ckCloseEdition();
			ckRemoveWorkspaceWidth();
			$ck('.menuckpanel[data-target="addons"]').trigger('click');
		}
	}
}