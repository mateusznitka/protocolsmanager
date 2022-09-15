<?php
class MailReminder
{

	private $settingsInfo;

	public function __construct()
	{
		$this->settingsInfo = $this->setDataSettings();
	}

	public function send()
	{
		if ($this->settingsInfo['reminder_on'] && $this->settingsInfo['how_often_remind']) {
			$data = $this->setDataToSendEmailReminderToAdmins();
			foreach ($data as $dt) {
				$this->sendMail($dt);
			}
		}
	}

	private function setDataToSendEmailReminderToAdmins(): array
	{
		$result = [];
		$dates = $this->setDate();
		$firstReminder = $this->getDataReminder($dates['first_remind_from'], $dates['first_remind_to']);
		$secondReminder = $this->getDataReminder($dates['second_remind_from'], $dates['second_remind_To']);
		$userReminder = $this->setDataForSendEmailToUSer($dates);
		if (!empty($firstReminder) && !empty($this->settingsInfo['first_emial_reminder'])) {
			$first_remind = [
				'id_user' => '',
				'email' => $this->settingsInfo['first_emial_reminder'],
				'conntent' => $this->createBodyMessageForProtocolsList($firstReminder),
			];
			array_push($result, $first_remind);
		}
		if (!empty($secondReminder) && !empty($this->settingsInfo['second_emial_reminder'])) {
			$second_remind = [
				'id_user' => '',
				'email' => $this->settingsInfo['second_emial_reminder'],
				'conntent' => $this->createBodyMessageForProtocolsList($secondReminder),
			];
			array_push($result, $second_remind);
		}
		if (!empty($userReminder)) {
			foreach ($userReminder as $usr) {
				$second_remind = [
					'id_user' => $usr['profile_id'],
					'email' => '',
					'conntent' => $this->createBodyMessageForUser($usr),
				];
				array_push($result, $second_remind);
			}
		}
		return $result;
	}

	private function getDataReminder($dateFrom, $dateTo): array
	{
		global $DB;
		$result = [];

		$req = $DB->request(
			[
				'SELECT' => [
					'realname',
					'firstname',
					'email',
					'modified',
					'glpi_plugin_protocolsmanager_protocols' => 'name AS protocol_name',
				],
				'FROM' => 'glpi_plugin_protocolsmanager_receipt',
				'LEFT JOIN' => [
					'glpi_users' => [
						['FKEY' => [
							'glpi_plugin_protocolsmanager_receipt' => 'profile_id',
							'glpi_users' => 'id']],
					],
					'glpi_useremails' => [
						['FKEY' => [
							'glpi_plugin_protocolsmanager_receipt' => 'profile_id',
							'glpi_useremails' => 'users_id']],
					],
					'glpi_plugin_protocolsmanager_protocols' => [
						['FKEY' => [
							'glpi_plugin_protocolsmanager_receipt' => 'protocol_id',
							'glpi_plugin_protocolsmanager_protocols' => 'document_id']],
					],
				],
				'WHERE' =>
				[
					['modified' => ['>=', $dateFrom],
						'AND' => ['modified' => ['<=', $dateTo]]],
					'confirmed' => 0,
				],

			]
		);
		foreach ($req as $id => $res) {
			array_push($result, $res);
		}
		return $result;

	}

	private function setDataForSendEmailToUSer($dates) //: array

	{
		global $DB;
		$result = [];

		$req = $DB->request(
			[
				'SELECT' => [
					'realname',
					'firstname',
					'email',
					'modified',
					'glpi_plugin_protocolsmanager_protocols' => 'name AS protocol_name',
					'profile_id',
				],
				'FROM' => 'glpi_plugin_protocolsmanager_receipt',
				'LEFT JOIN' => [
					'glpi_users' => [
						['FKEY' => [
							'glpi_plugin_protocolsmanager_receipt' => 'profile_id',
							'glpi_users' => 'id']],
					],
					'glpi_useremails' => [
						['FKEY' => [
							'glpi_plugin_protocolsmanager_receipt' => 'profile_id',
							'glpi_useremails' => 'users_id']],
					],
					'glpi_plugin_protocolsmanager_protocols' => [
						['FKEY' => [
							'glpi_plugin_protocolsmanager_receipt' => 'protocol_id',
							'glpi_plugin_protocolsmanager_protocols' => 'document_id']],
					],
				],
				'WHERE' =>
				[
					[
						'OR' => [
							[
								'modified' => ['>=', $dates['first_remind_from']],
								'AND' => ['modified' => ['<=', $dates['first_remind_to']]],
							],
							[
								'modified' => ['>=', $dates['second_remind_from']],
								'AND' => ['modified' => ['<=', $dates['second_remind_To']]],
							],
						],
					],
					'confirmed' => 0,
				],

			]
		);

		foreach ($req as $id => $res) {
			array_push($result, $res);
		}
		return $result;
	}

	private function setDataSettings(): array
	{
		global $DB;
		$req = $DB->request('glpi_plugin_protocolsmanager_settings');
		return $req->next();

	}

	private function createBodyMessageForProtocolsList($data): string
	{
		$dataContent = $data;
		$date = (new DateTime($dataContent[0]['modified']))->format('Y-m-d');
		$body_message = '<table style="border: solid 1px black">';
		$body_message .= '<tr style="border: solid 1px black" >' . __('Unsigned protocols list - ') . $date . '</tr>';
		$body_message .= '<tr style="border: solid 1px black">';
		$body_message .= '<th style="border: solid 1px black; padding: 15px;">' . __('user name') . '</th>';
		$body_message .= '<th style="border: solid 1px black; padding: 15px;">' . __('protocols name') . '</th>';
		$body_message .= '<th style="border: solid 1px black; padding: 15px;">' . __('user eamil') . '</th>';
		$body_message .= '</tr>';
		foreach ($data as $dt) {
			$body_message .= '<tr style="border: solid 1px black">';
			$body_message .= '<td style="border: solid 1px black; padding: 15px;">' . $dt['realname'] . ' ' . $dt['firstname'] . '</td>';
			$body_message .= '<td style="border: solid 1px black; padding: 15px;">' . $dt['protocol_name'] . '</td>';
			$body_message .= '<td style="border: solid 1px black; padding: 15px;">' . $dt['email'] . '</td>';
			$body_message .= '</tr>';
		}
		$body_message .= '</table>';
		return $body_message;
	}

	private function createBodyMessageForUser($data): string
	{
		global $CFG_GLPI;

		$body = __('You have an unsigned protocol');
		$body .= ' - ' . $data['protocol_name'];
		$body .= __(' from') . ' ' . $data['modified'] . '<br>';
		$body .= __('Go to GLPI and sign protocol');
		return $body;
	}

	private function setDate(): array
	{
		$firstReminInterval = $this->settingsInfo['how_often_remind'];
		$secondReminInterval = $this->settingsInfo['how_often_remind'] * 2;
		$firstRemindFrom = (new DateTime())->modify("- " . $firstReminInterval . " day")->setTime(0, 0)->format('Y-m-d H:i:s');
		$firstRemindTo = (new DateTime())->modify("- " . $firstReminInterval . " day")->setTime(23, 59, 59, 59)->format('Y-m-d H:i:s');
		$secondRemindFrom = (new DateTime())->modify("- " . $secondReminInterval . " day")->setTime(0, 0)->format('Y-m-d H:i:s');
		$secondRemindTo = (new DateTime())->modify("- " . $secondReminInterval . " day")->setTime(23, 59, 59, 59)->format('Y-m-d H:i:s');

		return [
			'first_remind_from' => $firstRemindFrom,
			'first_remind_to' => $firstRemindTo,
			'second_remind_from' => $secondRemindFrom,
			'second_remind_To' => $secondRemindTo,
		];

	}

	private function sendMail($protocols): void
	{
		global $CFG_GLPI, $DB;

		$nmail = new GLPIMailer();

		$nmail->SetFrom($CFG_GLPI["admin_email"], $CFG_GLPI["admin_email_name"], false);
		$email_subject = __('GLPI REMINDER');
		$email_content = $protocols['conntent'];

		if ($protocols['id_user'] != '') {
			$req = $DB->request(
				'glpi_useremails',
				['users_id' => $this->idUser, 'is_default' => 1]);
			$owner_email = $req->next()["email"];
		} else {
			$owner_email = $protocols["email"];
		}

		if (!empty($owner_email)) {
			$nmail->AddAddress($owner_email);
			$nmail->IsHtml(true);
			$nmail->Subject = $email_subject;
			$nmail->Body = nl2br(stripcslashes($email_content));
			if (!$nmail->Send()) {
				Session::addMessageAfterRedirect(__('Failed to send email'), false, ERROR);
			} else {
				Session::addMessageAfterRedirect(__('Email sent') . " to " . $owner_email);
			}
		} else {
			Session::addMessageAfterRedirect(__('Can not confirm, add e-mail'), false, ERROR);
		}
	}
}
