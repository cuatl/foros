<?php
   if(isset($_GET['fb']) && !strcmp($_GET['fb'],1)) {
      //paso 1: generamos URL para acceder.
      require_once __DIR__ . '/vendor/autoload.php'; 
      if(!isset($configfb)) die("no tiene disponible configfb en config.php :(");
      $fb = new \Facebook\Facebook($configfb);
      $helper = $fb->getRedirectLoginHelper();
      $permissions = ['email']; // Optional permissions
      $url = $helper->getLoginUrl($sitio."login/?fb=2", $permissions);
      if(preg_match("/^https:\/\/www\.facebook\.com/",$url)) {
      ?>
      Direccionando a Facebook...
      <script>
         window.location = '<?php echo $url;?>';
      </script>
      <?php
      } else {
         printf('<p class="lead text-danger">no se pudo crear la identificación (url)</p>');
      }
   } else {
      //paso 2: si todo salió bien, nos debió devolver un código y un estado, 
      //usamos según la doc https://github.com/facebook/php-graph-sdk/blob/5.x/docs/examples/facebook_login.md
      require_once __DIR__ . '/vendor/autoload.php'; 
      if(!isset($configfb)) die("no tiene disponible configfb en config.php :(");
      $fb = new \Facebook\Facebook($configfb);
      printf(" - Obteniendo datos...");
      $helper = $fb->getRedirectLoginHelper();
      try {
         $accessToken = $helper->getAccessToken();
      } catch(Facebook\Exceptions\FacebookResponseException $e) {
         // When Graph returns an error
         echo 'Graph returned an error: ' . $e->getMessage();
         exit;
      } catch(Facebook\Exceptions\FacebookSDKException $e) {
         // When validation fails or other local issues
         echo 'Facebook SDK returned an error: ' . $e->getMessage();
         exit;
      }
      if (! isset($accessToken)) {
         if ($helper->getError()) {
            header('HTTP/1.0 401 Unauthorized');
            echo "Error: " . $helper->getError() . "\n";
            echo "Error Code: " . $helper->getErrorCode() . "\n";
            echo "Error Reason: " . $helper->getErrorReason() . "\n";
            echo "Error Description: " . $helper->getErrorDescription() . "\n";
         } else {
            header('HTTP/1.0 400 Bad Request');
            echo 'Bad request';
         }
         exit;
      }
      $token = $accessToken->getValue();
      //print_r($accessToken->getValue());
      //ahora si, obtenemos sus datos.
      try {
         // Returns a `Facebook\FacebookResponse` object
         $response = $fb->get('/me?fields=id,email,name,first_name,last_name,age_range,link,gender,verified', $token);
      } catch(Facebook\Exceptions\FacebookResponseException $e) {
         echo 'Graph returned an error: ' . $e->getMessage();
         exit;
      } catch(Facebook\Exceptions\FacebookSDKException $e) {
         echo 'Facebook SDK returned an error: ' . $e->getMessage();
         exit;
      }
      $user = $response->getGraphUser();
      $save= new stdclass;
      $save->id         = $user->getId();
      $save->correo     = $user->getEmail();
      $save->social     = "FB";
      $save->nombres    = $user->getName();
      $save->nombre     = $user->getFirstName();
      $save->apellido   = $user->getLastName();
      $save->genero     = $user->getGender();
      $save->web        = $user->getLink();
      $da = $utils->saveUser($save,$token);
      if(isset($da->id)) {
         //almacenado.
         $_SESSION['foro'] = $da->id;
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
   }
