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
   $meme=md5('ola ke ase o ke ase'.date('YmdH'));
   $sitio= "https://tar.mx/foros/"; // URL sitio publicado
   $sitios = ["https://tar.mx","http://tar.mx"]; //dominios donde esté el foro
   //SOCIAL para identificarnos con Facebook o Google
   define("GOOGLE","1056839610231-12vh6bs27gsg1adqdc3orqvo997lp97b.apps.googleusercontent.com"); // Google+ Client ID
   define("FACEBOOK","139376216081066"); //Facebook APP ID
