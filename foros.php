<?php
   if($_GET['foro'] == '0') {
      require_once("lastForos.php");
   } else {
      $foro=$sql->Query("SELECT F.*,FO.nombre FROM foros F, forotes FO WHERE FO.id = F.master AND F.id = '".__($_GET['foro'])."'");
      if($foro->num_rows<1) die("no existe:(");
      $foro = $foro->fetch_object();
   ?>
   <h2>
      <a href="<?php echo $sitio;?>?foros=1"><?php echo $foro->nombre;?></a>
      /
      <?php echo $foro->titulo; ?>
   </h2>

   <div class="row">
      <div class="col-sm-9">
         <table class="table">
            <thead class="thead-default">
               <tr>
                  <th>título</th>
                  <th>autor</th>
                  <th>publicado</th>
               </tr>
            </thead>
            <tbody>
               <?php
                  $res= $sql->Query("SELECT P.*,U.nombre,U.alta,U.avatar,U.alias,U.id as lid FROM posts P,users U WHERE  P.me=U.id and P.foro = '".$foro->id."' and padre=0");
                  $no=0;
                  while($k = $res->fetch_object()) {
                     $no++;
                  ?>
                  <tr>
                     <td>
                        <a href="<?php echo $sitio;?>?view=<?php echo $k->id; ?>"><?php echo $k->titulo;?></a>
                        <?php
                           //leído?
                           $last=$sql->Query("SELECT fecha FROM posts WHERE padre='".$k->id."' order by id desc limit 1");
                           if($last->num_rows>0) { $last = $last->fetch_object(); $last = $last->fecha; } 
                           else { $last = $k->fecha; }
                           $read=sprintf("SELECT * FROM postslog WHERE postid='%d' and me='%d'",$k->id,$_SESSION['me']);
                           $read=$sql->Query($read);
                           if($read->num_rows<1) { echo ' <span class="badge badge-warning">sin leer</span>'; } 
                           else {
                              $read = $read->fetch_object();
                              if(strtotime($read->fecha) > strtotime($last)) echo ' <span class="badge badge-light">leído</span>';
                              else echo ' <span class="badge badge-info">nuevo</span>';
                           }
                        ?>
                     </td>
                     <td><a href="<?php echo $sitio;?>?u=<?php echo $k->lid;?>"><?php echo (empty($k->alias)?$k->nombre:$k->alias); ?></a></td>
                     <td><?php $d=lafecha($k->fecha); echo $d->fechas;?></td>
                  </tr>
                  <?php
                  }
               ?>
            </tbody>
         </table>
         <?php
            if(!isset($_SESSION['foro'])) {
               printf('¿Quieres escribir algo? por favor <a href="%s?entrar=1">identificate</a>',$sitio);
            } else {
               if($no==0) printf('Aún <strong>no hay</strong> escritos en este foro - <a href="%s?nuevo=%d" class="btn btn-outline-secondary btn-sm">escribir nuevo</a>',$sitio,$foro->id);
               else printf('<p class="lead">Hay %s escritos 😬 en <strong>%s</strong> -  <a href="%s?nuevo=%d" class="btn btn-outline-secondary btn-sm">escribir nuevo</a></p>',$no,$foro->titulo,$sitio,$foro->id);
            }
         ?>
      </div>
      <div class="col-sm-3">
         <h4><?php echo $foro->nombre;?></h4>
         <div class="list-group">
            <?php
               $foros=$sql->Query("SELECT F.* FROM foros F WHERE F.master = '".$foro->master."'");
               while($k = $foros->fetch_object()) {
                  printf('<a href="%s?foro=%d#%s" class="list-group-item list-group-item-action">%s</a>',$sitio,$k->id,$k->titulo,$k->titulo);
               }
            ?>
         </div>
         <br />
         <h5>Más foros</h5>
         <div class="list-group">
            <?php
               $foros=$sql->Query("SELECT F.* FROM foros F order by master");
               while($k = $foros->fetch_object()) {
                  printf('<a href="%s?foro=%d#%s" class="list-group-item list-group-item-action">%s</a>',$sitio,$k->id,$k->titulo,$k->titulo);
               }
            ?>
         </div>
      </div>
   </div>
   <?php
   }
?>
