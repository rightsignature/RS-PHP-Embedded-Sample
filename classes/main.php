<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/adodb5/adodb-active-record.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/adodb5/adodb.inc.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/config/creds.php");

// DB connection
$DB = NewADOConnection("mysql://" . $db_user . ":" . $db_pass . "@" . $db_server . "/" . $db_name);
ADOdb_Active_Record::SetDatabaseAdapter($DB);
?>