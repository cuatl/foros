<?php
   /* utilidades */
   class Utils {
      var $sql;
      var $foto;
      var $tipo;
      public $error;
      public $q; //last sql query string
      public $user; //objeto usuario actual
      //
      function __construct() {
         $this->sql = $GLOBALS['sql'];
      }
      public function pic() {
         $data = func_get_arg(0);
         @$size = func_get_arg(1);
         if(empty($size) || $size < 200 || $size >720) $size=200;
         if(!empty($data[2])) return sprintf('up/perfil/%s%s.jpg',date('/Y/m/',strtotime($data[3])),$data[2]);
         elseif($data[0] == 'GO') return str_replace("s96-c","s".$size."-c",$data[1]);
         elseif($data[0] == 'FB') return sprintf("https://graph.facebook.com/v2.11/%s/picture?width=%d",$data[4],$size);
         elseif($data[0] == 'TW') {
            return $data[1];
         }
         else return "s.gif";
      }
      /* regresa un arreglo de fechas */
      public function fecha($f) {
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
      //almacena usuario registrado.
      public function saveUser() {
         $this->error = null;
         $data =func_get_arg(0); //objeto
         @$token = func_get_arg(1);
         //DB.tabla
         $tabla = USERS;
         if(empty($tabla)) $tabla = "users";
         if(!empty($tabla));
         if(!isset($data->id) || !preg_match("/^[0-9]{1,}/",$data->id)) $this->error = "no se puede registrar.";
         $this->user = $data; unset($data);
         $u =& $this->user;
         //partimos nombre si es twitter
         if($u->social == 'TW') {
            $tmp = explode(" ",$u->nombres);
            $u->nombre = $tmp[0];
            $u->apellido  = implode(" ",array_slice($tmp,1));
         }
         //existe?
         $this->q = sprintf("SELECT * FROM %s WHERE socialid = '%s' AND tipo='%s' AND correo='%s'",$tabla,__($u->id), __($u->social), __($u->correo));
         $e = $this->sql->Query($this->q);
         $this->sqlerror();
         if($e->num_rows>0) {
            $existe = $e->fetch_object();
            $ID = $existe->id;
            $u->alta = $existe->alta;
            $u->alias = $existe->alias;
            $u->socialid = $u->id;
            $u->avatar = $existe->avatar;
            $u->id = $ID;
            @$this->q = sprintf("UPDATE %s SET perfil='%s', genero='%s', nombre='%s', apellido='%s' WHERE id = %d",$tabla,__($u->perfil), __($u->genero),  __($u->nombre),  __($u->apellido),  $ID);
            $this->sql->Query($this->q);
            $this->sqlerror();
            $u->nuevo = false;
         } else {
            //nuevo
            $u->alta = date('Y-m-d');
            if(!isset($u->alias)) $u->alias = $u->nombre;
            @$this->q = sprintf("INSERT INTO %s (id,socialid,alta, correo, genero, nombre,apellido,alias,tipo,perfil) values(null,'%s', now(), '%s', '%s', '%s', '%s','%s','%s','%s')",$tabla,__($u->id), __($u->correo), __($u->genero),  __($u->nombre),  __($u->apellido), __($u->alias), __($u->social), __($u->perfil));
            if($this->sql->Query($this->q)) {
               $ID = $this->sql->insert_id;
               $u->socialid = $u->id;
               $u->id = $ID;
            }
            $this->sqlerror();
            $u->nuevo = true;
         }
         // actualizamos token.
         if(!empty($token)) {
            $this->q = sprintf("UPDATE %s SET token = '%s' WHERE id '%d'",$tabla,__($token), $ID);
            $this->sql->Query($this->q);
         }
         return $ID ? $u : false;
      }
      /* sql error {{{ */
      private function sqlerror() {
         if(!empty($this->sql->error)) {
            die(sprintf('<p class="lead text-danger"><strong>SQL ERROR:</strong> %s</p>',$sql->error));
         } else return true;
      } /* }}} */
   }
