<?php

class PluginProtocolsmanagerConfig extends CommonDBTM
{
	
	function showFormProtocolsmanager() {
		global $CFG_GLPI, $DB;
		
		if (isset($_POST["edit_id"])) {
			
			$edit_id = $_POST['edit_id'];
			$mode = $edit_id;
			
			$req = $DB->request(
				'glpi_plugin_protocolsmanager_config',
				['id' => $edit_id ]);
				
			if ($row = $req->next()) {
				$template_content = $row["content"];
				$template_footer = $row["footer"];
				$template_name = $row["name"];
				$font = $row["font"];
				$city = $row["city"];
				$logo = $row["logo"];
			}
			
		}
		else {
			$template_content = '';
			$template_footer = '';
			$template_name = '';
			$font = '';
			$city = '';
			$mode = 0;
		}
			
		echo "<div class='center'>";
		echo "<table class='tab_cadre_fixe' style='width:90%;'>";
		echo "<td style='font-size:12pt; font-weight:bold; text-align:center;'>Protocols Manager - ".__('Templates')."</td>";
		echo "</table>";
		
		echo "<form name='form' action='config.form.php' method='post'  enctype='multipart/form-data'>";
		echo "<input type='hidden' name='MAX_FILE_SIZE' value=1948000>";
		echo "<input type='hidden' name='mode' value='$mode'>";
		echo "<table class='tab_cadre_fixe'>";
		echo "<tr><th colspan='2'>".__('Create')." ".__('template')."</th></tr>";
		echo "<tr><td>".__('Template name')."</td><td><input type='text' name='template_name' style='width:80%;' value='$template_name'></td></tr>";
		
		if(!isset($font)) {
			$font='freesans';
		}
		
		$fonts = array('freesans' => 'Free Sans',
						'freemono' => 'Free Mono', 
						'freeserif' => 'Free Serif', 
						'dejavusans' => 'Dejavusans', 
						'dejavuserif' => 'Dejavuserif', 
						'helvetica' => 'Helvetica');
						
		echo "<tr><td>Font</td><td><select name='font'>";
				foreach($fonts as $code => $fontname) {
					echo "<option value='".$code."' ";
					if ($code == $font) {
						echo " selected";
					}
					echo ">".$fontname."</option>";
				}
		echo "</select></td></tr>";
		echo "<tr><td>".__('City')."</td><td><input type='text' name='city' style='width:80%;' value='$city'></td></tr>";
		echo "<tr><td>".__('Content')."</td><td class='middle'><textarea style='width:80%; height:100px;' cols='50' rows'8' name='template_content'>".$template_content."</textarea></td></tr>";
		echo "<tr><td>".__('Footer')."</td><td><textarea style='width:80%; height:100px;' cols='45' rows'4' name='footer_text'>".$template_footer."</textarea></td></tr>";
		echo "<tr><td>".__('Logo')."</td><td><input type='file' name='logo' accept='image/png, image/jpeg'>";
		if (isset($logo)) {
			$full_img_name = GLPI_ROOT.'/files/_pictures/'.$logo;
			$img_type = pathinfo($full_img_name, PATHINFO_EXTENSION);
			$img_data = file_get_contents($full_img_name);
			$base64 = 'data:image/'.$img_type.';base64,'.base64_encode($img_data);
			echo "&nbsp&nbsp<img src = ".$base64." style='height:50px; width:auto;'>";
		}
		echo "</td></tr>";
		echo "<td colspan='2' style='text-align:center;'><input type='submit' name='save' class='submit'></td></table>";
		Html::closeForm();
		echo "</div><br>";
		self::showConfigs();

		
	}
	
	static function saveConfigs() {
		global $DB, $CFG_GLPI;
		
		$template_name = $_POST['template_name'];
		$template_content = $_POST['template_content'];
		$template_footer = $_POST['footer_text'];
		$font = $_POST["font"];
		$city = $_POST["city"];
		$mode = $_POST["mode"];
		$full_img_name = self::uploadImage();
		
		if ($mode == 0) {
			
			$DB->insert('glpi_plugin_protocolsmanager_config', [
				'name' => $template_name,
				'content' => $template_content,
				'footer' => $template_footer,
				'logo' => $full_img_name,
				'font' => $font,
				'city' => $city
				]
			);
		}
		
		if ($mode != 0) {
			
			if (isset($full_img_name)) {
				
				$DB->update('glpi_plugin_protocolsmanager_config', [
						'name' => $template_name,
						'content' => $template_content,
						'footer' => $template_footer,
						'logo' => $full_img_name,
						'font' => $font,
						'city' => $city
					], [
						'id' => $mode
					]
				);
			}
			else {
				
				$DB->update('glpi_plugin_protocolsmanager_config', [
						'name' => $template_name,
						'content' => $template_content,
						'footer' => $template_footer,
						'font' => $font,
						'city' => $city
					], [
						'id' => $mode
					]
				);
			}
		}
			
	}
	
	static function showConfigs() {
		global $DB, $CFG_GLPI;
		$configs = [];
		
		echo "<div class='spaced'>";
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
						Html::closeForm();	
						echo "<td class='center' width='7%'><form method='post' action='config.form.php'><input type='hidden' value='$conf_id' name='conf_id'><input type='submit' name='delete' value=".__('Delete')." class='submit'></td></tr>";
						Html::closeForm();				
			}
		echo "</table></div>";
	}
	
	static function uploadImage() {
		global $DB, $CFG_GLPI;;
		
		if($_FILES['logo']['name']) {
			
			if (!$_FILES['logo']['error']) {
				
				if ($_FILES['logo']['type'] = 'image/jpeg' || $_FILES['logo']['type'] = 'image/png' || $_FILES['logo']['type'] = 'image/jpg') {
					
					$img_name = "logo".time();
					$ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
					$full_img_name = $img_name.'.'.$ext;
					$img_path = GLPI_ROOT.'/files/_pictures/'.$full_img_name;
					
					move_uploaded_file($_FILES['logo']['tmp_name'], $img_path);
					
					return $full_img_name;
					
				}
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


}

?>