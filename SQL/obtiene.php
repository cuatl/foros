<?php
   require_once(__DIR__."/../config.php");
   $q = sprintf("show tables FROM %s",$sqls->data);
   $res = $sql->Query($q);
   $data = null;
   while($k = $res->fetch_array()) {
      $q = sprintf("show create table %s",$k[0]);
      $table = $sql->Query($q);
      $table = $table->fetch_array();
      $data .= "#\n# tabla ".$table[0]."\n#\n";
      $data .= $table[1]."\n\n";
   }
   file_put_contents(__DIR__."/database.sql",$data);
