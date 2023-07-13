<?php
require_once dirname(__DIR__) . '/inc/EncodeDecode.php';
require_once dirname(__DIR__) . '/inc/sign.class.php';

class SignProtocolByEmail
{
	
	private $userID;
	private $protocolsID;
	private $hash = '6786yguguy6rt7gcdaxgs8y87auygsdfvafa67guadg';
	/**
	 * @return mixed
	 */
	public function getUserID()
	{
		return $this->userID;
	}
	
	/**
	 * @param mixed $userID
	 */
	public function setUserID($userID): void
	{
		$this->userID = $userID;
	}
	
	/**
	 * @return mixed
	 */
	public function getProtocolsID()
	{
		return $this->protocolsID;
	}
	
	/**
	 * @param mixed $protocolsID
	 */
	public function setProtocolsID($protocolsID): void
	{
		$this->protocolsID = $protocolsID;
	}
	
	public function createPathToSignProtocol(): string
	{
		$result = '';
		$queryString = $this->userID.','.$this->hash.','.$this->protocolsID;
		if($this->userID AND $this->hash AND $this->protocolsID){
			$result = EncodeDecode::encrypt($queryString,$this->hash);
			$result = base64_encode($result);
		}
		return $result;
	}
	
	public function updateDataFromEmailLink($string): array
	{
		$arrayResult = EncodeDecode::decrypt(base64_decode($string), $this->hash);
		$arrayResult = explode(',',$arrayResult);
		try{
			if(!isset($arrayResult[3]) || !isset($arrayResult[1])){
				throw new Exception();
			}
			$sp = new SignProtocol();
			$sp->signdocumentByEmail($arrayResult[2],$arrayResult[0]);
			$result = ['message' =>__('Document signed','protocolsmanager'), 'message_color' => 'green' ];
			
		}catch (\Exception $exception){
			$result = ['message' => __('Error - Document not signed','protocolsmanager'), 'message_color' => 'red'];
		}
		return $result;
	}
}