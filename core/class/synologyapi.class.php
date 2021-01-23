<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 
 
 supprimer la récompense :
config_group_id=3&ultra_rewards=%5B%5D&api=SYNO.SafeAccess.AccessControl.ConfigGroup.Reward.Ultra&method=set&version=1

Tout mettre en pause :
config_group_id=3&pause=true&api=SYNO.SafeAccess.AccessControl.ConfigGroup&method=set&version=1
config_group_id=3&pause=true

Reprendre :
config_group_id=3&pause=false&api=SYNO.SafeAccess.AccessControl.ConfigGroup&method=set&version=1
config_group_id=3&pause=true
 
 
 
 
 
 
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class synologyapi extends eqLogic {
    /*     * *************************Attributs****************************** */
    
  /*
   * Permet de définir les possibilités de personnalisation du widget (en cas d'utilisation de la fonction 'toHtml' par exemple)
   * Tableau multidimensionnel - exemple: array('custom' => true, 'custom::layout' => false)
	public static $_widgetPossibility = array();
   */
    
    /*     * ***********************Methode static*************************** */

    /*
     * Fonction exécutée automatiquement toutes les minutes par Jeedom
      public static function cron() {
      }
     */

    /*
     * Fonction exécutée automatiquement toutes les 5 minutes par Jeedom
      public static function cron5() {
      }
     */

    /*
     * Fonction exécutée automatiquement toutes les 10 minutes par Jeedom
      public static function cron10() {
      }
     */
    
    /*
     * Fonction exécutée automatiquement toutes les 15 minutes par Jeedom
      public static function cron15() {
      }
     */
    
    /*
     * Fonction exécutée automatiquement toutes les 30 minutes par Jeedom
      public static function cron30() {
      }
     */
    
    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom
      public static function cronHourly() {
      }
     */

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDaily() {
      }
     */



    /*     * *********************Méthodes d'instance************************* */

//Ajouté par l'installateur, c'est un cron exécuté toutes les minutes
public static function update() {
	// On va tester s'il y a un cron qui doit se lancer ou pas
	$onaunCronsurSyno1=false;
	$onaunCronsurSyno2=false;
	$onaunCronsurSyno3=false;
	foreach (self::byType('synologyapi') as $synologyapi) { //on teste tous les devices
		if ($synologyapi->getConfiguration('type') != "cmd") {
			$autorefresh = checkAndFixCron($synologyapi->getConfiguration('autorefresh'));
//		log::add('synologyapi', 'info', " autorefresh1: ".$autorefresh);
//		log::add('synologyapi', 'info', " autorefresh2: ".checkAndFixCron($autorefresh));
			if ($synologyapi->getIsEnable() == 1 && $autorefresh != '') {
				$cron = new Cron\CronExpression($autorefresh, new Cron\FieldFactory);
				if ($cron->isDue()) {
					if ($synologyapi->getConfiguration('devicetype') =="1") $onaunCronsurSyno1=true;
					if ($synologyapi->getConfiguration('devicetype') =="2") $onaunCronsurSyno2=true;
					if ($synologyapi->getConfiguration('devicetype') =="3") $onaunCronsurSyno3=true;
				}
			}
		}
	}
	
	if ($onaunCronsurSyno1 || $onaunCronsurSyno2 || $onaunCronsurSyno3) {
		//log::add('synologyapi','debug',"Lancement de l'actualisation automatique des données");
		log::add('synologyapi', 'info', " ╔══════════════════════[Lancement de l'actualisation automatique des données]═════════════════════════════════════════════════════════");
		// On va chercher un SID pour chaque synology
		$ArraySID=array();
			$ArraySID[1]="";
			$ArraySID[2]="";
			$ArraySID[3]="";
		if ((config::byKey('Syno1_name','synologyapi') !="") && $onaunCronsurSyno1)	$ArraySID[1]=self::vaChercherSID("1");	
		if ((config::byKey('Syno2_name','synologyapi') !="") && $onaunCronsurSyno2)	$ArraySID[2]=self::vaChercherSID("2");	
		if ((config::byKey('Syno3_name','synologyapi') !="") && $onaunCronsurSyno3)	$ArraySID[3]=self::vaChercherSID("3");		
		foreach (self::byType('synologyapi') as $synologyapi) {
			if ($synologyapi->getConfiguration('type') != "cmd") { // on ne lance que si ce n'est pas une commande action
				//log::add('synologyapi','debug','Lancement update de '.$synologyapi->getName().' ('.$synologyapi->getConfiguration('device').')');
				$autorefresh = checkAndFixCron($synologyapi->getConfiguration('autorefresh'));
				$synologyapi->setConfiguration('dernierLancement',date("d.m.Y")." ".date("H:i:s")); // PRECON c'est pour signaler que le CRON va etre sauvegarder
				if ($synologyapi->getIsEnable() == 1 && $autorefresh != '') {
					try {
						$c = new Cron\CronExpression($autorefresh, new Cron\FieldFactory);
						if ($c->isDue()) {
						self::actualiseCmdInfo($synologyapi, $ArraySID);
						}
					} catch (Exception $exc) {
						log::add('synologyapi', 'error', __('Expression cron non valide pour ', __FILE__) . $synologyapi->getHumanName() . ' : ' . $autorefresh);
					}
				}
			}
		}
		log::add('synologyapi', 'info', ' ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════');
	}
}

	public function actualiseCmdInfo($synologyapi, $ArraySID) {
		try {
			if 		($synologyapi->getConfiguration('devicetype') == "2") $nomSynology = config::byKey('Syno2_name','synologyapi');
			elseif	($synologyapi->getConfiguration('devicetype') == "3") $nomSynology = config::byKey('Syno3_name','synologyapi');
			else 														  $nomSynology = config::byKey('Syno1_name','synologyapi');
			//$synologyapi->setConfiguration('boucleEnCours', "CRON");
			//$_boucleEnCours="CRON";
			//log::add('synologyapi','debug','-----------------------------------------------------------------');
			//log::add('synologyapi','debug',"Actualisation des données de l'API ".$synologyapi->getName().' ('.$synologyapi->getConfiguration('device').') sur '.$nomSynology);
			log::add('synologyapi', 'info', " ║");
			log::add('synologyapi', 'info', " ╠═ Actualisation des données de l'API ".$synologyapi->getName().' ('.$synologyapi->getConfiguration('device').') sur '.$nomSynology);
			//log::add('synologyapi','debug','-----------------------------------------------------------------');
			$nbmaxdetentative=3;//a voir si on le met en paramètre
			$compteerreur=0;
			$RequeteOK=false;
			while (($compteerreur <= $nbmaxdetentative) && (!$RequeteOK)) {
				$RequeteOK=$synologyapi->lancerControle($synologyapi, $ArraySID);
				if ($RequeteOK) break; // pour ne pas executer la recherche de SID derrière
				// on va rechercher SID pour faire un genre de pause
				if (config::byKey('Syno1_name','synologyapi') !="" )	$ArraySID[1]=self::vaChercherSID("1");	else 	$ArraySID[1]="";
				if (config::byKey('Syno2_name','synologyapi') !="" )	$ArraySID[2]=self::vaChercherSID("2");	else 	$ArraySID[2]="";
				if (config::byKey('Syno3_name','synologyapi') !="" )	$ArraySID[3]=self::vaChercherSID("3");	else 	$ArraySID[3]="";
				$compteerreur++;
			}
			//log::add('synologyapi','debug','fin cron-----------------------------------------------------------------');
		} catch (Exception $exc) {
			log::add('synologyapi', 'error', __('Erreur pour ', __FILE__) . $synologyapi->getHumanName() . ' : ' . $exc->getMessage());
		}
		$synologyapi->save();
	}
	
	public function refresh() {
		
	$idsynology=$this->getConfiguration('devicetype');
	$sid=synologyapi::vaChercherSID($idsynology);
	$ArraySID=array();
	$ArraySID[$idsynology]=$sid;
	self::actualiseCmdInfo($this, $ArraySID);
	}
	
	public function lancerControle($synologyapi,$ArraySID) {
		
		//log::add('synologyapi', 'debug', "$ArraySID ".json_encode($ArraySID));
		
		$requeteaEnvoyer=$synologyapi->getConfiguration('urlAPI');
		$idsynology=$synologyapi->getConfiguration('devicetype');
		$requeteaEnvoyer=str_replace("amp;", "", $requeteaEnvoyer);//rustine toujours ce souci de amp;

		//log::add('synologyapi', 'debug', "Il faut lancer ".$requeteaEnvoyer);
		
		if (count($ArraySID)==0) $sid=self::vaChercherSID($synologyapi->getConfiguration('devicetype'));
		else $sid=$ArraySID[$synologyapi->getConfiguration('devicetype')];

		$obj_Data=self::recupereDonneesJson ($sid,  $requeteaEnvoyer, "", $idsynology);
		//echo "résultat :".json_encode($obj_Data);
		//log::add('synologyapi', 'debug', "résultat :".json_encode($obj_Data));
		log::add('synologyapi', 'debug', "╠═══> Résultat :".json_encode($obj_Data));
		
		
		if ($obj_Data['success']== true) {
		foreach ($synologyapi->getCmd('info') as $cmd) {
					//log::add('synologyapi', 'debug', '[Mise à jour de] '.$cmd->getName()." (".$cmd->getConfiguration('requestAPI').")");
					
					$syntaxedeBase=$cmd->getConfiguration('requestAPI');
					$nbdeNiveaux=mb_substr_count($syntaxedeBase, "|");
					$parchamps = explode("|", $syntaxedeBase);
					//log::add('synologyapi', 'debug', '[syntaxedeBase] '.$syntaxedeBase);
					//log::add('synologyapi', 'debug', '[count] '.$nbdeNiveaux);
					//log::add('synologyapi', 'debug', '[parchamps] '.$parchamps[1]);
					//log::add('synologyapi', 'debug', '[parchamps] '.json_encode($parchamps));
					$value="rien";
					switch ($nbdeNiveaux) {
									case 0:
										$value=$obj_Data[$parchamps[0]];
										break;
									case 1:
										$value=$obj_Data	[$parchamps[0]]
															[$parchamps[1]];
										break;
									case 2:
										$value=$obj_Data	[$parchamps[0]]
															[$parchamps[1]]
															[$parchamps[2]];
										break;
									case 3:
										$value=$obj_Data	[$parchamps[0]]
															[$parchamps[1]]
															[$parchamps[2]]
															[$parchamps[3]];										
										break;
									case 4:
										$value=$obj_Data	[$parchamps[0]]
															[$parchamps[1]]
															[$parchamps[2]]
															[$parchamps[3]]
															[$parchamps[4]];
										break;
								}
					if ($value === false ) $value="false";
					if ($value === true  ) $value="true";
					//log::add('synologyapi', 'debug', '[value] '.$value);
					
					log::add('synologyapi', 'info', " ╠═ ".$cmd->getName()." = ".$value);

					//log::add('synologyapi', 'debug', '[valeur] '.$cmd->getName()." : ".$value);
					$synologyapi->checkAndUpdateCmd($cmd,$value);

					//2 lignes inutiles car le controle se fait déja au moment de preSave
					//$resultat=$cmd->faireTestExpression($cmd->getConfiguration('controle'));
					//$cmd->setConfiguration('resultat', $resultat);
					//$cmd->save();
					//log::add('synologyapi', 'debug', '[>>>>FIN>>>>Contrôle] Lancer le contrôle ** '.$cmd->getName()." **");
				}
			return true;
		} else 
		log::add('synologyapi', 'debug', "║ ECHEC-ECHEC-ECHEC-ECHEC-ECHEC-ECHEC-ECHEC-ECHEC-ECHEC-ECHEC-ECHEC-ECHEC-ECHEC");
			return false;
	}
	
	public function vaChercherSID($idsynology) {
		//log::add('synologyapi', 'debug', 'lancement  vaChercherSID '.$idsynology);
	
		if ($idsynology == "2") {
			$server = config::byKey('Syno2_server','synologyapi');
			$login = config::byKey('Syno2_login','synologyapi');
			$pass = config::byKey('Syno2_password','synologyapi');
			$nomSynology = config::byKey('Syno2_name','synologyapi');
			}
		elseif ($idsynology == "3") {
			$server = config::byKey('Syno3_server','synologyapi');
			$login = config::byKey('Syno3_login','synologyapi');
			$pass = config::byKey('Syno3_password','synologyapi');
			$nomSynology = config::byKey('Syno3_name','synologyapi');
			}
		else {
			$server = config::byKey('Syno1_server','synologyapi');
			$login = config::byKey('Syno1_login','synologyapi');
			$pass = config::byKey('Syno1_password','synologyapi');
			$nomSynology = config::byKey('Syno1_name','synologyapi');
			}
//echo "<br>".$server;
//echo "<br>".$login;
//echo "<br>".$pass;
//echo "<br>".$nomSynology;
		//echo "<b>API demandée</b> : ".str_replace("SYNO.", "", $API)." <b>sur</b> ".$nomSynology; 
		//echo "<br><b>Paramètres</b> : ".str_replace("v=d&plugin=synologyapi&modal=testAPI&", "", str_replace("SYNO.", "", $parametresAPI))."<hr>"; 

		//Define ssl arguments
$arrContextOptions=array(
			"ssl"=>array(
				"verify_peer"=>false,
				"verify_peer_name"=>false,
			),
			"http"=>array(
					"timeout" => 50, //50s
				)
				,
			"https"=>array(
					"timeout" => 50, //50s
				)
		);

		//Get SYNO.API.Auth Path (recommended by Synology for further update) and maxVersion
		// https://192.168.1.1:1976/webapi/query.cgi?api=SYNO.API.Info&version=1&method=query&query=SYNO.API.Auth
		$begin_time = array_sum(explode(' ', microtime()));
		$json = file_get_contents($server.'/webapi/query.cgi?api=SYNO.API.Info&version=1&method=query&query=SYNO.API.Auth', false, stream_context_create($arrContextOptions));
		//echo "<br>A envoyer pour le pré-LOGIN :".$server.'/webapi/query.cgi?api=SYNO.API.Info&version=1&method=query&query=SYNO.API.Auth';
		// http://192.168.1.250:1975/webapi/query.cgi?api=SYNO.API.Info&version=1&method=query&query=SYNO.API.Auth
		//   http://192.168.1.1:1976/webapi/query.cgi?api=SYNO.API.Info&version=1&method=query&query=SYNO.API.Auth OK {"data":{"SYNO.API.Auth":{"maxVersion":3,"minVersion":1,"path":"auth.cgi"}},"success":true}
		$end_time = array_sum(explode(' ', microtime()));
		//log::add('synologyapi', 'debug', 'Le temps d\'exécution est '.($end_time - $begin_time));
		//echo "JSON pré-LOGIN :".$json;
		if ($json=="")
		{
		echo "<br>► <span class='badge-warning'>Echec de lien avec l'adresse du Synology N° ".$idsynology." Vérifier ".$server."</span>      <span class='badgenonvolant badge-danger'>Echec</span><br>"; 
		log::add('synologyapi', 'debug', "╠═ L'adresse du Synology N°".$idsynology." est FAUX. Vérifier : ".$server);		
		return false;
		}
		
		$obj = json_decode($json);
	
		
		$path = $obj->data->{'SYNO.API.Auth'}->path;
		$vAuth = $obj->data->{'SYNO.API.Auth'}->maxVersion;
		//$vAuth='2';
		$requeteaEnvoyer=$server.'/webapi/'.$path.'?api=SYNO.API.Auth&version='.$vAuth.'&method=login&account='.$login.'&passwd='.$pass.'&format=sid';
		//echo "<br>A envoyer pour le LOGIN :".$requeteaEnvoyer;
		//log::add('synologyapi', 'debug', "A envoyer pour le LOGIN :".$requeteaEnvoyer);
		//log::add('synologyapi', 'debug', 'Identification A envoyer :'.$requeteaEnvoyer);		
		$json_login = file_get_contents($requeteaEnvoyer, false, stream_context_create($arrContextOptions));
		$obj_login = json_decode($json_login);
		//log::add('synologyapi', 'debug', "resultat JSON :".json_encode($obj_login));
		
		
		//echo $server.'/webapi/'.$path.'?api=SYNO.API.Auth&version='.$vAuth.'&method=login&account='.$login.'&passwd='.$pass.'&format=sid';

		if($obj_login->success != "true"){	
		log::add('synologyapi', 'debug', '╠═╦═ Login FAILED sur Synology N°'.$idsynology);		
		log::add('synologyapi', 'debug', "║ ╚═ Message d'erreur :".$json_login);	
		echo "<br>► <span class='badge-warning'> Erreur sur Synology N° ".$idsynology." </span>      <span class='badgenonvolant badge-danger'>Echec</span> Debut : ".$json_login; 
		if ($obj_login->error->code=="407") 	echo "   <span class='badge-warning'> Code 407= Trop d'échecs, IP blacklistée (à corriger dans Sécurité/compte/liste des blocages)</span>"; 
		return false;
		}
		$sid = $obj_login->data->sid;
		//log::add('synologyapi', 'debug', "Login OK sur Synology N°".$idsynology." (".$sid.")");
		log::add('synologyapi', 'debug', '╠═ Login OK sur Synology N°'.$idsynology." (".$sid.")");		

		return $sid;
	}

	public function recupereDonneesJson ($sid,$parametresAPI,$parameters,$idsynology)
	{
		parse_str(str_replace("?", "", $parametresAPI), $outputArray);
		$API=$outputArray['api'];
		if(array_key_exists('version', $outputArray)){$versionSiPresente=$outputArray['version'];}
		else{$versionSiPresente=0;}
 /*
	log::add('synologyapi', 'debug', '[parametresAPI] '.$parametresAPI);
	log::add('synologyapi', 'debug', 'lancement  recupereDonneesJson '.$API);
    log::add('synologyapi', 'debug', '[sid] '.$sid);
    log::add('synologyapi', 'debug', '[idsynology] '.$idsynology);
    log::add('synologyapi', 'debug', '[API] '.$API);
    log::add('synologyapi', 'debug', '[parameters] '.$parameters);*/
	//echo "<br><b>MD5</b> : ".$md5."<hr>"; 
	//Define ssl arguments
		//Define ssl arguments
$arrContextOptions=array(
			"ssl"=>array(
				"verify_peer"=>false,
				"verify_peer_name"=>false,
			),
			"http"=>array(
					"timeout" => 50, //50s
				)
		);

	if ($idsynology == "2") {
		$server = config::byKey('Syno2_server','synologyapi');
		$login = config::byKey('Syno2_login','synologyapi');
		$pass = config::byKey('Syno2_password','synologyapi');
		$nomSynology = config::byKey('Syno2_name','synologyapi');
		}
	elseif ($idsynology == "3") {
		$server = config::byKey('Syno3_server','synologyapi');
		$login = config::byKey('Syno3_login','synologyapi');
		$pass = config::byKey('Syno3_password','synologyapi');
		$nomSynology = config::byKey('Syno3_name','synologyapi');
		}
	else {
		$server = config::byKey('Syno1_server','synologyapi');
		$login = config::byKey('Syno1_login','synologyapi');
		$pass = config::byKey('Syno1_password','synologyapi');
		$nomSynology = config::byKey('Syno1_name','synologyapi');
		}
		//log::add('synologyapi', 'debug', '***********idsynology :'.$idsynology);
		//log::add('synologyapi', 'debug', '***********server :'.$server);

		$RequeteaEnvoyer=$server.'/webapi/query.cgi?api=SYNO.API.Info&method=Query&version=1&query='.$API;
		//echo "<br>A envoyer Info :".$RequeteaEnvoyer;
	//	log::add('synologyapi', 'debug', 'A envoyer Info :'.$RequeteaEnvoyer);
		
		
		//$begin_time = array_sum(explode(' ', microtime()));
		$json_core = file_get_contents($RequeteaEnvoyer, false, stream_context_create($arrContextOptions));
		//$end_time = array_sum(explode(' ', microtime()));
		//log::add('synologyapi', 'debug', 'Le temps d\'exécution est '.($end_time - $begin_time));
		$obj_core = json_decode($json_core);
		$path_core = $obj_core->data->{$API}->path;	
		$vCore = $obj_core->data->{$API}->maxVersion;	
		//$vCore = "1";
		if ($versionSiPresente>0) $vCore=$versionSiPresente; // affecte la version si elle est précisée dans les paramètres
	//log::add('synologyapi', 'debug', '[versionSiPresente] '.$versionSiPresente);
		
		/*
		$path_core = "entry.cgi";	
		log::add('synologyapi', 'debug', 'path : '.$path_core);
		log::add('synologyapi', 'debug', 'version : '.$vCore);
*/
		// DEV: LINK FOR LIVE CPU MEMORY AND NETWORK DATA INFO
		//echo '<p>Login SUCCESS! : sid = '.$sid.'</p>';
		//echo '<p>success 2: api path = '.$path_core.'</p>';
		// https://trionix.homeftp.net:5555/webapi/_______________________________________________________entry.cgi?api=SYNO.Core.System.Utilization&method=get&version=1&type=current&resource=cpu	

		//echo $server.'/webapi/'.$path_core.'?api=SYNO.Core.System.Utilization&version='.$vCore.'&method=get&type=current&_sid='.$sid;


//Activer wifi           	&config=%7B%22enabled%22%3Atrue%7D&netif=smartconnect
//Desactiver Wifi 			&config=%7B%22enabled%22%3Afalse%7D&netif=smartconnect
//Activer wifi invité 		&config={"guest_enabled":true}&netif=smartconnect
//Désactiver wifi invité 	&config={"guest_enabled":false}&netif=smartconnect


		//json of SYNO.Core.System.Utilization (cpu, mem, network etc)
		$RequeteaEnvoyer=$server.'/webapi/'.$path_core.$parametresAPI.'&version='.$vCore.'&_sid='.$sid.$parameters;
		//echo "<br>".$RequeteaEnvoyer;
		log::add('synologyapi', 'debug', '║ Envoi de :'.$RequeteaEnvoyer);

		//$begin_time = array_sum(explode(' ', microtime()));
		$json_Data = file_get_contents($RequeteaEnvoyer, false, stream_context_create($arrContextOptions));
		//$end_time = array_sum(explode(' ', microtime()));
		//log::add('synologyapi', 'debug', 'Le temps d\'exécution est '.($end_time - $begin_time));
		$obj_Data = json_decode($json_Data, true);
		//echo "<br>avant boucke";
		//echo "Retour:".$obj_Data;
		//echo $json_coreData;
		//$array1 = array("color" => "red", 2, 4);
		//$array2 = array("a", "b", "color" => "green", "shape" => "trapezoid", 4);
		//$result = array_merge($array1, $array2);

	return $obj_Data;

	}	

    
 // Fonction exécutée automatiquement avant la création de l'équipement 
    public function preInsert() {
    }

 // Fonction exécutée automatiquement après la création de l'équipement 
    public function postInsert() {
         
    }

 // Fonction exécutée automatiquement avant la mise à jour de l'équipement 
    public function preUpdate() {
        
		//	log::add('synologyapi', 'debug', 'preUpdate '.$this->getName().$this->getConfiguration('compteurinfo'));

					}

 // Fonction exécutée automatiquement après la mise à jour de l'équipement 
    public function postUpdate() {
 				//log::add('synologyapi', 'debug', 'postUpdate '.$this->getName().$this->getConfiguration('compteurinfo'));

    }

 // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement 
    public function preSave() {
     
			/*log::add('synologyapi', 'debug', 'preSave '.$this->getName().$this->getConfiguration('compteurinfo'));

				$compteurinfo=count($this->getCmd('info'));
				$this->setConfiguration('compteurinfo', $compteurinfo);
				$compteurcmd=0;
				foreach ($this->getCmd('action') as $cmd) {
					if ($cmd->getLogicalId() != "refresh") $compteurcmd++;
				}
				$this->setConfiguration('compteurcmd', $compteurcmd);					
*/
	}

 // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement 
    public function postSave() {
        
		//	log::add('synologyapi', 'debug', 'PostSave '.$this->getName().$this->getConfiguration('compteurinfo'));


				/*$compteurinfo=count($this->getCmd('info'));
				$compteurcmd=0;
				foreach ($this->getCmd('action') as $cmd) {
					if ($cmd->getLogicalId() != "refresh") $compteurcmd++;
				}
						//log::add('synologyapi', 'debug', '******************                compteurinfo: '.$compteurinfo);
						//log::add('synologyapi', 'debug', '******************getConfigurationcompteurinfo: '.$this->getConfiguration('compteurinfo'));
						//log::add('synologyapi', 'debug', '******************                compteurcmd: '.$compteurcmd);
						//log::add('synologyapi', 'debug', '******************getConfigurationcompteurcmd: '.$this->getConfiguration('compteurcmd'));
				if (($compteurinfo != $this->getConfiguration('compteurinfo')) || ($compteurcmd != $this->getConfiguration('compteurcmd'))) {
						//log::add('synologyapi', 'debug', '******************Différent ');
				$this->setConfiguration('compteurinfo', $compteurinfo);
				$this->setConfiguration('compteurcmd', $compteurcmd);
				$this->save();
				}	
				*/


					
			if ($this->getConfiguration('type') != "cmd") {
		        //Commande Refresh, ajoutée si n'existe pas
                $createRefreshCmd = true;
                $refresh = $this->getCmd(null, 'refresh');
                if (!is_object($refresh)) {
					//log::add('synologyapi', 'debug', 'Post Save- ajoute');
                        $refresh = new synologyapiCmd();
                        $refresh->setLogicalId('refresh');
                        $refresh->setIsVisible(1);
                        $refresh->setDisplay('icon', '<i class="fas fa-sync"></i>');
                        $refresh->setName(__('Refresh', __FILE__));
						$refresh->setType('action');
						$refresh->setSubType('other');
	                    $refresh->setOrder('99');
						$refresh->setEqLogic_id($this->getId());
						$refresh->save();
                }
			}
    }

 // Fonction exécutée automatiquement avant la suppression de l'équipement 
    public function preRemove() {
        
    }

 // Fonction exécutée automatiquement après la suppression de l'équipement 
    public function postRemove() {
        
    }

    /*
     * Non obligatoire : permet de modifier l'affichage du widget (également utilisable par les commandes)
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*
     * Non obligatoire : permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire : permet de déclencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */

    /*     * **********************Getteur Setteur*************************** */
}

class synologyapiCmd extends cmd {
    /*     * *************************Attributs****************************** */
    
    /*
      public static $_widgetPossibility = array();
    */
    
    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

  // Exécution d'une commande  
 public function execute($_options = array()) {
	 
	    if ($this->getLogicalId() == 'refresh') {
		log::add('synologyapi', 'info', " ╔══════════════════════[Lancement Refresh de ".$this->getEqLogic()->getName()."]═════════════════════════════════════════════════════════");
            $this->getEqLogic()->refresh();
		log::add('synologyapi', 'info', ' ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════');
            return;
        }
	 
    if ($this->getType() != 'action') return;
	
	
		log::add('synologyapi', 'info', " ╔══════════════════════[Execution de la commande ".$this->getName()."]═════════════════════════════════════════════════════════");
								//log::add('synologyapi', 'info', " ║");
								//log::add('synologyapi', 'info', " ╠═ Actualisation des données de l'API ".$synologyapi->getName().' ('.$synologyapi->getConfiguration('device').') sur '.$nomSynology);

	$idsynology=$this->getEqLogic()->getConfiguration('devicetype');
	$sid=synologyapi::vaChercherSID($idsynology);
	$parameters="";
	$parametresAPI="?".str_replace("api=", "api=SYNO.", $this->getConfiguration('request'));
	$obj_Data=synologyapi::recupereDonneesJson ($sid, $parametresAPI, $parameters, $idsynology);
	log::add('synologyapi', 'debug', '╠═ Résultat: '.json_encode($obj_Data));		
		log::add('synologyapi', 'info', ' ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════');

       }

    /*     * **********************Getteur Setteur*************************** */
}


