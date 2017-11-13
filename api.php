<?php
   //api foros
   session_start();
   include_once(__DIR__."/config.php");
   if(!isset($_SESSION['redir'])) $_SESSION['redir'] = $sitio;
   $msg = new stdclass; $msg->error = "error :(";
   //
   if(isset($_SERVER['HTTP_ORIGIN'])) {
      if(in_array($_SERVER['HTTP_ORIGIN'], $sitios)) { header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']); } 
      else { header("HTTP/1.0 403 Origin Denied"); return; }
   }
   if(strcmp($_POST['meme'],$meme)) {
   } elseif(isset($_POST['post'])) {
      //post leído
      unset($msg->error);
      $e=$sql->Query("SELECT * FROM postslog WHERE postid='".__($_POST['post'])."' AND me='".$_SESSION['foro']."'");
      $msg->new=0;
      if($e->num_rows<1) {
         $msg->new=1;
         $q=sprintf("INSERT INTO postslog (postid,me,fecha) VALUES('%d','%d',now())",__($_POST['post']),$_SESSION['foro']);
      } else {
         $q=sprintf("UPDATE postslog SET fecha = now() WHERE postid='%d' and me='%d'",__($_POST['post']),$_SESSION['foro']);
      }
      unset($msg->error);
      $sql->Query($q);
   }
   echo json_encode($msg);
