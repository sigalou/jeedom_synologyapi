<?php 

// ********** LOGIN ************
echo "Méthode demandée : ".$_GET['method']; 
echo "<br>Limite : ".$_GET['limit']; 
echo "<br>Masque échecs : ".$_GET['cache_errors']; 

$server = 'https://192.168.0.4:1975'; //your synology nas ip and port
$server = 'https://192.168.0.1:1976'; //your synology nas ip and port
$login = 'admin'; // your created api user
$pass = ''; //your password here in url encode

//Define ssl arguments
$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);

//Get SYNO.API.Auth Path (recommended by Synology for further update) and maxVersion
// https://192.168.0.1:1976/webapi/query.cgi?api=SYNO.API.Info&version=1&method=query&query=SYNO.API.Auth
$json = file_get_contents($server.'/webapi/query.cgi?api=SYNO.API.Info&version=1&method=query&query=SYNO.API.Auth', false, stream_context_create($arrContextOptions));

$obj = json_decode($json);
$path = $obj->data->{'SYNO.API.Auth'}->path;
$vAuth = $obj->data->{'SYNO.API.Auth'}->maxVersion;
//https://192.168.0.4:1975/webapi/auth.cgi?api=SYNO.API.Auth&method=Login&version=2&account=admin&passwd=christel

$json_login = file_get_contents($server.'/webapi/'.$path.'?api=SYNO.API.Auth&version='.$vAuth.'&method=login&account='.$login.'&passwd='.$pass.'&format=sid', false, stream_context_create($arrContextOptions));
$obj_login = json_decode($json_login);
//echo $server.'/webapi/'.$path.'?api=SYNO.API.Auth&version='.$vAuth.'&method=login&account='.$login.'&passwd='.$pass.'&format=sid';

if($obj_login->success != "true"){	echo "Login FAILED core";exit();}
	$sid = $obj_login->data->sid;
   echo "<br>Login SUCCESS";
   
  // recupereDonneesJson ($sid,"SYNO.Core.System.Utilization","get",$server,$arrContextOptions);
  // recupereDonneesJson ($sid,"SYNO.Core.System","info",$server,$arrContextOptions);
 //  recupereDonneesJson ($sid,"SYNO.Core.System","info&type=network",$server,$arrContextOptions);
   //recupereDonneesJson ($sid,"SYNO.Core.System","info&type=storage",$server,$arrContextOptions);
  // recupereDonneesJson ($sid,"SYNO.Core.SyslogClient.Log","list&logtype=ftp,filestation,webdav,cifs,tftp",$server,$arrContextOptions);
  //recupereDonneesJson ($sid,"SYNO.Core.CurrentConnection","list&start=0&limit=50&sort_by=%22time%22&sort_direction=%22DESC%22&offset=0&action=%22enum%22",$server,$arrContextOptions);
   $inforetour=recupereDonneesJson ($sid,"SYNO.API.Info","query",$server,$arrContextOptions);
	
	$limit=$_GET['limit'];
	$compte=0;
	//$compteAPI=intval(count($inforetour)/3);
	//echo "<br>".$compteAPI." API trouvées";
	foreach ($inforetour as $key => $value) {
		//echo "<br>".$key.":".$value;
		if ($value=="_______________________________________________________entry.cgi")
		{
		$compte++;
		$APIaTester=str_replace(".path", "", str_replace("SYNO.API.Info_data.", "", $key));
		//echo "<br><br><FONT COLOR=#FF00FF>".$APIaTester."<FONT>";
		if ($compte<$limit) {
			$method=$_GET['method'];
			$inforetour2=recupereDonneesJson ($sid,$APIaTester,$method,$server,$arrContextOptions);
				if ($inforetour2[$APIaTester."_error.code"] == "103") {
					if (!$_GET['cache_errors']) echo "<br>".$APIaTester." : ERREUR 103 (Méthode <b>".$method."</b> n'existe pas pour cette API) ".$compte;
				} else {
					foreach ($inforetour2 as $key2 => $value2) {
					echo "<br><FONT COLOR=#FF00FF>".$key2.":".$value2."</font>";
					//echo "TEST : ".$inforetour2[$APIaTester."_error.code"];
					} 
				}
			} 
		}
	}

					
function recupereDonneesJson ($sid,$API,$method,$server,$arrContextOptions)
{
	//authentification successful
	$RequeteaEnvoyer=$server.'/webapi/query.cgi?api=SYNO.API.Info&method=Query&version=1&query='.$API;
	//echo "<br>".$RequeteaEnvoyer;
	$json_core = file_get_contents($RequeteaEnvoyer, false, stream_context_create($arrContextOptions));
	$obj_core = json_decode($json_core);
	$path_core = $obj_core->data->{$API}->path;	
	$vCore = $obj_core->data->{$API}->maxVersion;	

	// DEV: LINK FOR LIVE CPU MEMORY AND NETWORK DATA INFO
	//echo '<p>Login SUCCESS! : sid = '.$sid.'</p>';
	//echo '<p>success 2: api path = '.$path_core.'</p>';
	// https://trionix.homeftp.net:5555/webapi/_______________________________________________________entry.cgi?api=SYNO.Core.System.Utilization&method=get&version=1&type=current&resource=cpu	

	//echo $server.'/webapi/'.$path_core.'?api=SYNO.Core.System.Utilization&version='.$vCore.'&method=get&type=current&_sid='.$sid;

	 
	//json of SYNO.Core.System.Utilization (cpu, mem, network etc)
	$RequeteaEnvoyer=$server.'/webapi/'.$path_core.'?api='.$API.'&version='.$vCore.'&method='.$method.'&_sid='.$sid;
	//echo "<br>".$RequeteaEnvoyer;
	//echo "<br>";
	$json_coreData = file_get_contents($RequeteaEnvoyer, false, stream_context_create($arrContextOptions));
	$obj_coreData = json_decode($json_coreData, true);
	//echo "<br>avant boucke";
	//echo $obj_coreData;
	//echo $json_coreData;

	//$array1 = array("color" => "red", 2, 4);
	//$array2 = array("a", "b", "color" => "green", "shape" => "trapezoid", 4);
	//$result = array_merge($array1, $array2);

	return traiteDonneesJson ($API,$obj_coreData);
}	


function traiteDonneesJson ($API,$obj_coreData)
{
	$resultat=array();
	foreach ($obj_coreData as $key => $value) {
		$nom_Valeur=$API.".".$key;
		if (is_array($value)) {
			//echo "<br>c'est un array1";
			if (empty($value)) {
				//echo "vide";
			} else {
				foreach ($value as $key2 => $value2) {
					//---------->>>>>>echo "<br>".$API."_".$key.".".$key2;
					$nom_Valeur=$API."_".$key.".".$key2;
					if (is_array($value2)) {
						//echo "<br>c'est un array2";
						if (empty($value2)) {
							//echo "vide";
						} else {
							foreach ($value2 as $key3 => $value3) {
								//echo "<br><br>1".$key3;
								$nom_Valeur=$API."_".$key.".".$key2.".".$key3;
								if (is_array($value3)) {
									//echo "<br>c'est un array3";
									if (empty($value3)) {
										//echo "vide";
									} else {
										foreach ($value3 as $key4 => $value4) {
											$nom_Valeur=$API."_".$key.".".$key2.".".$key3.".".$key4;
											if (is_array($value4)) {
												//echo "<br>c'est un array4";
												if (empty($value4)) {
													//echo "vide";
												} else {
													foreach ($value4 as $key5 => $value5) {
														$nom_Valeur=$API."_".$key.".".$key2.".".$key3.".".$key4.".".$key5;
														if (is_array($value5)) {
															//echo "<br>c'est un array";
															if (empty($value5)) {
																//echo "vide";
															} else {
																foreach ($value5 as $key6 => $value6) {
																	$nom_Valeur=$API."_".$key.".".$key2.".".$key3.".".$key4.".".$key5.".".$key6;
																	if (is_array($value6)) {
																		//echo "<br>c'est un array";
																		if (empty($value6)) {
																			//echo "vide";
																		} else {
																			echo "pasvide!!!!!!!!!!!!!!!!!!!!!!!!!!";
																		}
																	}
																		else {
																		//echo '<li><FONT COLOR="#0000FF">'.$nom_Valeur.'</FONT> :<FONT COLOR="#FF00FF">'.$value6.'</FONT></li>';
																		$resultat[$nom_Valeur] = $value6;
																		}
																}
															}
														}
															else {
															//echo '<li><FONT COLOR="#0000FF">'.$nom_Valeur.'</FONT> :<FONT COLOR="#FF00FF">'.$value5.'</FONT></li>';
															$resultat[$nom_Valeur] = $value5;
															}
													}
													
												}
											}
												else {
												//echo '<li><FONT COLOR="#0000FF">'.$nom_Valeur.'</FONT> :<FONT COLOR="#FF00F4">'.$value4.'</FONT></li>';
												$resultat[$nom_Valeur] = $value4;
												}
										}
									}
								}
									else {
									//echo '<li><FONT COLOR="#0000FF">'.$nom_Valeur.'</FONT> :<FONT COLOR="#FF00F0">'.$value3.'</FONT></li>';
									$resultat[$nom_Valeur] = $value3;
									}
							}
						}
					}
						else {
						//echo '<li><FONT COLOR="#0000FF">'.$nom_Valeur.'</FONT> :<FONT COLOR="#FF00FA">'.$value2.'</FONT></li>';
						$resultat[$nom_Valeur] = $value2;
						}
				}
			}
		}
			else {
			//echo '<li><FONT COLOR="#0000FF">'.$nom_Valeur.'</FONT> :<FONT COLOR="#FF00FF">'.$value.'</FONT></li>';
			$resultat[$nom_Valeur] = $value;
			}
		}
		return $resultat;
}
	


//SECURITY Logout and destroying SID
$json_logout = file_get_contents($server.'/webapi/'.$path.'?api=SYNO.API.Auth&method=logout&version='.$vAuth.'&_sid='.$sid, false, stream_context_create($arrContextOptions));
$obj_logout = json_decode($json_logout);	
if($obj_logout->success == 1){
	echo '<br>Logout SUCCESS : session closed';
} else {
	echo '<br>Logout FAILED : please check code due to security issues!';
}	

//require_once('request.SYNO.Logout.php');  
 
?>