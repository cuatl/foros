<?php
   /* utilidades */
   class Utils {
      var $sql;
      var $foto;
      var $tipo;
      function __construct() {
         $this->sql = $GLOBALS['sql'];
      }
      function pic() {
         $data = func_get_arg(0);
         @$size = func_get_arg(1);
         if(empty($size)) $size=50;
         if($data[0] == 'GO') return $data[1];
         elseif($data[0] == 'FB') return sprintf("https://graph.facebook.com/v2.11/%s/picture?width=%d",$data[4],$size);
      }
      /* regresa un arreglo de fechas */
      function fecha($f) {
         if(preg_match("/[0-9]{4}\-[0-9]{2}\-[0-9]{2}/",$f)) $fecha = strtotime($f);
         else $fecha = time();
         if($fecha <1) $fecha= time();
         $f1 = new DateTime(date('Y-m-d H:i:s',$fecha));
         $f2 = new DateTime(date('Y-m-d H:i:s'));
         $diff = $f2->diff($f1);
         $tmp = ($diff->y+$diff->m+$diff->d+$diff->h);
         if($tmp==0 && $diff->i<3) $dia = "ahora";
         elseif($tmp==0) $dia = "hace minutos";
         elseif(date('Ymd') == date('Ymd',$fecha)) $dia = "hoy ".date("H:i",$fecha);
         elseif(date('Ymd',strtotime('-1 day')) == date('Ymd',$fecha)) $dia = "ayer ".date("H:i",$fecha);
         elseif(date('Ym',$fecha) && date('Ym')) $dia = strftime("%a %d",$fecha);
         elseif(date('Y',$fecha) && date('Y')) $dia = strftime("%d de %b",$fecha);
         else $dia = strftime("%d/%b/%Y",$fecha);
         $msg = new stdclass;
         $msg->r = $f;
         $msg->fecha = $fecha;
         $msg->fechas= $dia;
         $msg->full = strftime("%A %d de %B de %Y, %H:%M",$fecha);
         return $msg;
      }
   }
