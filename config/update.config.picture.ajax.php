<?php

include('../../../inc/includes.php');
header("Content-Type: text/html; charset=UTF-8");

Html::header_nocache();
Session::checkLoginUser();
Session::haveRight("config", UPDATE);
global $DB;

try{
$podglad = GLPI_PICTURE_DIR;
$pict_array = ["_picture" => $_REQUEST["_picture"]];
$input = prepareInputForUpdateMailPict($pict_array);

}catch(Exception $e){
			die('Error - field not updated');
}

function prepareInputForUpdateMailPict($input){
        global $CFG_GLPI;
		
		$dir1 = GLPI_PICTURE_DIR ;
		$fullpath = GLPI_TMP_DIR . "/" . $input["_picture"];
		if (Document::isImage($fullpath, 'image')) {	
			$timestamp1 = floor(microtime(true) * 1000);
			$filename     = 'mail_pict1' . $timestamp1;
			$sub          = 'mail_pict_folder';

		   // output images with possible transparency to png, other to jpg
			$extension = strtolower(pathinfo($fullpath, PATHINFO_EXTENSION));
			$extension = in_array($extension, ['png', 'gif'])
			? 'png'
			: 'jpg';

			@mkdir(GLPI_PICTURE_DIR . "/$sub");
			$picture_path = GLPI_PICTURE_DIR  . "/{$sub}/{$filename}.{$extension}";
			User::dropPictureFiles(CurrentPict());

			if (Document::renameForce($fullpath, $picture_path)) {
			// if (copy($fullpath, $picture_path)) {
				Session::addMessageAfterRedirect(__('The file is valid. Upload is successful.'));
				// For display
				$input['picture'] = "{$sub}/{$filename}.{$extension}";

				//prepare a thumbnail
				$thumb_path = GLPI_PICTURE_DIR . "/{$sub}/{$filename}_min.{$extension}";
				Toolbox::resizePicture($picture_path, $thumb_path);
				DbUpdateOrInsert($input['picture']);
				$token = Session::getNewCSRFToken();
				$imageHtml = User::getURLForPicture($input['picture']);
				$res = array("result" => "res_ok", "picturePath" => $input['picture'], "imageHtml" => $imageHtml, "token" => $token);
				$res_json = json_encode($res);
				die($res_json);
			} else {
				Session::addMessageAfterRedirect(
					__('Moving temporary file failed.'),
					false,
					ERROR
				);
				@unlink($fullpath);
			}
		} else {
			Session::addMessageAfterRedirect(
				__('The file is not an image file.'),
				false,
				ERROR
			);
			@unlink($fullpath);
		}			
}

function CurrentPict(){
	global $DB;
 		$mailPictReqest =  $DB->request(
            "settings_new",
            "`option_name` = 'mail_pict1'"
        );

		$mailPict ='';
		foreach ($mailPictReqest as $row) {
            $mailPict = $row['option_value'];
		}
	return $mailPict;
}

		
function DbUpdateOrInsert ($optVal){
	global $DB;
	$DB->updateOrInsert('settings_new',
		[   
			'option_value' => $optVal,
		],
		[
			'option_name' => 'mail_pict1'
		]
	);
}

?>

