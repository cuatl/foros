<?php
   require_once(__DIR__."/SimpleTW.php");
   $SimpleTW = new SimpleTW($configtw);
   if(isset($_GET['tw']) && !strcmp($_GET['tw'],1)) {
      //paso 1: generamos url para twitter. Usamos la clase https://github.com/cuatl/SimpleTW
      //require_once(__DIR__."/SimpleTW.php");
      //$SimpleTW = new SimpleTW($configtw);
      $url = "https://api.twitter.com/oauth/request_token";
      $SimpleTW->callback =  $sitio."login/?tw=2";
      $data = $SimpleTW->api("POST", $url, []);
      parse_str($data,$res);
      if(isset($res["oauth_token"]) && isset($res["oauth_callback_confirmed"])) {
         $urltoken = "https://api.twitter.com/oauth/authenticate?oauth_token=".$res["oauth_token"];
         printf("Estamos redireccionado a Twitter... ");
      ?>
      <script>
         window.location = '<?php echo $urltoken;?>';
      </script>
      <?php
      } else die("no se pudo generar el URL para twitter");
   } elseif(isset($_GET['oauth_verifier']) && !isset($_GET['verificar'])) {
      //paso 2: existe oauth_verifier, credenciales temporales
      printf(" - verificando credenciales... ");
      $args = ['oauth_verifier'=> __($_GET['oauth_verifier'])];
      $SimpleTW->oauthToken = __($_GET['oauth_token']);
      $url = "https://api.twitter.com/oauth/access_token";
      $data = $SimpleTW->api("POST", $url, $args);
      parse_str($data,$res);
      if(isset($res['oauth_token']) && isset($res['oauth_token_secret'])) {
         printf(" - verificando token... ");
         //ya estamos identificados
         $token =$res['oauth_token'].":".$res['oauth_token_secret'];
         //paso 3: obtenemos datos ahora.
         $SimpleTW->oauthToken       = $res['oauth_token'];
         $SimpleTW->oauthTokenSecret = $res['oauth_token_secret'];
         $url  = "https://api.twitter.com/1.1/account/verify_credentials.json";
         $args = ["include_email" => "true", "include_entities" => "false", "skip_status" => "true"];
         printf(" - obteniendo datos... ");
         $data = $SimpleTW->api("GET", $url, $args);
         @$data = json_decode($data);
         if(isset($data->id)) {
            //paso 4: almacenamos
            $save= new stdclass;
            $save->id       = $data->id;
            $save->correo   = $data->email;
            $save->social   = "TW";
            $save->nombres  = $data->name;
            $save->alias    = $data->screen_name;
            $save->perfil   = $data->profile_image_url_https;
            $da = $utils->saveUser($save,$token);
            if(isset($da->id)) {
               //almacenado.
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
         } else die("no se pudo identificar (4), verify_credentials");
         //
      } else {
         die("no se pudo identificar access_token.");
      } 
   }
