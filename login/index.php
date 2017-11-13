<?php
   session_start();
   require_once(__DIR__."/../config.php");
   require_once(__DIR__."/config.php");
   include_once(__DIR__."/../utils.php");
   $utils = new Utils();
   if(isset($_GET['fb'])) {
      echo "facebook";
      include_once(__DIR__."/fb.php");
   } elseif(isset($_GET['tw'])) {
      echo "twitter";
      include_once(__DIR__."/tw.php");
   } else {
      //lista de opciones
   ?>
   Elija con que red se quiere identificar:
   <a href="?tw=1">twitter</a>
   <a href="?fb=1">facebook</a>
   <p>
   Opcionalmente y una vez que se identifique con una red,
   puede vincular su cuenta con alguna de las otras disponibles
   para entrar con cualquiera de ellas.
   </p>
   <?php
   }
