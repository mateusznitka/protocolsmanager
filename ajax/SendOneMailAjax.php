<?php

include('../../../inc/includes.php');
header("Content-Type: text/html; charset=UTF-8");
Html::header_nocache();
Session::checkLoginUser();

global $DB;
$query = (['FROM' => 'glpi_plugin_protocolsmanager_settings', 'WHERE' => ['id' => 1]]);
$wynik = $DB->request($query)->current()['protocols_save_on'];

die(print_r($wynik));










///////////////////////////////

$hash_new2 =  $_POST['id'] *$_POST["doc_id"] * 386479 + 335235;

if ($hash_new2 != $_POST['hash'])
	die('Blad autoryzacji');
global $CFG_GLPI, $DB;
$id = $_POST["id"];
$nmail = new GLPIMailer();  //PHPMailer
$sender=Config::getEmailSender(null,true);
$nmail->SetFrom($sender["email"], $sender["name"], false);		
$doc_id = $_POST["doc_id"];

if (isset($_POST["em_list"])) {
	$recipients = $_POST["em_list"];
	
}

if (isset($_POST["email_subject"])) {
	$email_subject = $_POST["email_subject"];
} else {
	$email_subject = __('GLPI Protocols Manager mail','protocolsmanager');
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

$req2 = $DB->request(
		'glpi_useremails',
		['users_id' => $id, 'is_default' => 1]);
	
if ($row2 = $req2->current()) {
	$owner_email = $row2["email"];
}
$send_user = 1; // dla testu
if ($send_user == 1) {
	$nmail->AddAddress($owner_email);			
}

/* foreach($recipients_array as $recipient) {
	$nmail->AddAddress($recipient); //do konfiguracji
} */ //do ustalenia

$req = $DB->request(
		'glpi_documents',
		['id' => $doc_id ]);
	
if ($row = $req->current()) {
	$path = $row["filepath"];
	$filename = $row["filename"];
}

$fullpath = GLPI_ROOT."/files/".$path;
$nmail->IsHtml(true);
$nmail->addAttachment($fullpath, $filename);

if(PluginProtocolsmanagerGenerate::checkSignProtocolsOn()){
	$temlateData = PluginProtocolsmanagerConfig::getDataTemplate();
	$subject = isset($temlateData['template_title']) ? $temlateData['template_title'] : 'message title';
	$body_message = isset($temlateData['template_body']) ? $temlateData['template_body'] : 'message body';
	$button = new Buttons();
	$email_subject = (!empty($email_subject)) ? $email_subject : $subject;
	$email_content .= $button->createSignProtocolButton($body_message, $CFG_GLPI);
}


$nmail->Subject = $email_subject; // do ustalenia
$nmail->Body = htmlspecialchars_decode($email_content); // HTML in e-mail
$nmail->IsHtml(true);
$nmail->AltBody = strip_tags(htmlspecialchars_decode($email_content)); // for text mode - clean html tags

if (!$nmail->Send()) {
	die("nie wyslano" .  $nmail->ErrorInfo);
	Session::addMessageAfterRedirect(__('Error in sending the email'), false, ERROR);
	return false;
} else {
	die("Wysłano na adres $owner_email, dokument o nazwie $filename");
	if ($send_user == 1) {
		Session::addMessageAfterRedirect(sprintf(__('An email was sent to %s'), implode(", ", $recipients_array)." ".$owner_email));
		return true;
	} else {
		Session::addMessageAfterRedirect(sprintf(___('An email was sent to %s'), implode(", ", $recipients_array)));
		return true;
	}
}