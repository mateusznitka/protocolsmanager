<?php

class SignProtocol {
	private int $idUser;
	private int $idProtocol;
	private bool $emailCode;
	private int $confirmCode;
	private $db;
	
	/**
	 * @param $post
	 */
	public function trySaveAction($post): void {
		global $DB;
		$this->idUser = $post['user_id'];
		$this->idProtocol = $post['protocols_id'];
		$this->db = $DB;
		$this->checkEmailCode();
		if ($this->emailCode == true) {
			$this->insertConfirmCode();
			$this->sendEmailCode();
			$this->showFormconfirm();
		} else {
			$this->signdocument();
		}
	}
	
	/**
	 * @return void
	 */
	private function checkEmailCode(): void {
		global $DB;

		$query = 'SELECT * FROM `glpi_plugin_protocolsmanager_settings` WHERE id = 1';
		$this->emailCode = $DB->request($query)->current()['mail_confirm_on'];
	}
	
	private function sendEmailCode(): void {
		global $CFG_GLPI, $DB;
		
		$nmail = new GLPIMailer();
		$sender=Config::getEmailSender(null,true);
		$nmail->SetFrom($sender["email"], $sender["name"], false);
		$email_subject = __('GLPI Protocols Manager confirm code','protocolsmanager');
		$email_content = __('confirm code - ','protocolsmanager') . $this->confirmCode;
		
		$req = $DB->request(
			'glpi_useremails',
			['users_id' => $this->idUser, 'is_default' => 1]);
		$owner_email = $req->current()["email"];
		if (!empty($owner_email)) {
			$nmail->AddAddress($owner_email);
			$nmail->IsHtml(true);
			$nmail->Subject = $email_subject;
			$nmail->Body = nl2br(stripcslashes($email_content));
			if (!$nmail->Send()) {
				Session::addMessageAfterRedirect(__('Failed to send email'), false, ERROR);
			} else {
				Session::addMessageAfterRedirect(sprintf(__('Email sent to %s','protocolsmanager'), $owner_email));
			}
		} else {
			Session::addMessageAfterRedirect(__('Can not confirm, add e-mail','protocolsmanager'), false, ERROR);
		}
	}
	
	private function signdocument($idProtocol = null, $idUser = null) {
		global $DB;

		$usID = $idUser == null ? $this->idUser : $idUser;
		$prID = $idProtocol == null ? $this->idProtocol : $idProtocol;

		try {
			$date = new DateTime();
			$DB->update('glpi_plugin_protocolsmanager_receipt', [
				'confirmed' => 1,
				'modified' => $date->format('Y-m-d H:i:s')
				],
				[
					'profile_id' => $usID,
					'protocol_id' => $prID
				]
			);
			Session::addMessageAfterRedirect(__('Document signed','protocolsmanager'), false, 0);
			Html::redirect($CFG_GLPI["root_doc"]);
		} catch (Exception $e) {
			Session::addMessageAfterRedirect(__('Error - Document not signed','protocolsmanager'), false, 1);
			Html::redirect($CFG_GLPI["root_doc"]);
		}
	}
	
	private function insertConfirmCode() {
		global $DB;

		$date = new DateTime();
		$this->confirmCode = rand(1000, 9999);
		$DB->insert('glpi_plugin_protocolsmanager_confirm', [
				'id_user' => $this->idUser,
				'id_protocol' => $this->idProtocol,
				'code_confirm' => $this->confirmCode,
				'modified' => $date->format('Y-m-d H:i:s')
			]
		);
	}
	
	private function showFormconfirm($idProtocol = null, $idUser = null) {
		global $CFG_GLPI;

		$prID = $idProtocol == null ? $this->idProtocol : $idProtocol;
		$usID = $idUser == null ? $this->idUser : $idUser;
		
		echo "
			<form method='post' action='" . $CFG_GLPI["root_doc"] . "/plugins/protocolsmanager/front/protocols.form.php' >
				<input type='hidden' name='menu_mode' value='e'>
				<input type='hidden' name='protocols_id' value='" . $prID . "' >
				<input type='hidden' name='user_id' value='" . $usID . "' >
				<tr class='tab_bg_1' style='padding: 20px;'>
					<td class='center' width='7%' style='padding-top: 20px;'>
						" . __('Check your email and write code','protocolsmanager') . "
					</td>
					<td class='center' width='7%'>
					<input type='text' name='code' >
					</td>
					<td class='center' width='7%'>
						<input type='submit' name='sign_protocols_submit_confirm' class='submit' value='" . __('Submit','protocolsmanager') . "'>
					</td>
				</tr>";
			Html::closeForm();
	}
	
	public function checkConfirmationCode($data) {
		global $DB;

		$date = new DateTime();
		$toDelete = $date->modify('-5 minutes')->format('Y-m-d H:i:s');
		$query = 'DELETE FROM `glpi_plugin_protocolsmanager_confirm` WHERE modified < "' . $toDelete . '"';
		$DB->query($query);
		$query2 = 'SELECT COUNT(id) as cnt FROM `glpi_plugin_protocolsmanager_confirm`
					WHERE `id_user` = ' . $data['user_id'] . '
					AND `id_protocol` = ' . $data['protocols_id'] . '
					AND `code_confirm` = ' . $data['code'];
		if ($DB->request($query2)->current()['cnt'] == 1) {
			$this->signdocument($data['protocols_id'], $data['user_id']);
		} else {
			$this->showFormconfirm($data['protocols_id'], $data['user_id']);
			echo '<span style="color: red">' . __('wrong confirmation code','protocolsmanager') . '</span>';
		}
	}

	public function signdocumentByEmail($idProtocol = null, $idUser = null) {
		global $DB;
		
		$usID = $idUser == null ? $this->idUser : $idUser;
		$prID = $idProtocol == null ? $this->idProtocol : $idProtocol;
		$date = new DateTime();
		$DB->update('glpi_plugin_protocolsmanager_receipt', [
				'confirmed' => 1,
				'modified' => $date->format('Y-m-d H:i:s')
			],
			[
				'profile_id' => $usID,
				'protocol_id' => $prID
			]
		);
	}
}