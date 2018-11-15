/* Plugin Settings */

jQuery(document).ready(function($) {
	
	$('.g7g-cfg-reset-options').on('click', function(e) { console.log('T');
		e.preventDefault();
		$('.plugin-modal-dialog').dialog('destroy');
		var link = this;
		var button_names = {}
		button_names[alert_reset_options_true]  = function() { window.location = link.href; }
		button_names[alert_reset_options_false] = function() { $(this).dialog('close'); }
		$('<div class="plugin-modal-dialog">'+ alert_reset_options_message +'</div>').dialog({
			title: alert_reset_options_title,
			buttons: button_names,
			modal: true,
			width: 350
		});
	});
	
});
