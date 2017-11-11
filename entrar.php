<?php
   /* login con Facebook / Google */
   // identificado?
   if(isset($_SESSION['foro'])) {
      $redir = (!empty($_SESSION['redir'])) ? $_SESSION['redir'] : $sitio;
      echo $redir;
   ?>
   ya te reconocí... <a href="<?php echo $redir;?>">continuar</a>
   <script>
      window.location = '<?php echo $redir;?>';
   </script>
   <?php
   }
?>
<script src="https://apis.google.com/js/platform.js" async defer></script>
<div class="row">
   <div class="col-md-6 offset-md-3">
      <h3>Por favor identificate</h3>

      <p class="text-center">
      <button class="btn btn-primary mr-3" onclick="entrarFB()">Con Facebook</button>
      <button class="btn btn-danger" onclick="entrarGO()" id="signin-button">Con Google</button>
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
         appId            : '<?php echo FACEBOOK;?>', //cambiar al identificador de tu aplicación
         autoLogAppEvents : false, status : false, xfbml : false, version : 'v2.11'
      });
   }
   var datame = {};
   //https://developers.facebook.com/docs/facebook-login/permissions
   var entrarFB = function() {
      $("#status").html("Accediendo con Facebook...");
      FB.login(function(response) {
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
               entrar(datame,1);
            });
         } else { $("#status").html("No aceptó entrar 🙄"); }
      }, {scope: 'public_profile,email'}); //permisos
   }
   //g+ https://developers.google.com/identity/sign-in/web/sign-in
   var entrarGO = function() {
      var auth2 = {};
      $("#status").html("Accediendo con Google...");
      gapi.load('auth2', function() {
         gapi.signin2.render('signin-button', {
            fetch_basic_profile: true,
            scope: 'profile email',
         });
         gapi.auth2.init({
            client_id: '<?php echo GOOGLE;?>',
            fetch_basic_profile: true,
            scope: 'profile email' // permisos
         }).then(function() {
            auth2 = gapi.auth2.getAuthInstance();
            auth2.isSignedIn.listen(updateSignIn);
            auth2.then(updateSignIn);
         });
      });
      var updateSignIn = function() {
         $("#status").html('Identificando...');
         if(auth2.isSignedIn.get()) {
            var profile = auth2.currentUser.get().getBasicProfile();
            datame = {
               "social"      : "GO",
               "id"          : profile.getId(),
               "correo"      : profile.getEmail(),
               "nombres"     : profile.getName(),
               "nombre"      : profile.getGivenName(),
               "apellido"    : profile.getFamilyName(),
               "perfil"      : profile.getImageUrl(),
            }
            entrar(datame,1);
         } else {
            $("#status").html("Por favor de click de nuevo en el botón  de Google");
         }
      }
   }
   var entrar = function(datos, tok = "") {
      $.post("api.php",{login:datos,meme:'<?php echo $meme;?>',token:tok},function(m) {
         if(m.id !== undefined) { window.location = '<?php echo $_SESSION['redir'];?>'; } 
         else { $("#status").html('🤔 '+m.error); }
      },'json');
   }
</script>
