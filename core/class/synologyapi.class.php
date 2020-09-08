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
			
			
		//log::add('synologyapi','debug',"Lancement de l'actualisation automatique des données");
		log::add('synologyapi', 'info', " ╔══════════════════════[Lancement de l'actualisation automatique des données]═════════════════════════════════════════════════════════");
		
		// On va chercher un SID pour chaque synology
		$ArraySID=array();
		if (config::byKey('Syno1_name','synologyapi') !="" )	$ArraySID[1]=self::vaChercherSID("1");	else 	$ArraySID[1]="";
		if (config::byKey('Syno2_name','synologyapi') !="" )	$ArraySID[2]=self::vaChercherSID("2");	else 	$ArraySID[2]="";
		if (config::byKey('Syno3_name','synologyapi') !="" )	$ArraySID[3]=self::vaChercherSID("3");	else 	$ArraySID[3]="";
				
		
		foreach (self::byType('synologyapi') as $synologyapi) {

			//log::add('synologyapi','debug','Lancement update de '.$synologyapi->getName().' ('.$synologyapi->getConfiguration('device').')');
			$autorefresh = $synologyapi->getConfiguration('autorefresh');
			$synologyapi->setConfiguration('dernierLancement',date("d.m.Y")." ".date("H:i:s")); // PRECON c'est pour signaler que le CRON va etre sauvegarder
			//ESSAI
			//$synologyapi->setConfiguration("synologyapiAction", 'CRON '.date("d.m.Y")." ".date("H:i:s"));
			
			if ($synologyapi->getIsEnable() == 1 && $autorefresh != '') {
				try {
					$c = new Cron\CronExpression($autorefresh, new Cron\FieldFactory);
					if ($c->isDue()) {
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
				} catch (Exception $exc) {
					log::add('synologyapi', 'error', __('Expression cron non valide pour ', __FILE__) . $synologyapi->getHumanName() . ' : ' . $autorefresh);
				}
			}
		}
	log::add('synologyapi', 'info', ' ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════');

	}

	public function lancerControle($synologyapi,$ArraySID) {
		
		//log::add('synologyapi', 'debug', "$ArraySID ".json_encode($ArraySID));
		
		$requeteaEnvoyer=$synologyapi->getConfiguration('urlAPI');
		$idsynology=$synologyapi->getConfiguration('devicetype');
		$requeteaEnvoyer=str_replace("amp;", "", $requeteaEnvoyer);//rustine toujours ce souci de amp;

		//log::add('synologyapi', 'debug', "Il faut lancer ".$requeteaEnvoyer);
		
		if (count($ArraySID)==0) $sid=self::vaChercherSID($synologyapi->getConfiguration('devicetype'));
		else $sid=$ArraySID[$synologyapi->getConfiguration('devicetype')];

		$obj_Data=self::recupereDonneesJson ($sid, "SYNO.".$synologyapi->getConfiguration('device'), $requeteaEnvoyer, "", $idsynology);
		//echo "résultat :".json_encode($obj_Data);
		//log::add('synologyapi', 'debug', "résultat :".json_encode($obj_Data));
		log::add('synologyapi', 'debug', "╠═══> Résultat :".json_encode($obj_Data));
		
		
		if ($obj_Data['success']== true) {
		foreach ($synologyapi->getCmd('info') as $cmd) {
					//log::add('synologyapi', 'debug', '[Mise à jour de] '.$cmd->getName()." (".$cmd->getConfiguration('requestAPI').")");
					
					$syntaxedeBase=$cmd->getConfiguration('requestAPI');
					$nbdeNiveaux=mb_substr_count($syntaxedeBase, ".");
					$parchamps = explode(".", $syntaxedeBase);
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
		);

		//Get SYNO.API.Auth Path (recommended by Synology for further update) and maxVersion
		// https://192.168.0.1:1976/webapi/query.cgi?api=SYNO.API.Info&version=1&method=query&query=SYNO.API.Auth
		$begin_time = array_sum(explode(' ', microtime()));
		$json = file_get_contents($server.'/webapi/query.cgi?api=SYNO.API.Info&version=1&method=query&query=SYNO.API.Auth', false, stream_context_create($arrContextOptions));
		$end_time = array_sum(explode(' ', microtime()));
		//log::add('synologyapi', 'debug', 'Le temps d\'exécution est '.($end_time - $begin_time));
		$obj = json_decode($json);
		$path = $obj->data->{'SYNO.API.Auth'}->path;
		$vAuth = $obj->data->{'SYNO.API.Auth'}->maxVersion;
		//https://192.168.0.4:1975/webapi/auth.cgi?api=SYNO.API.Auth&method=Login&version=2&account=admin&passwd=christel
		//$vAuth='2';
		$requeteaEnvoyer=$server.'/webapi/'.$path.'?api=SYNO.API.Auth&version='.$vAuth.'&method=login&account='.$login.'&passwd='.$pass.'&format=sid';
		//echo "<br>A envoyer pour le LOGIN :".$requeteaEnvoyer;
		//log::add('synologyapi', 'debug', "A envoyer pour le LOGIN :".$requeteaEnvoyer);
		//log::add('synologyapi', 'debug', 'Identification A envoyer :'.$requeteaEnvoyer);		
		$json_login = file_get_contents($requeteaEnvoyer, false, stream_context_create($arrContextOptions));
		$obj_login = json_decode($json_login);
		//echo $server.'/webapi/'.$path.'?api=SYNO.API.Auth&version='.$vAuth.'&method=login&account='.$login.'&passwd='.$pass.'&format=sid';

		if($obj_login->success != "true"){	echo "Login FAILED core";return false;}
			$sid = $obj_login->data->sid;
		//log::add('synologyapi', 'debug', "Login OK sur Synology N°".$idsynology." (".$sid.")");
		log::add('synologyapi', 'debug', '╠═ Login OK sur Synology N°'.$idsynology." (".$sid.")");		

		return $sid;
	}

	public function recupereDonneesJson ($sid,$API,$parametresAPI,$parameters,$idsynology)
	{
	//log::add('synologyapi', 'debug', 'lancement  recupereDonneesJson '.$API);

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
		//echo "<br>A envoyer :".$RequeteaEnvoyer;
		//log::add('synologyapi', 'debug', 'A envoyer :'.$RequeteaEnvoyer);
		
		
		//$begin_time = array_sum(explode(' ', microtime()));
		$json_core = file_get_contents($RequeteaEnvoyer, false, stream_context_create($arrContextOptions));
		//$end_time = array_sum(explode(' ', microtime()));
		//log::add('synologyapi', 'debug', 'Le temps d\'exécution est '.($end_time - $begin_time));
		$obj_core = json_decode($json_core);
		$path_core = $obj_core->data->{$API}->path;	
		$vCore = $obj_core->data->{$API}->maxVersion;	
		
		/*
		$vCore = "1";
		$path_core = "entry.cgi";	
		log::add('synologyapi', 'debug', 'path : '.$path_core);
		log::add('synologyapi', 'debug', 'version : '.$vCore);
*/
		// DEV: LINK FOR LIVE CPU MEMORY AND NETWORK DATA INFO
		//echo '<p>Login SUCCESS! : sid = '.$sid.'</p>';
		//echo '<p>success 2: api path = '.$path_core.'</p>';
		// https://trionix.homeftp.net:5555/webapi/_______________________________________________________entry.cgi?api=SYNO.Core.System.Utilization&method=get&version=1&type=current&resource=cpu	

		//echo $server.'/webapi/'.$path_core.'?api=SYNO.Core.System.Utilization&version='.$vCore.'&method=get&type=current&_sid='.$sid;

		 
		//json of SYNO.Core.System.Utilization (cpu, mem, network etc)
		$RequeteaEnvoyer=$server.'/webapi/'.$path_core.$parametresAPI.'&version='.$vCore.'&_sid='.$sid.$parameters;
		//echo "<br>".$RequeteaEnvoyer;
		//log::add('synologyapi', 'debug', 'A envoyer :'.$RequeteaEnvoyer);

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
        
    }

 // Fonction exécutée automatiquement après la mise à jour de l'équipement 
    public function postUpdate() {
        
    }

 // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement 
    public function preSave() {
        
    }

 // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement 
    public function postSave() {
        
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
        
     }

    /*     * **********************Getteur Setteur*************************** */
}


