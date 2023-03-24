<?php
require_once dirname(__DIR__) . '/inc/CheckAccess.class.php';

class PluginProtocolsmanagerProtocols extends CommonDBTM {
	
	function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
		return self::createTabEntry(__('Protocols manager','protocolsmanager'));
	}
	
	public function showFormProtocols() {

        CheckAccess::checkRightsToSignProtocolsPage();

		$id = $_SESSION['glpiID'];
		global $DB, $CFG_GLPI;
		$rand = mt_rand();
		
		if(!$this->hasProtocols()){
			echo "<h2 style='color: green;text-align: center'>". __("You haven't any protocols to sign",'protocolsmanager'). "</h2>";
		} else {
			echo "<div id='protocols_table'>";
			echo "<table  class='tab_cadre_fixe'><td style='width:5%'></td><td style='width:5%'>";
			echo "</td><td style='width:90%'></table>";
			echo "<table class='tab_cadre_fixehov' id='myTable'>";
			$header2 = "<th class='center'>".__('Name')."</th>";
			$header2 .= "<th class='center'>".__('Type')."</th>";
			$header2 .= "<th class='center'>".__('Date')."</th>";
			$header2 .= "<th class='center'>".__('File')."</th>";
			$header2 .= "<th class='center'>".__('Creator')."</th>";
			$header2 .= "<th class='center'>".__('Sign','protocolsmanager')."</th></tr>";
			echo $header2;
			$exports = [];
			$doc_counter = 0;
			
			//TODO DB iterator
			foreach ($DB->request(
				"SELECT pp.* FROM `glpi_plugin_protocolsmanager_protocols` as pp 
								LEFT JOIN glpi_plugin_protocolsmanager_receipt as pr ON pr.protocol_id = pp.document_id 
								WHERE pp.user_id = ". $id . " AND pr.confirmed = 0" ) as $export_data => $exports) {
				echo "<tr class='tab_bg_1'>";
				echo "<td class='center'>";
				$Doc = new Document();
				$Doc->getFromDB($exports['document_id']);
				echo $Doc->getLink();
				echo "</td>";
				echo "<td class='center'>";
				echo $exports['document_type'];
				echo "</td>";
				echo "<td class='center'>";
				echo $exports['gen_date'];
				echo "</td>";
				echo "<td class='center'>";
				echo $Doc->getDownloadLink();
				echo "</td>";
				echo "<td class='center'>";
				echo $exports['author'];
				echo "</td>";
				echo '<td class="center"><button data-id="'.$exports['document_id'].'" type="button" class="btn btn-primary describe_modal">
					'.__('Show','protocolsmanager').'
					</button>';
				echo "</td>";
			}
			
			echo "</tr>";
			echo "</table>";
			echo '</div>
				<div id="sign_document" style="display:none;">
					<form method="post" name="protocolsmanager_form'.$rand.'" id="protocolsmanager_form'.$rand.'"
					action="'.$CFG_GLPI["root_doc"].'/plugins/protocolsmanager/front/protocols.form.php" >
						<input type="hidden" id="protocols_id" name="protocols_id" value="" >
						<input type="hidden" name="user_id" value="'.$id.'" >
						<input type="hidden" name="menu_mode" value="e">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="exampleModalLabel">Podpis protokołu</h5>
							</div>
							<div class="modal-body">
								<div>
									<div>
									<iframe id="iframe" type="application/pdf" style="width:100%; height: 500px;">
							</iframe>
						</div>
					</div>
				</div>
				<div class="modal-footer">
						<button id="back_to_list" type="button" class="btn btn-secondary">'.__("Back").'</button>
						<input type="submit" class="btn btn-primary" name="sign_protocols_submit" value="'.__("Sign protocol",'protocolsmanager').'"/>
					</div>';
			Html::closeForm();
			echo '</div>
			</div>
			';
			echo '<script>';
			echo '$(document).ready(() => {
				var src = "'.$CFG_GLPI["root_doc"].'/plugins/protocolsmanager/inc/document.php?docid=";
				$(".describe_modal").click(function(){
					$("#protocols_id").val($(this).data("id"));
					$("#iframe").attr("src", src + $(this).data("id"));
					$("#protocols_table").hide();
					$("#sign_document").show();
				})
				$("#back_to_list").click(()=>{
					$("#protocols_table").show();
					$("#sign_document").hide();
				})
			})';
			echo '</script>';
		}
	}
	
	public static function dbInstance() {
		global $DB;
		return $DB;
	}
	
	public function hasProtocols() : bool {
		global $DB;
		$id = $_SESSION['glpiID'];
		//TODO DB iterator
		$query = "SELECT COUNT(id) as field_count FROM `glpi_plugin_protocolsmanager_receipt` 
					where profile_id = " . $id . " AND confirmed = 0";
		$result = $DB->request($query)->current();
		return ($result['field_count'] > 0) ?? false;
	}
}
