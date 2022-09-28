<?php
use Glpi\Inventory\Conf;
include ('../../../inc/includes.php');
global $CFG_GLPI;

if (!$CFG_GLPI["use_public_faq"]) {
	Session::checkLoginUser();
}

$doc = new Document();

if (isset($_GET['docid'])) { // docid for document
	if (!$doc->getFromDB($_GET['docid'])) {
		Html::displayErrorAndDie(__('Unknown file'), true);
	}

	if (!file_exists(GLPI_DOC_DIR . "/" . $doc->fields['filepath'])) {
		Html::displayErrorAndDie(__('File not found'), true); // Not found
	} else if (checkDocPermisions($_GET['docid'])) {
		if (
			$doc->fields['sha1sum']
			&& $doc->fields['sha1sum'] != sha1_file(GLPI_DOC_DIR . "/" . $doc->fields['filepath'])
		) {
			Html::displayErrorAndDie(__('File is altered (bad checksum)'), true); // Doc alterated
		} else {
			$context = isset($_GET['context']) ? $_GET['context'] : null;
			$doc->send($context);
		}
	} else {
		Html::displayErrorAndDie(__('Unauthorized access to this file'), true); // No right
	}
}else{
	
}

function checkDocPermisions($docID){
	
	global $DB;
	$response = false;
	if(isset($_SESSION['glpiID'])) {
		$id = $_SESSION['glpiID'];
        $query = ['SELECT' => ['COUNT' => 'id'],
					'FROM' => 'glpi_plugin_protocolsmanager_receipt',
					'WHERE' => [
						'profile_id'=>$id,
						'protocol_id'=>$docID
					]
				];
		$result = $DB->request($query)->current();
        $result['COUNT(`id`)'] ? $response = true : '';
	}
	return $response;
}

