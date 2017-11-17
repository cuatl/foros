<?php
   /* mantiene sesión "viva" */
   if(isset($_POST['ping'])) {
      session_start();
      require_once("config.php");
      $msg = new stdclass;
      $msg->new = 0;
      if(isset($_SESSION[ME])) {
         $sql->Query("UPDATE seen=now() WHERE id = '".$_SESSION[ME]."'");
         $q = sprintf("SELECT count(M.cid) as no from mensajes M,posts P where P.id = M.msgid and P.me='%d' and M.enviado='1' and M.visto='0'",$_SESSION[ME]);
         $no = $sql->Query($q);
         $no = $no->fetch_object();
         $msg->new = (int)$no->no;
      }
      if(!isset($_SESSION['pong'])) $_SESSION['pong']=1;
      $_SESSION['pong']++;
      $msg->pong = number_format($_SESSION['pong']/60,2);
      //
      echo json_encode($msg);
   } else {
   ?>
   <script>
      var pong=0; $(document).ready(function(){ var pong = setTimeout("pingPong()",500); });
      function pingPong() {
            if(pong) { clearTimeout(pong); } 
            $.post("<?php echo $sitio;?>ping.php",{ping:1,ts:<?php echo time();?>},function(m) {
                  $(".pingpong").html(m.pong); 
                  if(m.new>0) {
                        $("#nuevos").html('mensajes <span class="badge badge-warning">'+m.new+'</span>');
                  }
            },'json'); 
            pong = setTimeout("pingPong()",1000*60); 
      }
   </script>
   <?php
      if(!isset($_SESSION['pong'])) $_SESSION['pong']=1;
      echo '<span class="text-muted muted pingpong">¿ping? ¡pong '.number_format($_SESSION['pong']/60,2).'!</span>';
   }
?>
