<?php
   $sqls = new stdclass;
   //config
   setlocale(LC_ALL,'es_MX.UTF-8');
   $sqls->host = "localhost";
   $sqls->user = "foros";
   $sqls->pass = "LWE7XE?!p7SRrJgg";
   $sqls->data = "foros";
   //DB: grant all on foros.* TO "foros"@"localhost" identified by "LWE7XE?!p7SRrJgg";
   $sql = new MySQLi($sqls->host,$sqls->user,$sqls->pass,$sqls->data);
   $sql->Query("SET names 'utf8mb4'");
   function __($t) { return addslashes(strip_tags($t)); }
