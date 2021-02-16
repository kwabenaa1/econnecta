/**
 * @copyright	Copyright (C) 2017 Cedric KEIFLIN alias ced1870
 * https://www.joomlack.fr
 * @license		GNU/GPL
 * */

/* Call the editor using Javascript. It needs an instance of a sample editor called "ckeditor" to run
 * the ckeditor instance shall be called in PHP using JEditor->display
 */
function ckLoadEditorOnTheFly(id) {
//	try {
		var oldid = id;
		var textArea = document.getElementById(id);
		var now = new Date().getTime();
		id = id + parseInt(now, 10);
		textArea.id = id;
		textArea.setAttribute('data-id', oldid);

var editorModals = document.querySelectorAll('.modal[id^="ckeditor_"][id$="Modal"]');
console.log(editorModals);
editorModals.forEach(function(modal) {
	var newModal = modal.cloneNode(true);
	console.log(newModal.attributes.length);
	for (var i=0; i<newModal.attributes.length; i++) {
		console.log(newModal.attributes[i].name);
		console.log(newModal.attributes[i].nodeValue);
		var name = newModal.attributes[i].name;
		var value = newModal.attributes[i].nodeValue.replace(/ckeditor/g, id);
		newModal.setAttribute(name, value);
		document.body.appendChild(newModal);
		Joomla.Bootstrap.initModal(newModal);
//		jQuery(newModal).modal();
	}
//	newModal.attributes.forEach(function(attrib) {
//		console.log(attrib);
//	});
});

		var pluginOptions;
		if (!Joomla.optionsStorage.plg_editor_tinymce) {
console.log('test');
			var elements = document.querySelectorAll('.joomla-script-options'),
				str, element, option, counter = 0;

			for (var i = 0, l = elements.length; i < l; i++) {
				element = elements[i];
				str     = element.text || element.textContent;
				str = str.replace(/ckeditor/g, id);
				option  = JSON.parse(str);
				if (option.plg_editor_tinymce) {
					pluginOptions = option.plg_editor_tinymce || {};
				}
			}
		} else {
	console.log('test2');
			pluginOptions = Joomla.optionsStorage.plg_editor_tinymce;
tinyMCEOptions = pluginOptions.tinyMCE;
//	console.log(tinyMCEOptions['ckeditor'].joomlaExtButtons);
//	console.log(tinyMCEOptions[id].joomlaExtButtons);
	
			str = JSON.stringify(pluginOptions);
			str = str.replace(/ckeditor/g, id);
			pluginOptions = JSON.parse(str);
console.log(pluginOptions);
console.log(tinyMCEOptions);
console.log(id);
console.log(tinyMCEOptions[id]);
//			tinyMCEOptions[id].joomlaExtButtons.names.forEach(function (btn) {
//				btn.id = btn.id.replace(id, 'ckeditor');
//			});
//console.log(pluginOptions);
		}

		const currentEditor = document.getElementById(id);
		if (! currentEditor.name) {
			currentEditor.setAttribute('name', currentEditor.id);
			currentEditor.setAttribute('data-name', currentEditor.name);
		} else {
			currentEditor.setAttribute('data-name', currentEditor.name);
			currentEditor.setAttribute('name', currentEditor.id);
		}
//		const toggleButton = currentEditor.querySelector('.js-tiny-toggler-button'); // Setup the editor

		Joomla.JoomlaTinyMCE.setupEditor(currentEditor, pluginOptions); // Setup the toggle button

//        if (toggleButton) {
//          toggleButton.removeAttribute('disabled');
//          toggleButton.addEventListener('click', () => {
//            if (Joomla.editors.instances[currentEditor.id].instance.isHidden()) {
//              Joomla.editors.instances[currentEditor.id].instance.show();
//            } else {
//              Joomla.editors.instances[currentEditor.id].instance.hide();
//            }
//          });
//        }
//		textArea.id = oldid;
//	}
//	catch(err) {
//		alert(err);
//	}
//currentEditor.setAttribute('name', currentEditor.getAttribute('name'));
console.log(Joomla.editors.instances);
}

/* save the content of the editor into the textarea */
function ckSaveEditorOnTheFly(id) {
	console.log(id);
	var textArea = document.querySelector('[data-id="' + id + '"]');
	if (! textArea)
		textArea = document.querySelector('[id="' + id + '"]');
	console.log(textArea);
//	var textArea = document.getElementById(id);
	try {
		var editor = tinymce.get(textArea.id);
		editor.save();
	} 
	catch(err) {
		alert('Error saving one of the editors');
	}
}

/* save the content of the editor into the textarea */
function ckRemoveEditorOnTheFly(id) {
	
//	var editor = document.querySelector('[data-id="' + id + '"]');
//	console.log(editor);
// faire suppression des modales 
	try {
		var textArea = document.querySelector('[data-id="' + id + '"]');
		if (! textArea)
			textArea = document.querySelector('[id="' + id + '"]');
		console.log(textArea);
		if (! textArea) return;

		tinymce.execCommand('mceRemoveEditor', false, textArea.id);

		var editorModals = document.querySelectorAll('.modal[id^="' + textArea.id + '_"][id$="Modal"]');
		console.log(editorModals);
		for (var i=0; i<editorModals.length; i++) {
			editorModals[i].remove();
		}
	}
	catch(err) {
		alert('Error removing one of the editors');
	}
}