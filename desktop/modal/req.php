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

///$return = jeedom::getThemeConfig();
//echo $return['current_desktop_theme'];
	log::add('synologyapi', 'info', " ═══════════════════════[Lancement fenêtre assistant]═════════════════════════════════════════════════════════");

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

?>

	<form method='get' id='selectionpage'>

	<div class="input-group " style="float:left">
		<div class="input-group ">
		
			<span class="input-group-addon success" id="basic-addon1" style="width: 140px">API à utiliser</span>
			<select onchange="" id="API" class="form-control input-sm expressionAttr" style="width: 300px">
			<?php
			
			foreach ($listeAPI as $key => $value) {
				foreach ($value as $key2 => $value2) {
					echo '<option value="'.$key2.'"';
					if ($key2 == "SYNO.Core.System.Utilization") echo "selected"	;		
					echo '> '.str_replace("SYNO.", "", $key2).'</option>' ;
				}
			}
			
			?>
			</select></div>	</div>&nbsp;&nbsp;&nbsp;<?php echo "<FONT COLOR=#8dc935><i><small>".count($listeAPI['data'])." API disponibles</i></small>";?>
	</td><td>&nbsp;ou&nbsp;</td><td>


	<div class="input-group " style="float:left">

		
			<span class="input-group-addon info" style="width: 140px">API proposées</span>
			<select onchange="lanceAPI();" id="APIProposees" class="form-control input-sm expressionAttr" style="width: 300px">
				<option value="api=SYNO.Core.System.Utilization&method=get" selected>Utilisation Ressources</option>
				<option value="api=SYNO.Core.System&method=info&type=network">Informations Réseau</option>
				<option value="api=SYNO.Core.System&method=info&type=storage" >Informations Stockage</option>
				<option value="api=SYNO.Core.CurrentConnection&method=list&start=0&limit=50&sort_by=%22time%22&sort_direction=%22DESC%22&offset=0&action=%22enum%22" >Connexions actives</option>
				<option value="api=SYNO.Core.SyslogClient.Log&method=list&logtype=ftp,filestation,webdav,cifs,tftp" >Connexions actives2</option>

			</select>
			
	</div>

	</td></tr><tr><td BGCOLOR=#646464>
	<div class="input-group " >
			<span class="input-group-addon success" style="width: 140px">Méthode à utiliser</span>
			<select id="Methode" onchange="changeMethode();"class="form-control input-sm expressionAttr" style="width: 100px">
				<option value="get" selected>Get</option>
				<option value="system_get">System Get</option>
				<option value="get_remote" >Get_remote</option>
				<option value="query" >Query</option>
				<option value="set" >Set</option>
				<option value="update" >Update</option>
				<option value="start" >Start</option>
				<option value="check" >Check</option>
				<option value="info" >Info</option>
				<option value="autre" >Autre...</option>

			</select>&nbsp;
			<input id="autreMethode" style="width: 170px;height: 28px;" type="hidden" class="form-control" name="autreMethode" placeholder="Ajouter votre méthode">&nbsp;
	<input type="radio" id="Action"  name="typeCmdInfo" value="Action" > Action&nbsp;
	<input type="radio" id="Info" name="typeCmdInfo" value="Info" checked> Info

</div>
			
	</div>	
	</div>
	</td><td></td><td>

	

	</td></tr><tr><td BGCOLOR=#646464>

	<div class="input-group " style="float:left" >
			<span class="input-group-addon success" >Autres paramètres</span>
		  <input id="Parametres" type="text" class="form-control" name="Parametres" placeholder="Par exemple : &type=network">		
			

	</div>

	</td><td></td><td align=center><a class="btn btn-success btn-sm bt_lancerRequete">{{&nbsp;&nbsp;&nbsp;Lancer la requête&nbsp;&nbsp;&nbsp;}} </a>	</td></tr></table>
	</form>

<?php } // fin de test de la source
?>

<script>
	$('.bt_lancerRequete').off('click').on('click', function() {
	var typeCmdInfo="Action";
	if (document.getElementById('Info').checked) typeCmdInfo="Info";
  window.parent.document.getElementById('moniframe').src = 'index.php?v=d&plugin=synologyapi&modal=testAPI&idsynology='+document.getElementById('idsynology').value+'&api='+document.getElementById('API').value+"&typeCmdInfo="+typeCmdInfo+"&method="+document.getElementById('Methode').value+"&autreMethode="+document.getElementById('autreMethode').value+document.getElementById('Parametres').value;
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
		$('#md_modal').load('index.php?v=d&plugin=synologyapi&modal=req&idsynology='+urlAOuvrir).dialog('open');
		};
		
	function changeMethode() {
	var Methode = document.getElementById('Methode').value;
		if (Methode=="autre") 	document.getElementById("autreMethode").type = "text";
		else 					document.getElementById("autreMethode").type = "hidden";
		
	document.getElementById("Info").checked = true;
	if (Methode=="set") document.getElementById("Action").checked = true;
	if (Methode=="update") document.getElementById("Action").checked = true;
	if (Methode=="start") document.getElementById("Action").checked = true;

	}
		
window.closeModal = function(){
	$('#md_modal').dialog('close');
	window.location.reload();
};
</script>

<iframe id="moniframe" name="frame1"
    width="100%"
    height="<?php echo $hauteuriFrame?>"
    src="index.php?v=d&plugin=synologyapi&modal=testAPI&idsynology=<?php echo $arrayURL['idsynology']?>&api=<?php echo $arrayURL['api']?>&method=<?php echo $arrayURL['method'].$URLparametres?>">
</iframe>


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
