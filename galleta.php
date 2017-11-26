<?php
   if(isset($_COOKIE[GALLETA]) && !isset($_SESSION[ME])) {
      $galleta  = explode(":::",base64_decode($_COOKIE[GALLETA]));
      $soy      = sprintf("SELECT * FROM %s WHERE id = '%d'",USERS,$galleta[0]);
      $soy      = $sql->query($soy);
      if($soy->num_rows>0) {
         $soy   = $soy->fetch_object();
         $sisoy = md5($soy->id.":::".$soy->token);
         if(!strcmp($galleta[1],$sisoy)) {
            //si soy
            $_SESSION['data'] = new stdclass;
            $me =& $_SESSION['data'];
            $me->id     = $soy->id;
            $me->correo = $soy->correo;
            $me->social = $soy->tipo;
            $me->nombres= $soy->nombre." ".$soy->apellido;
            $me->alias  = $soy->alias;
            $me->perfil = $soy->perfil;
            $me->avatar = $soy->avatar;
            $me->nombre = $soy->nombre;
            $me->apellido= $soy->apellido;
            $me->alta   = $soy->alta;
            $me->socialid= $soy->socialid;
            $me->nuevo  = false;
            $me->mordida=true;
            $me->frecuencia = $soy->frecuencia;
            $_SESSION[ME] = $soy->id;
            header("Location: ".$sitio);
            exit();
         } else {
            setcookie(GALLETA,null, time(), $directorio, $host,true, true); //no soy
         }
      } else {
         setcookie(GALLETA,null, time(), $directorio, $host,true, true); //no soy
      }
   }
   //está loggeado y no tiene galleta?
   if(isset($_SESSION[ME]) && !isset($me->galleta)) {
      $q = sprintf("SELECT token FROM %s WHERE id = '%d'",USERS,$me->id);
      $tmp = $sql->Query($q);
      $tmp = $tmp->fetch_object();
      $tmp = md5(sprintf("%d:::%s",$me->id,$tmp->token));
      setcookie(GALLETA,base64_encode($me->id.":::".$tmp), (time() + (60 * 60 * 24 * 45)), $directorio, $host,true, true); //7d
      unset($me->galleta);
   }
