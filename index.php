<?php
   session_start();
   if(isset($_GET['salir'])) {
      session_destroy();
      header("Location: /foros/");
      exit();
   }
   include_once(__DIR__."/config.php");
   $mobile=false;
   if(preg_match("/(Blackberry|SymbianOS|iPod|iPhone|Android|Opera Mini|Windows Phone)/i",$_SERVER['HTTP_USER_AGENT'])) {
      if(!preg_match("/(iPad)/i",$_SERVER['HTTP_USER_AGENT'])){ $mobile=true; }
   }
   include_once("utils.php");
?>
<!DOCTYPE html>
<html lang="es">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <meta name="description" content="foros">
      <meta name="author" content="Jorge Martínez M @toro">
      <title>Foros tar.mx</title>
      <link rel="stylesheet" href="estilo.css">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> 
   </head>
   <body>
      <div class="container">
         <div class="masthead">
            <h3><a href="/foros/" class="text-muted">Foros</a></h3>
            <nav class="navbar navbar-expand-lg navbar-light bg-light rounded mb-3">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
               <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
               <ul class="navbar-nav mr-auto">
                  <li class="nav-item active">
                  <a class="nav-link" href="/foros/">Inicio <span class="sr-only">(current)</span></a></li>
                  <li class="nav-item"><a class="nav-link" href="/">tar.mx</a></li>
               </ul>
               <?php
                  if(isset($_SESSION['me'])) {
                     @$img = avatar([$_SESSION['fit'],$_SESSION['data']->avatar,$_SESSION['data']->fecha,$_SESSION['data']->image['url']],$_SESSION['tipo']);
                     $img = sprintf('<img src="%s" width="30" alt="me" /> ',$img);
                     printf('<span class="navbar-text float-right"><a href="/foros/?perfil=1">%s%s</a></span>',$img,$_SESSION['data']->alias);
                  }
               ?>
            </div>
            </nav>
         </div>


         <?php
            if(isset($_GET['entrar'])) {
               include_once("entrar.php");
            } elseif(isset($_GET['nuevo'])) {
               include_once("editor.php");
            } elseif(isset($_GET['view'])) {
               include_once("view.php");
            } elseif(isset($_GET['foro'])) {
               include_once("foros.php");
            } else {
               include_once("portada.php");
            }
         ?>


         <!-- Site footer -->
         <hr />
         <footer class="footer">
         <p class="text-center">
         &copy; tar.mx 
         <?php 
            echo date('Y');
            include_once("ping.php"); 
            if(isset($_SESSION['foro'])) {
               printf('/ <a href="/foros/?salir=1">salir</a>');
            } else {
               printf('/ <a href="/foros/?entrar=1">entrar</a>');
            }
         ?>
         </p>
         </footer>

      </div> <!-- /container -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
   </body>
</html>
<pre>
   <?php
      print_r($_SESSION);
   ?>
</pre>
