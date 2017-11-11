<?php
   //twitter auth :-) -- está repetitivo pero un día lo acomodaremos.
   // otro día
   // hoy no.
   if(!isset($inc)) die(":o");
   //configuración, se deben obtener estas credenciales de https://apps.twitter.com/
   require_once(__DIR__."/tw.config.php"); //renombrar tw.config-example.php y poner sus propias llaves.
   if(isset($_GET['oauth_token']) && isset($_GET['oauth_verifier'])) {
      //ya se identificó
      echo "Estamos buscando datos...";
      $ch = curl_init();
      $url = "https://api.twitter.com/oauth/access_token";
      $hash = [
      "oauth_consumer_key"     => $tw["consumer_key"],
      "oauth_nonce"            => substr(base64_encode(md5(time())),0,32),
      "oauth_signature_method" => "HMAC-SHA1",
      "oauth_timestamp"        => time(),
      "oauth_token"            => __($_GET['oauth_token']),
      "oauth_version"          => "1.0",
      ];
      $post = ['oauth_verifier'=>__($_GET['oauth_verifier'])];
      ksort($hash); //reordenamos
      $hashstring = null;
      foreach($hash AS $k=>$v) $hashstring .= sprintf('%s=%s&',$k,$v);
      $hashstring = substr($hashstring,0,-1);
      //base
      $base = sprintf("POST&%s&%s",urlencode($url), rawurlencode($hashstring));
      //key
      $key = sprintf("%s&%s",rawurlencode($tw["consumer_secret"]),rawurlencode($_GET['oauth_token']));
      $signature = base64_encode(hash_hmac('sha1', $base, $key, TRUE));
      $hash['oauth_signature'] = urlencode($signature);
      ksort($hash); //reordenamos
      //headers
      $headers=null;
      foreach($hash AS $k=>$v) $headers .= sprintf('%s="%s", ',$k,$v);
      $headers = substr($headers,0,-2);
      $header = ["Authorization: OAuth ".$headers];
      $ch = curl_init();
      curl_setopt($ch,CURLOPT_HEADER,0); curl_setopt($ch,CURLOPT_POST,1); curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
      curl_setopt($ch,CURLOPT_HTTPHEADER, $header); curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch,CURLOPT_POSTFIELDS, $post);
      $twreturn= curl_exec($ch);
      //print_r($twreturn);
      curl_close($ch);
      parse_str($twreturn, $res);
      /*
      ( 
         [oauth_token] => 782210-
         [oauth_token_secret] => 57OX4D
         [user_id] => 782210 
         [screen_name] => ToRo 
         [x_auth_expires] => 0 
      )
      */
      if(isset($res['user_id'])) {
         $hash = [
         //"include_email"          => "true",
         "oauth_consumer_key"     => $tw["consumer_key"],
         "oauth_nonce"            => substr(base64_encode(md5(time()+1)),0,32),
         "oauth_signature_method" => "HMAC-SHA1",
         "oauth_timestamp"        => (time()+1),
         "oauth_token"            => $res['oauth_token'],
         "oauth_version"          => "1.0",
         "include_email"          => "true",
         ];
         echo " - otros datos... ";
         $url = "https://api.twitter.com/1.1/account/verify_credentials.json";
         ksort($hash);
         $hashstring = null;
         foreach($hash AS $k=>$v) $hashstring .= sprintf('%s=%s&',$k,$v);
         $hashstring = substr($hashstring,0,-1);
         $base = sprintf("GET&%s&%s",urlencode($url), rawurlencode($hashstring));
         echo "<p>BASE: ".$base."</p>\n";
         $key = sprintf("%s&%s",rawurlencode($tw["consumer_secret"]),$res['oauth_token_secret']);
         $signature = base64_encode(hash_hmac('sha1', $base, $key, TRUE));
         $hash['oauth_signature'] = urlencode($signature);
         ksort($hash); //reordenamos
         //headers
         $headers=null;
         //unset($hash['include_email']);
         foreach($hash AS $k=>$v) $headers .= sprintf('%s="%s", ',$k,$v);
         $headers = substr($headers,0,-2);
         $header = ["Authorization: OAuth ".$headers];
         $ch = curl_init();
         curl_setopt($ch,CURLOPT_VERBOSE,0);
         curl_setopt($ch,CURLOPT_HTTPGET,1);
         curl_setopt($ch,CURLOPT_HEADER,0); 
         curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
         curl_setopt($ch,CURLOPT_HTTPHEADER, $header); 
         curl_setopt($ch,CURLOPT_URL,$url."?include_email=true");
         $twreturn= curl_exec($ch);
         $data = json_decode($twreturn);
         //por último, lo mandamos por js.
         if(isset($data->id)) {
         ?>
         <script>
            datame = {
                  "social"  : "TW",
                  "id"      : <?php echo $data->id; ?>,
                  "correo"  : '<?php echo $data->email;?>',
                  "nombres" : '<?php echo $data->name;?>',
                  "alias"   : '<?php echo $data->screen_name;?>',
                  "perfil"  : '<?php echo $data->profile_image_url_https;?>',
            };
            $.post("api.php",{login:datame,meme:'<?php echo $meme;?>',token:1977},function(m) {
                  if(m.id !== undefined) { window.location = '<?php echo $_SESSION['redir'];?>'; } 
                  else { console.log('error '+m.error);$("#status").html('🤔 '+m.error); }
            },'json');
         </script>
         <?php
         } else echo "<p class=\"lead text-danger\">No se pudo identificar con Twitter :(</p>";
      } else {
         echo "<p class=\"lead text-danger\">No se pudo identificar con Twitter :(</p>";
      }
   } elseif(isset($_GET['entrar']) && $_GET['entrar'] == 'tw') {
      //request token
      echo "Estamos cargando Twitter...";
      $url = "https://api.twitter.com/oauth/request_token";
      $hash = [
      "oauth_callback"          => urlencode($sitio."?entrar=tw"),
      "oauth_consumer_key"     => $tw["consumer_key"],
      "oauth_signature_method" => "HMAC-SHA1",
      "oauth_timestamp"        => time(),
      "oauth_nonce"            => substr(base64_encode(md5(time())),0,32),
      "oauth_version"          => "1.0",
      ];
      ksort($hash); //reordenamos
      //hash string
      $hashstring = null;
      foreach($hash AS $k=>$v) $hashstring .= sprintf('%s=%s&',$k,$v);
      $hashstring = substr($hashstring,0,-1);
      //base
      $base = sprintf("POST&%s&%s",urlencode($url), rawurlencode($hashstring));
      //key
      $key = sprintf("%s&",rawurlencode($tw["consumer_secret"]));
      //http://quonos.nl/oauthTester/
      //https://developer.twitter.com/en/docs/basics/authentication/guides/creating-a-signature.html
      $signature = base64_encode(hash_hmac('sha1', $base, $key, TRUE));
      $hash['oauth_signature'] = urlencode($signature);
      ksort($hash); //reordenamos
      //headers
      $headers=null;
      foreach($hash AS $k=>$v) $headers .= sprintf('%s="%s", ',$k,$v);
      $headers = substr($headers,0,-2);
      //
      //$header = ["Authorization: OAuth ".$headers, "Expect:"];
      $header = ["Authorization: OAuth ".$headers];
      /* echo "HASH\n"; print_r($hash); echo "BASE = "; echo $base."\n"; echo "KEY = $key\n"; echo "SIGNATURE: $signature \n"; echo "HEADERS\n"; print_r($header); */
      //
      $ch = curl_init();
      curl_setopt($ch,CURLOPT_HEADER,0); curl_setopt($ch,CURLOPT_POST,1); curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header); curl_setopt($ch, CURLOPT_URL, $url);
      $twreturn= curl_exec($ch);
      curl_close($ch);
      parse_str($twreturn, $res);
      //print_r($res);
      if(isset($res['oauth_callback_confirmed']) && $res['oauth_callback_confirmed'] == 'true') {
         $goto = "https://api.twitter.com/oauth/authenticate?oauth_token=".$res["oauth_token"];
      }
   ?>
   <script>
      window.location="<?php echo $goto;?>";
   </script>
   <?php
   }
?>
