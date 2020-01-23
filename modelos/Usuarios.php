<?php

  //conexion a la base de datos

  require_once("../config/conexion.php");


  class Usuarios extends Conectar {

    //1. Creamos la funcion login   
    public function login(){

      $conectar=parent::conexion();
      parent::set_names();//para evitar problemas con las tildes

      //campo oculto de enviar, si existe el valor del campo oculto enviar 
      if(isset($_POST["enviar"])){

        //INICIO DE VALIDACIONES
        $password = $_POST["password"];

        $correo = $_POST["correo"];

        $estado = "1";

        //si los dos campos estan vacios
        if(empty($correo) and empty($password)){
          //redireccionamos con header, .Conectar::ruta() viene de la clase conexion de la carpeta config y accede al metodo ruta() este retorna una direccion
          //tbn envia un parametro 2 que va a recibir el swicht para mostrar un mensaje personalizado en bootstrap de los campos vacios 
          header("Location:".Conectar::ruta()."vistas/index.php?m=2");
          //echo("<script>Location.href='".Conectar::ruta()."vistas/index.php?m=2';</script>");
          exit();


        }
        //si no entonces, sino tiene caracteres, caracteres extrano y numericos entre 12 a 15 
        else if(!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])([A-Za-z\d$@$!%*?&]|[^ ]){12,15}$/", $password)) {
          //envia un parametro 1 que va a recibir el swicht para mostrar un mensaje personalizado en bootstrap del pass no esta correcto  
          header("Location:".Conectar::ruta()."vistas/index.php?m=1");
          //echo("<script>Location.href='".Conectar::ruta()."vistas/index.php?m=1';</script>");
          exit();

        }
        //si no , entonces comparamos el user pass y estado
        else {

          $sql= "select * from usuarios where correo=? and password=? and estado=?";
          //se prepara la sentencia sql
          $sql=$conectar->prepare($sql);
          //se setea los valores
          $sql->bindValue(1, $correo);
          $sql->bindValue(2, $password);
          $sql->bindValue(3, $estado);
          $sql->execute();//se ejecuta la sentencia 
          $resultado = $sql->fetch();//devuelve el resultado en una variable

          //si existe el registro y contiene valores de array entonces se conecta en session
          if(is_array($resultado) and count($resultado)>0){

              /*IMPORTANTE: la session guarda los valores de los campos de la tabla de la bd*/
            //valores creador de la sesion = valor de la bd
            $_SESSION["id_usuario"] = $resultado["id_usuario"];
            $_SESSION["correo"] = $resultado["correo"];
            $_SESSION["cedula"] = $resultado["cedula"];
            $_SESSION["nombre"] = $resultado["nombres"];

            //redireccionamos a la pagina de inicio
            header("Location:".Conectar::ruta()."vistas/home.php");
            //salimos de la pagina actual
            exit();


          } else {
                
                //si no existe el registro entonces le aparece un mensaje
                header("Location:".Conectar::ruta()."vistas/index.php?m=1");
                exit();
          } 
          
        }//cierre del else


      }//condicion enviar
      }

        //listar los usuarios
          public function get_usuarios(){

            $conectar=parent::conexion();
            parent::set_names();

            $sql="select * from usuarios";

            $sql=$conectar->prepare($sql);
            $sql->execute();

            return $resultado=$sql->fetchAll();
          }

          //metodo para registrar usuario
          public function registrar_usuario($nombre,$apellido,$cedula,$telefono,$email,$direccion,$cargo,$usuario,$password1,$password2,$estado){

              $conectar=parent::conexion();
              parent::set_names();

              $sql="insert into usuarios 
              values(null,?,?,?,?,?,?,?,?,?,?,now(),?);";

              $sql=$conectar->prepare($sql);

              $sql->bindValue(1, $_POST["nombre"]);
              $sql->bindValue(2, $_POST["apellido"]);
              $sql->bindValue(3, $_POST["cedula"]);
              $sql->bindValue(4, $_POST["telefono"]);
              $sql->bindValue(5, $_POST["email"]);
              $sql->bindValue(6, $_POST["direccion"]);
              $sql->bindValue(7, $_POST["cargo"]);
              $sql->bindValue(8, $_POST["usuario"]);
              $sql->bindValue(9, $_POST["password1"]);
              $sql->bindValue(10, $_POST["password2"]);
              $sql->bindValue(11, $_POST["estado"]);
              $sql->execute();
          }

          //metodo para editar usuario
          public function editar_usuario($id_usuario,$nombre,$apellido,$cedula,$telefono,$email,$direccion,$cargo,$usuario,$password1,$password2,$estado){

              $conectar=parent::conexion();
              parent::set_names();

              $sql="update usuarios set 

                nombres=?,
                apellidos=?,
                cedula=?,
                telefono=?,
                correo=?,
                direccion=?,
                cargo=?,
                usuario=?,
                password=?,
                password2=?,
                estado = ?

                where 
                id_usuario=?



              ";

              //imprimimos la consulta en el navegador
                //echo $sql;

              $sql=$conectar->prepare($sql);

              $sql->bindValue(1,$_POST["nombre"]);
              $sql->bindValue(2,$_POST["apellido"]);
              $sql->bindValue(3,$_POST["cedula"]);
              $sql->bindValue(4,$_POST["telefono"]);
              $sql->bindValue(5,$_POST["email"]);
              $sql->bindValue(6,$_POST["direccion"]);
              $sql->bindValue(7,$_POST["cargo"]);
              $sql->bindValue(8,$_POST["usuario"]);
              $sql->bindValue(9,$_POST["password1"]);
              $sql->bindValue(10,$_POST["password2"]);
              $sql->bindValue(11,$_POST["estado"]);
              $sql->bindValue(12,$_POST["id_usuario"]);
              $sql->execute();

              //2da forma de ver el error en consola, imprimimos el query update lo que se enviar por post 
              
              //print_r($_POST);
          }

          
          //mostrar los datos del usuario por el id
          public function get_usuario_por_id($id_usuario){
            
            $conectar=parent::conexion();
            parent::set_names();

            $sql="select * from usuarios where id_usuario=?";

            $sql=$conectar->prepare($sql);

            $sql->bindValue(1, $id_usuario);
            $sql->execute();

            return $resultado=$sql->fetchAll();

          }

          //editar el estado del usuario, activar y desactiva el estado

          public function editar_estado($id_usuario,$estado){


            $conectar=parent::conexion();
            parent::set_names();

              //el parametro est se envia por via ajax
            if($_POST["est"]=="0"){

              $estado=1;

            } else {

              $estado=0;
            }

            $sql="update usuarios set 
              
              estado=?

              where 
              id_usuario=?


            ";

            $sql=$conectar->prepare($sql);


            $sql->bindValue(1,$estado);
            $sql->bindValue(2,$id_usuario);
            $sql->execute();


          }


          //valida correo y cedula del usuario

          public function get_cedula_correo_del_usuario($cedula,$email){
            
            $conectar=parent::conexion();
            parent::set_names();

            $sql="select * from usuarios where cedula=? or correo=?";

            $sql=$conectar->prepare($sql);

            $sql->bindValue(1, $cedula);
            $sql->bindValue(2, $email);
            $sql->execute();

            return $resultado=$sql->fetchAll();

          }
   }



?>