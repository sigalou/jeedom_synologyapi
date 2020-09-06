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
      <i class="fas fa-wrench" style="color:#221916"></i>
    <br>
    <span style="color:#221916">{{Configuration}}</span>
  </div>
  </div>
  
<!-- -------------- Premier SYNO ---------------->  
<input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
<legend style="color:#221916"><i class="fas fa-cogs"></i> {{API de <?php echo config::byKey('Syno1_name','synologyapi')?>}}</legend>
<div class="eqLogicThumbnailContainer">
    <?php
foreach ($eqLogics as $eqLogic) {
		if ($eqLogic->getConfiguration('devicetype') == "1") {
		$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
		echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
		echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
		echo '<br>';
		echo '<span class="name" style="color:#221916">' . $eqLogic->getHumanName(true, true) . '</span>';
		echo '</div>';
		}
}
echo "</div>";

//-- -------------- deuxième SYNO ----------------

	if (config::byKey('Syno2_name','synologyapi')!="") {
	?>
	<legend style="color:#221916"><i class="fas fa-cogs"></i> {{API de <?php echo config::byKey('Syno2_name','synologyapi')?>}}</legend>
	<div class="eqLogicThumbnailContainer">
		<?php
	foreach ($eqLogics as $eqLogic) {
			if ($eqLogic->getConfiguration('devicetype') == "2") {
			$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
			echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
			echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
			echo '<br>';
			echo '<span class="name" style="color:#221916">' . $eqLogic->getHumanName(true, true) . '</span>';
			echo '</div>';
			}
	}
	echo "</div>";
	}

//-- -------------- troisème SYNO ----------------

	if (config::byKey('Syno3_name','synologyapi')!="") {
	?>
	<legend style="color:#221916"><i class="fas fa-cogs"></i> {{API de <?php echo config::byKey('Syno3_name','synologyapi')?>}}</legend>
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
    <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
    <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
  </ul>
  <div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
    <div role="tabpanel" class="tab-pane active" id="eqlogictab">
      <br/>
    <form class="form-horizontal">
        <fieldset>
	<br><legend><i class="fas fa-cogs" style="font-size : 2em;color:#221916;"></i> <span style="color:#221916">{{Identification de l'API à utiliser}}</span></legend>
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


<br><legend><i class="far fa-clock" style="font-size : 2em;color:#221916;"></i> <span style="color:#221916">{{Actualisation périodique des données}}</span></legend>

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
<!--<a class="btn btn-success btn-sm cmdAction pull-right" data-action="add" style="margin-top:5px;"><i class="fa fa-plus-circle"></i> {{Commandes}}</a><br/><br/>-->
<table id="table_cmd" class="table table-bordered table-condensed">
    <thead>
        <tr>
            <th>{{#}}</th><th>{{Nom personnalisé}}</th><th>{{Type}}</th><th>{{Champs API}}</th><th>{{Options}}</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
</div>
</div>

</div>
</div>

<!-- Inclusion du fichier javascript du plugin (dossier, nom_du_fichier, extension_du_fichier, nom_du_plugin) -->
<?php include_file('desktop', 'synologyapi', 'js', 'synologyapi');?>
<!-- Inclusion du fichier javascript du core - NE PAS MODIFIER NI SUPPRIMER -->
<?php include_file('core', 'plugin.template', 'js');?>
