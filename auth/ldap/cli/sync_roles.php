<?php
define('CLI_SCRIPT', true);

require(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php'); // global moodle config file.
require_once($CFG->dirroot.'/course/lib.php');

// Ensure errors are well explained
$CFG->debug = DEBUG_NORMAL;

if (!is_enabled_auth('ldap')) {
    error_log('[AUTH LDAP] '.get_string('pluginnotenabled', 'auth_ldap'));
    die;
}

$ldapauth = get_auth_plugin('ldap');

$auths = array('ldap', 'ldapcapture');
list($insql, $params) = $DB->get_in_or_equal($auths);
$where = 'auth '.$insql.' AND deleted = ?';
$params[] = 0;
$users = $DB->get_records_select('user', $where, $params);

foreach ($users as $user) {
    $ldapauth->sync_roles($user);
}

