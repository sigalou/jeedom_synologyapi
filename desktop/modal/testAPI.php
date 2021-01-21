<body style="background-color:#646464;">
<FONT COLOR="#000000">
<?php 

if (!isConnect('admin')) {
  throw new Exception('{{401 - Accès non autorisé}}');
}
//include_file('desktop', 'bootstrap/css/bootstrap-theme', 'css');
	$plugin = plugin::byId('synologyapi');
	//sendVarToJS('eqType', $plugin->getId());
	$eqLogics = eqLogic::byType($plugin->getId());

//$API="SYNO.Core.System.Utilization";
//$method="get";
// ********** LOGIN ************
$API=$_GET['api'];
$method=$_GET['method'];
$parameters=$_GET['Parametres'];
$idsynology=$_GET['idsynology'];
//echo "<br>Limite : ".$_GET['limit']; 
//echo "<br>Masque échecs : ".$_GET['cache_errors']; 



function getURI(){
    $adresse = "";$_SERVER['PHP_SELF'];
    $i = 0;
    foreach($_GET as $cle => $valeur){
        $adresse .= ($i == 0 ? '?' : '&').$cle.($valeur ? '='.$valeur : '');
        $i++;
    }
    return $adresse;
}



		$parametresAPI=getURI();
		$sid=synologyapi::vaChercherSID($idsynology);

  // recupereDonneesJson ($sid,"SYNO.Core.System.Utilization","get",$server,$arrContextOptions);
  // recupereDonneesJson ($sid,"SYNO.Core.System","info",$server,$arrContextOptions);
 //  recupereDonneesJson ($sid,"SYNO.Core.System","info&type=network",$server,$arrContextOptions);
   //recupereDonneesJson ($sid,"SYNO.Core.System","info&type=storage",$server,$arrContextOptions);
  // recupereDonneesJson ($sid,"SYNO.Core.SyslogClient.Log","list&logtype=ftp,filestation,webdav,cifs,tftp",$server,$arrContextOptions);
  //recupereDonneesJson ($sid,"SYNO.Core.CurrentConnection","list&start=0&limit=50&sort_by=%22time%22&sort_direction=%22DESC%22&offset=0&action=%22enum%22",$server,$arrContextOptions);
  
  
//echo "<br>API : ".$API; 
//echo "<br>parametresAPI : ".$parametresAPI; 
//echo "<br>parameters : ".$parameters; 

 
	$obj_Data=synologyapi::recupereDonneesJson ($sid, $parametresAPI, $parameters, $idsynology);
	log::add('synologyapi', 'debug', 'résultat: '.json_encode($obj_Data));		
	$inforetour=traiteDonneesJson ($API, $obj_Data, $idsynology, $parametresAPI, $method, $eqLogics);

?>
<script>
function actionCaseCocherTOUT(laCase) {
	var cases = document.getElementsByTagName('input'); // toutes les cases
	console.log(cases);
	if (laCase.checked) {
		console.log("oui");
  		for (var i = 0; i < cases.length; i++) {
		 if (cases[i].type == 'checkbox') {
			 cases[i].checked = true;
		 }
		}
	} else {
  console.log("non");
  		for (var i = 0; i < cases.length; i++) {
		 if (cases[i].type == 'checkbox') {
			 cases[i].checked = false;
		 }
		}
	}
};
function actionCaseCocher(laCase, API) {
	var cases = document.getElementsByTagName('input'); // toutes les cases
	console.log("coche faite");
	console.log(cases);
		//console.log(API);
	if (laCase.checked) {
		//console.log("oui");
  		for (var i = 0; i < cases.length; i++) {
		console.log("------------------------------------");
		//console.log(substr(cases[i].id,0,strlen(API)+" - "+API);
		//console.log(cases[i].id+" - "+API);
		 if ((cases[i].type == 'checkbox') && (cases[i].id.substring(0,API.length)== API)) {
			 cases[i].checked = true;
		 }
		}
	} else {
  //console.log("non");
  		for (var i = 0; i < cases.length; i++) {
		 if ((cases[i].type == 'checkbox') && (cases[i].id.substring(0,API.length)== API)) {
			 cases[i].checked = false;
		 }
		}
	}
};</script>
<?php

function traiteDonneesJson ($API,$obj_coreData,$idsynology, $parametresAPI, $method, $eqLogics)
{

//On va tester si l'API a renvoyé une erreur (xx enregistrements) ou si ça a fonctionné
//echo "success:".$obj_coreData['success'];
//echo "<br>valeur:".json_encode($obj_coreData);

	// Le codage MD5 permet d'enregistré l'empreinte de cette requete, cela est nécessaire car certains API ont des paramètres particuliers, nota : le N° du syno est intégré
	$md5=md5(str_replace("?v=d&plugin=synologyapi&modal=testAPI&api=", "", str_replace("SYNO.", "", $parametresAPI)));
	
	$nomBoutonVert="Sauvegarder";
	if ($obj_coreData['success']== true) {
		if ($method =="set") {
			//log::add('synologyapi', 'debug', 'method!!!!!!!!!**!!!: '.$method);	
			$autresParametres=str_replace("?v=d&plugin=synologyapi&modal=testAPI", "", str_replace("&idsynology=1&","",str_replace("&idsynology=2&","",str_replace("&idsynology=3&","",str_replace("SYNO.", "", $parametresAPI)))));
			//echo "<br>autresParametres : ".$autresParametres; 

			echo "<br><font color=#8fc935>Requête envoyée et executée avec succès !<br>";
			echo "Résultat : <b>".json_encode($obj_coreData)."</b></font>";
			
			echo '<form action="index.php?v=d&plugin=synologyapi&modal=testAPISaveCmd" method="post">';
				/*
				// Avant de traiter les données, il faut aller voir si l'API existe ainsi que ses commandes
				$eqLogics = synologyapi::byType('synologyapi');
				$device = synologyapi::byLogicalId($md5, 'synologyapi');
				$listeExistent=array();
				if (is_object($device)) {
					// Faut chercher les commandes déja créées
					foreach ($device->getCmd('info') as $cmd) {
							array_push($listeExistent, $cmd->getLogicalId());
					}	
				}
				*/
				
			echo '<br><input id="API" name="API" type="hidden" value="'.$API.'">';
			echo '<input id="md5" name="md5"  type="hidden" value="'.$md5.'">';
			echo '<input id="IdSyno" name="IdSyno" type="hidden" value="'.$idsynology.'">';
//			echo '<input id="nomCmd" name="nomCmd" value="">';
			echo '<input id="parametresAPI" type="hidden" name="parametresAPI" value="'.str_replace("v=d&plugin=synologyapi&modal=testAPI&", "", $parametresAPI).'">'; 

			echo '
				<div class="card">
				<div class="card-header" style="background-color:#979797">
				<table border=0 width=100%>
				<tr><td colspan=2><h5 class="card-title"><B>Nouvelle commande avec l\'api '.str_replace("SYNO.", "", $API).'</B></h5></td></tr>
				<tr><td>Titre de la nouvelle commande (à personnaliser) : </td><td><input  id="nouvelleCmd" size=80% name="nouvelleCmd" value="'.str_replace("SYNO.", "", $API).'"></td></tr>
				<tr><td>Requête de la nouvelle commande : </td><td><input id="request" size=90% name="request" readonly value="'.$autresParametres.'"></td></tr>
				<tr><td colspan=2><hr></td></tr>';
			echo "				<tr><td>Ajouter la nouvelle commande à l'équipement : </td><td>";
				//trouver la liste des groupes de cmd
?>
<script>
function formupdate() {
    var selectedPackage = document.getElementById("id_group").value;
    if ( selectedPackage == '' ) {
        document.getElementById('nouveauGroupe').style.display = "block";
    } else {
        document.getElementById('nouveauGroupe').style.display = "none";
	}
}
</script>

								<select style="width:400px;" id="id_group" name="id_group" onchange="formupdate();" class="eqLogicAttr form-control" data-l1key="object_id">
									<option selected value="">{{Créer un nouvel équipement}}</option>
									<?php
										foreach ($eqLogics as $eqLogic) {
											//if (($eqLogic->getConfiguration('devicetype') == $idsynology) && ($eqLogic->getConfiguration('type') == "cmd")) {
											if ($eqLogic->getConfiguration('devicetype') == $idsynology) {
											echo '<option value="' . $eqLogic->getId() . '">' . $eqLogic->getName() .'</option>';
											}
										}
										?>
								</select>
				<div id="nouveauGroupe" style="display:block;"><input id="nouveauGroupe" style="width:400px;" name="nouveauGroupe" placeholder="{{Nom du nouvel équipement}}"></div>
<?php
				
			//	<input id="nouveauGroupe" size=90% name="nouveauGroupe" value="Mes commandes">';
				
			echo '	</td></tr>
				<tr><td></td><td><input type="submit" style="background-color:#67b367;width: 200;border: 0px;padding: 12px 12px;color:#efefef"  value="Ajouter cette commande"></td></TR>
				</table>
				</div>
				';

			echo '</form>';			

			
		} else {
		
			echo '<form action="index.php?v=d&plugin=synologyapi&modal=testAPISaveRequete" method="post">';
				// Avant de traiter les données, il faut aller voir si l'API existe ainsi que ses commandes
				$eqLogics = synologyapi::byType('synologyapi');
				$device = synologyapi::byLogicalId($md5, 'synologyapi');
				$listeExistent=array();
				if (is_object($device)) {
					$nomBoutonVert="Modifier";
					echo "<div class='alert-info bg-success'>Cette requète existe déja, l'équipement Jeedom correspondant est ";
					echo '<a class="btn btn-default btn-sm roundedRight" href="' . $device->getLinkToConfiguration() . '"  target="_blank"><b><u>'.$device->getName().'</u></b></a></div>';

					// Faut chercher les commandes déja créées
					foreach ($device->getCmd('info') as $cmd) {
							array_push($listeExistent, $cmd->getLogicalId());
					}	
				}
			//calage checkbox indéterminé : https://mdbootstrap.com/docs/jquery/forms/checkbox/
			// <form action="/action_page.php" method="get"> 
			echo '<br><input id="API" name="API" type="hidden" value="'.$API.'">';
			echo '<input id="md5" name="md5" type="hidden" value="'.$md5.'">';
			echo '<input id="IdSyno" name="IdSyno" type="hidden" value="'.$idsynology.'">';
			echo '<input id="parametresAPI" name="parametresAPI" type="hidden" value="'.str_replace("v=d&plugin=synologyapi&modal=testAPI&", "", $parametresAPI).'">'; 
			echo '
				<div class="card">
				  <div class="card-header" style="background-color:#454648"><table border=0 width=100%><tr><td>
									<h5 class="card-title">
									<input style="position: relative;left:150px;" type="checkbox" class="custom-control-input" id="tout" onchange="actionCaseCocherTOUT(this)" >
									<label style="color:#ffffff;" class="custom-control-label" for="tout"><B>'.str_replace("SYNO.", "", $API).'</B></label></h5></td><td align=right>
									<input type="submit" style="background-color:#67b367;width: 200;border: 0px;padding: 12px 12px;color:#efefef"  value="'.$nomBoutonVert.'"></td></TR></table>
								</div>
				  <div class="card-body"  style="background-color:#727272">				
				';
				$resultat=array();
				foreach ($obj_coreData as $key => $value) {
					$nom_Valeur1=$API."|".$key;
					if (is_array($value)) {
						//echo "<br>c'est un array1";
						if (empty($value)) {
							//echo "vide";
						} else {
							foreach ($value as $key2 => $value2) {
								$nom_Valeur2=$API."-".$key."|".$key2;
								echo '<br>
								<div class="card">
								  <div class="card-header" style="background-color:#454648"><h5 class="card-title">
													<input style="position: relative;left:150px;" type="checkbox" class="custom-control-input" id="'.$key2.'" onchange="actionCaseCocher(this,`'.$nom_Valeur2.'`)" >
													<label style="color:#ffffff;" class="custom-control-label" for="'.$key2.'">'.$key2.'</label></h5>
												</div>
								  <div class="card-body" style="background-color:#646464">
								';
								if (is_array($value2)) {
									//echo "<br>c'est un array2";
									if (empty($value2)) {
										//echo "vide";
									} else {
										foreach ($value2 as $key3 => $value3) {
											//echo "<br><br>1".$key3;
											$nom_Valeur3=$API."-".$key."|".$key2."|".$key3;
											$nom_Valeur3_courte=$key2."|".$key3;
											if (is_array($value3)) {
												echo "<hr>";
												//echo "<br>c'est un array3";
												if (empty($value3)) {
													//echo "vide";
												} else {
													foreach ($value3 as $key4 => $value4) {
														$nom_Valeur4=$API."-".$key."|".$key2."|".$key3."|".$key4;
														if (is_array($value4)) {
															//echo "<br>c'est un array4";
															if (empty($value4)) {
																//echo "vide";
															} else {
																foreach ($value4 as $key5 => $value5) {
																	$nom_Valeur5=$API."-".$key."|".$key2."|".$key3."|".$key4."|".$key5;
																	if (is_array($value5)) {
																		//echo "<br>c'est un array5";
																		if (empty($value5)) {
																			//echo "vide";
																		} else {
																			foreach ($value5 as $key6 => $value6) {
																				$nom_Valeur6=$API."-".$key."|".$key2."|".$key3."|".$key4."|".$key5."|".$key6;
																				if (is_array($value6)) {
																					//echo "<br>c'est un array";
																					if (empty($value6)) {
																						//echo "vide";
																					} else {
																						echo "pasvide!!!!!!!!!!!!!!!!!!!!!!!!!!";
																					}
																				}
																			else afficheSectionaCocher ($API, $key6, $nom_Valeur6, $value6, $listeExistent);
																			}
																		}
																	}
																	else afficheSectionaCocher ($API, $key5, $nom_Valeur5, $value5, $listeExistent);
																}
																
															}
														}
															else afficheSectionaCocher ($API, $key4, $nom_Valeur4, $value4, $listeExistent);
													}
												}
											}
												else afficheSectionaCocher ($API, $key3, $nom_Valeur3, $value3, $listeExistent);
										}
									}
								}
									else afficheSectionaCocher ($API, $key2, $nom_Valeur2, $value2, $listeExistent);
									echo '  </div>
										</div>';
							}
						}
					}
						else {
						//	if ($value==false) $value="False";
						//echo '<li><input type="checkbox" id="'.$nom_Valeur1.'" name="'.$nom_Valeur1.'"><FONT COLOR="#e0e2e2">'.str_replace("SYNO.", "", $nom_Valeur1).'</FONT> : <FONT COLOR="#ffed4a"><B>'.$value1.'</B></FONT></li>';
						//$resultat[$nom_Valeur1] = $value;
						}
					}
			echo $BoutonAjouteModifAPI;	
			echo '</form>';
		}
	}
	else 	{
	//echo ">".$parametresAPI;
	// On a une erreur
			$CodeError=$obj_coreData['error']['code']; //{"error":{"code":119},"success":false}
	//echo '<form action="index.php?v=d&plugin=synologyapi&modal=testAPISaveRequete" method="post">';
	echo "<table border=0 width=100%><tr><td width=50%><b>Oups ! Echec !</b><br><br>Debug :<b><br><ul>";
	echo "<li>". str_replace("v=d&plugin=synologyapi&modal=testAPI&", "", $parametresAPI)."</li><li>";
	echo json_encode($obj_coreData)."</b></li><li>".date("Y-m-d H:i:s")."</li></ul>";
	echo "</td><td width=50%><br> <button style='background-color:#ffa638;font-size:120%;width: 400;border: 0px;padding: 12px 12px;color:#ffffff;' onclick='window.parent.lanceAPIdepuisIFrame(\"".$parametresAPI."\")'>Relancer la requête</button> ";
	echo "</td></tr></table></form>";

	// Afficher les codes d'erreur :
	if ($CodeError=="100") echo "<br>Le code Erreur <b>100</b> signifie : <b>Erreur inconnue</b>";
	if ($CodeError=="101") echo '<br>Le code Erreur <b>101</b> signifie : <b>Paramètres invalides</b>, corrigez le champs "Autres paramètres"';
	if ($CodeError=="102") echo "<br>Le code Erreur <b>102</b> signifie : <b>L'API n'existe pas</b>, il faut donc en choisir une autre";
	if ($CodeError=="103") echo "<br>Le code Erreur <b>103</b> signifie : <b>La méthode n'existe pas</b>, il faut donc essayer une autre méthode";
	if ($CodeError=="104") echo "<br>Le code Erreur <b>104</b> signifie : <b>Version non supportée</b>, il faut vérifier le paramètre version";
	if ($CodeError=="105") echo "<br>Le code Erreur <b>105</b> signifie : <b>Paramètres utilisateur insuffisants</b>, il y a un souci de droit d'accès";
	if ($CodeError=="106") echo "<br>Le code Erreur <b>106</b> signifie : <b>Connexion en Time-Out</b>, la requete a été trop longue à s'executer";
	if ($CodeError=="107") echo "<br>Le code Erreur <b>107</b> signifie : <b>Multiple login detected</b>";
	if ($CodeError=="117") echo "<br>Le code Erreur <b>107</b> signifie : <b>Need manager rights for operation</b>";
	if ($CodeError=="119") echo "<br>Le code Erreur <b>119</b> arrive ponctuellement, il faut retenter d'envoyer la requête";
	else echo "<br><br>Rappel : La ligne <b>Autres paramètres</b> doit commander par <b>&</b> s'il y a des paramètres";
	if ($CodeError=="400") echo "<br>Le code Erreur <b>400</b> signifie : <b>Invalid credentials</b>";
	if ($CodeError=="401") echo "<br>Le code Erreur <b>401</b> signifie : <b>Account disabled</b>";
	if ($CodeError=="402") echo "<br>Le code Erreur <b>402</b> signifie : <b>Permission denied</b>";
	if ($CodeError=="403") echo "<br>Le code Erreur <b>403</b> signifie : <b>2-step verification code required</b>";
	if ($CodeError=="404") echo "<br>Le code Erreur <b>404</b> signifie : <b>Failed to authenticate 2-step verification code</b>";
	$resultat=$obj_coreData;
	}
	return $resultat;
}
	
function afficheSectionaCocher ($API, $keyX, $nom_ValeurX, $valueX, $listeExistent)
{
	if ($valueX === false ) $valueX="false";
	if ($valueX === true  ) $valueX="true";
	echo '<div class="custom-control custom-checkbox">
	<input type="checkbox" ';
	if (array_search ( str_replace($API."-", "", $nom_ValeurX) , $listeExistent)!== false) echo ' checked="" ';
	echo ' class="custom-control-input" id='.$nom_ValeurX.' name="'.str_replace(".", "@", $nom_ValeurX).'">
	<label class="custom-control-label" for="'.$nom_ValeurX.'">'.str_replace("_", " ", $keyX).'</label> : <FONT COLOR="#cdcdcd">'.$valueX.'</FONT>
	</div>';
}

//SECURITY Logout and destroying SID
/*
$json_logout = file_get_contents($server.'/webapi/'.$path.'?api=SYNO.API.Auth&method=logout&version='.$vAuth.'&_sid='.$sid, false, stream_context_create($arrContextOptions));
$obj_logout = json_decode($json_logout);	
if($obj_logout->success == 1){
	//echo '<br>Logout SUCCESS : session closed';
} else {
	echo '<br>Logout FAILED : please check code due to security issues!';
}	
*/
//require_once('request.SYNO.Logout.php');  
?>
<?php include_file('desktop', 'synologyapi', 'css', 'synologyapi'); ?>
<?php include_file('desktop', 'bootstrap/bootstrap.min', 'css', 'synologyapi'); ?>
<?php include_file('core', 'plugin.template', 'js');?>
