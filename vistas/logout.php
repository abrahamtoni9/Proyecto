<?php

 require_once("../config/conexion.php");

 //cerramos la sesion
 session_destroy();

 //redirecciona para loguearnos de nuevo 
  header("Location:".Conectar::ruta()."vistas/index.php");
  exit();

?>