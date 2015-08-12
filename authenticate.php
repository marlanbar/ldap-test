<?php
function authenticate($user, $password) {
	if(empty($user) || empty($password)) return false;
	$ldap_host = 'ldap://mataburros1.bl.fcen.uba.ar';
	$ldap_host2 = 'ldap://mataburros2.bl.fcen.uba.ar';
	$ldap_port = 389;
	$ldap_user_group = 'Circulante';
	$ldap_dn = 'cn=Users,dc=interna,dc=bl,dc=fcen,dc=uba,dc=ar';
 	$ldap = ldap_connect($ldap_host, $ldap_port) or $ldap = ldap_connect($ldap_host2, $ldap_port);
	if($bind = @ldap_bind($ldap, 'INTERNA\\' . $user, $password)) {
		// login successful
		$access = 0;
		// managers
		$ok_users = array('nrucks', 'vteppa', 'gpascual');
		// group filter
		$filter = '(sAMAccountName=' . $user . ')';
		$attr = array('memberof');
		$result = ldap_search($ldap, $ldap_dn, $filter, $attr) or exit('Unable to search LDAP server');
		$entries = ldap_get_entries($ldap, $result);
		ldap_unbind($ldap);
		foreach($entries[0]['memberof'] as $grps) {
			// is manager, break loop
			if (in_array($user, $ok_users)) { $access = 2; break; }
			// is user in group
			if (strpos($grps, $ldap_user_group)) { $access = 1; break; }		
		}	
		return $access != 0;
	} else {
		// invalid name or password
		return false;
	}
}
?>