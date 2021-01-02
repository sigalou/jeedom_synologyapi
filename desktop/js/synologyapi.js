
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

$('#bt_importTemplate').off('click').on('click', function () {
  $.ajax({
    type: "POST",
    url: "plugins/virtual/core/ajax/virtual.ajax.php",
    data: {
      action: "getTemplateList",
    },
    dataType: 'json',
    error: function (request, status, error) {
      handleAjaxError(request, status, error);
    },
    success: function (data) {
      var inputOptions = [];
      for(var i in data.result){
        inputOptions.push({
          text : data.result[i].name,
          value : i
        })
      }
      bootbox.prompt({
        title: "Quel template ?",
        inputType: 'select',
        inputOptions: inputOptions,
        callback: function (result) {
          $.ajax({
            type: "POST",
            url: "plugins/virtual/core/ajax/virtual.ajax.php",
            data: {
              action: "applyTemplate",
              id: $('.eqLogicAttr[data-l1key=id]').value(),
              name : result
            },
            dataType: 'json',
            error: function (request, status, error) {
              handleAjaxError(request, status, error);
            },
            success: function (data) {
              $('.eqLogicDisplayCard[data-eqLogic_id='+$('.eqLogicAttr[data-l1key=id]').value()+']').click();
            }
          });
        }
      });
    }
  });
});

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
	
	// Enregistre le nom du groupe dans name (donc dans le cas d'un groupe de commandes), même chose pour autres commandes
	if (_eqLogic.configuration.nomGroup != ""){	
		_eqLogic.name=_eqLogic.configuration.nomGroup;
		_eqLogic.isVisible=_eqLogic.configuration.isVisibleGroup;
		_eqLogic.isEnable=_eqLogic.configuration.isEnableGroup;
		_eqLogic.object_id=_eqLogic.configuration.object_idGroup;
	}
	return _eqLogic;

}

function printEqLogic(_eqLogic) {

    $('#OngletCommandes').hide();
    $('#OngletGroupeCmd').hide();
    $('#EcranGroupeCmd').hide();
    $('#EcranEquipement').hide();
    $('#OngletEquipement').hide();
    $('#EcranListeAPI').hide();
    $('#OngletListeAPI').hide();

if ($('#typefield').value() == 'all') {
	$('#EcranListeAPI').show();
	$('#OngletListeAPI').show();
	//console.log ("changement3");
}
else if ($('#typefield').value() == 'cmd') {
	$('#OngletGroupeCmd').show();
	$('#EcranGroupeCmd').show();
	//console.log ("changement4");
}
else { //les requetes infos
	$('#OngletCommandes').show();
	$('#OngletEquipement').show();
	$('#EcranEquipement').show();
	//console.log ("changement4");
}
  
 /* 
titreCmd=' <tr><th style="width: 410px;">{{  Nom personnalisable}}</th><th style="width: 130px;">{{Type}}</th><th style="width: 400px;">{{Champs API}}</th><th style="width: 280px;">{{Options}}</th>';
$essai="coiucou";


$('#table_cmdTitre').empty();
if (_eqLogic.configuration.type == "")  //vaut cmd quand c'ets une commande
	$('#table_cmdTitre').append(titreCmd);
else 
	$('#table_cmdTitre').append(titreRequete);
	
	$('#table_cmdTitre2').append(titreCmd);
	$('#table_cmdTitre').append(titreRequete);*/
titreCmd=' <tr><th style="width: 410px;">{{  Nom de la commande}}</th><th style="width: 400px;">{{Commentaire ou explication (factultatif)}}</th><th >{{Commande à envoyer}}</th><th style="width: 240px;">{{Options}}</th>';
	
$('#table_cmdTitre2').empty();
	$('#table_cmdTitre2').append(titreCmd);
	
}

function addCmdToTable(_cmd) {
	
	//console.log("coucou");
	//console.dir(_cmd);
	// 400 (name) + 130 (info) + 400 (requete) + 200 + 90
	
	    DefinitionDivPourCommandesPredefinies = 'style="display: none;"';
	
  if (!isset(_cmd)) {
    var _cmd = {configuration: {}};
  }
  if (!isset(_cmd.configuration)) {
    _cmd.configuration = {};
  }
  if (init(_cmd.logicalId) == 'refresh') {
    return;
  }
  
  if (init(_cmd.type) == 'info') {
	  
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '" virtualAction="' + init(_cmd.configuration.virtualAction) + '">';
		
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
    tr += '<td width=130>';
    tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="info" disabled style="margin-bottom : 5px;" />';
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
    tr += '</td>';
	    tr += '<td width=400>' +
      '<input style="margin-bottom : 30px;" class="cmdAttr form-control input-sm"';
    if (init(_cmd.logicalId) != "")
      tr += 'readonly';

    if (init(_cmd.logicalId) == "refresh")
      tr += ' style="display:none;" ';
    tr += ' data-l1key="configuration" data-l2key="requestAPI">';
    tr += '<td width=200>';
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width:30%;display:inline-block;">';
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width:30%;display:inline-block;">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="unite" placeholder="Unité" title="{{Unité}}" style="width:50px;display:inline-block;margin-right:5px;">';
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="listValue" placeholder="{{Liste de valeur|texte séparé par ;}}" title="{{Liste}}">';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> ';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" checked/>{{Historiser}}</label></span> ';
    tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="display" data-l2key="invertBinary"/>{{Inverser}}</label></span> ';
    tr += '</td>';
    
    tr += '<td width=90>';
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
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '" virtualAction="' + init(_cmd.configuration.virtualAction) + '">';
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
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '" virtualAction="' + init(_cmd.configuration.virtualAction) + '">';
		
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
    tr += '<td><input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="request"></td>';


    tr +=   '<td width=90><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label>' +
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