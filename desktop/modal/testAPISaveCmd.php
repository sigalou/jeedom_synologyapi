<body style="background-color:#646464;">
<FONT COLOR="#000000">
<?php 

if (!isConnect('admin')) {
  throw new Exception('{{401 - Accès non autorisé}}');
}
include_file('desktop', 'synologyapi', 'css', 'synologyapi'); 

//$API="SYNO.Core.System.Utilization";
//$method="get";
// ********** LOGIN ************
//$method=$_GET['method'];
//$parameters=$_GET['plus'];
//$idsynology=$_GET['idsynology'];
//echo "<br>Limite : ".$_GET['limit']; 
//echo "<br>Masque échecs : ".$_GET['cache_errors']; 
$API=str_replace("SYNO.", "", $_POST['API']);
$idSyno=$_POST['IdSyno'];
//echo "IdSyno:".$idSyno."<BR>"; 
//echo "md5:".$_POST['md5']."<BR>"; 
$md5=$_POST['md5'];
$nouveauGroupe=$_POST['nouveauGroupe'];
$id_group=$_POST['id_group'];
$request=$_POST['request'];
$nouvelleCmd=$_POST['nouvelleCmd'];
$parametresAPI=$_POST['parametresAPI'];
//$array = $_POST;
//echo json_encode($array); 
//unset($array[array_search($idSyno, $array)]); // on enlève IdSyno du tableau Array
//unset($array[array_search($_POST['API'], $array)]); // on enlève API du tableau Array
//unset($array[array_search($_POST['md5'], $array)]); // on enlève md5 du tableau Array
//unset($array[array_search($_POST['parametresAPI'], $array)]); // on enlève parametresAPI du tableau Array

echo "<BR><span class='badge-info'>Ajout d'une nouvelle commande</span><br><br><font color=#e0e2e2><B>";
echo "<BR>► Sélection du Synology N°".$idSyno." : <span class='badge-info'> ";
if ($idSyno == "1") 
	echo config::byKey('Syno1_name','synologyapi');
if ($idSyno == "2") 
	echo config::byKey('Syno2_name','synologyapi');
if ($idSyno == "3") 
	echo config::byKey('Syno3_name','synologyapi');
echo " </span>     <span class='badgenonvolant badge-success'>OK</span><BR><br>"; 


//." --- <font color=green><b>OK</b></font><BR>"; 

//echo "----------";
//echo $id_group;
//echo "----------";
if (($nouveauGroupe =="") && ($id_group=="")) {
echo "► <span class='badge-warning'>Le nom du groupe de commandes à utiliser est vide.</span>      <span class='badgenonvolant badge-danger'>Echec</span><br>Dans l'écran précédent, choisissez un groupe existant ou donnez un nom au nouveau groupe de commandes.<br>"; 
exit();	
}




//echo json_encode($array); 
//$array = array_keys($array);
$eqLogics = synologyapi::byType('synologyapi');

						$group = synologyapi::byId($id_group, 'synologyapi');
						$groupeID = strtolower(str_replace(" ", "", $nouveauGroupe));
						
						if (!is_object($group)) {
							$group = createNewGroup($nouveauGroupe, $groupeID, $idSyno);
							echo "► Création du groupe de commandes <span class='badge-info'> ".$nouveauGroupe." </span>    <span class='badgenonvolant badge-success'>OK</span><BR><br>"; 
						} else {
						echo "► Sélection du groupe <span class='badge-info'> ".$group->getName()." </span>     <span class='badgenonvolant badge-success'>OK</span><BR><br>"; 
						}
							//else echo "<br>Existe"; 
						//log::add('alexaapi_scan', 'debug', '*** [Plugin ' . $pluginAlexaUnparUn . '] ->> détection1 de ' . $group->getName());
						// Update group configuration
						//$group->setConfiguration('device', $groupeID);
						//$group->setConfiguration('type', $item['type']);
						//$group->setConfiguration('urlAPI', $parametresAPI); // chemin de l'API
						//$group->setConfiguration('family', $item['family']);
						//$group->setConfiguration('members', $item['members']);
						//$group->setIsVisible(1);
						//$group->setIsEnable(0);
						//$group->setConfiguration('capabilities', $item['capabilities']);
						try {
							$group->save();
						} catch (Exception $e) {
							$group->setName($groupeID . ' doublon ' . rand(0, 9999));
							$group->save();
						}
/*						$group->setStatus('online', (($item['online']) ? true : false));*/



//echo json_encode($array); 
//foreach ($array as $value => $CMDaCreerouModifier)

	//echo "<br>!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!";
 	//echo $parametresAPI;
   
	// On doit enlever SYNO. mais uniquement dans la partie du nom de l'API et non la partie du chemin vers la donnée
	//$partiedelAPI=substr($parametresAPI, 0, strrpos($parametresAPI, '-'));
	//$secondepartie=substr($parametresAPI, 1+strrpos($parametresAPI, '-'));
	$LogicalId=str_replace("api=SYNO.", "", str_replace($API."-", "", str_replace("@", ".", $parametresAPI)));
	$LogicalId=str_replace("?idsynology=", "", $LogicalId);
	$LogicalId=str_replace("&", "_", $LogicalId);
	//echo "<br>tout:".$CMDaCreerouModifier;
	//echo "<br>partiedelAPI:".$partiedelAPI;
	//echo "<br>secondepartie:".$secondepartie;
	//echo "<br>.";
	
	//echo "<br>AVANT:".$CMDaCreerouModifier;
	//echo "<br>APRES:".$LogicalId;
	
//test : ".substr($LogicalId, 0,(strrpos($LogicalId, '|'))); echo '<br />';

	//echo $value; echo '-';
//echo "CMD : ".$LogicalId; echo '<br />';
//$test=$LogicalId."|truc|machin";
//echo "test1 : ".$test; echo '<br />';
 ////   echo "position : ".(strrpos($test, '|')); echo '<br />';
  //  echo "test : ".substr($LogicalId, 0,(strrpos($LogicalId, '|'))); echo '<br />';

            

			foreach ($group->getCmd() as $cmdAtester) {
				if ($cmdAtester->getName() == $nouvelleCmd) {
				echo "► <span class='badge-warning'>Une commande porte déja le nom : ".$nouvelleCmd."</span>      <span class='badgenonvolant badge-danger'>Echec</span><br>Dans l'écran précédent, choisissez un autre nom de commande.<br>"; 
				exit();	
				}
			}

				$cmd = $group->getCmd(null, $LogicalId);
				if (!is_object($cmd)) {
					$cmd = new alexaapiCmd();
					$cmd->setType('action');
					$cmd->setSubType('other');
					$cmd->setLogicalId($LogicalId);
					$cmd->setEqLogic_id($group->getId());
					$cmd->setName($nouvelleCmd);
					$cmd->setIsVisible(1);
					$cmd->setConfiguration('request', $request);
					try {
						$cmd->save();
						echo "► <font color=#e0e2e2><B>Nouvelle commande : </B></font><span class='badge-info'> ".$nouvelleCmd." </span>     <span class='badgenonvolant badge-success'>Ajoutée</span><BR>"; 
					} catch (Exception $e) {
						$nouvelleCmd=$nouvelleCmd . ' doublon ' . rand(0, 9999);
						$cmd->setName($nouvelleCmd );
						$cmd->save();
						echo "► <font color=#e0e2e2><B>Nouvelle commande : </B></font><span class='badge-info'> ".$nouvelleCmd." </span>     <span class='badgenonvolant badge-success'>Ajoutée</span><BR>"; 
					}
				} else {
				echo "► <span class='badge-info'> ".$nouvelleCmd." </span>     <span class='badge-warning'> Cette commande existe déja dans le groupe ".$group->getName()."</span>     <span class='badgenonvolant badge-danger'>Echec</span><BR>"; 
				}




					
	function createNewGroup($groupeName, $deviceSerial, $idSyno)
	{
		event::add('jeedom::alert', array('level' => 'success', 'page' => 'synologyapi', 'message' => __('Ajout du groupe de commandes "' . $groupeName . '"', __FILE__),));
		$newDevice = new synologyapi();
		$newDevice->setName($groupeName);
		$newDevice->setLogicalId($deviceSerial);
		$newDevice->setEqType_name('synologyapi');
		$newDevice->setIsVisible(1);
		$newDevice->setDisplay('height', '500');
		$newDevice->setConfiguration('devicetype', $idSyno); // N° du synology 1 ou 2 ou 3
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		//$newDevice->setConfiguration('device', $groupeName);
		$newDevice->setConfiguration('nomGroup', $groupeName);
		$newDevice->setConfiguration('serial', $deviceSerial);
		$newDevice->setConfiguration('autorefresh', '* * * * *');
		$newDevice->setConfiguration('type', "cmd"); // all = toutes les api // syno=un device syno // cmd=un groupe de commandes
		$newDevice->setIsEnable(1);
		return $newDevice;
	}
						
		//	echo parent.document.getElementById(window.name);			
?><br>
<input type='submit' onClick="window.parent.closeModal()" style='background-color:#539f53;font-size:120%;width: 400;border: 0px;padding: 12px 12px;color:#ffaf47;' value='Terminer'>




