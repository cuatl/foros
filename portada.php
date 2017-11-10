<?php
   $master=$sql->Query("SELECT * FROM forotes order by orden");
   while($ma = $master->fetch_object()) {
   ?>
   <div class="bg-dark text-white py-1 px-3">
      <h2><?php echo stripslashes($ma->nombre); ?></h2>
   </div>
   Foros: 
   <?php
      $res=$sql->Query("SELECT * FROM foros WHERE master='".$ma->id."'");
      while($k = $res->fetch_object()) {
         printf('<a href="/foros/?foro=%d#%s">%s</a> / ',$k->id,$k->titulo,$k->titulo);
      }
   ?>
   <div class="card-deck">
      <?php
         $rex=$sql->Query("SELECT * FROM foros WHERE master='".$ma->id."' order by orden");
         while($foro = $rex->fetch_object()) {
            $res=$sql->Query("SELECT * FROM posts WHERE foro='".$foro->id."' AND padre=0 order by id desc limit 2");
            while($k = $res->fetch_object()) {
               //
               $last=$sql->Query("SELECT fecha FROM posts WHERE padre='".$k->id."' order by id desc limit 1");
               if($last->num_rows>0) { $last = $last->fetch_object(); $last = $last->fecha; } 
               else { $last = $k->fecha; }
               //
               $no = $sql->Query("SELECT count(id) as no FROM posts WHERE padre='".$k->id."'");
               $no = $no->fetch_object();
               $image=null;
               preg_match("/<img(.*)src=\"(.*)up(.*)\.(jpg|gif|png)\"/",stripslashes($k->contenido),$y);
               $len=250;
               if(isset($y[3])) {
                  $image = "up/".$y[3].".".$y[4];
                  $len=100;
               }
               $texto = mb_substr(strip_tags(stripslashes($k->contenido)),0,$len,'UTF-8');
               $texto = explode(" ",$texto);
               array_pop($texto);
               $texto = implode(" ",$texto);
            ?>
            <div class="card">
               <?php
                  if(!empty($image)) {
                  ?>
                  <a href="/foros/?view=<?php echo $k->id;?>">
                     <img class="card-img-top picportada" src="s.gif" style="background:url(<?php echo $image;?>)" alt="Card image cap">
                  </a>
                  <?php
                  }
               ?>
               <div class="card-body">
                  <h4 class="card-title"><a href="/foros/?view=<?php echo $k->id;?>"><?php echo stripslashes($k->titulo);?></a></h4>
                  <h6 class="card-subtitle mb-2 text-muted"><?php echo $foro->titulo;?></h6>
                  <p class="card-text">
                  <?php
                     echo $texto;
                  ?>
                  </p>
               </div>
               <div class="card-footer text-muted small">
                  <?php
                     if($no->no>0) echo $no->no." respuesta".($no->no>1?'s':'');
                     else echo "🙄";
                     //leido?
                     $read=sprintf("SELECT * FROM postslog WHERE postid='%d' and me='%d'",$k->id,$_SESSION['me']);
                     $read=$sql->Query($read);
                     if($read->num_rows<1) {
                        echo ' <span class="badge badge-warning">sin leer</span>';
                     } else {
                        $read = $read->fetch_object();
                        if(strtotime($read->fecha) > strtotime($last)) echo ' <span class="badge badge-light">leído</span>';
                        else echo ' <span class="badge badge-info">nuevo</span>';
                     }
                  ?>
               </div>
            </div>
            <?php
            }
         }
      ?>
   </div>
   <?php
   }
