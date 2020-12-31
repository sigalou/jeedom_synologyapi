<?php
	if (!isConnect('admin')) {
		throw new Exception('{{401 - Accès non autorisé}}');
	}
	$plugin = plugin::byId('synologyapi');
	sendVarToJS('eqType', $plugin->getId());
	$eqLogics = eqLogic::byType($plugin->getId());
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
		</div>
	<!-- -------------- Premier SYNO ---------------->  
	<input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
	<legend ><i class="fas fa-cogs"></i> {{API de <?php echo config::byKey('Syno1_name','synologyapi')?>}}</legend>
		<div class="eqLogicThumbnailContainer">
		<?php
			foreach ($eqLogics as $eqLogic) {
				
					$compteur=0;
					foreach ($eqLogic->getCmd() as $cmd) {
						$compteur++;
					}
	if ($compteur>1) $Salafin="s"; else $Salafin="" ;
				
				//echo "<br>".$eqLogic->getHumanName(true, true);
					if ($eqLogic->getConfiguration('devicetype') == "1") {
						//if ($eqLogic->getConfiguration('type') == "all") { pour le test de l'icone toutes les api
						if ($eqLogic->getConfiguration('type') == "cmd") {
						$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
						echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
						//echo '<img class="lazy" src="plugins/synologyapi/plugin_info//synologyapi_dev.png" style="min-height:75px !important;" />';
						echo '<span class="badge badge-info">'.$compteur.' Cmd'.$Salafin.'</span>';
						echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
						echo '<br>';
						echo '<span class="name" >'. $eqLogic->getHumanName(true, true) .'</span>';
						echo '</div>';
						}	else {			
						$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
						echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
						echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
						echo '<span class="badge badge-purple">'.$compteur.' Info'.$Salafin.'</span>';
						echo '<br>';
						echo '<span class="name" >' . $eqLogic->getHumanName(true, true) . '</span>';
						echo '</div>';
						}
					}
			}
			
		echo "</div>";
		
		//-- -------------- deuxième SYNO ----------------
		
		if (config::byKey('Syno2_name','synologyapi')!="") {
		?>
		<legend ><i class="fas fa-cogs"></i> {{API de <?php echo config::byKey('Syno2_name','synologyapi')?>}}</legend>
		<div class="eqLogicThumbnailContainer">
			<?php
				foreach ($eqLogics as $eqLogic) {
						if ($eqLogic->getConfiguration('devicetype') == "2") {
						$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
						echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
						echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
						echo '<br>';
						echo '<span class="name" >' . $eqLogic->getHumanName(true, true) . '</span>';
						echo '</div>';
						}
				}
		echo "</div>";
		}
			
			//-- -------------- troisème SYNO ----------------
			
		if (config::byKey('Syno3_name','synologyapi')!="") {
		?>
		<legend ><i class="fas fa-cogs"></i> {{API de <?php echo config::byKey('Syno3_name','synologyapi')?>}}</legend>
		<div class="eqLogicThumbnailContainer">
			<?php
				foreach ($eqLogics as $eqLogic) {
						if ($eqLogic->getConfiguration('devicetype') == "3") {
						$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
						echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
						echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
						echo '<br>';
						echo '<span class="name" style="color:#221916" >' . $eqLogic->getHumanName(true, true) . '</span>';
						echo '</div>';
						}
				}
		echo "</div>";
		}
				
				?>
	</div>
	<div class="col-lg-12 eqLogic" style="display: none;">
		<div class="input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
			<a class="btn btn-default btn-sm eqLogicAction roundedLeft" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a><a class="btn btn-info btn-sm eqLogicAction" onclick="ouvreModalavecAPI();" ><i class="fas fa-edit"></i> {{Modifier dans l'Assistant}}</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a><a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
			</span>
		</div>
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
			<li id="OngletEquipement" role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
			<li id="OngletGroupeCmd" role="presentation" class="active"><a href="#eqlogictab2" aria-controls="home2" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Groupe de commandes}}</a></li>
			<li id="OngletCommandes" role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Informations}}</a></li>
			<li id="OngletListeAPI" role="presentation"><a href="#eqlogictab" class="active" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Liste des API}}</a></li>
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
		<!---------------- Onglet Equipement ---------------->  
		<div id="EcranEquipement" class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
				<br/>
				<form class="form-horizontal">
					<fieldset>
						<br>
						<legend><i class="fas fa-cogs" style="font-size : 2em;"></i> <span >{{Identification de l'API à utiliser}}</span></legend>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Nom de l'API personnalisé}}</label>
							<div class="col-sm-3">
								<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
								<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement synologyapi}}"/>
							</div>
						</div>
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
								<span style="position:relative;top:+5px;left:+5px;visibility:collapse;" class="eqLogicAttr" data-l1key="configuration" id="typefield" data-l2key="type"> </span>
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
						<br>
						<legend><i class="far fa-clock" style="font-size : 2em;"></i> <span >{{Actualisation périodique des données}}</span></legend>
						<br><br>
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
			</div>
			<div role="tabpanel" class="tab-pane" id="commandtab">
				<br>
				<!--<a class="btn btn-success btn-sm cmdAction pull-right" data-action="add" style="margin-top:5px;"><i class="fa fa-plus-circle"></i> {{Commandes}}</a><br/><br/>-->
				<table class="table-condensed" border=0 width=100%><tr><th style="width: 510px;">{{  Nom}}</th><th>{{  Paramètres de la commande à envoyer}}</th><th class="text-center" style="width:440px;">Options</th></tr></table>
				<table id="table_cmd" class="table-condensed ui-sortable table_controles" border=0 width=100%>
					<tbody></tbody>
				</table>
			</div>
		</div>
		<!---------------- Onglet GroupeCmd ---------------->  
		<div id="EcranGroupeCmd" class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
			<div role="tabpanel" class="tab-pane active" id="eqlogictab2">
				<form class="form-horizontal">
					<fieldset>
						
						<legend><i class="fas fa-cogs" style="font-size : 2em;"></i> <span >{{Groupe de Commandes}}</span></legend>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Nom du groupe}}</label>
							<div class="col-sm-3">
			<!---					<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />---> 
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="nomGroup" placeholder="{{Nom du groupe de commandes}}"/>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label" >{{Objet parent}}</label>
							<div class="col-sm-3">
								<select id="sel_object" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="object_idGroup">
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
							<label class="col-sm-3 control-label"></label>
							<div class="col-sm-9">
								<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="isEnableGroup" checked/>{{Activer}}</label>
								<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="isVisibleGroup" checked/>{{Visible}}</label>
							</div>
						</div>
						<legend><i class="fa fa-list-alt" style="font-size : 2em;"></i> <span >{{Liste des commandes du groupe}}</span></legend>
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
