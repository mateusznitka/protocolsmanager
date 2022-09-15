<?php
require_once dirname(__DIR__) . '/inc/mailReminder.php';
class PluginProtocolsmanagerReminder extends CommonDBTM {
	
	/**
	* Give cron information
	*
	* @param $name : automatic action's name
	*
	* @return arrray of information
	**/
	static function cronInfo($name) {
		
		switch ($name) {
			case 'protocolsManagerReminder' :
			return array('description' => __('Send Emails Reminder'),
			'parameter'   => __('Protocolsmanager send email reminder'),
			'itemtype' => __('Protocols manager Reminder'));
		}
		return [];
	}
	
	/**
	* Cron action send email
	*
	* @param CommonDBTM $task for log (default NULL)
	*
	* @return integer either 0 or 1
	**/
	static function cronPluginProtocolsmanagerReminder($task=NULL) {
		try{
				$send = new MailReminder();
				$send->send();
				$cron_status = 1;
			}catch(Exception $e){
				$cron_status = 0;
		}
		return $cron_status;
	}
}
