<?php
   session_start();
   require_once(__DIR__."/../config.php");
   require_once(__DIR__."/config.php");
   include_once(__DIR__."/../utils.php");
   $utils = new Utils();
?>
<!DOCTYPE html>
<html lang="es">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <meta name="description" content="Login en foros de tar.mx">
      <meta name="author" content="Jorge Martínez M @toro">
      <title>Entrar a los foros de tar.mx</title>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
   </head>
   <body>
      <div class="container">
         <?php
            if(isset($_GET['fb'])) {
               include_once(__DIR__."/fb.php");
            } elseif(isset($_GET['tw'])) {
               echo "twitter";
               include_once(__DIR__."/tw.php");
            } else {
               //lista de opciones
            ?>
            <p class="lead">
            Elija con que red se quiere identificar:
            </p>
            <div class="text-center">
               <div class="btn-group btn-group-vertical btn-group-lg" role="group">
                  <a href="?fb=1" class="btn btn-primary">Entrar con Facebook</a>
                  <a href="?tw=1" class="btn btn-secondary">Entrar con Twitter</a>
               </div>
            </div>
            <hr />
            <p>
            Opcionalmente y una vez que se identifique con una red,
            puede vincular su cuenta con alguna de las otras disponibles
            para entrar con cualquiera de ellas.
            </p>
            <?php
            }
         ?>
      </div>
   </body>
</html>
