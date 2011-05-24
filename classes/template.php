<?php
class Template extends ADOdb_Active_Record{} {
}

ADODB_Active_Record::ClassHasMany('template', 'templates','user_id');
?>