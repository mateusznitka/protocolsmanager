<?php
class ConfigNewSettingsForms
{
    static function setUserFieldsForm($field_user)
    {
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
                                        ". __('Set user fields option') . "
                                    </td>
                                    <td class='center' width='7%'>
                                        
                                    <select name='user_fields' style='font-size:14px; width:95%'>";
        foreach ($User_Fields as $fuid => $userfield) {
            echo '<option value="'.$userfield["fieldname"].'" '.($userfield["fieldname"] == $field_user ? 'selected style="font-weight:bold"' : '').'>'.__($userfield["label"],'fields').'</option>';
        }
        echo "<option value='users_id' ".('users_id' == $field_user ? 'selected style="font-weight:bold"' : '').">".__('User')."</option>
              <option value='users_id_tech' ".('users_id_tech' == $field_user ? 'selected style="font-weight:bold"' : '').">".__('Technician')."</option>
                                    </select>
                                    </td>

                                    <td class='center' width='7%'>
                                        <input type='hidden' name='witch_field_settings' value='user_fields' >
                                        <input type='submit' name='service_settings' class='submit' value='".__('change')."'>
                                    </td>
                               </tr>";
        Html::closeForm();
    }

    static function show_own_assets($showOwnAssetsOn, $showOwnAssetsOff)
    {
        echo "<form method='post' action='config.form.php'>
                               <input type='hidden' name='menu_mode' value='e'>
                               <tr class='tab_bg_1' style='padding-top: 20px;'>
                                    <td class='center' width='7%' style='padding-top: 20px;'>
        ". __('Show user assets') . "
                                    </td>
                                    <td class='center' width='7%'>
        " . __("on") . "
    <input type='radio' name='show_own_assets' value='1' ".$showOwnAssetsOn.">
                                    </td>
                                    <td class='center' width='7%'>
        " . __("off") . "
    <input type='radio' name='show_own_assets' value='0' ".$showOwnAssetsOff .">
                                    </td>
                                    <td class='center' width='7%'>
                                        <input type='hidden' name='witch_field_settings' value='show_own_assets' >
                                        <input type='submit' name='service_settings' class='submit' value='".__('change')."'>
                                    </td>
                               </tr>";
        Html::closeForm();
    }

    static function protocols_save_on($serviceSignOn, $serviceSignOff)
    {
        echo "
						<form method='post' action='config.form.php'>
							<input type='hidden' name='menu_mode' value='e'>
								<tr class='tab_bg_1' style='padding-top: 20px;'>
									<td class='center' width='7%' style='padding-top: 20px;'>
											". __('sign protococols service') . "
									</td>
									<td class='center' width='7%'>
										" . __("on") . "
										<input type='radio' name='protocols_save_on' value='1' ".$serviceSignOn.">
									</td>
									<td class='center' width='7%'>
										" . __("off") . "
										<input type='radio' name='protocols_save_on' value='0' ".$serviceSignOff.">
									</td>
									<td class='center' width='7%'>
										<input type='hidden' name='witch_field_settings' value='protocols_save_on' >
										<input type='submit' name='service_settings' class='submit' value='".__('change')."'>
									</td>
							   </tr>";
        Html::closeForm();
    }

    static function mail_confirm_on($emailConfirmationOn, $emailConfirmationOff)
    {
        echo "<form method='post' action='config.form.php'>
							   <input type='hidden' name='menu_mode' value='e'>
							   <tr class='tab_bg_1' style='padding-top: 20px;'>
									<td class='center' width='7%' style='padding-top: 20px;'>
										". __('email confirmation') . "
									</td>
									<td class='center' width='7%'>
										" . __("on") . "
										<input type='radio' name='mail_confirm_on' value='1' ".$emailConfirmationOn.">
									</td>
									<td class='center' width='7%'>
										" . __("off") . "
									<input type='radio' name='mail_confirm_on' value='0' ".$emailConfirmationOff.">
									</td>
									<td class='center' width='7%'>
										<input type='hidden' name='witch_field_settings' value='mail_confirm_on' >
										<input type='submit' name='service_settings' class='submit' value='".__('change')."'>
									</td>
							   </tr>";
        Html::closeForm();
    }

    static function reminder_on($serviceReminderOn, $serviceReminderOff)
    {
        echo "<form method='post' action='config.form.php'>
							   <input type='hidden' name='menu_mode' value='e'>
							   <tr class='tab_bg_1' style='padding-top: 20px;'>
									<td class='center' width='7%' style='padding-top: 20px;'>
										". __('protococols reminder service') . "
									</td>
									<td class='center' width='7%'>
										" . __("on") . "
										<input type='radio' name='reminder_on' value='1' ".$serviceReminderOn.">
									</td>
									<td class='center' width='7%'>
										" . __("off") . "
									<input type='radio' name='reminder_on' value='0' ".$serviceReminderOff.">
									</td>
									<td class='center' width='7%'>
										<input type='hidden' name='witch_field_settings' value='reminder_on' >
										<input type='submit' name='service_settings' class='submit' value='".__('change')."'>
									</td>
							   </tr>";
        Html::closeForm();
    }

    static function first_emial_reminder($emailSettings, $formData)
    {
        echo "<form method='post' action='config.form.php' >
							  <input type='hidden' name='menu_mode' value='e'>
							   <tr class='tab_bg_1' style='padding-top: 20px;". $emailSettings ."' >
									<td class='center' width='7%' style='padding-top: 20px;'>
										". __('protococols first reminder email') . "
									</td>
									<td class='center' width='7%'>
									   " . $formData['first_emial_reminder'] . "
									</td>
									<td class='center' width='7%'>
									<input type='email' name='first_emial_reminder' >
									</td>
									<td class='center' width='7%'>
										<input type='hidden' name='witch_field_settings' value='first_emial_reminder' >
										<input type='submit' name='service_settings' class='submit' value='".__('change')."'>
									</td>
							   </tr>";
        Html::closeForm();
    }

    static function second_emial_reminder($emailSettings, $formData)
    {
        echo "<form method='post' action='config.form.php' >
							  <input type='hidden' name='menu_mode' value='e'>
							   <tr class='tab_bg_1 remindersEmails' style='padding-top: 20px;". $emailSettings ."' >
									<td class='center' width='7%' style='padding-top: 20px;'>
										". __('protococols second reminder email') . "
									</td>
									<td class='center' width='7%'>
									   " . $formData['second_emial_reminder'] . "
									</td>
									<td class='center' width='7%'>
									<input type='email' name='second_emial_reminder' >
									</td>
									<td class='center' width='7%'>
										<input type='hidden' name='witch_field_settings' value='second_emial_reminder' >
										<input type='submit' name='service_settings' class='submit' value='".__('change')."'>
									</td>
							   </tr>";
        Html::closeForm();
    }

    static function how_often_remind($emailSettings, $formData)
    {
        echo "<form method='post' action='config.form.php' >
							  <input type='hidden' name='menu_mode' value='e'>
							   <tr class='tab_bg_1 remindersEmails' style='padding-top: 20px;". $emailSettings ."'>
									<td class='center' width='7%' style='padding-top: 20px;'>
										". __('How long should one wait before sending a reminder') . "
									</td>
									<td class='center' width='7%'>
									   " . $formData['how_often_remind'] . "
									</td>
									<td class='center' width='7%'>
									<input type='number' name='how_often_remind' >
									</td>
									<td class='center' width='7%'>
										<input type='hidden' name='witch_field_settings' value='how_often_remind' >
										<input type='submit' name='service_settings' class='submit' value='".__('change')."'>
									</td>
								</tr>";
        Html::closeForm();
    }
}