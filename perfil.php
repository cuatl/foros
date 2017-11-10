<?php
   /* edición básica perfil usuario */
   if(!isset($_SESSION['foro'])) {
      $_SESSION['redir'] = $sitio."?perfil=me";
   ?>
   <script>
      window.location = '<?php echo $sitio;?>?entrar=1';
   </script>
   <?php
   }
   $u=$sql->query("SELECT * FROM users WHERE id='".__($_SESSION['foro'])."'");
   $me = $u->fetch_object();
   if(isset($_POST['alias'])&&!empty($_POST['alias'])) {
      $q=sprintf("UPDATE users SET alias = '%s' WHERE id = '%s'",__($_POST['alias']), $_SESSION['foro']);
      $sql->Query($q);
      $me->alias = __($_POST['alias']);
      $_SESSION['data']->alias= __($_POST['alias']);
      if(isset($_FILES['archivo']['tmp_name']) && is_uploaded_file($_FILES['archivo']['tmp_name']) && substr($_FILES['archivo']['type'],0,6) == 'image/') {
         $pato= __DIR__."/up/perfil/".date('Y/m/',strtotime($me->alta));
         if(!is_dir($pato)) mkdir($pato,0755,true);
         $image = uniqid();
         $cmd = "convert -thumbnail 400 -flatten -quality 70 -background white -auto-orient ".$_FILES['archivo']['tmp_name']."[0] ".$pato.$image.".jpg";
         $cmd = `$cmd`;
         $me->avatar = $image;
         $_SESSION['data']->avatar = $image;
         $q = sprintf("UPDATE users SET avatar='%s' WHERE id = '%s'",__($image), $_SESSION['foro']);
         $sql->Query($q);
      }
      echo "<em>Se actualizaron los datos</em>";
   }
?>
<p class="lead">Perfil de <strong><?php echo $me->nombre;?></strong></p>

<div class="row">
   <div class="col-sm-6">
      <form method="post" enctype="multipart/form-data">
         <div class="form-row">
            <div class="form-group col-md-6">
               <label class="col-form-label">Alias a mostrar</label>
               <input type="text" name="alias" class="form-control" placeholder="apodo" maxlength="16" value="<?php echo (empty($me->alias)?$me->nombre:$me->alias); ?>">
            </div>
            <div class="form-group col-md-6">
               <label for="inputPassword4" class="col-form-label">Fotografía a mostrar</label>
               <input type="file" name="archivo" accept="image/*" class="form-control" />
            </div>
         </div>
         <button type="submit" class="btn btn-primary">actualizar</button>
      </form>
   </div>
   <div class="col-sm-3">
      Opciones
      <div class="list-group">
         <a href="<?php echo $sitio;?>?perfil=me" class="list-group-item"> Actualizar datos</a>
      </div>
   </div>
   <div class="col-sm-3">
      <?php
         $e = $sql->Query("SELECT * FROM users WHERE lid = '".$_SESSION['me']."'");
         $e = $e->fetch_object();
         if(empty($e->vinculada)) {
         ?>
         Vincular cuenta
         <?php
            if($_SESSION['tipo'] == 'FB') {
               printf('<a href="logingp.php?from=FB">con Google</a>');
            } else {
               printf('<a href="login.php?from=FB">con Facebook</a>');
            }
         } else {
            printf('Su cuenta está vinculada con Facebook y Google.');
         }
      ?>
   </div>
</div>

