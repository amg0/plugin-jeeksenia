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

// $eqLogic = eqLogic::byId(init('eqLogic_id'));
// $cmd = cmd::byEqLogicIdAndLogicalId(  init('eqLogic_id'),  'events' );
// $events = $cmd->execCmd();

$eqid = init('eqLogic_id');
sendVarToJs('cmdid', init('cmdid'));
sendVarToJs('eqLogic_id', $eqid );
$eqLogic = eqLogic::byId( $eqid );
$cmd = cmd::byEqLogicIdAndLogicalId(  $eqid,  'events' );

sendVarToJs('testeqid', $eqLogic->getId());
sendVarToJs('testcmdid', $cmd->getId());
?>

Exemple de modale pour la commande <?=init('cmdid')?> de l'eq id <?=init('eqLogic_id')?> 
<?php
    $eqLogics = eqLogic::byType('jeeksenia');
    foreach ($eqLogics as $eqLogic) { 
?>
    <?= $eqLogic->getID() ?>
<?php } ?>

<div class="col-lg-12">
    <legend><i class="far fa-calendar-alt"></i> {{Evenements}}</legend>
    <div class="table-responsive">
        <span id="jeeksenia-events">to do add events here</span>
    </div>
</div>


<?php include_file('desktop', 'modal', 'js', 'jeeksenia'); ?>
<?php include_file('desktop', 'utils', 'js', 'jeeksenia');?>
<?php include_file('desktop', 'jeeksenia', 'js', 'jeeksenia');?>
<?php include_file('core', 'plugin.template', 'js');?>
