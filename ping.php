<?php
   if(isset($_POST['ping'])) {
      session_start();
      if(isset($_SESSION['foro'])) {
         require_once("config.php");
         $sql->Query("UPDATE seen=now() WHERE lid = '".$_SESSION['foro']."'");
      }
      if(!isset($_SESSION['pong'])) $_SESSION['pong']=1;
      $_SESSION['pong']++;
      $msg = new stdclass;
      $msg->pong = number_format($_SESSION['pong']/60,2);
      echo json_encode($msg);
   } else {
   ?>
   <script>
      var pong=0; $(document).ready(function(){ var pong = setTimeout("pingPong()",500); });
      function pingPong() {
            if(pong) { clearTimeout(pong); } 
            $.post("<?php echo $sitio;?>ping.php",{ping:1,ts:<?php echo time();?>},function(m) {
                  $(".pingpong").html(m.pong); 
            },'json'); 
            pong = setTimeout("pingPong()",1000*30); 
      }
   </script>
   <?php
      if(!isset($_SESSION['pong'])) $_SESSION['pong']=1;
      echo '<span class="text-muted muted pingpong">¿ping? ¡pong '.number_format($_SESSION['pong']/60,2).'!</span>';
   }
?>
