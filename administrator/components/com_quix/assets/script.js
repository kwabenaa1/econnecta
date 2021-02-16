(function($){

  	$(document).ready(function(){
  		jQuery('form#adminForm,form#message-form').fadeIn(50);

		function saveAjaxIntegration(item){
  			$('input[name=task]').val('integrations.update');
		    // console.log($(this).is(':checked'));

			var value   = $('#adminForm').serializeArray();
			// console.log(value);
			$.ajax({
				type   : 'POST',
				data   : value,
				beforeSend: function(){
		          	item.parent().parent().parent().addClass('disabled');
				    item.attr('disabled', true);
		        },
				success: function (res) {
					var response = JSON.parse(res);
					if(!response.success){
						console.log(response.data);
					}
					item.parent().parent().parent().removeClass('disabled');
				    item.attr('disabled', false);
					item.parent().parent().parent().find('.success-message').fadeIn('fast').delay(1000).fadeOut('fast');
				}
			});
  		}

		$('.toggleIntegration').change(function() {
			var item = $(this);
			saveAjaxIntegration(item);
	 	});

	 	$('#customIntegrationSave').on('click', function(e) {
	 		e.preventDefault();
			var item = $(this);
			saveAjaxIntegration(item);
		 });

		// new template modal
		$('#collection-window-modal form').on('submit', function(e){
			e.preventDefault();
			if(!document.formvalidator.isValid(document.getElementById("item-form"))) return;

			$.ajax({
				url: 'index.php?option=com_quix&view=collections',
				type: 'post',
				dataType: 'json',
				data: $(this).serialize(),
				beforeSend:function(){
					jQuery('#collection-window-modal .working').fadeIn('fast');
					jQuery('#collection-window-modal button[type=submit]').prop('disabled', 'true');
					window.parent.jQuery('#newLibraryModal').addClass('request-open');
				},
				complete:function(){
					// $('#collection-window-modal form').find('.loader').fadeOut();
					jQuery('#collection-window-modal .working').fadeOut('fast');
				},
				success:function(result)
				{
					$('#collection-window-modal form .success').fadeIn();

					var data = result.data;
					window.parent.location = data.url;
				},
				error:function(result){
					$('#collection-window-modal form .error').fadeIn();
				}
			});
		});

		setTimeout(function(){
	  		// replace trash icon
	  		jQuery('.icon-trash').replaceWith('<i class="icon-trash"></i>')

			if(window.QuixVersion == 'free'){
				var sideBanner = '<div class="filter-select hidden-phone" style="margin-top: 20px;">'+
				'<a href="https://www.themexpert.com/quix-pagebuilder?utm_campaign=quix-pro&utm_source=joomla-admin&utm_medium=sidebar-banner" target="_blank">'+
				'<img src="https://www.themexpert.com/images/quix-banner/banner.png">' +
				'</a></div>';
				jQuery('#sidebar .sidebar-nav').append(sideBanner);
			}
		}, 5000);

		setTimeout(function(){
	  		// replace trash icon
	  		jQuery('.subhead .icon-trash').replaceWith('<i class="icon-trash"></i>');
		}, 100);

		var validation = jQuery('[data-validation-submit]');
		var licenses = jQuery('[data-message]');

		// Change the behavior of form submission
		validation.on('click', function(e)
		{
			e.preventDefault();
			var f = document.adminForm;
			if (document.formvalidator.isValid(f)) {
				validation.addClass('disabled');
				licenses.html('<p class="muted">Activating your license...</p>');
				licenses.removeClass('hide');


				let username = document.getElementById("jform_username").value;
				let key = document.getElementById("jform_key").value;

				// let url = "https://www.themexpert.com/index.php?option=com_digicom&task=responses&source=authapi&catid=38&username=" + username + "&key=" + key;
        let url = "index.php?option=com_quix&task=config.validateLicense&catid=38&username=" + username + "&key=" + key;

				fetch(url).then(function(response) {
					return response.json();
				}).then(function(myJson) {
					// console.log(myJson);
					let jsonData = JSON.stringify(myJson);
					submitValidationJSON(jsonData);
				});

			}

			return;
		});

		function submitValidationJSON(jsonData)
		{
			// Validate api key
			jQuery.ajax({
				type: 'POST',
				url: 'index.php?option=com_quix&task=verify',
				// data: {'username': jQuery('#jform_username').val(), 'key': jQuery('#jform_key').val()}
				data: {'data' : jsonData }
			}).done(function(result) {
				// console.log(result);
				var data = JSON.parse(result);
				// User is not allowed to install
				if (!data.success) {
					// Set the error message
					licenses.html('<p class="alert alert-danger">' + data.message + '</p>');
					return false;
				}
				else
				{
					licenses.html('<p class="alert alert-success">' + data.message + '</p><p class="alert alert-info">Updating your configuration. Please wait...</p>');
					jQuery('#jform_activated').val(1);

					setTimeout(function(){
						Joomla.submitform('config.save');window.top.setTimeout('window.parent.jModalClose();location.reload();', 700);
					}, 2000)
				}
			})
			.fail(function( jqXHR, textStatus ) {
				// Set the error message
				licenses.html('<p class="alert alert-success">Request failed: ' + textStatus + '</p>');
			})
			.always(function() {
				validation.removeClass('disabled');
			});
		}

 	});

})(jQuery);
