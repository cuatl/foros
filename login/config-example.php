<?php
   /* 
   * configuración de aplicaciones para login 
   * renombrar a config.php, establecer los datos disponibles, si alguno no está, eliminarlo.
   */
   // facebook https://developers.facebook.com/apps/
   $configfb = [
   'app_id' =>      'APP_ID',
   'app_secret' =>  'APP_SECRET',
   'default_graph_version' => 'v2.11',
   ];
   //twitter https://apps.twitter.com/
   $configtw = ["CONSUMER_KEY","CONSUMER_SECRET"];
   //Google
   $configgo = ["CLIENT_ID","CLIENT_SECRET"];
   //este archivo se descarga de la aplicación en la consola de developers de Google, son las credenciales.
   $configgojson = __DIR__."/archivo.json";
   //
   //
   // ACCESOS OFFLINE (por ejemplo para postear en nombre del usuario identificado)
   $offline = false;
