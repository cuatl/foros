<?php
   if(!isset($_SESSION['foro'])) {
      $_SESSION['redir'] = "foros/?nuevo=".__($_GET['nuevo']);
      die("Debe estar <a href=\"/foros/?entrar=1\">identificado</a>.");
   }
?>
<h1>Escribir nuevo post</h1>
<?php
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
            'advlist autolink lists link image charmap print preview anchor textcolor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table contextmenu paste code help'
            ],
            toolbar: 'insert | undo redo | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist | removeformat | link image code | help',
            images_upload_url: 'postimg.php',
            images_upload_credentials: true,
            menubar:false,
            image_class_list: [
            { title: "Adaptable", value: "img-fluid" }
            ],
      });
   </script>
   <?php
   } /* }}} */
   //
   if(isset($_POST['contenido']) && !empty($_POST['contenido'])) {
      $image=null;
      $debug=false;
      /* post image {{{ */
      if(isset($_FILES['archivo']['tmp_name']) && is_uploaded_file($_FILES['archivo']['tmp_name']) && substr($_FILES['archivo']['type'],0,6) == 'image/') {
         if($debug) echo "<pre> --- \n";
            $imageFolder = "/var/www/tar/foros/up/";
         $pato = $imageFolder.date('Y/m/');
         $ext = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION));
         $file =    uniqid()."_o.".$ext;
         $filet= str_replace("_o","",$file);
         $filete=str_replace("_o","_t",$file);
         //
         if(!preg_match("/\.jpg$/",$filet)) { $filet = str_replace(".".$ext,".jpg",$filet); }
         if(!preg_match("/\.jpg$/",$filete)) { $filete = str_replace(".".$ext,".jpg",$filete); }
         //
         if(!is_dir($pato)) mkdir($pato,0755,true);
         $filetowrite = $pato.$file;
         move_uploaded_file($_FILES['archivo']['tmp_name'], $filetowrite);
         if($debug) echo " filetowrite = $filetowrite \n";
         $t = `/usr/bin/identify $filetowrite`;
         $s = explode(" ",$t);
         if($debug) { 
            echo "size: \n";
            print_r($t);
            print_r($s);
            echo "\n";
         }
         if(in_array($s[1],['JPG','JPEG','PNG','GIF'])) {
            $ext = trim(strtolower($s[1]));
            $ss = explode("x",$s[2]);
            if($ss[0]>=720 && $ext!='gif') {
               //
               $cmd = "convert -thumbnail 720 -flatten -quality 70 -background white -auto-orient ".$filetowrite."[0] ".$pato.$filet; 
               if($debug) echo "$cmd\n";
               $cmd = `$cmd`;
               $cmd = "convert -thumbnail 200 -flatten -quality 60 ".$pato.$filet." ".$pato.$filete; 
               if($debug) echo "$cmd\n";
               $cmd = `$cmd`;
               $size=720;
            } elseif($ss[0]>=200 && $ext!='gif') {
               $cmd = "convert -thumbnail ".$ss[0]." -flatten -quality 70 -background white -auto-orient ".$filetowrite."[0] ".$pato.$filet;
               if($debug) echo "$cmd\n";
               $cmd = `$cmd`;
               $cmd = "convert -thumbnail 200 -flatten -quality 60 ".$pato.$filet." ".$pato.$filete; 
               if($debug) echo "$cmd\n";
               $cmd = `$cmd`;
               $size=$ss[0];
            } else {
               if(!empty($ss[0])) $size=$ss[0];
               copy($filetowrite,$filet);
               $cmd = "convert -thumbnail 200 -flatten -quality 60 -background white ".$filetowrite."[0] ". $pato.$filete;
               if($debug) echo "$cmd\n";
               $cmd = `$cmd`;
            }
         }
         $image = "<p class=\"text-center\"><img class=\"img-fluid\" src=\"https://tar.mx/foros/up/".date('Y/m/').$filet."\" alt=\"imagen\" /></p>";
      }
      /* }}} */
      if($mobile) $body = $image.nl2br($_POST['contenido']);
      else $body = $image.$_POST['contenido'];
      //
      $padre=(isset($_POST['padre']))?__($_POST['padre']):0;
      //
      if(!isset($_POST['edit'])) {
         $q = sprintf("INSERT INTO posts (id,padre, me, foro, fecha, titulo, contenido) values(null,'%d','%s','%s',now(),'%s','%s')",$padre,$_SESSION['foro'], __($_POST['cat']), __($_POST['titulo']), addslashes($body));
         $sql->Query($q);
         if(!empty($sql->error)) echo "SQL ERROR: ".$sql->error;
         $ID = $sql->insert_id;
      } else {
         $ID = __($_POST['edit']);
         $q= sprintf("UPDATE posts SET padre='%d', foro='%d', titulo='%s', contenido='%s' WHERE id ='%d' and me = '%s'", $padre, __($_POST['cat']), __($_POST['titulo']), addslashes($body), $ID, $_SESSION['foro']);
         $sql->Query($q);
         if(!empty($sql->error)) echo "SQL ERROR: ".$sql->error;
      }
      if($debug) print_r($_FILES);echo "</pre>";
   ?>
   <h3>¡Gracias!</h3>
   <div class="alert alert-success" role="alert">
      <p class="lead">
      Su post ha sido almacenado y publicado.
      </p>
   </div>
   <a class="btn btn-light" href="/foros/?nuevo=1&edit=<?php echo $ID;?>">volver a editar</a>
   <?php
      $urlt = ($padre>0) ? "view=".$padre."#r".$ID : "view=".$ID;
   ?>
   <a class="btn btn-secondary" href="/foros/?<?php echo $urlt;?>">ver escrito</a>
   <script>
      $(function() { setTimeout(function() { window.location = '/foros/?<?php echo $urlt;?>'; },5000); });
   </script>
   <?php
      exit();
   }
   if(isset($_GET['edit'])) {
      $edit = sprintf("SELECT * FROM posts WHERE id = '%d' AND me = '%s'",__($_GET['edit']),__($_SESSION['foro']));
      $edit = $sql->Query($edit);
      if($edit->num_rows>0) $edit = $edit->fetch_object();
      else unset($edit);
   }
?>
<form method="post" enctype="multipart/form-data">
   <?php
      if(isset($edit->id)) printf('<input type="hidden" name="edit" value="%d" />',$edit->id);
      if(isset($edit->padre) && $edit->padre>0) printf('<input type="hidden" name="padre" value="%d" />',$edit->padre);
   ?>
   <div class="form-row">
      <div class="form-group col-md-8">
         <input type="text" name="titulo" class="form-control" placeholder="Título del post" value="<?php if(isset($edit->id)) echo stripslashes($edit->titulo); ?>" autofocus />
      </div>
      <div class="form-group col-md-4">
         <select class="form-control" name="cat">
            <?php
               $master=$sql->Query("SELECT * FROM forotes");
               while($ma = $master->fetch_object()) {
                  $res = $sql->Query("SELECT id,titulo FROM foros where master='".$ma->id."'");
                  printf('<optgroup label="%s" />',$ma->nombre);
                  while($k = $res->fetch_object()) {
                     $tmp =(isset($edit->id) && $edit->foro == $k->id) ? ' selected ':null;
                     if(empty($tmp) && $_GET['nuevo'] == $k->id) $tmp = " selected";
                     printf('<option value="%s"%s>%s</option>', $k->id,$tmp,$k->titulo);
                  }
               }
            ?>
         </select>
      </div>
   </div>
   <textarea name="contenido" class="form-control" rows="<?php echo ($mobile)?10:20;?>"><?php if(isset($edit->id)) echo stripslashes($edit->contenido); ?></textarea>
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
      <button class="btn btn-lg btn-primary">Almacenar escrito</button>
   </div>
</form>
