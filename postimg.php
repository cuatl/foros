<?php
   /*******************************************************
   * Only these origins will be allowed to upload images *
   ******************************************************/
   /*********************************************
   * Change this line to set the upload folder *
   *********************************************/
   require_once(__DIR__."/config.php");
   $imageFolder = __DIR__."/up/";
   reset ($_FILES);
   $temp = current($_FILES);
   if (is_uploaded_file($temp['tmp_name'])){
      if (isset($_SERVER['HTTP_ORIGIN'])) {
         // same-origin requests won't set an origin. If the origin is set, it must be valid.
         if (in_array($_SERVER['HTTP_ORIGIN'], $sitios)) {
            header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
         } else {
            header("HTTP/1.0 403 Origin Denied");
            return;
         }
      }
      // Verify extension
      if(!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png","jpeg","gif"))) {
         header("HTTP/1.0 500 Invalid extension.");
         return;
      }
      // Accept upload if there was no origin, or if it is an accepted origin
      $pato = $imageFolder.date('Y/m/');
      $ext = strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION));
      $file =    uniqid()."_o.".$ext;
      $filet= str_replace("_o","",$file);
      $filete=str_replace("_o","_t",$file);
      //
      if(!preg_match("/\.jpg$/",$filet)) { $filet = str_replace(".".$ext,".jpg",$filet); }
      if(!preg_match("/\.jpg$/",$filete)) { $filete = str_replace(".".$ext,".jpg",$filete); }
      //
      if(!is_dir($pato)) mkdir($pato,0755,true);
      //$filetowrite = $imageFolder . $temp['name'];
      $filetowrite = $pato.$file;
      move_uploaded_file($temp['tmp_name'], $filetowrite);
      $t = `identify $filetowrite`;
      $s = explode(" ",$t);
      if(in_array($s[1],['JPG','JPEG','PNG','GIF'])) {
         $ext = trim(strtolower($s[1]));
         $ss = explode("x",$s[2]);
         if($ss[0]>=720 && $ext!='gif') {
            //
            $cmd = "convert -thumbnail 720 -flatten -quality 70 -background white -auto-orient ".$filetowrite."[0] ".$pato.$filet; $cmd = `$cmd`;
            $cmd = "convert -thumbnail 200 -flatten -quality 60 ".$pato.$filet." ".$pato.$filete; $cmd = `$cmd`;
            $size=720;
         } elseif($ss[0]>=200 && $ext!='gif') {
            $cmd = "convert -thumbnail ".$ss[0]." -flatten -quality 70 -background white -auto-orient ".$filetowrite."[0] ".$pato.$filet; $cmd = `$cmd`;
            $cmd = "convert -thumbnail 200 -flatten -quality 60 ".$pato.$filet." ".$pato.$filete; $cmd = `$cmd`;
            $size=$ss[0];
         } else {
            if(!empty($ss[0])) $size=$ss[0];
            copy($filetowrite,$filet);
            $cmd = "convert -thumbnail 200 -flatten -quality 60 -background white ".$filetowrite."[0] ". $pato.$filete; $cmd = `$cmd`;
         }
         echo json_encode(array('location' => "https://tar.mx/foros/up/".date('Y/m/').$filet));
      }
   } else {
      header("HTTP/1.0 500 Server Error");
   }
