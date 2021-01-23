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

if (!isConnect('admin')) {
  throw new Exception('{{401 - Accès non autorisé}}');
}
include_file('desktop', 'synologyapi', 'css', 'synologyapi'); 

	$plugin = plugin::byId('synologyapi');
	//sendVarToJS('eqType', $plugin->getId());
	$eqLogics = eqLogic::byType($plugin->getId());

if ($_GET['titre'] == "") 
{
	echo "► Merci de donner un nom au futur équipement fusionné <span class='badgenonvolant badge-danger'>Echec</span>";
	exit();
}


$eqLogicSauvegarde="";

	foreach ($eqLogics as $eqLogic) {
		if ($eqLogic->getLogicalId() == $_GET['api1']) {
			echo "► Récupération des informations de l'équipement ".$eqLogic->getName()." <span class='badgenonvolant badge-success'>OK</span><br>" ;
			$eqLogicaModifier=$eqLogic;
		}
		if ($eqLogic->getLogicalId() == $_GET['api2']) {
			echo '► Récupération des commandes de '.$eqLogic->getName()." <span class='badgenonvolant badge-success'>OK</span><br>" ;
			// On récupères les infos du groupe de commandes puisqu'on va le supprimer. Finalement on ne garde que les commandes.
			$eqLogicSauvegarde=$eqLogic;
		}		
	}
	if (($eqLogicSauvegarde=="") || ($eqLogicaModifier==""))
	{
		echo "► Souci dans la récupération des informations <span class='badgenonvolant badge-danger'>Echec</span>";
		exit();
	}

	foreach ($eqLogicSauvegarde->getCmd() as $eqLogic_cmd) {
		//echo "AVANT<br>" ;
		$nouvellecmd = new synologyapiCmd();
		$nouvellecmd->setType('action');
		$nouvellecmd->setSubType('other');
		$nouvellecmd->setLogicalId($eqLogic_cmd->getLogicalId());
		$nouvellecmd->setEqLogic_id($eqLogicaModifier->getId());
		$nouvellecmd->setName($eqLogic_cmd->getName());
		$nouvellecmd->setIsVisible(1);
		$nouvellecmd->setConfiguration('request', $eqLogic_cmd->getConfiguration('request'));		
		$nouvellecmd->save();
		//echo "APRES<br>" ;
		echo "► La commande ".$eqLogic_cmd->getName()." transférée  <span class='badgenonvolant badge-success'>OK</span><br>" ;
	}
	try {
		$ancienNom=$eqLogicaModifier->getName();
		$eqLogicaModifier->setConfiguration('type', 'cmdinfo');
		echo "► Changement de type de : ".$ancienNom." <span class='badgenonvolant badge-success'>OK</span><br>" ;
		echo '► Suppression de '.$eqLogicSauvegarde->getName()." <span class='badgenonvolant badge-success'>OK</span><br>" ;
		$eqLogicSauvegarde->remove();						
		$eqLogicaModifier->setName($_GET['titre']); 
		echo "► ".$ancienNom.' a été renommé '.$_GET['titre']." <span class='badgenonvolant badge-success'>OK</span><br>" ;
		$eqLogicaModifier->save();
	} catch (Exception $e) {echo "<font color=#e0e2e2><B>Souci détecté</B></font> <span class='badgenonvolant badge-danger'>Echec</span><BR>";}



?>

<br>
<input type='submit' onClick="window.parent.closeModal()" style='background-color:#539f53;font-size:120%;width: 400;border: 0px;padding: 12px 12px;color:#ffffff;' value='Terminer'>


