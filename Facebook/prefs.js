function enableDisablePushAllow(force_enable) {
	var inputNever = $('#facebook-push-allow-never'); 
	var inputs = inputNever.closest('fieldset').find('input');
	var input;
	for (var i = 0; i < inputs.length; i++){
		input = $(inputs[i]);
		
		if(input.attr('id').indexOf('facebook-push-allow') || input.attr('id') != 'facebook-push-allow-never') {
			if (inputNever.attr('checked') && (!force_enable)) {
				input.attr('disabled','disabled');
			} else {
				input.removeAttr('disabled');
			}
			
		}	
	}
}


$(function(){
	$('#facebookDisconnect').click(
	function() {
		WET.byStr( 'FBconnect/prefs/disconnect' );
		$('#facebookDisconnectDone').hide();
		$('#fbDisconnectProgress').show();
		$.postJSON(wgServer + wgScript + "?action=ajax&rs=Facebook::disconnectFromFB" , 
			null, 
		function(data) {
			if (data.status = "ok") {
				$('#fbDisconnectLink').hide();
				$('#fbDisconnectProgressImg').hide();
				$('#fbDisconnectDone').show();
				$('#facebookDisconnectDone').show();
			} else {
				window.location.reload();
			}	
		});
	});

	$('#facebook-push-allow-never').change( function() { enableDisablePushAllow(false); });
	enableDisablePushAllow(false);
	$('#mw-preferences-form').submit(function() { enableDisablePushAllow(true); });
	
	$('#fbPrefsConnect').click(function() {
		WET.byStr( 'FBconnect/prefs/connect' );	
	});
});

