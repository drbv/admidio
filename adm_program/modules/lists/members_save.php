<?php
/******************************************************************************
 * Functions to save user memberships of roles
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Parameters:
 *
 * rol_id : edit the membership of this role id
 * usr_id : edit the membership of this user id
 *
 *****************************************************************************/
require_once('../../system/common.php');
require_once('../../system/login_valid.php');
require_once('../../system/classes/role_dependency.php');
require_once('../../system/classes/table_members.php');
require_once('../../system/classes/table_roles.php');

// Initialize and check the parameters
$getRoleId = admFuncVariableIsValid($_GET, 'rol_id', 'numeric', null, true, null, true);
$getUserId = admFuncVariableIsValid($_GET, 'usr_id', 'numeric', null, true, null, true);

//Member
$member = new TableMembers($gDb);

// Objekt der uebergeben Rollen-ID erstellen
$role = new TableRoles($gDb, $getRoleId);

// roles of other organizations can't be edited
if($role->getValue('cat_org_id') != $gCurrentOrganization->getValue('org_id') && $role->getValue('cat_org_id') > 0)
{
	echo 'SYS_NO_RIGHTS';
	exit(); 
}

// check if user is allowed to assign members to this role
if($role->allowedToAssignMembers($gCurrentUser) == false)
{
	echo 'SYS_NO_RIGHTS';
	exit(); 
}

//POST Daten übernehmen
$membership = 0;
$leadership = 0;

if(isset($_POST['member_'.$getUserId]) && $_POST['member_'.$getUserId]=='true')
{
    $membership = 1;
}
if(isset($_POST['leader_'.$getUserId]) && $_POST['leader_'.$getUserId]=='true')
{
    $membership = 1;    
    $leadership = 1;
}

//Datensatzupdate
$mem_count = $role->countMembers($getUserId);

//Wenn Rolle weniger mitglieder hätte als zugelassen oder Leiter hinzugefügt werden soll
if($leadership==1 || ($leadership==0 && $membership==1 && ($role->getValue('rol_max_members') > $mem_count || $role->getValue('rol_max_members') == 0 || $role->getValue('rol_max_members')==0)))
{
	$member->startMembership($role->getValue('rol_id'), $getUserId, $leadership);
    echo 'success';
}
elseif($leadership==0 && $membership==0)
{
	$member->stopMembership($role->getValue('rol_id'), $getUserId);
    echo 'success';
}
else
{
    echo 'max_mem_reached';
}

?>