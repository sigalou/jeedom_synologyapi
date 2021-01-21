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



	$plugin = plugin::byId('synologyapi');
	//sendVarToJS('eqType', $plugin->getId());
	$eqLogics = eqLogic::byType($plugin->getId());


?><center><img src="/plugins/synologyapi/desktop/modal/fusion.jpg"></center>
<form action="index.php?v=d&plugin=synologyapi&modal=fusionner2" method="post">

	<fieldset>
		
		<legend><i class="fas fa-vector-square"></i> Premier équipement (comportant les infos)</legend>
		<div class="form-group">
			<label class="col-sm-4 control-label">Cet équipement sera conservé et renommé, il récupèrera les commandes du second équipement</label>
			<div class="col-lg-4">
				<select id="API1" class="form-control input-sm expressionAttr" style="width: 300px">
					<?php
					foreach ($eqLogics as $eqLogic) {
						
						if ($eqLogic->getConfiguration('type') != "cmd") {
							echo '<option value="'.$eqLogic->getLogicalId().'"';
							echo '> '.$eqLogic->getName().'</option>' ;
						}
					}?>
			</select>			
			</div>
		</div>		
<br>			
		
<br>

		<legend><i class="fas fa-vector-square"></i> Second équipement (comportant les commandes)</legend>
		<div class="form-group">
			<label class="col-sm-4 control-label">Cet équipement sera supprimé après transfert de ses commandes au premier équipement</label>
			<div class="col-lg-4">
			<select onchange="" id="API2" class="form-control input-sm expressionAttr" style="width: 300px">
				<?php
				foreach ($eqLogics as $eqLogic) {
					
					if ($eqLogic->getConfiguration('type') == "cmd") {
							echo '<option value="'.$eqLogic->getLogicalId().'"';
						echo '> '.$eqLogic->getName().'</option>' ;
					}
				}?>
			</select>
			</div>
		</div>
		
<br><br>		

	<legend><i class="fas fa-object-ungroup"></i> Equipement fusionné</legend>
		<div class="form-group">
			<label class="col-sm-4 control-label">{{Nom du futur équipement}}</label>
			<div class="col-lg-4">
			<input class="configKey form-control" id="titre" required />
			</div>
		</div>			
	</fieldset>
	<br><br><div class="col-sm-8">
	<center>
				<a class="btn btn-success btn-sm bt_verifierFusion">{{&nbsp;&nbsp;&nbsp;Lancer la fusion&nbsp;&nbsp;&nbsp;}} </a>	
				<a style="display: none" class="btn btn-info btn-sm bt_envoyee">Demande envoyée...</a>	

	</center></div>
	
</form>

<script>
	$('.bt_verifierFusion').off('click').on('click', function() {
	$(".bt_verifierFusion").hide();
	$(".bt_envoyee").show();
	//$(".bt_verifierFusion").attr("disabled", "disabled");	
  window.parent.document.getElementById('moniframe').src = 'index.php?v=d&plugin=synologyapi&modal=fusionner2&idsynology=1&api1='+document.getElementById('API1').value+'&api2='+document.getElementById('API2').value+'&titre='+document.getElementById('titre').value+'&method=44';
setTimeout(() => {  $(".bt_verifierFusion").show();$(".bt_envoyee").hide(); }, 2000);
	
	
  
	});

// bouton de chargement https://webdevdesigner.com/q/jquery-how-to-grey-out-the-background-while-showing-the-loading-icon-over-it-9247/

window.closeModal = function(){
	$('#md_modal').dialog('close');
	window.location.reload();
};
</script>

	<br><br>	
<iframe id="moniframe" name="frame1"
    width="100%"
    height="200"
</iframe>
<?php
include_file('desktop', 'synologyapi', 'js', 'synologyapi'); ?>
