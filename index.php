<?php
   session_start();
   include_once(__DIR__."/config.php");
   if(isset($_GET['salir'])) {
      session_destroy();
      header("Location: ".$sitio);
      exit();
   }
   $mobile=false;
   if(preg_match("/(Blackberry|SymbianOS|iPod|iPhone|Android|Opera Mini|Windows Phone)/i",$_SERVER['HTTP_USER_AGENT'])) {
      if(!preg_match("/(iPad)/i",$_SERVER['HTTP_USER_AGENT'])){ $mobile=true; }
   }
   include_once("utils.php");
   $utils = new Utils();
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
            <h3><a href="<?php echo $sitio;?>" class="text-muted">Foros</a></h3>
            <nav class="navbar navbar-expand-lg navbar-light bg-light rounded mb-3">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
               <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
               <ul class="navbar-nav mr-auto">
                  <li class="nav-item active">
                  <a class="nav-link" href="<?php echo $sitio;?>">Inicio <span class="sr-only">(current)</span></a></li>
                  <li class="nav-item"><a class="nav-link" href="/">tar.mx</a></li>
               </ul>
               <?php
                  //imagen usuario
                  if(!isset($_SESSION['foro'])) {
                     printf('<span class="navbar-text float-right"><a href="%s?entrar=1">entrar</a></span>',$sitio);
                  } else {
                     @printf('<span class="navbar-text float-right"><a href="%s?perfil=me"><img src="%s" height="30" alt="me" /></a></span>',$sitio,$utils->pic([$_SESSION['data']->social, $_SESSION['data']->perfil, $_SESSION['data']->avatar,$_SESSION['data']->alta,$_SESSION['data']->socialid],14));
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
            } elseif(isset($_GET['perfil'])) {
               include_once("perfil.php");
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
               printf(' / <a href="%s?salir=1">salir</a>',$sitio);
            } else {
               printf(' / <a href="%s?entrar=1">entrar</a>',$sitio);
            }
         ?>
         / <a href="https://github.com/cuatl/foros">código fuente</a>
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
