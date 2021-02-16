jQuery(function($){

	jQuery("#" + window.quixEditorID).parent().before('<p id="quix-switch-mode" style="display:inline-block;"><button id="quix-switch-mode-button" type="button" class="btn btn-primary"><span class="quix-switch-mode-on" style="display:none;">← Back to Joomla! Editor</span><span class="quix-switch-mode-off"><i class="icon-eye" aria-hidden="true"></i>Edit with Quix</span></button></p>');
	jQuery("#" + window.quixEditorID).parent().before('<div id="quix-editor" style="display:none;"><a id="quix-go-to-edit-page-link" href="#"><div id="quix-editor-button" class="btn btn-primary btn-large btn-hero"><i class="icon-eye" aria-hidden="true"></i>Edit with Quix</div></a></div>');

	if( typeof window.builtWithQuixEditor == 'boolean' && window.builtWithQuixEditor == true)
	{
		jQuery('body').addClass('quix-editor-active');
		jQuery('.quix-switch-mode-on').show();
		jQuery('.quix-switch-mode-off').hide();
		jQuery('#quix-editor').show();
		jQuery('#' + window.quixEditorID).parent().hide();
		jQuery('#jform_attribs_article_layout').val('quix_canvas:quix');
	}

	jQuery('#quix-switch-mode').on('click', function(e){
		e.preventDefault();
		if(jQuery('body').hasClass('quix-editor-active')){
			if(confirm('You are switching from Powerful content builder. Are you sure?')){
				jQuery('body').removeClass('quix-editor-active');
				jQuery('.quix-switch-mode-on').hide();
				jQuery('.quix-switch-mode-off').show();
				jQuery('#quix-editor').hide();
				jQuery('#' + window.quixEditorID).parent().show();
				jQuery('#jform_attribs_article_layout').val('');

				jQuery.ajax({
					type: 'post',
					url: 'index.php?option=com_quix&task=get.disableEditor',
					data: {'quixEditorMapID': window.quixEditorMapID}
				});

			}
		}
		else
		{
			jQuery('body').addClass('quix-editor-active');
			jQuery('.quix-switch-mode-on').show();
			jQuery('.quix-switch-mode-off').hide();
			jQuery('#quix-editor').show();
			jQuery('#' + window.quixEditorID).parent().hide();
			jQuery('#jform_attribs_article_layout').val('quix_canvas:quix');

			jQuery.ajax({
				type: 'post',
				url: 'index.php?option=com_quix&task=get.enableEditor',
				data: {'quixEditorMapID': window.quixEditorMapID}
			});

		}
	});
	
	jQuery('#quix-editor-button').on('click', function(e){
		e.preventDefault();
		if(window.quixEditorItemID == 0)
		{
			alert('Please save your item first!');
			return;
		}

		window.open(window.quixEditorUrl, '_blank');

	});

	jSelectQuixShortcode = function(tag)
	{
		jInsertEditorText(tag, window.quixEditorID);
		SqueezeBox.close();
	}

});
