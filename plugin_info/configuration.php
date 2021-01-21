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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}
?>
<style>
	pre#pre_eventlog {
		font-family: Menlo, Monaco, Consolas, "Courier New", monospace !important;
	}
</style>
<form class="form-horizontal">
	<fieldset>
		<legend><i class="fas fa-server"></i> {{Synology 1}}</legend>
		
		<div class="form-group">
			<label class="col-sm-4 control-label">{{Nom et Serveur:port}}</label>
			<div class="col-lg-3">
				<input class="configKey form-control" data-l1key="Syno1_name" placeholder="{{Mon Syno1}}" />
			</div>
			<div class="col-lg-4">
				<input class="configKey form-control" data-l1key="Syno1_server" placeholder="{{https://192.168.0.4:8001}}" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">{{Login et Mot de passe}}</label>
			<div class="col-lg-3">
				<input class="configKey form-control" data-l1key="Syno1_login" placeholder="{{admin}}" />
			</div>
			<div class="col-lg-4">
				<input type="password" class="configKey form-control" data-l1key="Syno1_password" placeholder="{{password}}" />
			</div>
		</div>		
		
	</fieldset>
</form>
<form class="form-horizontal">
	<fieldset>
		<legend><i class="fas fa-server"></i> {{Synology 2}}</legend>
		
		<div class="form-group">
			<label class="col-sm-4 control-label">{{Nom et Serveur:port}}</label>
			<div class="col-lg-3">
				<input class="configKey form-control" data-l1key="Syno2_name" placeholder="{{Mon Syno3}}" />
			</div>
			<div class="col-lg-4">
				<input class="configKey form-control" data-l1key="Syno2_server" placeholder="{{https://192.168.0.4:4001}}" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">{{Login et Mot de passe}}</label>
			<div class="col-lg-3">
				<input class="configKey form-control" data-l1key="Syno2_login" placeholder="{{admin}}" />
			</div>
			<div class="col-lg-4">
				<input type="password" class="configKey form-control" data-l1key="Syno2_password" placeholder="{{password}}" />
			</div>
		</div>		
		
	</fieldset>
</form>
<form class="form-horizontal">
	<fieldset>
		<legend><i class="fas fa-server"></i> {{Synology 3}}</legend>
		
		<div class="form-group">
			<label class="col-sm-4 control-label">{{Nom et Serveur:port}}</label>
			<div class="col-lg-3">
				<input class="configKey form-control" data-l1key="Syno3_name" placeholder="{{Mon Syno3}}" />
			</div>
			<div class="col-lg-4">
				<input class="configKey form-control" data-l1key="Syno3_server" placeholder="{{https://192.168.0.4:8001}}" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">{{Login et Mot de passe}}</label>
			<div class="col-lg-3">
				<input class="configKey form-control" data-l1key="Syno3_login" placeholder="{{admin}}" />
			</div>
			<div class="col-lg-4">
				<input type="password" class="configKey form-control" data-l1key="Syno3_password" placeholder="{{password}}" />
			</div>
		</div>		
		
	</fieldset>
</form>

