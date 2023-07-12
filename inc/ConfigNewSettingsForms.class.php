<?php
class ConfigNewSettingsForms {
	static function setUserFieldsForm($field_user) {
		global $DB;
		
		$User_Fields = $DB->request(['glpi_plugin_fields_fields','glpi_plugin_fields_containers'],
			['FIELDS' => ['glpi_plugin_fields_fields' => ['name AS fieldname' ,'label'],
				'glpi_plugin_fields_containers' => ['name AS containername']],
				['FKEY' => ['glpi_plugin_fields_fields' => 'plugin_fields_containers_id',
					'glpi_plugin_fields_containers'  => 'id']],
				['AND' => [ 'glpi_plugin_fields_fields.type' => "dropdown-User"]]
			]);
			
		echo "<form method='post' action='config.form.php'>
							   <input type='hidden' name='menu_mode' value='e'>
							   <tr class='tab_bg_1' style='padding-top: 20px;'>
									<td class='center' width='7%' style='padding-top: 20px;'>
										". __('Set user fields option','protocolsmanager') . "
									</td>
									<td class='center' width='7%'>
										
									<select name='user_fields' style='font-size:14px; width:95%'>";
		foreach ($User_Fields as $fuid => $userfield) {
			echo '<option value="'.$userfield["fieldname"].','.$userfield["containername"].'" '.($userfield["fieldname"] == $field_user ? 'selected style="font-weight:bold"' : '').'>'.__($userfield["label"],'fields').'</option>';
		}
		echo "<option value='users_id' ".('users_id' == $field_user ? 'selected style="font-weight:bold"' : '').">".__('User')."</option>
			  <option value='users_id_tech' ".('users_id_tech' == $field_user ? 'selected style="font-weight:bold"' : '').">".__('Technician')."</option>
									</select>
									</td>

									<td class='center' width='7%'>
										<input type='hidden' name='witch_field_settings' value='user_fields' >
										<input type='submit' name='service_settings' class='submit' value='".__('Change','protocolsmanager')."'>
									</td>
								</tr>";
		Html::closeForm();
	}

	static function show_own_assets($showOwnAssetsOn, $showOwnAssetsOff ) {
		echo "
		<script type='text/javascript' src='../js/jquery.switcher.js'></script>
		<form method='post' action='config.form.php'>
								<input type='hidden' name='menu_mode' value='e'>
								<tr class='tab_bg_1' style='padding-top: 20px;'>
									<td class='center' width='7%' style='padding-top: 20px;'>
		". __('Show user assets') . "
									</td>
									<td class='center' width='7%'>
									<input class='form-check-input' type='checkbox' id='inlineCheckbox1' witch_field_settings='show_own_assets' name='_glpi_csrf_token' 
									value='" . Session::getNewCSRFToken() ."' " . ($showOwnAssetsOn == 'checked' ? "checked='checked'" : "") . ">
								</tr>";
		Html::closeForm();
	}

	static function protocols_save_on($serviceSignOn, $serviceSignOff) {
		echo "
						<form method='post' action='config.form.php'>
							<input type='hidden' name='menu_mode' value='e'>
								<tr class='tab_bg_1' style='padding-top: 20px;'>
									<td class='center' width='7%' style='padding-top: 20px;'>
											". __('sign protococols service','protocolsmanager') . "
									</td>
									<td class='center' width='7%'>
									
									<input class='form-check-input' type='checkbox' id='inlineCheckbox2' witch_field_settings='protocols_save_on' name='_glpi_csrf_token'
									value='" . Session::getNewCSRFToken() ."' " . ($serviceSignOn == 'checked' ? "checked='checked'" : "") . ">
								</tr>";
		Html::closeForm();
	}

	static function mail_confirm_on($emailConfirmationOn, $emailConfirmationOff) {
		echo "<form method='post' action='config.form.php'>
								<input type='hidden' name='menu_mode' value='e'>
								<tr class='tab_bg_1' style='padding-top: 20px;'>
									<td class='center' width='7%' style='padding-top: 20px;'>
										". __('email confirmation','protocolsmanager') . "
									</td>
									<td class='center' width='7%'>
									<input class='form-check-input' type='checkbox' id='inlineCheckbox3' witch_field_settings='mail_confirm_on' name='_glpi_csrf_token' 
									value='" . Session::getNewCSRFToken() ."' " . ($emailConfirmationOn == 'checked' ? "checked='checked'" : "") . ">
								</tr>";
		Html::closeForm();
	}

	static function reminder_on($serviceReminderOn, $serviceReminderOff) {
		echo "<form method='post' action='config.form.php'>
							   <input type='hidden' name='menu_mode' value='e'>
							   <tr class='tab_bg_1' style='padding-top: 20px;'>
									<td class='center' width='7%' style='padding-top: 20px;'>
										". __('protococols reminder service','protocolsmanager') . "
									</td>
									<td class='center' width='7%'>
									<input class='form-check-input' type='checkbox' id='inlineCheckbox4' witch_field_settings='reminder_on' name='_glpi_csrf_token'
									value='" . Session::getNewCSRFToken() ."' " . ($serviceReminderOn == 'checked' ? "checked='checked'" : "") . ">
								</tr>
								<script>
								$.switcher();</script>
								";
		Html::closeForm();
	}

	static function first_emial_reminder($emailSettings, $formData) {
		echo "<form method='post' action='config.form.php' >
							  <input type='hidden' name='menu_mode' value='e'>
							   <tr class='tab_bg_1' style='padding-top: 20px;". $emailSettings ."' >
									<td class='center' width='7%' style='padding-top: 20px;'>
										". __('protococols first reminder email','protocolsmanager') . "
									</td>
									<td class='center' width='7%'>
									   " . $formData['first_emial_reminder'] . "
									</td>
									<td class='center' width='7%'>
									<input type='email' name='first_emial_reminder' >
									</td>
									<td class='center' width='7%'>
										<input type='hidden' name='witch_field_settings' value='first_emial_reminder' >
										<input type='submit' name='service_settings' class='submit' value='".__('Change','protocolsmanager')."'>
									</td>
								</tr>";
		Html::closeForm();
	}

	static function second_emial_reminder($emailSettings, $formData) {
		echo "<form method='post' action='config.form.php' >
							  <input type='hidden' name='menu_mode' value='e'>
							   <tr class='tab_bg_1 remindersEmails' style='padding-top: 20px;". $emailSettings ."' >
									<td class='center' width='7%' style='padding-top: 20px;'>
										". __('protococols second reminder email','protocolsmanager') . "
									</td>
									<td class='center' width='7%'>
									   " . $formData['second_emial_reminder'] . "
									</td>
									<td class='center' width='7%'>
									<input type='email' name='second_emial_reminder' >
									</td>
									<td class='center' width='7%'>
										<input type='hidden' name='witch_field_settings' value='second_emial_reminder' >
										<input type='submit' name='service_settings' class='submit' value='".__('Change','protocolsmanager')."'>
									</td>
								</tr>";
		Html::closeForm();
	}

	static function how_often_remind($emailSettings, $formData) {
		echo "<form method='post' action='config.form.php' >
							  <input type='hidden' name='menu_mode' value='e'>
							   <tr class='tab_bg_1 remindersEmails' style='padding-top: 20px;". $emailSettings ."'>
									<td class='center' width='7%' style='padding-top: 20px;'>
										". __('How long should one wait before sending a reminder','protocolsmanager') . "
									</td>
									<td class='center' width='7%'>
									   " . $formData['how_often_remind'] . "
									</td>
									<td class='center' width='7%'>
									<input type='number' name='how_often_remind' >
									</td>
									<td class='center' width='7%'>
										<input type='hidden' name='witch_field_settings' value='how_often_remind' >
										<input type='submit' name='service_settings' class='submit' value='".__('Change','protocolsmanager')."'>
									</td>
								</tr>";
		Html::closeForm();
	}

	public static function template_emails($temlateData) {
		
		$title = isset($temlateData['template_title']) ? $temlateData['template_title'] : 'message title';
		$body = isset($temlateData['template_body']) ? $temlateData['template_body'] : 'message body';

		echo "<form method='post' action='config.form.php' >
							  <input type='hidden' name='menu_mode' value='e'>
							   <tr class='tab_bg_1' style='padding-top: 20px;'>
									<td class='center' width='7%' style='padding-top: 20px;'>
										". __('sign email template','protocolsmanager') . "
									</td>
									<td class='center' colspan='2'>
									  <input style='width:80%' type='text' name='template_title' value='".$title."'>
									</td>
							  
									
								</tr>
								<tr>
								<td class='center'></td>
								<td class='center' colspan='2'>
									<textarea style='width:80%; height: 150px;' type='text' name='template_body'>".
			$body."</textarea>
								</td>
								
								<td class='center' width='7%'>
										<input type='submit' name='email_template_new' class='submit' value='".__('Save','protocolsmanager')."'>
								 </td>
								</tr>
								";
		Html::closeForm();
	}
}