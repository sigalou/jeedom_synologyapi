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
							log::add('synologyapi','debug','-----------------------------------------------------------------');
							log::add('synologyapi','debug',"Actualisation des données de l'API **".$synologyapi->getName().'** sur '.$nomSynology);
							log::add('synologyapi','debug','-----------------------------------------------------------------');
							$synologyapi->lancerControle($synologyapi);
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
	}

	public function lancerControle($synologyapi) {
		
		//log::add('synologyapi', 'debug', "* Avant de lancer le contrôle on lance les actions d'avant contrôle (s'il y en a).");
		
		
		//log::add('synologyapi', 'debug', '* Maintenant, on lance les contrôles :');
		
		//set_boucleEnCours("7888885613");
		foreach ($synologyapi->getCmd('info') as $cmd) {
					log::add('synologyapi', 'debug', '[Mise à jour] Lancer le contrôle ** '.$cmd->getName()." **");
					//2 lignes inutiles car le controle se fait déja au moment de preSave
					//$resultat=$cmd->faireTestExpression($cmd->getConfiguration('controle'));
					//$cmd->setConfiguration('resultat', $resultat);
					//$cmd->save();
					//log::add('synologyapi', 'debug', '[>>>>FIN>>>>Contrôle] Lancer le contrôle ** '.$cmd->getName()." **");
				}
		
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


