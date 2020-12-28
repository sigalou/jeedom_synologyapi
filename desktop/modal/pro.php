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
echo "Lancement Modal Pro";

	function createNewDevice($deviceName, $idSyno)
	{
		event::add('jeedom::alert', array('level' => 'success', 'page' => 'synologyapi', 'message' => __('Ajout de "' . $deviceName . '"', __FILE__),));
		$newDevice = new synologyapi();
		$newDevice->setName($deviceName);
		$newDevice->setLogicalId($deviceName);
		$newDevice->setEqType_name('synologyapi');
		$newDevice->setIsVisible(0);
		$newDevice->setConfiguration('device', $deviceName);
		$newDevice->setConfiguration('serial', $deviceName);
		$newDevice->setConfiguration('type', "all"); // all = toutes les api // syno=un device syno
		$newDevice->setConfiguration('devicetype', $idSyno); // N° du synology 1 ou 2 ou 3
		$newDevice->setIsEnable(1);
		return $newDevice;
	}
$idsynology=$_GET['idsynology'];
$nomDeviceAll="AllAPI_".$idsynology;
echo $nomDeviceAll;
$eqLogics = synologyapi::byType('synologyapi');
$device = synologyapi::byLogicalId($nomDeviceAll, 'synologyapi');
						if (!is_object($device)) {
							$device = createNewDevice($nomDeviceAll, $idsynology);
							echo "Création du device <font color=#e0e2e2><B>".$nomDeviceAll."</B></font> --- <font color=#8fc935><b>OK</b></font><BR>"; 
						} else {
						echo "Modification du device <font color=#e0e2e2><B>".$nomDeviceAll."</B></font> --- <font color=#8fc935><b>OK</b></font><BR>"; 
						}
						$device->save();
						
						
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
$arrContextOptions=array("ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false,),);					
$RequeteaEnvoyer=$server.'/webapi/query.cgi?api=SYNO.API.Info&method=Query&version=1';
$json = file_get_contents($RequeteaEnvoyer, false, stream_context_create($arrContextOptions));
$listeAPI = json_decode($json, true);

$compteur=0;

			foreach ($listeAPI as $key => $value) {
				foreach ($value as $key2 => $value2) {
					//echo '<option value="'.$key2.'"';
					//echo '> '.str_replace("SYNO.", "", $key2).'</option>' ;
					$LogicalId=str_replace("SYNO.", "", $key2);
					$Categorie=substr($LogicalId, 0,(strpos($LogicalId, '.')));
					echo "<br>".substr($LogicalId, 0,(strpos($LogicalId, '.')));
					$compteur++;
					//if ($compteur<20) {
					if ($Categorie=="Core") {

						$cmd = $device->getCmd(null, $LogicalId);
						if ((!is_object($cmd))) {
							if (!is_object($cmd)) $cmd = new alexaapiCmd();
							$cmd->setType('API');
							$cmd->setLogicalId($LogicalId);
							$cmd->setSubType('string');
							$cmd->setEqLogic_id($device->getId());
							$cmd->setName($LogicalId);
							$cmd->setIsVisible(1);
							//$cmd->setConfiguration('RunWhenRefresh', $RunWhenRefresh);
							//$cmd->setDisplay('title_disable', $title_disable);
						//	$cmd->setOrder($Order);
						//echo "<font color=#e0e2e2><B>".$name."</B></font> (".$LogicalId.")  --- <font color=#8fc935><b>Ajoutée</b></font><BR>"; 
						}
						$cmd->save();
					}

				}
			}
exit();







///$return = jeedom::getThemeConfig();
//echo $return['current_desktop_theme'];

    $adresse = "";//$_SERVER['PHP_SELF'];
	$arrayURL=array();
	$URLparametres=""; // c'est ce qui suit method
	$NousSommesApresMethod=false;
    $i = 0;
   foreach($_GET as $cle => $valeur){
		$cle=str_replace("amp;", "", $cle); // rustine pour corriger la transformation de & en &amp;
		//echo "<br>>>".$cle."-----".$valeur;
		$arrayURL[$cle]=$valeur;
        $adresse .= ($i == 0 ? '?' : '&').$cle.($valeur ? '='.$valeur : '');
		if ($NousSommesApresMethod) $URLparametres="&".$cle.($valeur ? '='.$valeur : '');
		if ($cle=="method") $NousSommesApresMethod=true;
        $i++;
    }

//$arrayURL=getURI();
//echo json_encode($arrayURL);
//echo "<br>URLparametres:".$URLparametres;

$idsynology=$arrayURL['idsynology'];
$source=$arrayURL['source'];
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
//$arrayGET=$_GET;
//echo json_encode($_GET);
/*
echo'<br>';
foreach ($arrayGET as $cle => &$str) {
	echo $cle;
    $cle = str_replace('amp;', '&', $cle);
}*/
//echo json_encode($arrayGET);
//echo'<br>';


$hauteuriFrame="100%";
if ($source!="device") {
$hauteuriFrame="80%";

	echo "<table width=100% border=0><tr><td BGCOLOR=#646464>";

	//Define ssl arguments
	$arrContextOptions=array(
		"ssl"=>array(
			"verify_peer"=>false,
			"verify_peer_name"=>false,
		),
	);
	//echo "coucou";

		$RequeteaEnvoyer=$server.'/webapi/query.cgi?api=SYNO.API.Info&method=Query&version=1';
		//echo "<br>".$RequeteaEnvoyer;
		$json = file_get_contents($RequeteaEnvoyer, false, stream_context_create($arrContextOptions));
	$listeAPI = json_decode($json, true);/*
	echo "coucou2";
		foreach ($listeAPI as $key => $value) {
			foreach ($value as $key2 => $value2) {
			echo "<br>".$key2;
			}
			}*/

			
			foreach ($listeAPI as $key => $value) {
				foreach ($value as $key2 => $value2) {
					echo '<option value="'.$key2.'"';
					if ($key2 == "SYNO.Core.System.Utilization") echo "selected"	;		
					echo '> '.str_replace("SYNO.", "", $key2).'</option>' ;
				}
			}
			



} // fin de test de la source
?>

<script>
	$('.bt_lancerRequete').off('click').on('click', function() {
  window.parent.document.getElementById('moniframe').src = 'index.php?v=d&plugin=synologyapi&modal=testAPI&idsynology='+document.getElementById('idsynology').value+'&api='+document.getElementById('API').value+"&method="+document.getElementById('Methode').value+document.getElementById('Parametres').value;
	});

	function lanceAPI() {document.getElementById('moniframe').src = 'index.php?v=d&plugin=synologyapi&modal=testAPI&'+"idsynology="+document.getElementById('idsynology').value+"&"+document.getElementById('APIProposees').value;};
	
	function lanceAPIdepuisIFrame(url) {
	//console.log (url);
	document.getElementById('moniframe').src = 'index.php'+url;
	};

	function changeSynology() {
		var urlAOuvrir = document.getElementById('idsynology').value+"&"+document.getElementById('APIProposees').value;
		$('#md_modal').dialog('close');
		$('#md_modal').dialog({title: "{{Routines}}"});
		$('#md_modal').load('index.php?v=d&plugin=synologyapi&modal=pro&idsynology='+urlAOuvrir).dialog('open');
		};

window.closeModal = function(){
	$('#md_modal').dialog('close');
	window.location.reload();
};
</script>




<?php if ($source!="device") {?>
<div class="input-group " style="float:right">
	<div class="input-group ">
		<span class="input-group-addon warning" style="width: 140px">Synology à questionner</span>
		<select id="idsynology" onchange="changeSynology();" class="form-control input-sm expressionAttr" style="width: 300px">
			<option value="1" <?php if ($arrayURL['idsynology'] == "1") echo "selected" ?>><?php echo config::byKey('Syno1_name','synologyapi')?></option>
			<option value="2" <?php if ($arrayURL['idsynology'] == "2") echo "selected" ?>><?php echo config::byKey('Syno2_name','synologyapi')?></option>
			<option value="3" <?php if ($arrayURL['idsynology'] == "3") echo "selected" ?>><?php echo config::byKey('Syno3_name','synologyapi')?></option>

		</select>
</div>	
</div>

<?php 
}
include_file('desktop', 'synologyapi', 'js', 'synologyapi'); ?>
