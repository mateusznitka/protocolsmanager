<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

class PluginProtocolsmanagerGenerate extends CommonDBTM {
	
		function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
			return self::createTabEntry('Protocols manager');
		}

		
		static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {
			global $DB, $CFG_GLPI;
			
			$tab_access = self::checkRights();
		
			if ($tab_access) {
				$PluginProtocolsmanagerGenerate = new self();
				$PluginProtocolsmanagerGenerate->showContent($item);	
			} else {
				echo "<div align='center'><br><img src='".$CFG_GLPI['root_doc']."/pics/warning.png'><br>".__("Access denied")."</div>";
			}
		}
		
		static function getIcon() {
			return 'ti ti-clipboard-list';
		}

		static function checkRights() {
			return Session::haveRight('plugin_protocolsmanager_tab', READ);
		}
		
		
		//show plugin content
		function showContent($item) {
			global $DB, $CFG_GLPI;
			$id = $item->getField('id');
			$type_user   = $CFG_GLPI['linkuser_types'];
			$field_user  = 'users_id';
			$rand = mt_rand();
			
			$counter = 0;

			$Owner = new User();
			$Owner->getFromDB($id);
			$Author = new User();
			$Author->getFromDB(Session::getLoginUserID());
			$owner = $Owner->getName();
			$author = $Author->getName();

			echo "<div class='card mb-4'>";
			echo "<div class='card-header'><i class='ti ti-clipboard-plus'></i> ".__('Generate new document')."</div>";
			echo "<div class='card-body'>";
			echo "<form method='post' name='protocolsmanager_form$rand' id='protocolsmanager_form$rand' action=\"" . $CFG_GLPI["root_doc"] . "/plugins/protocolsmanager/front/generate.form.php\">";
			echo "<p class='text-muted small mb-2'><strong>1.</strong> ".__('Choose a template and add an optional note')."</p>";
			echo "<div class='row mb-3'>";
			echo "<div class='col-md-4'>";
			echo "<label class='form-label'>".__('Template')."</label>";
			echo "<select name='list' class='form-select'>";
				foreach ($doc_types = $DB->request(['FROM' => 'glpi_plugin_protocolsmanager_configs', 'FIELDS' => ['glpi_plugin_protocolsmanager_configs' => ['id', 'name']]]) as $uid => $list) {
					echo '<option value="';
					echo $list["id"];
					echo '">';
					echo $list["name"];
					echo '</option>';
				}
			echo "</select>";
			echo "</div>";
			echo "<div class='col-md-5'>";
			echo "<label class='form-label'>".__('Note')." <small class='text-muted'>(" . __('visible in generated documents list') . ")</small></label>";
			echo "<input type='text' name='notes' class='form-control' placeholder='".__('Optional')."'>";
			echo "</div>";
			echo "</div>"; // row
			echo "<p class='text-muted small mb-2'><strong>2.</strong> ".__('Select assets to include in the document')."</p>";
			echo "<table class='table table-hover table-sm' id='additional_table'>";
			echo "<thead>";
			$header = "<tr><th width='10'>" . Html::getCheckAllAsCheckbox('additional_table') . "</th>";
			$header .= "<th class='text-uppercase'>".__('Type')."</th>";
			$header .= "<th class='text-uppercase'>".__('Manufacturer');
			$header .= " ".__('Model')."</th>";
			$header .= "<th class='text-uppercase'>".__('Name')."</th>";
			$header .= "<th class='text-uppercase'>".__('Serial number')."</th>";
			$header .= "<th class='text-uppercase'>".__('Inventory number')."</th>";
			$header .= "<th class='text-uppercase'>".__('Comments')."</th></tr>";
			echo $header;
			echo "</thead><tbody>";
			
			foreach ($type_user as $itemtype) {
				if (!($item = getItemForItemtype($itemtype))) {
					continue;
				}
				if ($item->canView()) {
					$itemtable = getTableForItemType($itemtype);
					$iterator_params = [
					   'FROM'   => $itemtable,
					   'WHERE'  => [$field_user => $id]
					];
					if ($item->maybeTemplate()) {
					   $iterator_params['WHERE']['is_template'] = 0;
					}
					
					if ($item->maybeDeleted()) {
					   $iterator_params['WHERE']['is_deleted'] = 0;
					}
					
					$item_iterator = $DB->request($iterator_params);
					$type_name = $item->getTypeName();
					
					foreach ($item_iterator as $data) {
						$cansee = $item->can($data["id"], READ);
						   $link   = $data["name"];
							if ($cansee) {
								$link_item = $item::getFormURLWithID($data['id']);
								if (!empty($_SESSION["glpiis_ids_visible"]) || empty($link)) {
								 $link = sprintf(__('%1$s (%2$s)'), $link, $data["id"]);
								}
								$link = "<a href='".$link_item."'>".$link."</a>";
							}
							$linktype = "";
							if ($data[$field_user] == $id) {
								$linktype = self::getTypeName(1);
							}
							
							echo "<tr class='tab_bg_1'>";
							echo "<td width='10'>";
							echo "<input type='checkbox' name='number[]' value='$counter' class='form-check-input massive_action_checkbox' checked>";
							echo "</td>";	
							echo "<td>$type_name</td>";
							echo "<td>";
							
							if (isset($data["manufacturers_id"]) && !empty($data["manufacturers_id"])) {
								
								$man_id = $data["manufacturers_id"];
														
								$man_name = '';
								foreach ($DB->request(['FROM' => 'glpi_manufacturers', 'WHERE' => ['id' => $man_id]]) as $row) {
									$man_name = $row["name"];
									break;
								}
								
								$modeltypes = ["computer", "phone", "monitor", "networkequipment", "printer", "peripheral"];
								$mod_name = '';
								
								foreach($modeltypes as $prefix) {
									if(isset($data[$prefix.'models_id']) && !empty($data[$prefix.'models_id'])) {
										$mod_id = $data[$prefix.'models_id'];
										
										foreach ($DB->request(['FROM' => 'glpi_'.$prefix.'models', 'WHERE' => ['id' => $mod_id]]) as $row2) {
											$mod_name = $row2["name"];
											break;
										}
									}
								}
								
								$man_name = explode(' ',trim($man_name))[0];
								echo $man_name.' '.$mod_name;
								
							} 
							else {
								echo '&nbsp;';
								$man_name = '';
								$mod_name = '';
							}
							echo "</td>";
							echo "<td>$link</td>";
							echo "<td>";
							
							if (isset($data["serial"]) && !empty($data["serial"])) {
								$serial = $data["serial"];
								echo $serial;
							} else {
								echo '&nbsp;';
								$serial = '';
							}
							
							echo "</td>";
							echo "<td>";
							
							if (isset($data["otherserial"]) && !empty($data["otherserial"])) {
								$otherserial = $data["otherserial"];
								echo $otherserial;
							} else {
								echo '&nbsp;';
								$otherserial = '';
							}
							
							echo "</td>";
							
							if (isset($data["name"]) && !empty($data["name"])) {
								$item_name = $data["name"];
							} else {
								$item_name = '';
							}

							echo "<td>";
							echo "<input type='hidden' name='type_name[]' value='$type_name'>";
							echo "<input type='hidden' name='man_name[]' value='$man_name'>";
							echo "<input type='hidden' name='mod_name[]' value='$mod_name'>";
							echo "<input type='hidden' name='serial[]' value='$serial'>";
							echo "<input type='hidden' name='otherserial[]' value='$otherserial'>";
							echo "<input type='hidden' name='item_name[]' value='$item_name'>";
							echo "<input type='text' name='comments[]' class='form-control form-control-sm'>";
							echo "</td>";
							echo "</tr>";

							
						$counter++;
					}
					
				}
				
			}				
				
				echo "</tbody></table>";
				echo "<div class='mt-2'>";
				echo "<button class='btn btn-sm btn-outline-secondary' id='addNewRow' type='button'><i class='ti ti-plus'></i> Add Custom Fields</button>";
				echo "</div>";
				echo "<div class='d-flex justify-content-end mt-3'>";
				echo "<button type='submit' name='generate' class='btn btn-primary'><i class='ti ti-file-plus'></i> ".__('Generate document')."</button>";
				echo "</div>";
				echo "<input type='hidden' name='owner' value='$owner'>";
				echo "<input type='hidden' name='author' value='$author'>";
				echo "<input type='hidden' name='user_id' value='$id'>";
				Html::closeForm();
				echo "</div>"; // card-body
				echo "</div>"; // card


				//send email modal
				echo "<div class='modal fade' id='emailModal' tabindex='-1'>";
				echo "<div class='modal-dialog'>";
				echo "<div class='modal-content'>";
				echo "<div class='modal-header'>";
				echo "<h5 class='modal-title'>".__('Send')." email</h5>";
				echo "<button type='button' class='btn-close' data-bs-dismiss='modal'></button>";
				echo "</div>";
				echo "<div class='modal-body'>";
				echo "<form method='post' action='".$CFG_GLPI["root_doc"]."/plugins/protocolsmanager/front/generate.form.php'>";
				echo "<input type='hidden' id='dialogVal' name='doc_id' value=''>";
				echo "<div class='mb-2'><input type='radio' name='send_type' id='manually' class='send_type' value='1'> <b>".__('Enter recipients manually')."</b></div>";
				echo "<textarea class='form-control man_recs mb-2' name='em_list' rows='2' placeholder='".__('Recipients (use ; to separate emails)')."'></textarea>";
				echo "<input type='text' class='form-control man_recs mb-2' name='email_subject' placeholder='".__('Subject')."'>";
				echo "<textarea class='form-control man_recs mb-3' name='email_content' rows='3' placeholder='".__('Content')."'></textarea>";
				echo "<div class='mb-2'><input type='radio' name='send_type' id='auto' class='send_type' value='2'> <b>".__('Select recipients from template')."</b></div>";
				echo "<select name='e_list' id='auto_recs' disabled='disabled' class='form-select mb-3'>";
				foreach ($DB->request(['FROM' => 'glpi_plugin_protocolsmanager_emailconfig']) as $uid => $list) {
					echo '<option value="';
					echo $list["recipients"]."|".$list["email_subject"]."|".$list["email_content"]."|".$list["send_user"];
					echo '">';
					echo $list["tname"]." - ".$list["recipients"];
					echo '</option>';
				}
				echo "</select>";
				echo "<input type='submit' name='send' class='btn btn-primary' value='".__('Send')."'>";
				echo "<input type='hidden' name='author' value='$author'>";
				echo "<input type='hidden' name='owner' value='$owner'>";
				echo "<input type='hidden' name='user_id' value='$id'>";
				Html::closeForm();
				echo "</div>";
				echo "</div>";
				echo "</div>";
				echo "</div>";
				
				echo "<div class='card mt-4'>";
				echo "<div class='card-header'><i class='ti ti-files'></i> ".__('Generated documents')."</div>";
				echo "<div class='card-body'>";
				echo "<form method='post' name='docs_form' action='".$CFG_GLPI["root_doc"]."/plugins/protocolsmanager/front/generate.form.php'>";
				echo "<div class='mb-2'><button type='submit' name='delete' class='btn btn-sm btn-outline-danger'><i class='ti ti-trash'></i> ".__('Delete')."</button></div>";
				echo "<table class='table table-hover table-sm' id='myTable'>";
				echo "<thead>";
				$header2 = "<tr><th width='10'>" . Html::getCheckAllAsCheckbox('myTable') . "</th>";
				$header2 .= "<th class='text-uppercase'>".__('Name')."</th>";
				$header2 .= "<th class='text-uppercase'>".__('Type')."</th>";
				$header2 .= "<th class='text-uppercase'>".__('Date')."</th>";
				$header2 .= "<th class='text-uppercase'>".__('File')."</th>";
				$header2 .= "<th class='text-uppercase'>".__('Creator')."</th>";
				$header2 .= "<th class='text-uppercase'>".__('Comment')."</th>";
				$header2 .= "<th class='text-uppercase'>".__('Send email')."</th></tr>";
				echo $header2;
				echo "</thead><tbody>";
				
				self::getAllForUser($id);
				echo "</tbody></table>";
				Html::closeForm();
				echo "</div>"; // card-body
				echo "</div>"; // card

				return true;
	
		}
		
		//show user's generated documents
		static function getAllForUser($id) {
			global $DB, $CFG_GLPI;
			
			$exports = [];
			$doc_counter = 0;
			
			foreach ($DB->request(['FROM' => 'glpi_plugin_protocolsmanager_protocols', 'WHERE' => ['user_id' => $id]]) as $export_data => $exports) {
					
					
					echo "<tr class='tab_bg_1'>";
					
					echo "<td>";
					echo "<input type='checkbox' name='docnumber[]' value='".$exports['document_id']."' class='form-check-input massive_action_checkbox'>";
					echo "</td>";
					
					echo "<td>";
					$Doc = new Document();
					$Doc->getFromDB($exports['document_id']);
					echo $Doc->getLink();
					echo "</td>";
					
					echo "<td>";
					echo $exports['document_type'];
					echo "</td>";
					
					echo "<td>";
					echo $exports['gen_date'];
					echo "</td>";
					
					echo "<td>";
					echo $Doc->getDownloadLink();
					echo "</td>";
					
					echo "<td>";
					echo $exports['author'];
					echo "</td>";
					
					echo "<td>";
					echo $Doc->getField("comment");
					echo "</td>";					
					
					echo "<td>";
					echo "<span class='docid' style='display:none'>".$exports['document_id']."</span>";
					echo "<a class='btn btn-sm btn-outline-primary openDialog' href='#'>".__('Send')."</a>";
					echo "</td>";

					
					echo "</tr>";

					$doc_counter++;
				}
		}

		
		//make PDF and save to DB
		static function makeProtocol() {
			
			global $DB, $CFG_GLPI;
			
			if (empty($_POST['number'])) {
				Session::addMessageAfterRedirect(__('No items selected'), false, ERROR);
				Html::back();
				return;
			}
			$number = $_POST['number'];
			$type_name = $_POST['type_name'];
			$man_name = $_POST['man_name'];
			$mod_name = $_POST['mod_name'];
			$serial = $_POST['serial'];
			$otherserial = $_POST['otherserial'];
			$item_name = $_POST['item_name'];
			$owner = $_POST['owner'];
			$author = $_POST['author'];
			$doc_no = $_POST['list'];
			$id = $_POST['user_id'];
			$notes = $_POST['notes'];
			
			$prot_num = self::getDocNumber();
			
			foreach ($DB->request(['FROM' => 'glpi_plugin_protocolsmanager_configs', 'WHERE' => ['id' => $doc_no]]) as $row) {
				$content = nl2br($row["content"]);
				$content = str_replace("{cur_date}", date("d.m.Y"), $content);
				$content = str_replace("{owner}", $owner, $content);
				$content = str_replace("{admin}", $author, $content);
				$upper_content = nl2br($row["upper_content"]);
				$upper_content = str_replace("{cur_date}", date("d.m.Y"), $upper_content);
				$upper_content = str_replace("{owner}", $owner, $upper_content);
				$upper_content = str_replace("{admin}", $author, $upper_content);
				$footer = nl2br($row["footer"]);
				$title = $row["name"];
				$full_img_name = $row["logo"];
				$font = $row["font"];
				$fontsize = $row["fontsize"];
				$city = $row["city"];
				$serial_mode = $row["serial_mode"];
				$orientation = $row["orientation"];
				$breakword = $row["breakword"];
				$email_mode = $row["email_mode"];
				$email_template = $row["email_template"];
				$header_color = $row["header_color"] ?? '#dee2e6';
				break;
			}
			
			foreach ($DB->request(['FROM' => 'glpi_plugin_protocolsmanager_emailconfig', 'WHERE' => ['id' => $email_template]]) as $row) {
				$send_user = $row["send_user"];
				$email_subject = $row["email_subject"];
				$email_content = $row["email_content"];
				$recipients = $row["recipients"];
				break;
			}
			
			$comments = $_POST['comments'];
		
			if (!isset($font) || empty($font)) {
				$font = 'DejaVu Sans';
			}

			if (!isset($fontsize) || empty($fontsize)) {
				$fontsize = '9';
			}				
			
			if (!isset($city) || empty($city)) {
				$city = '';
			}			
			
			if (!isset($email_content) || empty($email_content)) {
				$email_content = '';
			}
			$email_content = str_replace("{owner}", $owner, $email_content);
			$email_content = str_replace("{admin}", $author, $email_content);
			$email_content = str_replace("{cur_date}", date("d.m.Y"), $email_content);
			
			if (!isset($email_subject) || empty($email_subject)) {
				$email_subject = '';
			}
			
			$email_subject = str_replace("{owner}", $owner, $email_subject);
			$email_subject = str_replace("{admin}", $author, $email_subject);
			$email_subject = str_replace("{cur_date}", date("d.m.Y"), $email_subject);

			if (!isset($recipients) || empty($recipients)) {
				$recipients = '';
			}			
			
			//change margin if no image
			if (!isset($full_img_name) || empty($full_img_name)) {
				$backtop = "20mm";
				$islogo = 0;
			} else {
				$logo = GLPI_ROOT.'/files/_pictures/'.$full_img_name;
				$backtop = "40mm";
				$islogo = 1;
			}
			
			
			ob_start();
			include dirname(__FILE__).'/template.php';
			$html = ob_get_clean();

			$fd = GLPI_ROOT . '/plugins/protocolsmanager/fonts/';
			$html = str_replace('</head>', "<style>
				@font-face{font-family:'Roboto';src:url('file://{$fd}Roboto-Regular.ttf');font-weight:normal;}
				@font-face{font-family:'Roboto';src:url('file://{$fd}Roboto-Bold.ttf');font-weight:bold;}
				@font-face{font-family:'Noto Serif';src:url('file://{$fd}NotoSerif-Regular.ttf');font-weight:normal;}
				@font-face{font-family:'Noto Serif';src:url('file://{$fd}NotoSerif-Bold.ttf');font-weight:bold;}
			</style></head>", $html);

			$font_cache_dir = GLPI_UPLOAD_DIR . '/protocolsmanager/';
			if (!is_dir($font_cache_dir)) {
				mkdir($font_cache_dir, 0755, true);
			}
			$options = new Options();
			$options->set('defaultFont', $font);
			$options->setChroot(GLPI_ROOT);
			$options->setFontDir(GLPI_ROOT . '/plugins/protocolsmanager/fonts/');
			$options->setFontCache($font_cache_dir);
			$html2pdf = new Dompdf($options);
			$html2pdf->loadHtml($html);
			$html2pdf->setPaper('A4', $orientation);
			$html2pdf->render();
			
			$doc_name = $prot_num."-".date('mdY').'.pdf';	
			$output = $html2pdf->output();
			file_put_contents(GLPI_UPLOAD_DIR .'/'.$doc_name, $output);
			
			$doc_id = self::createDoc($doc_name, $notes, $id);
			
			if ($email_mode == 1) {
				
				self::sendMail($doc_id, $send_user, $email_subject, $email_content, $recipients, $id);
				
			}
			
			$gen_date = date('Y-m-d H:i:s');
			
			$DB->insert('glpi_plugin_protocolsmanager_protocols', [
				'name' => $doc_name,
				'gen_date' => $gen_date,
				'author' => $author,
				'user_id' => $id,
				'document_id' => $doc_id,
				'document_type' => $title
				]
			);
				
		}
		
		
		static function getDocNumber() {
			global $DB;
			
			foreach ($DB->request(['FROM' => 'glpi_plugin_protocolsmanager_protocols', 'ORDER' => 'id DESC', 'LIMIT' => 1]) as $row) {
				return $row['id'] + 1;
			}
			return 1;
		}
		
		//create GLPI document
		static function createDoc($doc_name, $notes, $id) {
			global $DB, $CFG_GLPI;
			
			$entity = 0;
			foreach ($DB->request(['FROM' => 'glpi_users', 'WHERE' => ['id' => $id]]) as $row) {
				$entity = $row["entities_id"];
				break;
			}
			
			$input = [];
			$doc = new Document();
			$input["entities_id"] = $entity;
			$input["name"] = date('mdY_Hi');
			$input["upload_file"] = $doc_name;
			$input["documentcategories_id"] = 0;
			$input["mime"] = "application/pdf";
			$input["date_mod"] = date("Y-m-d H:i:s");
			$input["users_id"] = Session::getLoginUserID();
			$input["comment"] = $notes;
			$doc->check(-1, CREATE, $input);
			$document_id = $doc->add($input);

			$document_item = new Document_Item();
			$document_item->add([
				'documents_id' => $document_id,
				'itemtype'     => 'User',
				'items_id'     => $id,
				'entities_id'  => $entity,
				'is_recursive' => 0,
				'date_mod'     => date("Y-m-d H:i:s"),
				'users_id'     => Session::getLoginUserID(),
			]);

			return $document_id;
		}
		
		//delete selected documents
		static function deleteDocs() {
			global $DB, $CFG_GLPI;
			
			$docnumber = $_POST['docnumber'];
			
			foreach ($docnumber as $del_key) {
				
				$DB->delete(
					'glpi_plugin_protocolsmanager_protocols', [
						'document_id' => $del_key
					]
				);
				
				$doc = new Document();
				$doc->getFromDB($del_key);
				$doc->delete(['id' => $del_key], true);
			}
		}
		
		//send mail notification
		static function sendMail($doc_id, $send_user, $email_subject, $email_content, $recipients, $id) {
			
			global $CFG_GLPI, $DB;
			$nmail = new GLPIMailer();
			
			$nmail->setFrom($CFG_GLPI["admin_email"], $CFG_GLPI["admin_email_name"], false);
			
			$recipients_array = explode(';',$recipients);
			
			$path = '';
			$filename = '';
			foreach ($DB->request(['FROM' => 'glpi_documents', 'WHERE' => ['id' => $doc_id]]) as $row) {
				$path = $row["filepath"];
				$filename = $row["filename"];
				break;
			}

			$fullpath = GLPI_ROOT."/files/".$path;

			$owner_email = '';
			foreach ($DB->request(['FROM' => 'glpi_useremails', 'WHERE' => ['users_id' => $id, 'is_default' => 1]]) as $row2) {
				$owner_email = $row2["email"];
				break;
			}

			if ($send_user == 1) {
				$nmail->addAddress($owner_email);
			}

			foreach($recipients_array as $recipient) {
				$nmail->addAddress($recipient);
			}

			$nmail->Subject = $email_subject;
			$nmail->addAttachment($fullpath, $filename);
			$nmail->Body = $email_content;
			
			if (!$nmail->send()) {
				Session::addMessageAfterRedirect(__('Failed to send email'), false, ERROR);
				return false;
			} else {
				
				if ($send_user == 1) {
					Session::addMessageAfterRedirect(__('Email sent')." to ".implode(", ", $recipients_array)." ".$owner_email);
					return true;
				} else {
					Session::addMessageAfterRedirect(__('Email sent')." to ".implode(", ", $recipients_array));
					return true;
				}
			}
			
			
		}
		
		static function sendOneMail($id) {
			
			global $CFG_GLPI, $DB;
			
			$nmail = new GLPIMailer();
			
			$nmail->setFrom($CFG_GLPI["admin_email"], $CFG_GLPI["admin_email_name"], false);
			
			$doc_id = $_POST["doc_id"];
			
			//if email is filled manually
			if (isset($_POST["em_list"])) {
				$recipients = $_POST["em_list"];
			}
			
			if (isset($_POST["email_subject"])) {
				$email_subject = $_POST["email_subject"];
			} else {
				$email_subject = "GLPI Protocols Manager mail";
			}
			
			if (isset($_POST['email_content'])) {
				$email_content = $_POST['email_content'];
			} else {
				$email_content = ' ';
			}
			
			//if email is from template
			if (isset($_POST['e_list'])) {
				$result = explode('|', $_POST['e_list']);
				$recipients = $result[0];
				$email_subject = $result[1];
				$email_content =  $result[2];
				$send_user =  $result[3];
			}
			
			$owner = $_POST["owner"];
			$author = $_POST["author"];
			
			$email_content = str_replace("{owner}", $owner, $email_content);
			$email_content = str_replace("{admin}", $author, $email_content);
			$email_content = str_replace("{cur_date}", date("d.m.Y"), $email_content);
			
			$email_subject = str_replace("{owner}", $owner, $email_subject);
			$email_subject = str_replace("{admin}", $author, $email_subject);
			$email_subject = str_replace("{cur_date}", date("d.m.Y"), $email_subject);
			
			$recipients_array = explode(';',$recipients);

			$owner_email = '';
			foreach ($DB->request(['FROM' => 'glpi_useremails', 'WHERE' => ['users_id' => $id, 'is_default' => 1]]) as $row2) {
				$owner_email = $row2["email"];
				break;
			}
			
			if ($send_user == 1) {
				$nmail->addAddress($owner_email);
			}			
			
			foreach($recipients_array as $recipient) {
				
				$nmail->addAddress($recipient); //do konfiguracji
			}			
			
			$path = '';
			$filename = '';
			foreach ($DB->request(['FROM' => 'glpi_documents', 'WHERE' => ['id' => $doc_id]]) as $row) {
				$path = $row["filepath"];
				$filename = $row["filename"];
				break;
			}

			$fullpath = GLPI_ROOT."/files/".$path;

			$nmail->isHTML(true);
			
			$nmail->Subject = $email_subject; //do konfiguracji
			$nmail->addAttachment($fullpath, $filename);
			$nmail->Body = nl2br(stripcslashes($email_content));
			
			if (!$nmail->send()) {
				Session::addMessageAfterRedirect(__('Failed to send email'), false, ERROR);
				return false;
			} else {
				
				if ($send_user == 1) {
					Session::addMessageAfterRedirect(__('Email sent')." to ".implode(", ", $recipients_array)." ".$owner_email);
					return true;
				} else {
					Session::addMessageAfterRedirect(__('Email sent')." to ".implode(", ", $recipients_array));
					return true;
				}
			}
			
		}
		

}


?>

<script>


$(function(){
	$(".man_recs").prop('disabled', true);
	$('.send_type').click(function(){
		if($(this).prop('id') == "manually"){
			$(".man_recs").prop('disabled', false);
			$("#auto_recs").prop('disabled', true);
		}else{
			$(".man_recs").prop('disabled', true);
			$("#auto_recs").prop('disabled', false);
		}
	});
});

$(function(){
	$("#myTable").on('click','.openDialog',function(){
		var currentRow=$(this).closest("tr");
		var docid=currentRow.find(".docid").html();
		$('#dialogVal').val(docid);
		bootstrap.Modal.getOrCreateInstance(document.getElementById('emailModal')).show();
	});
});


$(function() {

	var counter = $('input[name="number[]"]').length;
	
	var ctr = 0;
	
	    $("#addNewRow").on("click", function () {
        var newRow = $("<tr class='tab_bg_1'>");
        var cols = "";
		
		cols += '<td style="vertical-align: middle; text-align: center;"><button type="button" class="btn btn-sm btn-outline-danger ibtnDel" style="padding: 0; width: 1.75rem; height: 1.75rem;"><i class="ti ti-trash"></i></button></td>';
        cols += '<td><input type="text" class="form-control form-control-sm" name="type_name[]"></td>';
        cols += '<td><input type="text" class="form-control form-control-sm" name="man_name[]"><input type="hidden" name="mod_name[]" value=""></td>';
        cols += '<td><input type="text" class="form-control form-control-sm" name="item_name[]"></td>';
        cols += '<td><input type="text" class="form-control form-control-sm" name="serial[]"></td>';
        cols += '<td><input type="text" class="form-control form-control-sm" name="otherserial[]"></td>';
        cols += '<td><input type="text" class="form-control form-control-sm" name="comments[]"><input type="hidden" name="number[]" value="' + counter + '"></td>';

        newRow.append(cols);
        $("#additional_table").append(newRow);
		counter++;
        ctr++;
    });
	
    $("#additional_table").on("click", ".ibtnDel", function (event) {
        $(this).closest("tr").remove();       
        ctr -= 1
    });


});



</script>