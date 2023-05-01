<?php
/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
const JEEKSENIA = 'jeeksenia';     // plugin logical name

class jeeksenia extends eqLogic {
	// prefix by underscore : https://community.jeedom.com/t/mysql-error-code-42s22-1054-unknown-column-utils-in-field-list/64274/6


	/*     * *************************Attributs****************************** */

	/*
	* Permet de définir les possibilités de personnalisation du widget (en cas d'utilisation de la fonction 'toHtml' par exemple)
	* Tableau multidimensionnel - exemple: array('custom' => true, 'custom::layout' => false)
	public static $_widgetPossibility = array();
	*/

	/*
	* Permet de crypter/décrypter automatiquement des champs de configuration du plugin
	* Exemple : "param1" & "param2" seront cryptés mais pas "param3"
	public static $_encryptConfigKey = array('param1', 'param2');
	*/

	/*     * ***********************Methode static*************************** */
/*     * ***********************Methode static*************************** */

public static function daemon() {
	log::add(JEEKSENIA, 'debug', __METHOD__ . ' running: start');
	$starttime = microtime (true);   // current time in sec as a float

	// for all root equipments
	foreach (self::byType(JEEKSENIA) as $eqLogic) {
		// only the root equipment must refresh data from the KSENIA
		if (is_null( $eqLogic->getConfiguration('type',null) )) {
			$eqLogic->refreshFromKSenia();
		}
	}

	$seconds = config::byKey('refresh_freq', JEEKSENIA, 120, true);
	$endtime = microtime (true);     // current time in sec as a float
	if ( $endtime - $starttime < $seconds )
	{
		$ms = floor(($seconds - ($endtime - $starttime))*1000000);
		log::add(JEEKSENIA, 'info', sprintf('%s refresh_freq:%d sleeping for millisec:%d',__METHOD__,$seconds,$ms/1000) );
		usleep($ms);
	}
}

public static function deamon_info() {
	//log::add(JEEKSENIA, 'debug', __METHOD__);
	$return = array();
	$return['log'] = __CLASS__;
	$return['state'] = 'nok';
	$cron = cron::byClassAndFunction(JEEKSENIA, 'daemon');
	if (is_object($cron) && $cron->running()) {
		$return['state'] = 'ok';
	}
	$return['launchable'] = 'ok';
	return $return;
}

public static function deamon_start($debug = false) {
	log::add(JEEKSENIA, 'debug', __METHOD__);
	self::deamon_stop();
	$deamon_info = self::deamon_info();
	if ($deamon_info['launchable'] != 'ok') {
		throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
	}
	$cron = cron::byClassAndFunction(JEEKSENIA, 'daemon');
	if (!is_object($cron)) {
		throw new Exception(__('Tâche cron introuvable', __FILE__));
	}
	$cron->run();
}

public static function deamon_stop() {
	log::add(JEEKSENIA, 'debug', __METHOD__);
	$cron = cron::byClassAndFunction(JEEKSENIA, 'daemon');
	if (!is_object($cron)) {
		throw new Exception(__('Tâche cron introuvable', __FILE__));
	}
	$cron->halt();
}

public static function deamon_changeAutoMode($mode) {
	log::add(JEEKSENIA, 'debug', __METHOD__.'('.$mode.')');
	$cron = cron::byClassAndFunction(JEEKSENIA, 'daemon');
	if (!is_object($cron)) {
		throw new Exception(__('Tâche cron introuvable', __FILE__));
	}
	$cron->setEnable($mode);
	$cron->save();
}

	/*
	* Fonction exécutée automatiquement toutes les minutes par Jeedom
	public static function cron() {}
	*/

	/*
	* Fonction exécutée automatiquement toutes les 5 minutes par Jeedom
	public static function cron5() {}
	*/

	/*
	* Fonction exécutée automatiquement toutes les 10 minutes par Jeedom
	public static function cron10() {}
	*/

	/*
	* Fonction exécutée automatiquement toutes les 15 minutes par Jeedom
	public static function cron15() {}
	*/

	/*
	* Fonction exécutée automatiquement toutes les 30 minutes par Jeedom
	public static function cron30() {}
	*/

	/*
	* Fonction exécutée automatiquement toutes les heures par Jeedom
	public static function cronHourly() {}
	*/

	/*
	* Fonction exécutée automatiquement tous les jours par Jeedom
	public static function cronDaily() {}
	*/

	/*     * *********************Méthodes d'instance************************* */

	// Fonction exécutée automatiquement avant la création de l'équipement
	public function preInsert() {
		log::add(JEEKSENIA, 'debug', __METHOD__ .' id:' . $this->getId());
		$this->setEqType_name('jeeksenia');
	}

	// Fonction exécutée automatiquement après la création de l'équipement
	public function postInsert() {
		log::add(JEEKSENIA, 'debug', __METHOD__ .' id:' . $this->getId());
	}

	// Fonction exécutée automatiquement avant la mise à jour de l'équipement
	public function preUpdate() {
		log::add(JEEKSENIA, 'debug', __METHOD__ .' id:' . $this->getId());
	}

	// Fonction exécutée automatiquement après la mise à jour de l'équipement
	public function postUpdate() {
		log::add(JEEKSENIA, 'debug', __METHOD__ .' id:' . $this->getId());

		// do this only for update such that the initial parameters are set ( ipaddr etc ) and comm can happen with KSENIA
		$type = $this->getConfiguration('type',null);
		if (is_null($type)) {
			$this->updateConfigurationFromKsenia();
		}
	}

	// Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement
	public function preSave() {
		log::add(JEEKSENIA, 'debug', __METHOD__ .' id:' . $this->getId());
	}

	// Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
	public function postSave() {
		log::add(JEEKSENIA, 'debug', __METHOD__ .' id:' . $this->getId());
		$type = $this->getConfiguration('type',null);
		switch($type) {
			case 'zone': {
				$this->createOrUpdateCommand( __('Présence',__FILE__), 'presence', 'info', 'binary', 1, 'PRESENCE','timePresence' );
				$this->createOrUpdateCommand( 'Etat', 'status', 'info', 'autre', 1, 'GENERIC_INFO' );
				break;
			}
			default: {  // Root Equipment
				$this->createOrUpdateCommand( 'Etat', 'status', 'info', 'binary', 1, 'ENERGY_STATE' );
				$this->createOrUpdateCommand( 'Product Name', 'productname', 'info', 'string', 1, 'GENERIC_INFO' );
				$this->createOrUpdateCommand( 'Product Version', 'productversion', 'info', 'string', 1, 'GENERIC_INFO' );
				break;
			}
		}
	}

	// Fonction exécutée automatiquement avant la suppression de l'équipement
	public function preRemove() {
		log::add(JEEKSENIA, 'debug', __METHOD__ .' id:' . $this->getId());

		// if this is a root EqLogic then lets search for all its children
		$type = $this->getConfiguration('type',null);

		if (is_null($type)) { 
			$idroot = $this->getId();
			foreach (self::byType(JEEKSENIA) as $eqLogic) {
				// if it is a children, then remove it
				if ($idroot == $eqLogic->getConfiguration('rootid',null) ) {
					$eqLogic->remove();    
				}
			}
		}    
	}

	// Fonction exécutée automatiquement après la suppression de l'équipement
	public function postRemove() {
		log::add(JEEKSENIA, 'debug', __METHOD__ .' id:' . $this->getId());
	}

	/*
	* Permet de crypter/décrypter automatiquement des champs de configuration des équipements
	* Exemple avec le champ "Mot de passe" (password)
	public function decrypt() {
		$this->setConfiguration('password', utils::decrypt($this->getConfiguration('password')));
	}
	public function encrypt() {
		$this->setConfiguration('password', utils::encrypt($this->getConfiguration('password')));
	}
	*/

	/*
	* Permet de modifier l'affichage du widget (également utilisable par les commandes)
	public function toHtml($_version = 'dashboard') {}
	*/

	/*
	* Permet de déclencher une action avant modification d'une variable de configuration du plugin
	* Exemple avec la variable "param3"
	public static function preConfig_param3( $value ) {
		// do some checks or modify on $value
		return $value;
	}
	*/

	/*
	* Permet de déclencher une action après modification d'une variable de configuration du plugin
	* Exemple avec la variable "param3"
	public static function postConfig_param3($value) {
		// no return value
	}
	*/

	// Logical ID for child equipment
	// rootid_xxxn xxx is ksenia class and n is the index
	public function buildLogicalID($suffix) {
		return $this->getId()."_".$suffix;
	}

	public function splitLogicalID($lid){
		return preg_split('/_/',$lid);
	}

	// find the root equiment from any equipment, return $this is the eq is a root
	public function getRoot()
	{
		 log::add(JEEKSENIA, 'debug', __METHOD__);
		 $idroot = $this->getConfiguration('rootid',null);
		 return ( $idroot==null) ? $this : eqLogic::byId($idroot);
	}

	// get root URL of the IPX800 finishing by a /
	private function getUrl() {
		$url = 'http://';
		$url .= $this->getConfiguration('ipaddr');
		if ($this->getConfiguration('port') != '') {
			$url .= ':' . $this->getConfiguration('port');
		}
		return $url."/";
	}

	private function getCredentials() {
		$user = $this->getConfiguration('username',null);
		$pwd = $this->getConfiguration('password',null);
		return $user.':'.$pwd ;//'Authorization: Basic '. base64_encode( $user .':' . $pwd);
	}

	// return eqlogic's icon
	public function getImage() 
	{
		//log::add(JEEKSENIA, 'debug', __METHOD__);	   $icon = null;
		$configtype = $this->getConfiguration('type',null);
		if (isset($configtype)) {
			$icon = 'jeeksenia_' . $configtype . '.png';
		}
		return isset($icon) ? 'plugins/jeeksenia/desktop/img/'.$icon : parent::getImage();
	}

	// callback not used
	public function event() 
	{
		log::add(JEEKSENIA, 'debug', __METHOD__ .' $_GET:'.json_encode($_GET));
		//log::add(JEEKSENIA, 'debug', __METHOD__ .' $_POST:'.json_encode($_POST));
		//log::add(JEEKSENIA, 'debug', __METHOD__ .' $_REQUEST:'.json_encode($_REQUEST));
		log::add(JEEKSENIA, 'debug', __METHOD__ .' eqlogic id:'.init('id'));
	}
	
	private function KSeniaHttpCall($action) {
		$url = $this->getUrl() . $action;
		log::add(JEEKSENIA, 'debug', __METHOD__ .sprintf(' id:%s url:%s',$this->getId(),$url ));
		$ch = curl_init();

		curl_setopt_array($ch, [
			CURLOPT_URL => $url ,					// set the url
			CURLOPT_RETURNTRANSFER => true,			// return the transfer as a string
			CURLOPT_HTTPHEADER => [					// headers
//				'Content-Type: application/xml',
				'Authorization: Basic '. base64_encode($this->getCredentials())
			],
			//CURLOPT_TIMEOUT => 30
		]);		

        $result = array();
		$result['response'] = curl_exec($ch);							// output string
		$result['http_code'] = curl_getinfo($ch,CURLINFO_HTTP_CODE);	// HTTP Return code
		$result['ok'] =  ($result['response'] !== false) && ($result['http_code'] == 200) ;
		if ( ! $result['ok'] ) {
			$this->checkAndUpdateCmd('status', 0);
			$result['curl_error'] = curl_error($ch);
			log::add(JEEKSENIA, 'error', __METHOD__ .' Http code: ' . $result['http_code']  . ' Curl error: ' . $result['curl_error']);
		} else {
			log::add(JEEKSENIA, 'debug', __METHOD__ .sprintf(' response:%s',$result['response']));
			$this->checkAndUpdateCmd('status', 1);
		}
		// $header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
		// $result['header'] = substr($response, 0, $header_size);
        // $result['body'] = substr( $response, $header_size );
        //$result['last_url'] = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL);

		curl_close($ch);		// close curl resource to free up system resources
		return $result;
	}

	private function xmlKSeniaHttpCall($actionxml) {
		log::add(JEEKSENIA, 'debug', __METHOD__ .sprintf(' id:%s action:%s',$actionxml));
		$result = $this->KSeniaHttpCall($actionxml);
		if (!$result['ok']) {
			return null;
		}
		$xml = simplexml_load_string( $result['response'] );
		log::add(JEEKSENIA, 'debug', __METHOD__ .sprintf(' result:%s',json_encode($xml)));
		return $xml;
	}

	private function updateChildEQStatus($idx,string $status,string $bypass) {
		log::add(JEEKSENIA, 'debug', __METHOD__ .sprintf(' id:%s idx:%s status:%s bypass:%s',$this->getId(),$idx,$status,$bypass));
		$root = $this->getId();
		$eqLogic = self::byLogicalId($this->buildLogicalID('Z'.$idx), JEEKSENIA);
		if ( is_object($eqLogic) ) {
			$eqLogic->checkAndUpdateCmd('presence', ($status=="NORMAL") ? 0 : 1);
			$eqLogic->checkAndUpdateCmd('status', ($status=="LOST") ? "LOST" : $bypass);
		} else {
			log::add(JEEKSENIA, 'warning', __METHOD__ .' equipment '.'Z'.$idx.'not found');
		}
	}

	public function refreshFromKSenia() {
		log::add(JEEKSENIA, 'debug', __METHOD__ .' id:' . $this->getId());
		$xml = $this->xmlKSeniaHttpCall("xml/zones/zonesStatus16IP.xml");
		/*
		<?xml version="1.0" encoding="UTF-8"?>
		<zonesStatus>
			<zone>
				<status>NORMAL</status>
				<bypass>UN_BYPASS</bypass>
			</zone>
			<zone>
				<status>NORMAL</status>
				<bypass>UN_BYPASS</bypass>
			</zone>
			<zone>
				<status>NORMAL</status>
				<bypass>UN_BYPASS</bypass>
			</zone>
			<zone>
				<status>NORMAL</status>
				<bypass>UN_BYPASS</bypass>
			</zone>
			<zone>
				<status>NORMAL</status>
				<bypass>UN_BYPASS</bypass>
			</zone>
			<zone>
				<status>NORMAL</status>
				<bypass>UN_BYPASS</bypass>
			</zone>
			<zone>
				<status>NORMAL</status>
				<bypass>UN_BYPASS</bypass>
			</zone>
			<zone>
				<status>NORMAL</status>
				<bypass>UN_BYPASS</bypass>
			</zone>
			<zone>
				<status>NORMAL</status>
				<bypass>UN_BYPASS</bypass>
			</zone>
			<zone>
				<status>LOST</status>
				<bypass>UN_BYPASS</bypass>
			</zone>
			<zone>
				<status>NORMAL</status>
				<bypass>UN_BYPASS</bypass>
			</zone>
			<zone>
				<status>NORMAL</status>
				<bypass>UN_BYPASS</bypass>
			</zone>
			<zone>
				<status>NORMAL</status>
				<bypass>UN_BYPASS</bypass>
			</zone>
			<zone>
				<status>NORMAL</status>
				<bypass>UN_BYPASS</bypass>
			</zone>
			<zone>
				<status>NORMAL</status>
				<bypass>UN_BYPASS</bypass>
			</zone>
			<zone>
				<status>NORMAL</status>
				<bypass>UN_BYPASS</bypass>
			</zone>
		</zonesStatus>
		*/
		if (is_object($xml)) {
			$arr = $xml->xpath("//zone");
			foreach( $arr as $key=>$zone ) {
				$this->updateChildEQStatus($key,$zone->status, $zone->bypass);
			}
		}
		return null;
	}

	public function updateConfigurationFromKsenia() {
		log::add(JEEKSENIA, 'debug', __METHOD__ .' id:' . $this->getId());
		$xml = $this->xmlKSeniaHttpCall("xml/info/generalInfo.xml");
		/*
		<generalInfo>
			<productName>KSENIA lares 16IP</productName>
			<productHighRevision>1</productHighRevision>
			<productLowRevision>6</productLowRevision>
			<productBuildRevision>1265</productBuildRevision>
			<webServerFW>1.0.0.15</webServerFW>
			<webServerHTML>1.1.30</webServerHTML>
			<info1>Sécurité Vol Feu</info1>
			<info2>04 38 24 02 15</info2>
			<timestamp>1682886566</timestamp>
		</generalInfo>
		*/
		if (is_object($xml)) {
			$this->checkAndUpdateCmd('productname', (string) $xml->productName[0]);
			$this->checkAndUpdateCmd('productversion',  sprintf('%s.%s.%s',(string)$xml->productHighRevision[0],(string)$xml->productLowRevision[0],(string)$xml->productBuildRevision[0]));
		}

		$xml = $this->xmlKSeniaHttpCall("xml/zones/zonesDescription16IP.xml");
		/*
		<?xml version="1.0" encoding="ISO-8859-1"?> <zonesDescription>     <zone>IR hall d&#39;entr?e</zone>     <zone>IR sejour</zone>     <zone>IR bureau</zone>     <zone>IR chambre s.sol</zone>     <zone>IR buanderie</zone>     <zone>VX Cot? bureau</zone>     <zone>IR Salle de Gym</zone>     <zone>IRR chambre parent</zone>     <zone>IRR chambre ?tage</zone>     <zone>CTR bureau gauche</zone>     <zone>CTR bureau droite</zone>     <zone>CTR SDB parent velux droit</zone>     <zone>CTR SDB ?tage</zone>     <zone>VX Entr?e devant</zone>     <zone>VX Entr?e arri?re</zone>     <zone>CTR SDB parent velux gauche</zone> </zonesDescription>
		*/
		if (is_object($xml)) {
			$arr = $xml->xpath("//zone");
			foreach( $arr as $key=>$zone ) {
				$this->createOrUpdateChildEQ('security','zone','Z'.$key,1,1,$zone);
			}
		}

		return true; 
	}

	public function createOrUpdateChildEQ($category,$type,$child,$enable=0,$visible=0,$name=null) {
		log::add(JEEKSENIA, 'debug', __METHOD__ .sprintf(' for root:%d child:%s name:%s',$this->getId(),$chil,$name??''));
		//$child = ;
		$eqLogic = self::byLogicalId( $this->buildLogicalID($child) , JEEKSENIA);

		if (!is_object($eqLogic)) {
			 log::add(JEEKSENIA, 'info', __METHOD__.sprintf(' create for child:%s',$child));
			 $eqLogic = new jeeksenia();
			 $eqLogic->setEqType_name(JEEKSENIA);
			 $eqLogic->setLogicalId( $this->buildLogicalID($child) );
			 $eqLogic->setConfiguration('type', $type);
			 $eqLogic->setConfiguration('rootid', $this->getId());
			 $eqLogic->setIsEnable($enable);
			 $eqLogic->setIsVisible($visible);
			 $eqLogic->setCategory( $category ,'1');
			 $eqLogic->setObject_id($this->getObject_id());  // same parent as root parent
			 $eqLogic->setName( is_null($name) ? ($this->getName() . "_" . $child) : $name );
			 $eqLogic->save(); 
		}
		else {
			 // todo : if object is not new, try not to change its parent ID
			 // but should we verify that the old parent id is still a valid object ???
			 //$eqLogic->setObject_id($this->getObject_id());  // same parent as root parent
		}
	}

/* 	public function removeChildEQ( $child ) {
		log::add(JEEKSENIA, 'debug', __METHOD__ .' root id:' . $this->getId() . ' child:' . $child);
		$eqLogic = self::byLogicalId( $this->buildLogicalID($child) , JEEKSENIA);
		if (is_object($eqLogic)) {
			$eqLogic->remove();
		}
	} */

	public function createOrUpdateCommand( $name, $logicalid, $type, $subtype, $is_visible, $generic_type, $template=null, $targetcmdid=null) {
		log::add(JEEKSENIA, 'debug', __METHOD__ .' name:' . $name);
		$cmd = $this->getCmd(null, $logicalid);
		if (!is_object($cmd)) {
			$cmd = new jeekseniaCmd();
			$cmd->setName($name);
			$cmd->setEqLogic_id($this->getId());
			$cmd->setType($type);
			$cmd->setSubType($subtype);
			$cmd->setLogicalId($logicalid);
			$cmd->setIsVisible($is_visible);
			//$cmd->setDisplay('generic_type', $generic_type);
			$cmd->setGeneric_type($generic_type);
			if (!is_null($targetcmdid)) {
				$cmd->setValue( (int) $targetcmdid );
			} 
			if (!is_null($template)) {
				$cmd->setTemplate('dashboard',$template );    //template pour le dashboard
				$cmd->setTemplate('mobile',$template );    //template pour le dashboard
			}
			// $cmd->setUnite('');
			// $cmd->setIsHistorized(0);
			$cmd->save();
		} else {
			if ($cmd->getDisplay('generic_type') == "") {
				$cmd->setDisplay('generic_type', $generic_type);
				$cmd->save();
			}
		}
		return $cmd;
	}

	/*     * **********************Getteur Setteur*************************** */

}


class jeekseniaCmd extends cmd {
	/*     * *************************Attributs****************************** */

	/*
	public static $_widgetPossibility = array();
	*/

	/*     * ***********************Methode static*************************** */


	/*     * *********************Methode d'instance************************* */

	/*
	* Permet d'empêcher la suppression des commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
	public function dontRemoveCmd() {
		return true;
	}
	*/

	// Exécution d'une commande
	public function execute($_options = array()) {
		$cmdid = $this->getLogicalId();
		log::add(JEEKSENIA, 'debug', __METHOD__ .' cmd:'. $cmdid .' options:' . json_encode($_options));
		$eqLogic = $this->getEqLogic(); //Récupération de l’eqLogic
		$root = $eqLogic->getRoot();
		
		log::add(JEEKSENIA, 'debug', __METHOD__ . sprintf(' root:%s eqlogic:%s cmd:%s',$root->getId(),$eqLogic->getId(), $cmdid));
		switch ($cmdid) {
			
			default:
			log::add(JEEKSENIA, 'info', __METHOD__ .' ignoring unknown command');
		}
	}

	/*     * **********************Getteur Setteur*************************** */

}
