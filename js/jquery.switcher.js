;(function ($) {
	$.switcher = function (filter) {
		var $haul = $('input[type=checkbox]');
		
		if (filter !== undefined && filter.length) {
			$haul = $haul.filter(filter);
		}
		
		$haul.each(function () {
			var $checkbox = $(this).hide(),
				$switcher = $(document.createElement('div'))
					.addClass('ui-switcher')
					.attr('aria-checked', $checkbox.is(':checked'));
			toggleSwitch = function (e) {
				if (e.target.type === undefined) {
					$checkbox.trigger(e.type);
				}
				$switcher.attr('aria-checked', $checkbox.is(':checked'));
				if (e.target.value !== undefined && e.target.value.length){
					let dane = {
						'checkVal': CheckVal = (e.target.checked) ? 1 : 0,
						'_glpi_csrf_token': e.target.value,
						'witch_field_settings' : e.target.getAttribute("witch_field_settings")};
					jQuery.ajax({
						url:"../config/update.config.ajax.php",
						type: "POST",
						 dataType : "json",
						data: dane,
						beforeSend: function(){
							$('input[name="_glpi_csrf_token"][type="checkbox"]').prop( "disabled", true );
						},
						success: function(data){
							if (data.result == "res_ok") {
								$('input[name="_glpi_csrf_token"]').val(data.token);
							$('input[name="_glpi_csrf_token"][type="checkbox"]').prop( "disabled", false );
							}
							else
								alert("Error. Try to refresh page");
						},
						error: function (jqXHR, textStatus, errorThrown) {
							alert("Error. Try to refresh page");
						}
					});
				}
			};
			
			$switcher.on('click', toggleSwitch);
			$checkbox.on('click', toggleSwitch);
			
			$switcher.insertBefore($checkbox);
		});
	};
})(jQuery);
