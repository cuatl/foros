<?php
   /* muestra el post */
   if(isset($_GET['rm'])) {
      $q = sprintf("DELETE FROM posts WHERE id = '%d' AND me = '%s'",__($_GET['rm']),$_SESSION['foro']);
      $sql->Query($q);
      printf('<p class="lead">Se <strong>eliminó</strong> un escrito 😞</p>');
   }
   $q = "SELECT P.*,U.nombre,U.alta,U.avatar,U.perfil,U.tipo,U.alias,U.socialid,U.id as lid,U.perfil,U.tipo FROM posts P,users U WHERE  P.me=U.id and P.id = '    ".__($_GET['view'])."'";
   $post = $sql->Query($q);
   if($post->num_rows<1) {
      die(sprintf('<p class="lead">Ese escrito <strong>se ha desvanecido</strong> o nunca existió 😱</p>'));
   }
   $post = $post->fetch_object();
   $foro=$sql->Query("SELECT F.*,MA.nombre FROM foros F, forotes MA WHERE F.master=MA.id AND F.id = '".$post->foro."'");
   $foro = $foro->fetch_object();
   printf('<p class="lead text-center">%s » <a href="%s?foro=%d#%s">%s</a></p>',$foro->nombre,$sitio,$foro->id,$foro->titulo,stripslashes($foro->titulo));
?>
<div class="row">
   <div class="col-sm-3 bg-dark text-white">
      <p class="text-center">
      <a href="<?php echo $sitio;?>?u=<?php echo $post->lid;?>"><img src="<?php echo  $utils->pic([$post->tipo, $post->perfil, $post->avatar, $post->alta,$post->socialid],300);?>" class="rounded py-3" width="150" alt="imagen" /></a>
      </p>
      Escrito por <strong><?php echo (!empty($post->alias)?$post->alias:$post->nombre);?></strong> <?php $d = $utils->fecha($post->fecha);echo $d->fechas;?>
   </div>
   <div class="col-sm-9">
      <h1><?php echo stripslashes($post->titulo);?></h1>
      <?php
         echo stripslashes($post->contenido);
         printf('<br><p class="text-muted small font-italic">publicado: %s</p>',$d->full);
         if(isset($_SESSION['foro']) &&$_SESSION['foro'] == $post->me) {
         ?>
         <div class="bg-light">
            <a href="<?php echo $sitio;?>?nuevo=1&edit=<?php echo $post->id;?>">editar</a>
         </div>
         <?php
         }
      ?>
   </div>
</div>
<br />
<hr />
<?php
   //hijos
   $res = $sql->Query("SELECT P.*,U.nombre,U.alta,U.avatar,U.perfil,U.tipo,U.alias,U.socialid, U.id as lid,U.perfil,U.tipo FROM posts P, users U WHERE P.me=U.id and P.padre = '".$post->id."' order by P.id");
   $no=0;
   while($k = $res->fetch_object()) {
   ?>
   <div class="row" id="r<?php echo $k->id;?>">
      <div class="col-sm-3 bg-light">
         <p class="text-center">
         <a href="<?php echo $sitio;?>?u=<?php echo $k->lid;?>"><img src="<?php echo $utils->pic([$k->tipo, $k->perfil, $k->avatar, $k->alta,$k->socialid],300); ?>" class="rounded py-3" width="100" alt="imagen" /></a>
         </p>
         Escrito por <strong><?php echo (!empty($k->alias)?$k->alias:$k->nombre);?></strong> <?php $d = $utils->fecha($k->fecha);echo $d->fechas;?>
      </div>
      <div class="col-sm-9">
         <big class="float-right ml-2"><?php echo ++$no;?></big>
         <?php 
            if(!empty($k->titulo)) printf('<p class="lead">%s</p>',$k->titulo);
            echo stripslashes($k->contenido);
            printf('<p class="text-muted small font-italic">publicado: %s</p>',$d->full);
            if(isset($_SESSION['foro']) &&$_SESSION['foro'] == $k->me) {
            ?>
            <br />
            <div class="bg-light">
               <a href="<?php echo $sitio;?>?nuevo=1&edit=<?php echo $k->id;?>">editar</a>
               /
               <a href="#" onclick="return borramesta(<?php echo $k->id;?>)">borrar</a>
            </div>
            <?php
            }
         ?>
      </div>
   </div>
   <hr />
   <br />
   <?php
   }
   /* tinymce {{{ */

   if(!$mobile) {
   ?>
   <script src="tinymce/tinymce.min.js"></script>
   <script>
      tinymce.init({
            selector: 'textarea',  // change this value according to your html
            language: 'es_MX',
            entity_encoding: 'raw',
            relative_urls : false,
            plugins: [
            'autolink lists link image charmap print preview anchor textcolor',
            'searchreplace visualblocks code codesample fullscreen',
            'insertdatetime media table contextmenu paste help'
            ],
            toolbar: 'insert | undo redo |  bold italic | removeformat | link image code | codesample',
            images_upload_url: 'postimg.php',
            images_upload_credentials: true,
            menubar:false,
            statusbar:false,
            image_class_list: [
            { title: "Adaptable", value: "img-fluid" }
            ],
            codesample_languages: [
            {text: 'HTML/XML', value: 'markup'},
            {text: 'JavaScript', value: 'javascript'},
            {text: 'CSS', value: 'css'},
            {text: 'PHP', value: 'php'},
            {text: 'C++', value: 'cpp'},
            {text: 'Swift', value: 'swift'},
            {text: 'SQL', value: 'sql'},
            ],
      });
   </script>
   <?php
   } /* }}} */
?>
<br />
<hr />
<div class="row">
   <div class="col-sm-8">
      Escribe una respuesta
      <?php
         if(isset($_SESSION['foro'])) {
         ?>
         <form method="post" action="<?php echo $sitio;?>?nuevo=1" enctype="multipart/form-data">
            <input type="hidden" name="padre" value="<?php echo $post->id;?>" />
            <input type="hidden" name="cat" value="<?php echo $post->foro;?>" />
            <textarea name="contenido" class="form-control" rows="8"></textarea>
            <?php
               if($mobile) {
               ?>
               <div class="form-group">
                  <label>Agregar imagen</label>
                  <input type="file" name="archivo" class="form-control" placeholder="archivo" accept="image/*">
               </div>
               <?php
               }
            ?>
            <br />
            <div class="form-group text-center">
               <button class="btn btn-lg btn-primary">Enviar respuesta</button>
            </div>
         </form>
         <?php
         } else {
            $_SESSION['redir'] = $sitio."?view=".$post->id;
         ?>
         Para escribir, es necesario estar identificado. <a href="<?php echo $sitio;?>?entrar=1" class="btn btn-secondary">Puedes entrar</a>
         con tu cuenta de Facebook o Google+ :-)
         <?php
         }
      ?>
   </div>
   <div class="col-sm-4">
      Este contenido está almacenado en 
      <?php
         printf('<p class="lead">%s » <a href="%s?foro=%d#%s">%s</a></p>',$foro->nombre,$sitio,$foro->id,$foro->titulo,stripslashes($foro->titulo));
      ?>
      fue inicialmente escrito por <strong class="text-italic"><?php echo (!empty($post->alias)?$post->alias:$post->nombre);?></strong>
      y actualmente tiene <?php echo $no;?> respuesta<?php echo ($no<>1?'s':null); ?>.
   </div>
</div>
<script>
   $(function() {
         $("TITLE").html('<?php echo stripslashes($post->titulo);?>');
         $.post("api.php",{ post:<?php echo $post->id;?>,meme:'<?php echo $meme;?>' },function(m) {
         },'json');
   });
   var borramesta = function(id) {
         if(!window.confirm('Se eliminará para siempre, no se puede recuperar...')) {
               return false;
            } else {
               window.location = '<?php echo $sitio;?>?view=<?php echo $post->id;?>&rm='+id;
         }
         return false;
   }
</script>
<link rel="stylesheet" href="prism/prism.css">
<script src="prism/prism.js" async></script>
