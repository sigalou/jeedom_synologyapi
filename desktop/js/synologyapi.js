
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


$('#bt_req').off('click').on('click', function () {
  $('#md_modal').dialog({
    title: "{{Assistant API}}"
  });
//  $('#md_modal').load('index.php?v=d&plugin=synologyapi&modal=req&&idsynology=1&iddevice=' + $('.eqLogicAttr[data-l1key=logicalId]').value()).dialog('open');
  $('#md_modal').load('index.php?v=d&plugin=synologyapi&modal=req&idsynology=1&api=SYNO.Core.System.Utilization&method=get').dialog('open');
});

$('#gotofusionner').off('click').on('click', function () {
  $('#md_modal').dialog({
    title: "{{Fusionner Commandes et Infos}}"
  });
//  $('#md_modal').load('index.php?v=d&plugin=synologyapi&modal=req&&idsynology=1&iddevice=' + $('.eqLogicAttr[data-l1key=logicalId]').value()).dialog('open');
  $('#md_modal').load('index.php?v=d&plugin=synologyapi&modal=fusionner&idsynology=1&api=SYNO.Core.System.Utilization&method=get').dialog('open');
});

//$('#importer').off('click').on('click', function () {

$("#bt_importSYNODevice1").change(function(event) {
  BoutonImport("1", event);
})

$("#bt_importSYNODevice2").change(function(event) {
  BoutonImport("2", event);
})

$("#bt_importSYNODevice3").change(function(event) {
  BoutonImport("3", event);
})

function BoutonImport(idSyno, event) {
	$('#div_alert').hide()
	var uploadedFile = event.target.files[0]
	if(uploadedFile.type !== "application/json") {
		$('#div_alert').showAlert({message: "{{L'import se fait au format json à partir d'API précedemment exportées.}}", level: 'danger'})
		return false
	}
	if (uploadedFile) {
		var readFile = new FileReader()
		readFile.readAsText(uploadedFile)
		readFile.onload = function(e) {
			var objectDataTous = JSON.parse(e.target.result)
			var objectData = JSON.stringify(objectDataTous)
			bootbox.prompt("{{Nom du nouvel équipement :}}", function(result) {
				if (result !== null) {
				  jeedom.eqLogic.save({
					type: eqType,
					eqLogics: [{name: result}],
					error: function(error) {
					  $('#div_alert').showAlert({message: error.message, level: 'danger'});
					},
					success: function(_data) {
					importerJson(_data.id, idSyno, objectData);
					  var vars = getUrlVars()
					  var url = 'index.php?'
					  for (var i in vars) {
						if (i != 'id' && i != 'saveSuccessFull' && i != 'removeSuccessFull') {
						  url += i + '=' + vars[i].replace('#', '') + '&'
						}
					  }
					  modifyWithoutSave = false
					  url += 'id=' + _data.id + '&saveSuccessFull=1'
					  //loadPage(url) non utile, supprimé pour éviter que le redresh ne prenne pas les nouvelles commandes
					}
				  })
				}
			})
		}
	} else {
		$('#div_alert').showAlert({message: "{{Problème lors de la lecture du fichier.}}", level: 'danger'})
		return false
	}		
}


 
 
function importerJson(id, idSyno, objectData) {
	
  $.ajax({
    type: "POST",
    url: "plugins/synologyapi/core/ajax/synologyapi.ajax.php",
    data: {
      action: "importerJson",
      id: id,
      objectData: objectData,
      idSyno: idSyno
    },
    dataType: 'json',
    error: function (request, status, error) {
      handleAjaxError(request, status, error);
    },
    success: function (data) {
      if (data.state != 'ok') {
        $('#div_alert').showAlert({
          message: data.result,
          level: 'danger'
        });
        return;
      }
      window.location.reload();
    }
  });
}



$("#bt_exportSYNODevice").on('click', function(event) {

var tousObj = [];
	jeedom.eqLogic.byId({
			"id": $('.eqLogicAttr[data-l1key=id]')[0].value,
			noCache: true,
			success: function (obj) {tousObj.push(obj);}
		});
	
var idDesCommandes= $("#toutDevice").getValues('.cmdAttr')[0].id;
	for (var i in idDesCommandes) { // on boucle sur toutes les commandes
		jeedom.cmd.byId({
				"id": idDesCommandes[i],
				noCache: true,
				success: function (obj) {tousObj.push(obj);}
			});	  
	}
setTimeout(() => {downloadObjectAsJson(tousObj, $('.eqLogicAttr[data-l1key=name]')[0].value)}, 1000);
  //return false


/*
var data = [];
data[0] = { "ID": "1", "Status": "Valid" };
data[1] = { "ID": "2", "Status": "Invalid" };
data[2] = { "ID": "3", "Status": "jhg" };
data[3] = { "ID": "4", "Status": "eeeeeeee" };
console.log("----------------------------------data");
console.log(data)
console.log(JSON.stringify(data))
*/

/*
var tempData = [];

       tempData.push( data );

data = tempData;
//console.log(data)
console.log(JSON.stringify(data))

var tempData = [];
   tempData.push( tousObj );
console.log(JSON.stringify(tempData))


console.log("----------------------------------commandes");

console.log(tousObj)
console.log(JSON.stringify(tousObj))	
*/
//console.log(JSON.stringify(Object.assign({}, tousObj)))
//downloadObjectAsJson(tousObj, "exportName")

// [] array
// {} object
	
            //$('#123').attr('logicalId', obj.logicalId);

	/*var infoequipement= $("#TabInfo2").getValues('.eqLogicAttr')[0];
	console.log("----------------------------------infoequipement.configuration");
	console.dir(infoequipement.configuration);
	
	var equipement= $("#toutDevice").getValues('.eqLogicAttr')[0];
	var equipementconfiguration=equipement.configuration
	delete equipementconfiguration.dernierLancement
	delete equipementconfiguration.type
	
	//console.dir(equipement);
	//console.log("----------------------------------equipement");
	//console.dir(equipement);
	//console.log("----------------------------------equipementconfiguration");
	//console.dir(equipementconfiguration);
	var exportName=equipement.name
	delete equipement.id
	delete equipement.object_id
	delete equipement.category
	delete equipement.isEnable
	delete equipement.isVisible
	var commandes= $("#toutDevice").getValues('.cmdAttr')[0];
	//console.log("----------------------------------commandes");
	//console.dir(commandes);
	delete commandes.id
	delete commandes.isVisible
	delete commandes.configuration.request
  //downloadObjectAsJson(commandes, equipement.name)
  //var exportObj=$.extend(equipement, commandes)
  //var exportObj=commandes
  var exportObj = Object.assign({}, commandes, equipementconfiguration);
	//console.log("----------------------------------exportObj");
	//console.dir(exportObj);
 // downloadObjectAsJson(exportObj, exportName)
  return false*/
}) 



function downloadObjectAsJson(exportObj, exportName) {
  var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(exportObj))
  var downloadAnchorNode = document.createElement('a')
  downloadAnchorNode.setAttribute("href",     dataStr)
  downloadAnchorNode.setAttribute("target", "_blank")
  downloadAnchorNode.setAttribute("download", exportName + ".json")
  document.body.appendChild(downloadAnchorNode) // required for firefox
  downloadAnchorNode.click()
  downloadAnchorNode.remove()
}

// On passe d'un device All à un device Syno
/*
function typeChange(){
	console.log ("changement2");

}
$( "#typefield" ).change(function(){
	console.log ("changement");
  setTimeout(typeChange,100);
});
*/

function ouvreModalavecAPI() {

  $('#md_modal').dialog({
    title: "{{Assistant API}}"
  });
//  $('#md_modal').load('index.php?v=d&plugin=synologyapi&modal=req&&idsynology=1&iddevice=' + $('.eqLogicAttr[data-l1key=logicalId]').value()).dialog('open');
  $('#md_modal').load('index.php?v=d&plugin=synologyapi&modal=req&zzz=aaa&source=device'+$('.eqLogicAttr[data-l1key=configuration][data-l2key=urlAPI]').value().replace('?', '&')).dialog('open');
}

$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
/*
 * Fonction permettant l'affichage des commandes dans l'équipement, import des fonctionnalités de virtual
 */


$('#bt_importEqLogic').off('click').on('click', function () {
  jeedom.eqLogic.getSelectModal({}, function (result) {
    $.ajax({
      type: "POST",
      url: "plugins/virtual/core/ajax/virtual.ajax.php",
      data: {
        action: "copyFromEqLogic",
        eqLogic_id: result.id,
        id: $('.eqLogicAttr[data-l1key=id]').value()
      },
      dataType: 'json',
      global: false,
      error: function (request, status, error) {
        handleAjaxError(request, status, error);
      },
      success: function (data) {
        if (data.state != 'ok') {
          $('#div_alert').showAlert({message: data.result, level: 'danger'});
          return;
        }
        $('.eqLogicDisplayCard[data-eqLogic_id='+$('.eqLogicAttr[data-l1key=id]').value()+']').click();
      }
    });
  });
});

$('#bt_cronGenerator').on('click',function(){
  jeedom.getCronSelectModal({},function (result) {
    $('.eqLogicAttr[data-l1key=configuration][data-l2key=autorefresh]').value(result.value);
  });
});

$("#bt_addVirtualInfo").on('click', function (event) {
  addCmdToTable({type: 'info'});
  modifyWithoutSave = true;
});

$("#bt_addVirtualAction").on('click', function (event) {
  addCmdToTable({type: 'action'});
  modifyWithoutSave = true;
});

$('.bt_showExpressionTest').off('click').on('click', function () {
  $('#md_modal').dialog({title: "{{Testeur d'expression}}"});
  $("#md_modal").load('index.php?v=d&modal=expression.test').dialog('open');
});

$('#table_cmd tbody').delegate('tr .remove', 'click', function (event) {
  $(this).closest('tr').remove();
});

$('#table_API tbody').delegate('tr .remove', 'click', function (event) {
  $(this).closest('tr').remove();
});

$("#table_cmd").delegate(".listEquipementInfo", 'click', function () {
  var el = $(this);
  jeedom.cmd.getSelectModal({cmd: {type: 'info'}}, function (result) {
    var calcul = el.closest('tr').find('.cmdAttr[data-l1key=configuration][data-l2key=' + el.data('input') + ']');
    calcul.atCaret('insert', result.human);
  });
});


$("#table_cmd").delegate(".listEquipementAction", 'click', function () {
  var el = $(this);
  var subtype = $(this).closest('.cmd').find('.cmdAttr[data-l1key=subType]').value();
  jeedom.cmd.getSelectModal({cmd: {type: 'action', subType: subtype}}, function (result) {
    var calcul = el.closest('tr').find('.cmdAttr[data-l1key=configuration][data-l2key=' + el.attr('data-input') + ']');
    calcul.atCaret('insert', result.human);
  });
});

$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});

$("#table_API").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});

function saveEqLogic(_eqLogic) {
	
	    if (!isset(_eqLogic.configuration)) {
        _eqLogic.configuration = {};
    }
	

	return _eqLogic;

}

function printEqLogic(_eqLogic) {

//console.log("ICI:"+$('#Onglet_eqlogictab2').style.display);
//typefield vaut : syno ou cmd ou cmdinfo

if ($('#typefield').value() == 'syno') {// ne plus toucher ok
	document.getElementById("OngletInfo").className = "active";
	document.getElementById("TabInfo").className = "tab-pane active";
	
	document.getElementById("OngletInfo2").className = "";
	document.getElementById("TabInfo2").className = "tab-pane";
	
	document.getElementById("OngletCmd").className = "hidden";
	document.getElementById("TabCmd").className = "hidden";
}
if ($('#typefield').value() == 'cmd') { // ne plus toucher ok
	document.getElementById("OngletInfo").className = "active";
	document.getElementById("TabInfo").className = "tab-pane active";
	
	document.getElementById("OngletInfo2").className = "hidden";
	document.getElementById("TabInfo2").className = "hidden";
	
	document.getElementById("OngletCmd").className = "";
	document.getElementById("TabCmd").className = "tab-pane";
}
if ($('#typefield').value() == 'cmdinfo') { // ne plus toucher ok
	document.getElementById("OngletInfo").className = "active";
	document.getElementById("TabInfo").className = "tab-pane active";
	
	document.getElementById("OngletInfo2").className = "";
	document.getElementById("TabInfo2").className = "tab-pane";
	
	document.getElementById("OngletCmd").className = "";
	document.getElementById("TabCmd").className = "tab-pane";
}
titreCmd=' <tr><th style="width: 410px;">{{  Nom de la commande}}</th><th style="width: 400px;">{{Commentaire ou explication (facultatif)}}</th><th >{{Commande à envoyer}}</th><th style="width: 240px;">{{Options}}</th>';
$('#table_cmdTitre2').empty();
	$('#table_cmdTitre2').append(titreCmd);
}

function addCmdToTable(_cmd) {
	
	//console.log("coucou");
//	console.dir(_cmd);
	// 400 (name) + 130 (info) + 400 (requete) + 200 + 90
	
	    DefinitionDivPourCommandesPredefinies = 'style="display: none;"';
	
  if (!isset(_cmd)) {
    var _cmd = {configuration: {}};
  }
  if (!isset(_cmd.configuration)) {
    _cmd.configuration = {};
  }
  
  
  if (init(_cmd.logicalId) == 'refresh') {
    var tr = '<tr style="border-bottom: 1px solid #808080;" class="cmd" data-cmd_id="' + init(_cmd.id) + '" virtualAction="' + init(_cmd.configuration.virtualAction) + '">';
		
		tr += '<td width=400px>';
 tr += '<span class="cmdAttr" data-l1key="id" style="display:none;"></span>';
		tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="margin-bottom : 30px;width : 100%;" placeholder="{{Nom}}">';

    tr += '</td>';


    tr += '<td>';
    //tr += '<input style="margin-bottom : 30px;" class="cmdAttr form-control type input-sm" data-l1key="type" value="action" disabled />';
	
//	tr += '<input type="hidden" class="cmdAttr form-control type input-sm" data-l1key="type" value="action" />';
//	tr += '<input type="hidden" class="cmdAttr form-control type input-sm" data-l1key="subType"/>';
//	tr += '<input type="hidden" class="cmdAttr form-control type input-sm" data-l1key="configuration" data-l2key="requestAPI" value="refresh"/>';
//	tr += '<input type="hidden" class="cmdAttr form-control type input-sm" data-l1key="logicalId"/>';
//	tr += '<input type="hidden" class="cmdAttr form-control type input-sm" data-l1key="template" data-l2key="dashboard"/>';
//	tr += '<input type="hidden" class="cmdAttr form-control type input-sm" data-l1key="template" data-l2key="mobile"/>';
//	tr += '<input type="hidden" class="cmdAttr form-control type input-sm" data-l1key="display" data-l2key="showNameOndashboard"/>';
//	tr += '<input type="hidden" class="cmdAttr form-control type input-sm" data-l1key="display" data-l2key="showNameOnmobile"/>';
	
	
	
  //  tr += '<div ' + DefinitionDivPourCommandesPredefinies + '>';
  //  tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
  //  tr += '</div>';





    tr += '</td><td></td>' +
      '<td>' +
      '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> ' +
      '</td>' +
      '<td>';

    if (is_numeric(_cmd.id)) {
      tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fas fa-cogs"></i></a> ';
      if (!((init(_cmd.name) == "Routine") || (init(_cmd.name) == "xxxxxxxx"))) //Masquer le bouton Tester
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>' +
      '  </td>' +
      '</tr>';


    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr').last().setValues(_cmd, '.cmdAttr');
    if (isset(_cmd.type)) {
      $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
    }
    jeedom.cmd.changeType($('#table_cmd tbody tr').last(), init(_cmd.subType));
  
    return;
  }
  
  if (init(_cmd.type) == 'info') {
	  
    var tr = '<tr style="border-bottom: 1px solid #808080;" class="cmd" data-cmd_id="' + init(_cmd.id) + '" virtualAction="' + init(_cmd.configuration.virtualAction) + '">';
		
		tr += '<td width=400px>';
 tr += '<span class="cmdAttr" data-l1key="id" style="display:none;"></span>';
		tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="margin-bottom : 30px;width : 100%;" placeholder="{{Nom}}">';
/*
	  
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '" virtualAction="' + init(_cmd.configuration.virtualAction) + '">';
    tr += '<td>';
    tr += '<small><small><span class="cmdAttr" data-l1key="id"></span></small></small>';
    tr += '</td>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="{{Nom}}"></td>';*/
    tr += '</td>';
    tr += '<td width=130  >';
    tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="info" disabled style="margin-bottom : 5px;" />';
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
    tr += '</td>';
	    tr += '<td width=400>' +
      '<input style="margin-bottom : 2px;" class="cmdAttr form-control input-sm"';
	  
    if (init(_cmd.logicalId) != "")
      tr += 'readonly';

  //  if (init(_cmd.logicalId) == "refresh") 
 //     tr += ' style="display:none;" ';
	  
    tr += ' data-l1key="configuration" data-l2key="requestAPI">';
	tr += '<input type="hidden" class="cmdAttr form-control type input-sm" data-l1key="logicalId"/>';
//	tr += '<input type="hidden" class="cmdAttr form-control type input-sm" data-l1key="template" data-l2key="dashboard"/>';
//	tr += '<input type="hidden" class="cmdAttr form-control type input-sm" data-l1key="template" data-l2key="mobile"/>';
//	tr += '<input type="hidden" class="cmdAttr form-control type input-sm" data-l1key="display" data-l2key="showNameOndashboard"/>';
//	tr += '<input type="hidden" class="cmdAttr form-control type input-sm" data-l1key="display" data-l2key="showNameOnmobile"/>';
    tr += '<input  class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="calcul" placeholder="{{Calcul facultatif, utiliser #value# pour utiliser la valeur récupérée}}">';
    tr += '<td width=190>';
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width:30%;display:inline-block;">';
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width:30%;display:inline-block;">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="unite" placeholder="Unité" title="{{Unité}}" style="width:50px;display:inline-block;margin-right:5px;">';
   // tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="listValue" placeholder="{{Liste de valeur|texte séparé par ;}}" title="{{Liste}}">';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> ';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" checked/>{{Historiser}}</label></span> ';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="display" data-l2key="invertBinary"/>{{Inverser}}</label></span> ';
  //  tr += '88<span class="cmdAttr"  data-l1key="configuration" data-l2key="value">99</td>';
    
    tr += '<td width=100>';
    if (is_numeric(_cmd.id)) {
      tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fas fa-cogs"></i></a> ';
      tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr').last().setValues(_cmd, '.cmdAttr');
    if (isset(_cmd.type)) {
      $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
    }
    jeedom.cmd.changeType($('#table_cmd tbody tr').last(), init(_cmd.subType));
  }
  
   if (init(_cmd.type) == 'API') {
    var tr = '<tr style="border-bottom: 1px solid #808080;" class="cmd" data-cmd_id="' + init(_cmd.id) + '" virtualAction="' + init(_cmd.configuration.virtualAction) + '">';
    //tr += '<td>';
  //  tr += '<small><small><span class="cmdAttr" data-l1key="id"></span></small></small>';
   // tr += '</td>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 80%;" placeholder="{{Nom}}"></td>';
	/*    tr += '<td>1' +
      '<input class="cmdAttr form-control input-sm"';
    if (init(_cmd.logicalId) != "")
      tr += 'readonly';

    if (init(_cmd.logicalId) == "refresh")
      tr += ' style="display:none;" ';
    tr += ' data-l1key="configuration" data-l2key="requestAPI">';
    tr += '<td>2';
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width:30%;display:inline-block;">';
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width:30%;display:inline-block;">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="unite" placeholder="Unité" title="{{Unité}}" style="width:30%;display:inline-block;margin-right:5px;">';
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="listValue" placeholder="{{Liste de valeur|texte séparé par ;}}" title="{{Liste}}">';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> ';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" checked/>{{Historiser}}</label></span> ';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="display" data-l2key="invertBinary"/>{{Inverser}}</label></span> ';
    tr += '</td>';
    
    tr += '<td>3';
    if (is_numeric(_cmd.id)) {
      tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fas fa-cogs"></i></a> ';
      tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';*/
    tr += '</tr>';
    $('#table_API tbody').append(tr);
    $('#table_API tbody tr').last().setValues(_cmd, '.cmdAttr');
    if (isset(_cmd.type)) {
      $('#table_API tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
    }
    jeedom.cmd.changeType($('#table_API tbody tr').last(), init(_cmd.subType));
  } 
 
  
  if (init(_cmd.type) == 'action') {

			//var tr = '<tr class="cmd" >'; //la couleur ne foncitonne pas à cause de info mais on ne peut pas supprimer info
    var tr = '<tr style="border-bottom: 1px solid #808080;" class="cmd" data-cmd_id="' + init(_cmd.id) + '" virtualAction="' + init(_cmd.configuration.virtualAction) + '">';
		
		tr += '<td width=400>';
 tr += '<span class="cmdAttr" data-l1key="id" style="display:none;"></span>';
		tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 100%;" placeholder="{{Nom}}"></td>';
    tr += '<td width=400><input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="commentaire" style="width : 100%;" placeholder="{{Commentaire ou explication}}">';
    tr += '</td>' ;

/*   tr +=
      '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">' +
      '<td>' +
      '<small><small><span class="cmdAttr" data-l1key="id"></span></small></small>' +
      '</td>' +
      '<td>' +
      '<div class="row">' +
      '<div class="col-lg-1">' +
      '<span class="cmdAttr" data-l1key="display" data-l2key="icon" style="margin-left : 10px;"></span>' +
      '</div>' +
      '<div class="col-lg-8">' +
      '<input class="cmdAttr form-control input-sm" data-l1key="name">' +
      '</div>' +
      '</div>';

    tr += '<td width=90>';
    tr += '<input class="cmdAttr form-control type input-sm" style="display: none;" data-l1key="type" value="action" disabled />';
    tr += '<div ' + DefinitionDivPourCommandesPredefinies + '>';
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
    tr += '</div></td>';*/
    tr += '<td><input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="requestAPI">';
	
//	tr += '<input type="hidden" class="cmdAttr form-control type input-sm" data-l1key="type" value="action" />';
//	tr += '<input type="hidden" class="cmdAttr form-control type input-sm" data-l1key="subType"/>';
//	tr += '<input type="hidden" class="cmdAttr form-control type input-sm" data-l1key="logicalId"/>';
	//tr += '<input type="hidden" class="cmdAttr form-control type input-sm" data-l1key="configuration" data-l2key="requestAPI"/>';
//	tr += '<input type="hidden" class="cmdAttr form-control type input-sm" data-l1key="template" data-l2key="dashboard"/>';
//	tr += '<input type="hidden" class="cmdAttr form-control type input-sm" data-l1key="template" data-l2key="mobile"/>';
//	tr += '<input type="hidden" class="cmdAttr form-control type input-sm" data-l1key="display" data-l2key="showNameOndashboard"/>';
//	tr += '<input type="hidden" class="cmdAttr form-control type input-sm" data-l1key="display" data-l2key="showNameOnmobile"/>';	


    tr +=   '</td><td width=90><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label>' +
      '</td>' +
      '<td width=150>';

    if (is_numeric(_cmd.id)) {
      tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fas fa-cogs"></i></a> ';
      tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>' +
      '  </td>' +
      '</tr>';

    $('#table_cmd2 tbody').append(tr);
    var tr = $('#table_cmd2 tbody tr:last');
    jeedom.eqLogic.builSelectCmd({
      id: $(".li_eqLogic.active").attr('data-eqLogic_id'),
      filter: {
        type: 'i'
      },
      error: function (error) {
        $('#div_alert').showAlert({
          message: error.message,
          level: 'danger'
        });
      },
      success: function (result) {
        tr.find('.cmdAttr[data-l1key=value]').append(result);
        tr.setValues(_cmd, '.cmdAttr');
        jeedom.cmd.changeType(tr, init(_cmd.subType));
      }
    });
  }
}