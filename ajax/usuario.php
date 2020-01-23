<?php

  //1. llamar a la conexion de la base de datos

  require_once("../config/conexion.php");


  //2. llamar a el modelo Usuarios 

  require_once("../modelos/Usuarios.php");

  // 3. objeto  usuario es igual a la instancia usuario() llama a todos los metodos de esta clase  Usuarios dentro de la carpeta Modelos
  $usuarios = new Usuarios();

  //4. declaramos las variables de los valores que se envian por el formulario y que recibimos por ajax y decimos que si existe el parametro que estamos recibiendo


  //isset devuelve false si la variable esta definida como null
  //$_POST["nombre"] viene de la etiqueta name del elmento
   $id_usuario = isset($_POST["id_usuario"]);
   $nombre=isset($_POST["nombre"]);
   $apellido=isset($_POST["apellido"]);
   $cedula=isset($_POST["cedula"]);
   $telefono=isset($_POST["telefono"]);
   $email=isset($_POST["email"]);
   $direccion=isset($_POST["direccion"]); 
   $cargo=isset($_POST["cargo"]);
   $usuario=isset($_POST["usuario"]);
   $password1=isset($_POST["password1"]);
   $password2=isset($_POST["password2"]);
   //este es el que se envia del formulario
   $estado=isset($_POST["estado"]);


    //5. como vamos a hacer peticiones ajax usamos una bandera "op" y va a evaluar el parametro dependiendo del valor que recibe por medio de ajax
    switch($_GET["op"]){

      //operaciones
      //guardadar y editar
      //mostrar
      //activar y desactivar usuario
      //listar usuario

         case "guardaryeditar":

                 /*6. verificamos si existe la cedula y correo en la base de datos, si ya existe un registro con la cedula o correo entonces no se registra el usuario*/


                 //7. creamos la variable datos = al objeto usuarios y llama a los metodos de la clase usuarios y le pasa los parametros cedula y email, Usuarios dentro de la carpeta Modelos
                $datos = $usuarios->get_cedula_correo_del_usuario($_POST["cedula"],$_POST["email"]);
                 
                 //8. validacion de password
                 if($password1 == $password2){

                 	   /*9. si el id no existe entonces lo registra
	                     importante: se debe poner el $_POST sino no funciona*/

	                     if(empty($_POST["id_usuario"])){

                            /*10. si coincide password1 y password2 entonces verificamos si existe la cedula y correo en la base de datos, si ya existe un registro con la cedula o correo entonces no se registra el usuario*/
                            //si datos es un array  y que si decimos que el array es igual a 0 entonces, quiere decir que si no existe en la BD el correo y el CI

	                     	   if(is_array($datos)==true and count($datos)==0){
                                
                                 //11. no existe el usuario por lo tanto hacemos el registros, llama al obejto usuario y al metodo registrar_usuario, ejecuta y se inserta 


                                $usuarios->registrar_usuario($nombre,$apellido,$cedula,$telefono,$email,$direccion,$cargo,$usuario,$password1,$password2,$estado);

                                 $messages[]="El usuario se registró correctamente";

                                 /*12.  si ya exista el correo y la cedula entonces aparece el mensaje*/

	                     	   } else {

                                    $messages[]="La cédula o el correo ya existe";

	                     	   }
                     
	                     } //cierre de la validacion empty

	                     else {

                             /*13. si ya existe entonces editamos el usuario, llamamos al metodo editar del objeto usuario, Usuarios dentro de la carpeta Modelos*/

                            $usuarios->editar_usuario($id_usuario,$nombre,$apellido,$cedula,$telefono,$email,$direccion,$cargo,$usuario,$password1,$password2,$estado);

                            //14. mostramos un mensaje de edicion correctas

                             $messages[]="El usuario se editó correctamente";
	                     }

                     
                 } else {

                 	  /*15. si el password no conincide, entonces se muestra el mensaje de error*/

                        $errors[]="El password no coincide";
                 }


                 //16. mensaje success
     if(isset($messages)){
				 //17. cierre del PHP
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Bien hecho!</strong>
            <?php
            //18.hacemos un recorrido del array mensaje e imprimimos
							foreach($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
	 //fin success

      //19. mensaje error
         if(isset($errors)){
			
			?>
				<div class="alert alert-danger" role="alert">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>Error!</strong> 
            <?php
            //20.hacemos un recorrido del array mensaje de error e imprimimos
							foreach($errors as $error) {
									echo $error;
								}
							?>
				</div>
			<?php

			}

	 //fin mensaje error

        //21. Una vez seleccionado el registro se muestra la info por id
         break;


         case "mostrar":

            //22. selecciona el id del usuario

        //23. el parametro id_usuario se envia por AJAX cuando se edita el usuario, llama al metodo get_usuario_por_id, Usuarios dentro de la carpeta Modelos

          $datos = $usuarios->get_usuario_por_id($_POST["id_usuario"]);

          //24. validacion del id del usuario, si es array y si ahy un registro entonces entra  

             if(is_array($datos)==true and count($datos)>0){

              //25. recorremos el array datos
             	 foreach($datos as $row){
                      
                    $output["cedula"] = $row["cedula"];
                    $output["nombre"] = $row["nombres"];
            				$output["apellido"] = $row["apellidos"];
            				$output["cargo"] = $row["cargo"];
            				$output["usuario"] = $row["usuario"];
            				$output["password1"] = $row["password"];
            				$output["password2"] = $row["password2"];
            				$output["telefono"] = $row["telefono"];
            				$output["correo"] = $row["correo"];
            				$output["direccion"] = $row["direccion"];
            				$output["estado"] = $row["estado"];
             	 }

                //26. Se devuelve en formato JSON
             	 echo json_encode($output);

                //27. En caso contrario de que no exista en la BD
             } else {

                //si no existe el registro entonces no recorre el array
                $errors[]="El usuario no existe";

             }


	      //inicio de mensaje de error

				if(isset($errors)){
			
					?>
					<div class="alert alert-danger" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
							<strong>Error!</strong> 
							<?php
								foreach($errors as $error) {
										echo $error;
									}
								?>
					</div>
					<?php
			      }

	        //fin de mensaje de error

         break;

         //28. Cambiar estado del usuario
         case "activarydesactivar":
              
              //los parametros id_usuario y est vienen por via ajax, Usuarios dentro de la carpeta Modelos
              $datos = $usuarios->get_usuario_por_id($_POST["id_usuario"]);
                
                //valida el id del usuario
                 if(is_array($datos)==true and count($datos)>0){
                    
                    //edita el estado del usuario  
                    $usuarios->editar_estado($_POST["id_usuario"],$_POST["est"]);
                 }
         break;

         //29. accedemos al objeto usuario y al metodo get_usuario
         case "listar":
          
         $datos = $usuarios->get_usuarios();

         //30. declaramos el array

         $data = Array();


         //31. recorremos el array datos (que esta cargado de todos los registros)
         foreach($datos as $row){

            //32. Declaramos un subarray
            $sub_array= array();

          //33. creamos una variable de ESTADO
	        $est = '';
         
          //34. creamos un atributo que va a tener el color del estado activo
	         $atrib = "btn btn-success btn-md estado";
	        if($row["estado"] == 0){
            $est = 'INACTIVO';
            //35. creamos un atributo que va a tener el color del estado in activo  
	          $atrib = "btn btn-warning btn-md estado";
	        }
	        else{
	          if($row["estado"] == 1){
	            $est = 'ACTIVO';
	            
	          } 
	        }


            //cargo

            if($row["cargo"]==1){

              $cargo="ADMINISTRADOR";

            } else{

            	if($row["cargo"]==0){
                   
                   $cargo="EMPLEADO";
            	}
            }

       //36. cargamos los registros en el array declarado        
	     $sub_array[]= $row["cedula"];
	     $sub_array[] = $row["nombres"];
         $sub_array[] = $row["apellidos"];
         $sub_array[] = $row["usuario"];
         $sub_array[] = $cargo;
         $sub_array[] = $row["telefono"];
         $sub_array[] = $row["correo"];
         $sub_array[] = $row["direccion"];
         $sub_array[] = date("d-m-Y",strtotime($row["fecha_ingreso"]));

              //37. Creamos los botones para el cambio de estados, llamando a los metodos con sus parametros
          //el valor del class le hacemos en forma dinamica, indicando si es inactivo o activo
              $sub_array[] = '<button type="button" onClick="cambiarEstado('.$row["id_usuario"].','.$row["estado"].');" name="estado" id="'.$row["id_usuario"].'" class="'.$atrib.'">'.$est.'</button>';


                $sub_array[] = '<button type="button" onClick="mostrar('.$row["id_usuario"].');"  id="'.$row["id_usuario"].'" class="btn btn-warning btn-md update"><i class="glyphicon glyphicon-edit"></i> Editar</button>';


                $sub_array[] = '<button type="button" onClick="eliminar('.$row["id_usuario"].');"  id="'.$row["id_usuario"].'" class="btn btn-danger btn-md"><i class="glyphicon glyphicon-edit"></i> Eliminar</button>';
                

        //38. Se almacena dentro del array 
	     $data[]=$sub_array;
	    
	        
         }

         $results= array(	

         "sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
       "aaData"=>$data);
       //40. enviamos en formato JSON
         echo json_encode($results);


         break;
     }


?>