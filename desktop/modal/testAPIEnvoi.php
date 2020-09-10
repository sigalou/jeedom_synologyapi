<body style="background-color:#646464;">
<FONT COLOR="#000000">
<?php 

if (!isConnect('admin')) {
  throw new Exception('{{401 - Accès non autorisé}}');
}

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
$parametresAPI=$_POST['parametresAPI'];
$array = $_POST;
//echo json_encode($array); 
unset($array[array_search($idSyno, $array)]); // on enlève IdSyno du tableau Array
unset($array[array_search($_POST['API'], $array)]); // on enlève API du tableau Array
unset($array[array_search($_POST['md5'], $array)]); // on enlève md5 du tableau Array
unset($array[array_search($_POST['parametresAPI'], $array)]); // on enlève parametresAPI du tableau Array

echo "<BR>Sélection du Synology N°".$idSyno." : <font color=#e0e2e2><B>";
if ($idSyno == "1") 
	echo config::byKey('Syno1_name','synologyapi');
if ($idSyno == "2") 
	echo config::byKey('Syno2_name','synologyapi');
if ($idSyno == "3") 
	echo config::byKey('Syno3_name','synologyapi');
echo "</B></font> --- <font color=#8fc935><b>OK</b></font><BR>"; 


//." --- <font color=green><b>OK</b></font><BR>"; 

//echo "----------";
//echo json_encode($array); 
$array = array_keys($array);
$eqLogics = synologyapi::byType('synologyapi');

						$device = synologyapi::byLogicalId($md5, 'synologyapi');
						
						if (!is_object($device)) {
							$device = createNewDevice($API, $md5);
							echo "Création de l'API <font color=#e0e2e2><B>".$API."</B></font> --- <font color=#8fc935><b>OK</b></font><BR>"; 
						} else {
						echo "Modification de l'API <font color=#e0e2e2><B>".$API."</B></font> --- <font color=#8fc935><b>OK</b></font><BR>"; 
						}
							//else echo "<br>Existe"; 
						//log::add('alexaapi_scan', 'debug', '*** [Plugin ' . $pluginAlexaUnparUn . '] ->> détection1 de ' . $device->getName());
						// Update device configuration
						$device->setConfiguration('device', $API);
						//$device->setConfiguration('type', $item['type']);
						$device->setConfiguration('devicetype', $idSyno); // N° du synology 1 ou 2 ou 3
						$device->setConfiguration('urlAPI', $parametresAPI); // chemin de l'API
						//$device->setConfiguration('family', $item['family']);
						//$device->setConfiguration('members', $item['members']);
						//$device->setIsVisible(1);
						//$device->setIsEnable(0);
						//$device->setConfiguration('capabilities', $item['capabilities']);
						try {
							$device->save();
						} catch (Exception $e) {
							$device->setName($API . ' doublon ' . rand(0, 9999));
							$device->save();
						}
/*						$device->setStatus('online', (($item['online']) ? true : false));*/



//echo json_encode($array); 
foreach ($array as $value => $CMDaCreerouModifier)
{
    $CMDaCreerouModifierCorrigee=str_replace("SYNO.", "", str_replace($API."-", "", str_replace("@", ".", $CMDaCreerouModifier)));
	//echo $value; echo '-';
//echo "CMD : ".$CMDaCreerouModifierCorrigee; echo '<br />';
//$test=$CMDaCreerouModifierCorrigee."|truc|machin";
//echo "test1 : ".$test; echo '<br />';
 ////   echo "position : ".(strrpos($test, '|')); echo '<br />';
  //  echo "test : ".substr($CMDaCreerouModifierCorrigee, 0,(strrpos($CMDaCreerouModifierCorrigee, '|'))); echo '<br />';

$LogicalId=$CMDaCreerouModifierCorrigee;
// $name=substr($CMDaCreerouModifierCorrigee, 1+(strrpos($CMDaCreerouModifierCorrigee, '|'))); Non utilisé car on veut le premier champs (CPU par exemple)
$name=str_replace("data|", "", $CMDaCreerouModifierCorrigee); //On supprimer data|
$name=str_replace("|", ":", $name); //On remplace | par : pour le nom de la commande soit plus sympa
				$cmd = $device->getCmd(null, $LogicalId);
				if ((!is_object($cmd))) {
					if (!is_object($cmd)) $cmd = new alexaapiCmd();
					$cmd->setType('info');
					$cmd->setLogicalId($LogicalId);
					$cmd->setSubType('string');
					$cmd->setEqLogic_id($device->getId());
					$cmd->setName($name);
					$cmd->setIsVisible(1);
					if (!empty($setDisplayicon)) $cmd->setDisplay('icon', '<i class="' . $setDisplayicon . '"></i>');
					$cmd->setConfiguration('requestAPI', $CMDaCreerouModifierCorrigee);
					if (!empty($infoName)) $cmd->setConfiguration('infoName', $infoName);
					if (!empty($infoNameArray)) $cmd->setConfiguration('infoNameArray', $infoNameArray);
					if (!empty($listValue)) $cmd->setConfiguration('listValue', $listValue);
					//$cmd->setConfiguration('RunWhenRefresh', $RunWhenRefresh);
					//$cmd->setDisplay('title_disable', $title_disable);
				//	$cmd->setOrder($Order);
				echo "<font color=#e0e2e2><B>".$name."</B></font> (".$LogicalId.")  --- <font color=#8fc935><b>Ajoutée</b></font><BR>"; 

				} else {
				echo "<font color=#e0e2e2><B>".$name."</B></font> (".$LogicalId.")  --- <font color=#c0c0c0><b>Pas d'action</b></font> (existe déja)<BR>"; 
				}
				$cmd->save();


}

					
	function createNewDevice($deviceName, $deviceSerial)
	{
		event::add('jeedom::alert', array('level' => 'success', 'page' => 'synologyapi', 'message' => __('Ajout de "' . $deviceName . '"', __FILE__),));
		$newDevice = new synologyapi();
		$newDevice->setName($deviceName);
		$newDevice->setLogicalId($deviceSerial);
		$newDevice->setEqType_name('synologyapi');
		$newDevice->setIsVisible(1);
		$newDevice->setDisplay('height', '500');
		$newDevice->setConfiguration('device', $deviceName);
		$newDevice->setConfiguration('serial', $deviceSerial);
		$newDevice->setConfiguration('autorefresh', '* * * * *');
		$newDevice->setIsEnable(1);
		return $newDevice;
	}
						
		//	echo parent.document.getElementById(window.name);			
?><br>
<input type='submit' onClick="window.parent.closeModal()" style='background-color:#539f53;font-size:120%;width: 400;border: 0px;padding: 12px 12px;color:#ffaf47;' value='Terminer'>




