<?php
class User extends ADOdb_Active_Record{} 
{
	
}

ADODB_Active_Record::ClassHasMany('document', 'documents','user_id');
?>
