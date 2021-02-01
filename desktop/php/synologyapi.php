<?php
	if (!isConnect('admin')) {
		throw new Exception('{{401 - Accès non autorisé}}');
	}
	$plugin = plugin::byId('synologyapi');
	sendVarToJS('eqType', $plugin->getId());
	$eqLogics = eqLogic::byType($plugin->getId());
	 
	function afficheIcones($plugin, $eqLogics, $idSyno) {
		foreach ($eqLogics as $eqLogic) {
			// On recompte le nb de commandes pour actualiser les compteurs Info et Cmd
			$compteurinfo=count($eqLogic->getCmd('info'));
			$compteurcmd=0;
			foreach ($eqLogic->getCmd('action') as $cmd) {
				if ($cmd->getLogicalId() != "refresh") $compteurcmd++;
			}
			if (($compteurinfo != $eqLogic->getConfiguration('compteurinfo')) || ($compteurcmd != $eqLogic->getConfiguration('compteurcmd'))) {
				$eqLogic->setConfiguration('compteurinfo', $compteurinfo);
				$eqLogic->setConfiguration('compteurcmd', $compteurcmd);
					if ($compteurinfo==0) 
						$eqLogic->setConfiguration('type', 'cmd');
					elseif ($compteurcmd==0) 
						$eqLogic->setConfiguration('type', 'syno');
					else
						$eqLogic->setConfiguration('type', 'cmdinfo');					
				$eqLogic->save();
			}
				if ($eqLogic->getConfiguration('devicetype') == $idSyno) {
					if ($eqLogic->getConfiguration('type') == "cmd") {
							if ($compteurcmd>1) $Salafin="s"; else $Salafin="" ;
							$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
							echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
							echo '<span class="badge badge-info">'.$compteurcmd.' Cmd'.$Salafin.'</span>';
							echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
							echo '<br>';
							echo '<span class="name" >'. $eqLogic->getHumanName(true, true) .'</span>';
							echo '</div>';
					}	elseif ($eqLogic->getConfiguration('type') == "cmdinfo") {
							$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
							echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
							echo '<span class="badge badge-info">'.$compteurcmd.'</span>';
							echo '<span class="badgecentre badge-purple">'.$compteurinfo.'</span>';
							echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
							echo '<br>';
							echo '<span class="name" >'. $eqLogic->getHumanName(true, true) .'</span>';
							echo '</div>';
					}	else {	//type vaut syno					
							if ($compteurinfo>1) $Salafin="s"; else $Salafin="" ;
							$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
							echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
							echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
							echo '<span class="badge badge-purple">'.$compteurinfo.' Info'.$Salafin.'</span>';
							echo '<br>';
							echo '<span class="name" >' . $eqLogic->getHumanName(true, true) . '</span>';
							echo '</div>';
					}
				}
		}
	}

	
	?>
<div class="row row-overflow">
	<div class="col-xs-12 eqLogicThumbnailDisplay">
	<legend><i class="fas fa-cog"></i>  {{Gestion}}</legend>
		<div class="eqLogicThumbnailContainer">
			<div class="cursor eqLogicAction logoPrimary" id="bt_req">
				<i class="fas fa-plus-circle"></i>
				<br>
				<span>{{Assistant API}}</span>
			</div>
			<div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
				<i class="fas fa-wrench" ></i>
				<br>
				<span >{{Configuration}}</span>
			</div>
			<div class="cursor eqLogicAction logoSecondary" id="gotofusionner">
				<i class="fas fa-object-ungroup"></i>
				<br>
				<span >{{Fusionner}}</span>
			</div>		
		</div>
	<!-- -------------- Premier SYNO ---------------->  
	<input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
	<legend ><i class="fas fa-cogs"></i> {{API de <?php echo config::byKey('Syno1_name','synologyapi')?>}}</legend>
			<span class="btn btn-secondary btn-sm btn-file"><i class="fas fa-file-import"></i><span class="hidden-xs">{{ Importer un Modèle}}</span><input  id="bt_importSYNODevice1" type="file" name="file" style="display:inline-block;">
			</span>
		<div class="eqLogicThumbnailContainer">
		<?php
		afficheIcones($plugin,$eqLogics, "1");
		echo "</div>";
		
		//-- -------------- deuxième SYNO ----------------
		
	if (config::byKey('Syno2_name','synologyapi')!="") {
		?>
		<legend ><i class="fas fa-cogs"></i> {{API de <?php echo config::byKey('Syno2_name','synologyapi')?>}}</legend>
		<span class="btn btn-secondary btn-sm btn-file"><i class="fas fa-file-import"></i><span class="hidden-xs">{{ Importer un Modèle}}</span><input  id="bt_importSYNODevice2" type="file" name="file" style="display:inline-block;">
			</span>
		<div class="eqLogicThumbnailContainer">
		<?php
		afficheIcones($plugin,$eqLogics, "2");
		echo "</div>";
	}
			
			//-- -------------- troisème SYNO ----------------
			
	if (config::byKey('Syno3_name','synologyapi')!="") {
		?>
		<legend ><i class="fas fa-cogs"></i> {{API de <?php echo config::byKey('Syno3_name','synologyapi')?>}}</legend>
		<span class="btn btn-secondary btn-sm btn-file"><i class="fas fa-file-import"></i><span class="hidden-xs">{{ Importer un Modèle}}</span><input  id="bt_importSYNODevice3" type="file" name="file" style="display:inline-block;">
			</span>
		<div class="eqLogicThumbnailContainer">
		<?php
		afficheIcones($plugin,$eqLogics, "3");
		echo "</div>";
	}
				
				?>
	</div>
	<div id="toutDevice" class="col-lg-12 eqLogic" style="display: none;">
		<div class="input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
			<a class="btn btn-default btn-sm eqLogicAction roundedLeft" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a>
			<a class="btn btn-info btn-sm" id="bt_exportSYNODevice"><i class="fas fa-file-export"></i> <span class="hidden-xs">{{Exporter}}</span>
			<a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a><a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
			</span>
		</div>
		<span style="position:relative;top:+5px;left:+5px;visibility:collapse;" class="eqLogicAttr" data-l1key="configuration" id="typefield" data-l2key="type"> </span>
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
			<li id="OngletInfo"  role="presentation"><a href="#TabInfo"  aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
			<li id="OngletInfo2" role="presentation"><a href="#TabInfo2" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-download"></i> {{Informations à récupérer}} (<span class="eqLogicAttr" data-l1key="configuration" data-l2key="compteurinfo"></span>)</a></li>
			<li id="OngletCmd"   role="presentation"><a href="#TabCmd"   aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-upload"></i> {{Commandes à envoyer}} (<span class="eqLogicAttr" data-l1key="configuration" data-l2key="compteurcmd"></span>)</a></li>
		</ul>
		<!---------------- Onglet ListeAPI --------------  
		<div id="EcranListeAPI" class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
			<div role="tabpanel" class="tab-pane active" id="listeAPItab">
				<div role="tabpanel" class="tab-pane" id="commandtabAPI">
					<table id="table_API" class="table table-bordered table-condensed">
						<thead>
							<tr>
								<th>{{API}}</th>
								<th>{{--}}</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>-->
		<div class="tab-content">
		<!---------------- Onglet Equipement ---------------->  
			<div role="tabpanel" class="tab-pane " id="TabInfo">
				<form class="form-horizontal">
					<fieldset><br><br>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Nom de l'équipement Jeedom}}</label>
							<div class="col-sm-3">
								<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
								<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement synologyapi}}"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" >{{Objet parent}}</label>
							<div class="col-sm-3">
								<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
									<option value="">{{Aucun}}</option>
									<?php
										foreach (jeeObject::all() as $object) {
											echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
										}
										?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Catégorie}}</label>
							<div class="col-sm-9">
								<?php
									foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
									echo '<label class="checkbox-inline">';
									echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
									echo '</label>';
									}
									 ?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label"></label>
							<div class="col-sm-9">
								<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
								<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
							</div>
						</div>
					</fieldset>
				</form>
			</div>
			<div role="tabpanel" class="tab-pane" id="TabInfo2">
				<form class="form-horizontal">
					<fieldset>
						<legend><i class="fas fa-exchange-alt" style="font-size : 2em;"></i> <span >{{Identification de l'API à utiliser}}</span></legend>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Nom de l'API Synology}}</label>
							<div class="col-sm-5">
								<span style="position:relative;top:+5px;left:+5px;" class="eqLogicAttr" data-l1key="configuration" data-l2key="device"> </span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Url API Synology}}</label>
							<div class="col-sm-5">
								<span style="position:relative;top:+5px;left:+5px;" class="eqLogicAttr" data-l1key="configuration" data-l2key="urlAPI"> </span>
							</div>
						</div>
						<legend><i class="far fa-clock" style="font-size : 2em;"></i> <span >{{Interrogation périodique de l'API}}</span></legend>
						<div class="form-group">
							<label class="col-xs-3 control-label">{{Auto-actualisation (cron)}}</label>
							<div class="col-xs-2">
								<div class="input-group">
									<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="autorefresh" placeholder="{{Auto-actualisation (cron)}}"/>
									<span class="input-group-btn">
									<a class="btn btn-success btn-sm " id="bt_cronGenerator" ><i class="fas fa-question-circle"></i></a>
									</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-3 control-label">{{Dernier lancement}}</label>
							<div class="col-xs-3">
								<input type="text" disabled class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="dernierLancement">
							</div>
						</div>
					</fieldset>
				</form>				
				<legend><i class="fas fa-download" style="font-size : 2em;"></i> <span >{{Liste des informations à récupérer}}</span></legend>
				<a class="btn btn-info btn-sm eqLogicAction pull-right" onclick="ouvreModalavecAPI();" ><i class="fas fa-edit"></i> {{Modifier dans l'Assistant}}</a>
			
				<!--<a class="btn btn-success btn-sm cmdAction pull-right" data-action="add" style="margin-top:5px;"><i class="fa fa-plus-circle"></i> {{Commandes}}</a><br/><br/>-->
				<table class="table-condensed" border=0 width=100%><tr><th style="width: 530px;">{{  Nom}}</th><th style="width: 400px;">{{  Chemin de la donnée à récupérer}}</th><th class="text-center" style="width:290px;">Options</th></tr></table>
				<table id="table_cmd" class="table-condensed ui-sortable table_controles" border=0 width=100%>
					<tbody></tbody>
				</table>
				<a class="btn btn-info btn-sm eqLogicAction" onclick="ouvreModalavecAPI();" ><i class="fas fa-edit"></i> {{Modifier dans l'Assistant}}</a>
			</div>
		<!---------------- Onglet groupeCommandes -----------
		configuration / nomGroup doit être remplacé par name
		configuration / object_idGroup doit être remplacé par object_id
		configuration / isEnableGroup doit être remplacé par isEnable
		configuration / isVisibleGroup doit être remplacé par isVisible
				----->  
			<div role="tabpanel" class="tab-pane active" id="TabCmd">
				<form class="form-horizontal">
					<fieldset>
						<legend><i class="fas fa-upload" style="font-size : 2em;"></i> <span >{{Liste des commandes à envoyer}}</span></legend><br>
				<table id="table_cmdTitre2" class="table-condensed" border=0 width=100%></table>
				<table id="table_cmd2" class="table-condensed ui-sortable table_controles" border=0 width=100%>
					<tbody></tbody>
				</table>
					</fieldset>
				</form>
			</div>
		</div>
	<!---------------- Fin des onglets ---------------->  
	</div>
</div>
<!-- Inclusion du fichier javascript du plugin (dossier, nom_du_fichier, extension_du_fichier, nom_du_plugin) -->
<?php include_file('desktop', 'synologyapi', 'js', 'synologyapi');?>
<?php include_file('desktop', 'synologyapi', 'css', 'synologyapi'); ?>
<!-- Inclusion du fichier javascript du core - NE PAS MODIFIER NI SUPPRIMER -->
<?php include_file('core', 'plugin.template', 'js');?>
