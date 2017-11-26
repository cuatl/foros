<?php
   /*
   * procedimiento para identificación con Google (no Google+)
   * es necesario ver las variables del archivo config-example.php
   */
   require_once("../config.php");
   require_once("config.php");
   require_once __DIR__ . '/vendor/autoload.php'; 
   $client = new Google_Client();
   $client->setAuthConfig($configgojson);
   if(!isset($_GET['code'])) {
      //paso 1: generamos url para Google 
      // https://github.com/google/google-api-php-client/blob/master/examples/idtoken.php
      $client->setScopes('profile email'); //su correo :o
      $urltoken = $client->createAuthUrl();
      if(!empty($urltoken) && preg_match("/^https:\/\/accounts\.google\.com/",$urltoken)) {
         printf("Estamos redireccionado a Google... ");
      ?>
      <script>
         window.location = '<?php echo $urltoken;?>';
      </script>
      <?php
      } else die("no se pudo generar el URL para twitter");
   } elseif(isset($_GET['code'])) {
      //paso 2: obtenemos la información
      echo " - obteniendo datos... ";
      //
      $client->authenticate($_GET['code']);
      $access_token = $client->getAccessToken();
      //$client->setAccessToken($access_token);
      $token = $access_token['access_token'];
      //las siguientes dos líneas hicieron que perdiera como 2 horas de mi tiempo, no encontraba como obtener los datos... es más, había implementado el servicio people (people/me) ··_ y en los ejemplos de Google PHP SDK no hay nada!
      $os = new Google_Service_Oauth2($client);
      $me = $os->userinfo->get();
      //print_r($me);
      //objeto con la información obtenida
      $save =new stdclass;
      $save->id         = $me->getId();
      $save->correo     = $me->getEmail();
      $save->social     = "GO";
      $save->nombres    = $me->getName();
      $save->nombre     = $me->getGivenName();
      $save->apellido   = $me->getFamilyName();
      $save->genero     = $me->getGender();
      $save->perfil     = $me->getPicture();
      //almacenamos en la db, ver ../utils.php
      $da = $utils->saveUser($save,$token);
      if(isset($da->id)) {
         //almacenado en sesión
         $_SESSION[ME] = $da->id;
         $_SESSION['data'] = $da;
         //cerramos esta ventana :-)
      ?>
      <script>
         window.onunload = refresh;
         var refresh = function() { window.opener.location.reload(); window.self.close(); }
         refresh();
      </script>
      <?php
      } else die("no se pudo almacenar :(");
   } else die("no se identificó.");
