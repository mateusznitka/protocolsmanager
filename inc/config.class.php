<?php

require_once dirname(__DIR__) . '/inc/ConfigNewSettingsForms.class.php';

class PluginProtocolsmanagerConfig extends CommonDBTM {
	
	
	function showFormProtocolsmanager() {
		global $CFG_GLPI, $DB;
		$plugin_conf = self::checkRights();
		if ($plugin_conf == 'w') {
			self::displaySettings();
			self::displayContent();
		} else {
			echo "<div align='center'><br><img src='".$CFG_GLPI['root_doc']."/pics/warning.png'><br>".__("Access denied")."</div>";
		}
	}
	
	
	static function checkRights() {
		global $DB;
		$active_profile = $_SESSION['glpiactiveprofile']['id'];
		$req = $DB->request('glpi_plugin_protocolsmanager_profiles',
						['profile_id' => $active_profile]);
						
		if ($row = $req->current()) {
			$plugin_conf = $row["plugin_conf"];
		} else {
			$plugin_conf = "";
		}
		return $plugin_conf;
	}
	
	static function displaySettings(){
		self::setEmailsToSendReminder();
	}

	static function displayContent() {
		global $CFG_GLPI, $DB;
		
		if (isset($_POST["menu_mode"])) {
			$menu_mode = $_POST["menu_mode"];
		} else if (isset($_SESSION["menu_mode"])) {
			$menu_mode = $_SESSION["menu_mode"];
		} else $menu_mode = "t";
		
		if ($menu_mode == "e") {
			self::displayContentEmail();
		} else {
			self::displayContentConfig();
		}
	}
	
	static function displayContentConfig() {
		global $CFG_GLPI, $DB;
		
		if (isset($_POST["edit_id"])) {
			
			$edit_id = $_POST['edit_id'];
			$mode = $edit_id;
			
			$req = $DB->request(
				'glpi_plugin_protocolsmanager_config',
				['id' => $edit_id ]);
				
			if ($row = $req->current()) {
				$template_uppercontent = $row["upper_content"];
				$template_content = $row["content"];
				$template_footer = $row["footer"];
				$template_name = $row["name"];
				$font = $row["font"];
				$fontsize = $row["fontsize"];
				$city = $row["city"];
				$logo = $row["logo"];
				$serial_mode = $row["serial_mode"];
				$orientation = $row["orientation"];
				$breakword = $row["breakword"];
				$email_mode = $row["email_mode"];
				$email_template = $row["email_template"];
			}
			
		} else {
			$template_uppercontent = '';
			$template_content = '';
			$template_footer = '';
			$template_name = '';
			$font = '';
			$fontsize = '9';
			$city = '';
			$mode = 0; //if mode 0 then you creating new template instead of edit
			$serial_mode = 1;
			$orientation = "p";
			$breakword = 1;
			$email_mode = 2;
			$email_template = 1;
		}
		
		
		$fonts = array('Courier' => 'Courier',
						'Helvetica' => 'Helvetica', 
						'Times' => 'Times',
						'Istok' => 'Istok',
						'UbuntuMono' => 'UbuntuMono',
						'Roboto' => 'Roboto',
						'Liberation-Sans' => 'Liberation-Sans',
						'DroidSerif' => 'DroidSerif',
						'DejaVu Sans' => 'DejaVu Sans');
						
		$fontsizes = array('7' => '7',
							'8' => '8',
							'9' => '9',
							'10' => '10',
							'11' => '11',
							'12' => '12');
						
		$orientations = array('Portrait' => 'Portrait',
							'Landscape' => 'Landscape');
		
		if (!isset($font)) {
			$font='freesans';
		}
		
		echo "<div class='center'>";
		echo "<form action='config.form.php' method='post'>";
		echo "<input type='hidden' name='menu_mode' value='t'>";
		echo "<table class='tab_cadre_fixe' style='width:90%;'>";
		echo "<tr><td style='text-align:center'><input type='submit' class='submit' name='template_settings' value='" . __('Templates settings','protocolsmanager') . "' ></td>";
		Html::closeForm();
		echo "<form action='config.form.php' method='post'>";
		echo "<input type='hidden' name='menu_mode' value='e'>";
		echo "<td style='text-align:center'><input type='submit' class='submit' name='email_settings' value='" . __('Email settings','protocolsmanager') . "' ></td></tr>";
		echo "</table>";
		Html::closeForm();
		echo "</div>";
	
		
		echo "<form name='form' action='config.form.php' method='post'  enctype='multipart/form-data'>";
		echo "<input type='hidden' name='MAX_FILE_SIZE' value=1948000>";
		echo "<input type='hidden' name='mode' value='$mode'>";
		echo "<table class='tab_cadre_fixe'>";
		//echo "<tr><th></th>";
		echo "<tr><th colspan='3'>".__('Create','protocolsmanager')." ".__('template','protocolsmanager')."<a href='https://github.com/mateusznitka/protocolsmanager/wiki/Using-the-plugin' target='_blank'><img src='../img/help.png' width='20px' height='20px' align='right'></a></th></tr>";
		echo "<tr><td>".__('Template name','protocolsmanager')."*</td><td colspan='2'><input type='text' name='template_name' style='width:80%;' value='$template_name'></td></tr>";			
		echo "<tr><td>" . __('Font','protocolsmanager') . "</td><td colspan='2'><select name='font' style='width:150px'>";
			foreach($fonts as $code => $fontname) {
				echo "<option value='".$code."' ";
				if ($code == $font) {
					echo __('selected','protocolsmanager');
				}
				echo ">".$fontname."</option>";
			}
		echo "</select></td></tr>";
		
		echo "<tr><td>" . __('Font size','protocolsmanager') . "</td><td colspan='2'><select name='fontsize' style='width:150px'";
			foreach($fontsizes as $fsize => $fsizes) {
				echo "<option value='".$fsize."' ";
				if ($fsize == $fontsize) {
					echo __('selected','protocolsmanager');
				}
				echo ">".$fsizes."</option>";
			}
			
		echo "<tr><td>" . __('Word breaking','protocolsmanager') . "</td><td><input type='radio' name='breakword' value=1 ";
		if ($breakword == 1)
			echo "checked='checked'";
		echo ">" . __('Enabled') . "</td>";
		echo "<td><input type='radio' name='breakword' value=0 ";
		if ($breakword == 0)
			echo "checked='checked'";
		echo ">" . __('Disabled') . "</td></tr>";
		
		echo "<tr><td>".__('City')."</td><td colspan='2'><input type='text' name='city' style='width:80%;' value='$city'></td></tr>";
		echo "<tr><td>".__('Upper Content','protocolsmanager')."</td><td colspan='2' class='middle'><textarea style='width:80%; height:100px;' cols='50' rows'8' name='template_uppercontent'>".$template_uppercontent."</textarea></td></tr>";
		echo "<tr><td>".__('Content')."</td><td colspan='2' class='middle'><textarea style='width:80%; height:100px;' cols='50' rows'8' name='template_content'>".$template_content."</textarea></td></tr>";
		echo "<tr><td>".__('Footer','protocolsmanager')."</td><td class='middle' colspan='2'><textarea style='width:80%; height:100px;' cols='45' rows'4' name='footer_text'>".$template_footer."</textarea></td></tr>";
		echo "<tr><td>".__('Orientation')."</td><td colspan='2'><select name='orientation' style='width:150px'>";
			foreach($orientations as $vals => $valname) {
				echo "<option value='".$vals."' ";
				if ($vals == $orientation) {
					echo " selected";
				}
				echo ">".$valname."</option>";
			}	
		echo "</select></td></tr>";
		echo "<tr><td>".__('Serial number')."</td><td><input type='radio' name='serial_mode' value='1' ";
		if ($serial_mode == 1)
			echo "checked='checked'";
		echo ">" . __('serial and inventory number in separate columns','protocolsmanager') . "</td>";
		echo "<td><input type='radio' name='serial_mode' value='2' ";
		if ($serial_mode == 2)
			echo "checked='checked'";
		echo ">" . __("serial or inventory number if serial doesn't exists",'protocolsmanager') . "</td></tr>";
		echo "<tr><td>".__('Logo')."</td><td colspan='2'><input type='file' name='logo' accept='image/png, image/jpeg'>";
		if (isset($logo)) {
			$full_img_name = GLPI_ROOT.'/files/_pictures/'.$logo;
			$img_type = pathinfo($full_img_name, PATHINFO_EXTENSION);
			$img_data = file_get_contents($full_img_name);
			$base64 = 'data:image/'.$img_type.';base64,'.base64_encode($img_data);
			$img_delete = true;
			echo "&nbsp&nbsp<img src = ".$base64." style='height:50px; width:auto;'>";
			echo "&nbsp&nbsp<input type='checkbox' name='img_delete' value='$img_delete'>&nbsp ".__('Delete')." ".__('File');
		}
		echo "</td></tr>";
		echo "<tr><td>".__('Enable email autosending','protocolsmanager')."</td><td><input type='radio' name='email_mode' value='1'";
		if ($email_mode == 1)
			echo "checked='checked'";
		echo ">" . __('Enabled') . "</td>";
		echo "<td><input type='radio' name='email_mode' value='2'";
		if ($email_mode == 2)
			echo "checked='checked'";
		echo ">" . __('Disabled') . "</td></tr>";
		echo "<tr><td>".__('Email template','protocolsmanager')."</td><td colspan='2'><select name='email_template' style='width:150px'>";
			foreach ($DB->request('glpi_plugin_protocolsmanager_emailconfig') as $uid => $list) {
				echo '<option value=';
				echo $list["id"];
				if ($uid == $email_template) {
					echo '" selected';
				}
				echo '>';
				echo $list["tname"];
				echo '</option>';
			}	
		echo "</select></td></tr>";
		echo "</table>";
		echo "<table class='tab_cadre_fixe'><td style='text-align:right;'><input type='submit' name='save' class='submit'></td>";
		Html::closeForm();
		echo "<form name='cancelform' action='config.form.php' method='post'><td style='text-align:left;'><input type='submit' class='submit' name='cancel' value=".__('Cancel')."></td></table>";
		Html::closeForm();
		echo "</div>";
		echo "<br>";
		self::showConfigs();
		
		
	}
	
	static function DisplayContentEmail() {
		global $DB, $CFG_GLPI;
		
		echo "<div class='center'>";
		echo "<form action='config.form.php' method='post'>";
		echo "<input type='hidden' name='menu_mode' value='t'>";
		echo "<table class='tab_cadre_fixe' style='width:90%;'>";
		echo "<tr><td style='text-align:center'><input type='submit' class='submit' name='template_settings' value=" . __('Templates settings','protocolsmanager') . "></td>";
		Html::closeForm();
		echo "<form action='config.form.php' method='post'>";
		echo "<input type='hidden' name='menu_mode' value='e'>";
		echo "<td style='text-align:center'><input type='submit' class='submit' name='email_settings' value=" . __('Email settings','protocolsmanager') . "></td></tr>";
		echo "</table>";
		Html::closeForm();
		echo "</div>";
		
		if (isset($_POST["email_edit_id"])) {
			
			$email_edit_id = $_POST['email_edit_id'];
			
			$req = $DB->request(
				'glpi_plugin_protocolsmanager_emailconfig',
				['id' => $email_edit_id ]);
				
			if ($row = $req->current()) {
				$tname = $row["tname"];
				$send_user = $row["send_user"];
				$email_subject = $row["email_subject"];
				$email_content = $row["email_content"];
				$recipients = $row["recipients"];
			}
		} else {
			$tname = '';
			$send_user = 2;
			$email_subject = '';
			$email_content = '';
			$recipients = '';
			$email_edit_id=0;
		}
		
		//email template edit
		echo "<div class='center'>";
		echo "<form name ='email_template_edit' action='config.form.php' method='post' enctype='multipart/form-data'>";
		
		echo "<table class='tab_cadre_fixe'>";
		echo "<tr><th colspan='3'>".__('Create')." ".__('email template')."<a href='https://github.com/mateusznitka/protocolsmanager/wiki/Email-sending-configuration' target='_blank'><img src='../img/help.png' width='20px' height='20px' align='right'></a></th></tr>";
		echo "<tr><td>".__('Template name')."*</td><td colspan='2' class='middle'><input type='text' class='eboxes' name='tname' style='width:80%;' value='$tname'></td></tr>";
		echo "<tr><td>".__('Send to user')."</td><td><input type='radio' name='send_user' value='1' class='eboxes' ";
		if ($send_user == 1)
			echo "checked='checked'";
		echo ">".__('Send to user')."</td>";
		echo "<td><input type='radio' name='send_user' value='2' class='eboxes' ";
		if ($send_user == 2)
			echo "checked='checked'";
		echo ">".__("don't send to user") . "</td></tr>";
		echo "<tr><td>".__('Email content')."*</td><td colspan='2' class='middle'><textarea style='width:80%; height:100px;' class='eboxes' cols='50' rows'8' name='email_content'>".$email_content."</textarea></td></tr>";
		echo "<tr><td>".__('Email subject')."*</td><td colspan='2' class='middle'><input type='text' class='eboxes' name='email_subject' style='width:80%;' value='$email_subject'></td></tr>";
		echo "<tr><td>".__('Add emails - use ; to separate')."*</td><td colspan='2' class='middle'><textarea style='width:80%; height:100px;' class='eboxes' cols='50' rows '8' name='recipients'>".$recipients."</textarea></td></tr>";
		echo "</table>";
		echo "<input type='hidden' name='email_edit_id' value=$email_edit_id>";
		echo "<table class='tab_cadre_fixe'><td style='text-align:right;'><input type='submit' name='save_email' class='submit' id='email_submit'></td>";
		Html::closeForm();
		echo "<form name='cancelform' action='config.form.php' method='post'><td style='text-align:left;'><input type='submit' class='submit' name='cancel_email' value=".__('Cancel')."></td></table>";
		Html::closeForm();
		echo "</div>";
		echo "<br>";
		self::showEmailConfigs();
	}
	
	static function saveConfigs() {
		global $DB, $CFG_GLPI;
		
		if (empty($_POST["template_name"])) {
			Session::AddMessageAfterRedirect('Fill mandatory fields', 'WARNING', true);
		} else {
		
			$template_name = $_POST['template_name'];
			$template_uppercontent = $_POST['template_uppercontent'];
			$template_content = $_POST['template_content'];
			$template_footer = $_POST['footer_text'];
			$font = $_POST["font"];
			$fontsize = $_POST["fontsize"];
			$city = $_POST["city"];
			$mode = $_POST["mode"];
			$serial_mode = $_POST["serial_mode"];
			$orientation = $_POST["orientation"];
			$breakword = $_POST["breakword"];
			$email_mode = $_POST["email_mode"];
			$email_template = $_POST["email_template"];

			
			if (isset($_POST['img_delete'])) {
				
				$DB->update('glpi_plugin_protocolsmanager_config', [
						'logo' => $full_img_name
					], [
						'id' => $mode
					]
				);
			}
			
			$full_img_name = self::uploadImage();
			
			//if new template
			if ($mode == 0) {
				
				$DB->insert('glpi_plugin_protocolsmanager_config', [
					'name' => $template_name,
					'upper_content' => $template_uppercontent,
					'content' => $template_content,
					'footer' => $template_footer,
					'logo' => $full_img_name,
					'font' => $font,
					'fontsize' => $fontsize,
					'city' => $city,
					'serial_mode' => $serial_mode,
					'orientation' => $orientation,
					'breakword' => $breakword,
					'email_mode' => $email_mode,
					'email_template' =>$email_template
					]
				);
			}
			
			//if edit template
			if ($mode != 0) {
				
				//if logo is uploaded
				if (isset($full_img_name)) {
					
					$DB->update('glpi_plugin_protocolsmanager_config', [
							'name' => $template_name,
							'content' => $template_content,
							'upper_content' => $template_uppercontent,
							'footer' => $template_footer,
							'logo' => $full_img_name,
							'font' => $font,
							'fontsize' => $fontsize,
							'city' => $city,
							'serial_mode' => $serial_mode,
							'orientation' => $orientation,
							'breakword' => $breakword,
							'email_mode' => $email_mode,
							'email_template' =>$email_template
						], [
							'id' => $mode
						]
					);
				} else {
					
					$DB->update('glpi_plugin_protocolsmanager_config', [
							'name' => $template_name,
							'content' => $template_content,
							'upper_content' => $template_uppercontent,
							'footer' => $template_footer,
							'font' => $font,
							'fontsize' => $fontsize,
							'city' => $city,
							'serial_mode' => $serial_mode,
							'orientation' => $orientation,
							'breakword' => $breakword,
							'email_mode' => $email_mode,
							'email_template' =>$email_template
						], [
							'id' => $mode
						]
					);
				}
			}
			
			Session::AddMessageAfterRedirect('Config saved');
		}		
	
	}

	static function saveEmailConfigs() {
		global $DB, $CFG_GLPI;
		
		if (empty($_POST["email_subject"]) || empty($_POST["email_content"]) || empty($_POST["recipients"]) || empty($_POST["tname"])) {
			Session::AddMessageAfterRedirect('Fill mandatory fields', 'WARNING', true);
		} else {
			
		
			$tname = $_POST["tname"];
			$send_user = $_POST["send_user"];
			$email_subject = $_POST["email_subject"];
			$email_content = $_POST["email_content"];
			$recipients = $_POST["recipients"];
			$email_edit_id = $_POST["email_edit_id"];
			
			if($email_edit_id == 0) {
				
				$DB->insert('glpi_plugin_protocolsmanager_emailconfig', [
					'tname' => $tname,
					'send_user' => $send_user,
					'email_subject' => $email_subject,
					'email_content' => $email_content,
					'recipients' => $recipients
					]
				);
			}
				
			if($email_edit_id != 0) {
				
				$DB->update('glpi_plugin_protocolsmanager_emailconfig', [
					'tname' => $tname,
					'send_user' => $send_user,
					'email_subject' => $email_subject,
					'email_content' => $email_content,
					'recipients' => $recipients
					], [
					'id' => $email_edit_id
					]
				);
				
			}
			
			Session::AddMessageAfterRedirect('Config saved');
		}

	}
	

	static function showConfigs() {
		global $DB, $CFG_GLPI;
		$configs = [];
		
		echo "<div class='spaced' id='show_configs'>";
		echo "<table class='tab_cadre_fixehov' style='width:90%;'>";
		echo "<tr class='tab_bg_1'><th colspan='3'>".__('Templates')."</th></tr>";
		echo "<tr class='tab_bg_1'><td class='center'><b>".__('Name')."</b></td>";
		echo "<td class='center' colspan=2'><b>".__('Action')."</b></td></tr>";
		
		foreach ($DB->request(
			'glpi_plugin_protocolsmanager_config') as $config_data => $configs) {
				
				echo "<tr class='tab_bg_1'><td class='center'>";
				echo $configs['name'];
				echo "</td>";
				$conf_id = $configs['id'];
				echo "<td class='center' width='7%'>
						<form method='post' action='config.form.php'><input type='hidden' value='$conf_id' name='edit_id'><input type='submit' name='edit' value=".__('Edit')." class='submit'></td>";
						echo "<input type='hidden' name='menu_mode' value='t'>";
						Html::closeForm();	
						echo "<td class='center' width='7%'><form method='post' action='config.form.php'><input type='hidden' value='$conf_id' name='conf_id'><input type='submit' name='delete' value=".__('Delete')." class='submit'></td></tr>";
						Html::closeForm();				
			}
		echo "</table></div>";
	}
	
	static function showEmailConfigs() {
		global $DB, $CFG_GLPI;
		$emailconfigs = [];
		
		echo "<div class='spaced' id='show_emailconfigs'>";
		echo "<table class='tab_cadre_fixehov' style='width:90%;'>";
		echo "<tr class='tab_bg_1'><th colspan='4'>".__('Templates')."</th></tr>";
		echo "<tr class='tab_bg_1'><td class='center'><b>".__('Name')."</b></td>";
		echo "<td class='center'><b>".__('Recipients')."</b></td>";
		echo "<td class='center' colspan=2'><b>".__('Action')."</b></td></tr>";
		
		foreach ($DB->request(
			'glpi_plugin_protocolsmanager_emailconfig') as $configs_data => $emailconfigs) {
				
				echo "<tr class='tab_bg_1'><td class='center'>";
				echo $emailconfigs['tname'];
				echo "</td>";
				echo "<td class='center'>";
				echo $emailconfigs['recipients'];
				echo "</td>";
				$email_conf_id = $emailconfigs['id'];
				echo "<td class='center' width='7%'>
						<form method='post' action='config.form.php'><input type='hidden' value='$email_conf_id' name='email_edit_id'><input type='submit' name='email_edit' value=".__('Edit')." class='submit'></td>";
						echo "<input type='hidden' name='menu_mode' value='e'>";
						Html::closeForm();	
						echo "<td class='center' width='7%'><form method='post' action='config.form.php'><input type='hidden' value='$email_conf_id' name='email_conf_id'><input type='submit' name='delete_email' value=".__('Delete')." class='submit'></td></tr>";
						Html::closeForm();				
			}
		echo "</table></div>";
	}


    static function setEmailsToSendReminder(){
        $formData = self::getDataSettings();
        $templateData = self::getDataTemplate();
        $showOwnAssetsOn = '';
        $showOwnAssetsOff = '';
        $serviceSignOn = '';
        $serviceSignOff = '';
        $emailConfirmationOn = '';
        $emailConfirmationOff = '';
        $serviceReminderOn = '';
        $serviceReminderOff = '';

        $formData['show_own_assets'] == 1 ? $showOwnAssetsOn = 'checked' : $showOwnAssetsOff = 'checked';
        $formData['protocols_save_on'] == 1 ? $serviceSignOn = 'checked' : $serviceSignOff = 'checked';
        $formData['mail_confirm_on'] == 1 ? $emailConfirmationOn = 'checked' : $emailConfirmationOff = 'checked';
        $formData['reminder_on'] == 1 ? $serviceReminderOn = 'checked' : $serviceReminderOff = 'checked';
        ($formData['reminder_on'] == 1 && $formData['protocols_save_on'] == 1) ? $emailSettings = '' : $emailSettings = 'display:none';
        $formData['first_emial_reminder'] =  $formData['first_emial_reminder'] ?? ' - ';
        $formData['second_emial_reminder'] =  $formData['second_emial_reminder'] ?? ' - ';
        $user_fields = $formData['user_fields'];

        echo "<div class='spaced'>
                <table class='tab_cadre_fixehov'>
                <div style='text-align:center;'><h2>ProtocolsManager Settings</h2></div>
                    <div>";
        //ConfigNewSettingsForms - forms for new protocols setting

        ConfigNewSettingsForms::setUserFieldsForm($user_fields);

        ConfigNewSettingsForms::show_own_assets($showOwnAssetsOn, $showOwnAssetsOff);

        ConfigNewSettingsForms::protocols_save_on($serviceSignOn, $serviceSignOff);

        ConfigNewSettingsForms::mail_confirm_on($emailConfirmationOn, $emailConfirmationOff);

        ConfigNewSettingsForms::reminder_on($serviceReminderOn, $serviceReminderOff);

        ConfigNewSettingsForms::first_emial_reminder($emailSettings, $formData);

        ConfigNewSettingsForms::second_emial_reminder($emailSettings, $formData);

        ConfigNewSettingsForms::how_often_remind($emailSettings, $formData);

        ConfigNewSettingsForms::template_emails($templateData);

        echo "
					</div>
				</table>
		</div><hr>";
	}
	
	static function uploadImage() {
		global $DB, $CFG_GLPI;
		
		if($_FILES['logo']['name']) {
			
			if($_FILES['logo']['error'] != UPLOAD_ERR_FORM_SIZE) {
			
				if (!$_FILES['logo']['error']) {
					
					if ($_FILES['logo']['type'] == 'image/jpeg' || $_FILES['logo']['type'] == 'image/png' || $_FILES['logo']['type'] == 'image/jpg') {
						
						$img_name = "logo".time();
						$ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
						$full_img_name = $img_name.'.'.$ext;
						$img_path = GLPI_ROOT.'/files/_pictures/'.$full_img_name;
						
						move_uploaded_file($_FILES['logo']['tmp_name'], $img_path);
						
						return $full_img_name;
						
					} else {
						Session::addMessageAfterRedirect(__('Filetype not allowed.')." ".__('Only jpg and png files are accepted','protocolsmanager'),'WARNING',true);
					}
				} else {
					Session::addMessageAfterRedirect(__('Unknown error'), 'WARNING', true);
				}
			} else {
				Session::addMessageAfterRedirect('File too large to be added.', 'WARNING', true);
			}
		}

	}
	
	static function deleteConfigs() {
		global $DB;
		
		$conf_id = $_POST['conf_id'];
		
		$DB->delete(
			'glpi_plugin_protocolsmanager_config', [
				'id' => $conf_id
			]
		);	
		
		
		
	}
	
	
	static function deleteEmailConfigs() {
		global $DB;
		
		$email_conf_id = $_POST['email_conf_id'];
		
		$DB->delete(
			'glpi_plugin_protocolsmanager_emailconfig', [
				'id' => $email_conf_id
			]
		);
	}
	
	private static function getDataSettings(){
		global $DB;
		$query = (['FROM' => 'glpi_plugin_protocolsmanager_settings', 'WHERE' => ['id' => 1]]);
		return $DB->request($query)->current();
	}
	
	public static function setSettingsData($postData){
		global $DB;
		try{
			$DB->update('glpi_plugin_protocolsmanager_settings',
				[
					$postData['witch_field_settings'] => $postData[$postData['witch_field_settings']],
				],
				[
					'id' => 1
				]
			);
			Session::addMessageAfterRedirect(__('Field') . " " . $postData['witch_field_settings'] ." " . __('updated'));
		}catch(Exception $e){
			Session::addMessageAfterRedirect(__('Error - field not updated','protocolsmanager'), 'ERROR', true);
		}
	}

    public static function getDataTemplate(){
        global $DB;
        $query = (['FROM' => 'glpi_plugin_protocolsmanager_mails_templates', 'WHERE' => ['id' => 1]]);
        return $DB->request($query)->current();
    }

    public static function setEmailTemplate($postData){
        global $DB;
        try{
            if(empty(self::getDataTemplate())){
                $query = "INSERT INTO `glpi_plugin_protocolsmanager_mails_templates` (`id`, `template_name`, `template_title`, `template_body`) 
                            VALUES ('1', 'send_email','".$postData['template_title']."','".$postData['template_body']."')";
                $DB->queryOrDie($query, $DB->error());
            }else{
                $DB->update('glpi_plugin_protocolsmanager_mails_templates',
                    [
                        'template_title'  => $postData['template_title'],
                        'template_body'  => $postData['template_body'],
                    ],
                    [
                        'id' => 1
                    ]
                );
            }


            Session::addMessageAfterRedirect(__('Template email updated','protocolsmanager'));
        }catch(Exception $e){
            Session::addMessageAfterRedirect(__('Error - Template email not updated','protocolsmanager'), 'ERROR', true);
        }
    }
}

?>