<body style="background-color:#646464;">
<FONT COLOR="#000000">

<?php 

if (!isConnect('admin')) {
  throw new Exception('{{401 - Accès non autorisé}}');
}

//$API="SYNO.Core.System.Utilization";
//$method="get";
// ********** LOGIN ************
$API=$_GET['api'];
$method=$_GET['method'];
$parameters=$_GET['plus'];
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

  
		$obj_Data=synologyapi::recupereDonneesJson ($sid, $API, $parametresAPI, $parameters, $idsynology);
		$inforetour=traiteDonneesJson ($API, $obj_Data, $idsynology, $parametresAPI);

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
		console.log(cases[i].name+" - "+API);
		 if ((cases[i].type == 'checkbox') && (cases[i].id == API)) {
			 cases[i].checked = true;
		 }
		}
	} else {
  //console.log("non");
  		for (var i = 0; i < cases.length; i++) {
		 if ((cases[i].type == 'checkbox') && (cases[i].id == API)) {
			 cases[i].checked = false;
		 }
		}
	}
};</script>
<?php

function traiteDonneesJson ($API,$obj_coreData,$idsynology, $parametresAPI)
{

//On va tester si l'API a renvoyé une erreur (xx enregistrements) ou si ça a fonctionné
//echo "success:".$obj_coreData['success'];
//echo "<br>valeur:".json_encode($obj_coreData);

	// Le codage MD5 permet d'enregistré l'empreinte de cette requete, cela est nécessaire car certains API ont des paramètres particuliers, nota : le N° du syno est intégré
	$md5=md5(str_replace("?v=d&plugin=synologyapi&modal=testAPI&api=", "", str_replace("SYNO.", "", $parametresAPI)));

if ($obj_coreData['success']== true) {

	echo '<form action="index.php?v=d&plugin=synologyapi&modal=testAPIEnvoi" method="post">';
		// Avant de traiter les données, il faut aller voir si l'API existe ainsi que ses commandes
		$eqLogics = synologyapi::byType('synologyapi');
		$device = synologyapi::byLogicalId($md5, 'synologyapi');
		$listeExistent=array();
		if (!is_object($device)) {
			$couleurBoutonAjouteModifAPI="ffffff";
			$texteBoutonAjouteModifAPI="Ajouter";
		} else {
			$couleurBoutonAjouteModifAPI="ffaf47";
			$texteBoutonAjouteModifAPI="Modifier";
						//echo json_encode(jeeObject::fullData(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE, 1024);
			// Faut chercher les commandes déja créées
			foreach ($device->getCmd('info') as $cmd) {
					array_push($listeExistent, $cmd->getName());
			}	
		}


	echo "<table border=0 width=100%><tr><td width=50%><b>Consignes</b> :
	<br>1. Cocher ci-dessous les informations que vous souhaitez utiliser dans Jeedom
	<br>2. Cliquer sur le bouton ".$texteBoutonAjouteModifAPI." cette API </td><td width=50%><br><input type='submit' style='background-color:#539f53;font-size:120%;width: 400;border: 0px;padding: 12px 12px;color:#".$couleurBoutonAjouteModifAPI.";' value='".$texteBoutonAjouteModifAPI." cette API'>";
	echo "</td></tr></table>";
		
	// <form action="/action_page.php" method="get"> 
	echo '<br><input id="API" name="API" type="hidden" value="'.$API.'">';
	echo '<input id="md5" name="md5" type="hidden" value="'.$md5.'">';
	echo '<input id="IdSyno" name="IdSyno" type="hidden" value="'.$idsynology.'">';
	echo '<input id="parametresAPI" name="parametresAPI" type="hidden" value="'.str_replace("v=d&plugin=synologyapi&modal=testAPI&", "", $parametresAPI).'">'; 
	echo '<br><input type="checkbox" onchange="actionCaseCocherTOUT(this)" ><FONT COLOR="#000000">Tout</font><br>';
		$resultat=array();
		foreach ($obj_coreData as $key => $value) {
			$nom_Valeur1=$API.".".$key;
			if (is_array($value)) {
				//echo "<br>c'est un array1";
				if (empty($value)) {
					//echo "vide";
				} else {
					foreach ($value as $key2 => $value2) {
						$nom_Valeur2=$API."-".$key.".".$key2;
						echo '<br><input type="checkbox" onchange="actionCaseCocher(this,`'.$nom_Valeur2.'`)" ><b>'.str_replace("SYNO.", "", $API)."-".$key.".".$key2."</b>";
						if (is_array($value2)) {
							//echo "<br>c'est un array2";
							if (empty($value2)) {
								//echo "vide";
							} else {
								foreach ($value2 as $key3 => $value3) {
									//echo "<br><br>1".$key3;
									$nom_Valeur3=$API."-".$key.".".$key2.".".$key3;
									if (is_array($value3)) {
										//echo "<br>c'est un array3";
										if (empty($value3)) {
											//echo "vide";
										} else {
											foreach ($value3 as $key4 => $value4) {
												$nom_Valeur4=$API."-".$key.".".$key2.".".$key3.".".$key4;
												if (is_array($value4)) {
													//echo "<br>c'est un array4";
													if (empty($value4)) {
														//echo "vide";
													} else {
														foreach ($value4 as $key5 => $value5) {
															$nom_Valeur5=$API."-".$key.".".$key2.".".$key3.".".$key4.".".$key5;
															if (is_array($value5)) {
																//echo "<br>c'est un array";
																if (empty($value5)) {
																	//echo "vide";
																} else {
																	foreach ($value5 as $key6 => $value6) {
																		$nom_Valeur6=$API."-".$key.".".$key2.".".$key3.".".$key4.".".$key5.".".$key6;
																		if (is_array($value6)) {
																			//echo "<br>c'est un array";
																			if (empty($value6)) {
																				//echo "vide";
																			} else {
																				echo "pasvide!!!!!!!!!!!!!!!!!!!!!!!!!!";
																			}
																		}
																			else {
																			echo '<li><input type="checkbox" ';
																			if (array_search ( str_replace($API."-", "", $nom_Valeur6) , $listeExistent)!== false) echo " checked ";
																			echo 'id="'.$nom_Valeur2.'" name="'.str_replace(".", "@", $nom_Valeur6).'"><FONT COLOR="#e0e2e2">'.str_replace("SYNO.", "", $nom_Valeur6).'</FONT> : <FONT COLOR="#ffed4a"><B>'.$value6.'</B></FONT></li>';
																			$resultat[$nom_Valeur6] = $value6;
																			}
																	}
																}
															}
																else {
																echo '<li><input type="checkbox" ';
																if (array_search ( str_replace($API."-", "", $nom_Valeur5) , $listeExistent)!== false) echo " checked ";
																echo 'id="'.$nom_Valeur2.'" name="'.str_replace(".", "@", $nom_Valeur5).'"><FONT COLOR="#e0e2e2">'.str_replace("SYNO.", "", $nom_Valeur5).'</FONT> : <FONT COLOR="#ffed4a"><B>'.$value5.'</B></FONT></li>';
																$resultat[$nom_Valeur5] = $value5;
																}
														}
														
													}
												}
													else {
												echo '<li><input type="checkbox" ';
												if (array_search ( str_replace($API."-", "", $nom_Valeur4) , $listeExistent)!== false) echo " checked ";
												echo 'id="'.$nom_Valeur2.'" name="'.str_replace(".", "@", $nom_Valeur4).'"><FONT COLOR="#e0e2e2">'.str_replace("SYNO.", "", $nom_Valeur4).'</FONT> : <FONT COLOR="#ffed4a"><B>'.$value4.'</B></FONT></li>';												
												$resultat[$nom_Valeur4] = $value4;
													}
											}
										}
									}
										else {
										echo '<li><input type="checkbox" ';
										if (array_search ( str_replace($API."-", "", $nom_Valeur3) , $listeExistent)!== false) echo " checked ";
										echo 'id="'.$nom_Valeur2.'" name="'.str_replace(".", "@", $nom_Valeur3).'"><FONT COLOR="#e0e2e2">'.str_replace("SYNO.", "", $nom_Valeur3).'</FONT> : <FONT COLOR="#ffed4a"><B>'.$value3.'</B></FONT></li>';
										$resultat[$nom_Valeur3] = $value3;
										}
								}
							}
						}
							else {
							echo '<li><input type="checkbox" ';
							if (array_search ( str_replace($API."-", "", $nom_Valeur2) , $listeExistent)!== false) echo " checked ";
							echo 'id="'.$nom_Valeur2.'" name="'.str_replace(".", "@", $nom_Valeur2).'"><FONT COLOR="#e0e2e2">'.str_replace("SYNO.", "", $nom_Valeur2).'</FONT> : <FONT COLOR="#ffed4a"><B>'.$value2.'</B></FONT></li>';
							$resultat[$nom_Valeur2] = $value2;
							}
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
else 	{
	//echo ">".$parametresAPI;
	// On a une erreur
			$CodeError=$obj_coreData['error']['code']; //{"error":{"code":119},"success":false}
	//echo '<form action="index.php?v=d&plugin=synologyapi&modal=testAPIEnvoi" method="post">';
	echo "<table border=0 width=100%><tr><td width=50%>Oups ! Echec !<br>Debug :<b>";
	echo json_encode($obj_coreData)."</b> ".date("Y-m-d H:i:s");
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
		if ($CodeError=="400") echo "<br>Le code Erreur <b>400</b> signifie : <b>Invalid credentials</b>";
		if ($CodeError=="401") echo "<br>Le code Erreur <b>401</b> signifie : <b>Account disabled</b>";
		if ($CodeError=="402") echo "<br>Le code Erreur <b>402</b> signifie : <b>Permission denied</b>";
		if ($CodeError=="403") echo "<br>Le code Erreur <b>403</b> signifie : <b>2-step verification code required</b>";
		if ($CodeError=="404") echo "<br>Le code Erreur <b>404</b> signifie : <b>Failed to authenticate 2-step verification code</b>";

		$resultat=$obj_coreData;
		}
	
	return $resultat;
}
	


//SECURITY Logout and destroying SID
$json_logout = file_get_contents($server.'/webapi/'.$path.'?api=SYNO.API.Auth&method=logout&version='.$vAuth.'&_sid='.$sid, false, stream_context_create($arrContextOptions));
$obj_logout = json_decode($json_logout);	
if($obj_logout->success == 1){
	//echo '<br>Logout SUCCESS : session closed';
} else {
	echo '<br>Logout FAILED : please check code due to security issues!';
}	

//require_once('request.SYNO.Logout.php');  
?>
<?php include_file('desktop', 'synologyapi', 'css', 'synologyapi'); ?>