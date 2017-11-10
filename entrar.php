<?php
   if(isset($_SESSION['foro'])) {
      $redir = (!empty($_SESSION['redir'])) ? $_SESSION['redir'] : "/foros/";
      echo $redir;
   ?>
   ya te reconocí... <a href="<?php echo $redir;?>">continuar</a>
   <script>
      window.location = '<?php echo $redir;?>';
   </script>
   <?php
   }
?>
<div class="row">
   <div class="col-md-6 offset-md-3">
      <h3>Por favor identificate</h3>

      <p class="text-center">
      <button class="btn btn-primary" onclick="entrarFB()">Con Facebook</button>
      <button class="btn btn-danger">Con Google+</button>
      </p>

      <p>
      Puedes entrar con tu cuenta de
      Google+ o de Facebook, después
      en los ajustes de tu cuenta
      puedes vincular una cuenta con otra.
      </p>
      <div id="status" class="alert alert-info"></div>
   </div>
</div>
<script>
//Facebook API
(function(d, s, id){
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "https://connect.facebook.net/es_LA/sdk.js";
      fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
   window.fbAsyncInit = function() {
      FB.init({
         appId            : '139376216081066', //cambiar al identificador de tu aplicación
         autoLogAppEvents : false,
         status           : false,
         xfbml            : false,
         version          : 'v2.11'
      });
      //FB.AppEvents.logPageView();
   }
   //https://developers.facebook.com/docs/facebook-login/permissions
   var datame = {};
   var entrarFB = function() {
      FB.login(function(response) {
         // handle the response
         if (response.authResponse) {
            var tok = response.authResponse.accessToken;
            FB.api('/me?fields=id,email,name,first_name,last_name,age_range,link,gender,verified', function(response) {
               $("#status").html("Hola "+response.first_name);
               datame = {
                  "social"      : "FB",
                  "id"          : response.id,
                  "correo"      : response.email,
                  "nombres"     : response.name,
                  "nombre"      : response.first_name,
                  "apellido"    : response.last_name,
                  "genero"      : response.gender,
                  "verificado"  : response.verified,
                  "direccion"   : response.link,
               }
               $.post("api.php",{login:datame,meme:'<?php echo $meme;?>',token:tok},function(m) {
                  if(m.id !== undefined) {
                     window.location = '<?php echo $_SESSION['redir'];?>';
                  } else {
                     $("#status").html('🤔 '+m.error);
                  }
               },'json');
            });
         } else {
            $("#status").html("No aceptó entrar 🙄");
         }
      }, {scope: 'public_profile,email'});
   }
</script>
