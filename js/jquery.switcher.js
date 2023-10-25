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
	
$.mailPicture = function (){
		
		var pictTmpName = $(`input:hidden[name^='_picture[']`).val();
		var pictOnceCheck = $("#hidd_fileupload").val();
		var pictToken = $('input[name="_glpi_csrf_token"]').val();
		if(pictTmpName && pictOnceCheck == 'default1'){
			document.getElementById("hidd_fileupload").value="updated1";				
 			let dane = {'_glpi_csrf_token': pictToken,
						'_picture': pictTmpName
			}; 
			jQuery.ajax({
				url:"../config/update.config.picture.ajax.php",
				type: "POST",
				 dataType : "json",
				data: dane,
				success: function(data){
					if (data.result == "res_ok") {
							$('input[name="_glpi_csrf_token"]').val(data.token);
							document.getElementById("fileupload_pict_src").src = data.imageHtml;

							var list = document.getElementsByClassName("ti ti-circle-x pointer");
							for (var i=0; i<list.length; i++)
								list[i].click();
							
							var start = Date.now(),
							now = start;
							while (now - start < 5000) {
								now = Date.now();
							}
							 document.getElementById("hidd_fileupload").value="default1";	
							 displayAjaxMessageAfterRedirect();
							}
							else
								alert("Error. Try to refresh page");
				},
				error: function (jqXHR, textStatus, errorThrown) {
					alert("nie success");
				}
			});
		}
		
	};
		
})(jQuery);