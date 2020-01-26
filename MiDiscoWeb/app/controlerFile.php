<?php
include_once 'config.php';
include_once 'modeloUser.php';

function ctlFileVerFicheros($msg)
{
    $ficheros = modeloUserGetFiles(); // almaceno dentro de $usuarios el contenido de la sesion usuarios
                                      // Invoco la vista
    include_once 'plantilla/verarchivos.php';
}

function ctlFileNuevo($msg)
{
    // se incluyen los códigos de error que produce la subida de archivos en PHPP
    // Posibles errores de subida
    $codigosErrorSubida = [
        0 => 'Subida correcta',
        1 => 'El tamaño del archivo excede el admitido por el servidor', // directiva upload_max_filesize en php.ini
        2 => 'El tamaño del archivo excede el admitido por el cliente', // directiva MAX_FILE_SIZE en el formulario HTML
        3 => 'El archivo no se pudo subir completamente',
        4 => 'No se seleccionó ningún archivo para ser subido',
        6 => 'No existe un directorio temporal donde subir el archivo',
        7 => 'No se pudo guardar el archivo en disco', // permisos
        8 => 'Una extensión PHP evito la subida del archivo' // extensión PHP
    ];
    $msg = '';

    // si no se reciben el archivo, se se carga la pagina para subir el archivo
    if ((! isset($_FILES['archivo1']['name']))) {
        include_once 'plantilla/subirfichero.php';
    } else { // se reciben el directorio de alojamiento y el archivo
        $directorioSubida = "C:\Users\Bryan\Desktop\Prueba\\".$_SESSION["user"]."\\"; // $_session user para crear una carpetadel usuario
       
        if (file_exists($directorioSubida)) {
            
        } else {
            mkdir($directorioSubida, 0777, true);
        }
        //debe permitir la escritua para Apache
           echo $directorioSubida;                                                  // Información sobre el archivo subido
        $nombreFichero = $_FILES['archivo1']['name'];
        $tipoFichero = $_FILES['archivo1']['type'];
        $tamanioFichero = $_FILES['archivo1']['size'];
        $temporalFichero = $_FILES['archivo1']['tmp_name'];
        $errorFichero = $_FILES['archivo1']['error'];

        //CREO UN ARRAY DONDE ALMACENAR LOS DATOS DEL FICHERO
        $id = $_SESSION["user"];
        $data = [$nombreFichero,
        $directorioSubida,
        $tipoFichero,
        $tamanioFichero,
       // $temporalFichero,
        ];

        /*
         * $msg .= 'Intentando subir el archivo: ' . ' <br />';
         * $msg .= "- Nombre: $nombreFichero" . ' <br />';
         * $msg .= '- Tamaño: ' . ($tamanioFichero / 1024) . ' KB <br />';
         * $msg .= "- Tipo: $tipoFichero" . ' <br />' ;
         * $msg .= "- Nombre archivo temporal: $temporalFichero" . ' <br />';
         * $msg .= "- Código de estado: $errorFichero" . ' <br />';
         *
         * $msg .= '<br />RESULTADO<br />';
         */
        /*
         * // Obtengo el código de error de la operación, 0 si todo ha ido bien
         * if ($errorFichero > 0) {
         * $msg .= "Se a producido el error: $errorFichero:"
         * . $codigosErrorSubida[$errorFichero] . ' <br />';
         * } else { // subida correcta del temporal
         * // si es un directorio y tengo permisos
         * if ( is_dir($directorioSubida) && is_writable ($directorioSubida)) {
         * //Intento mover el archivo temporal al directorio indicado
         * if (move_uploaded_file($temporalFichero, $directorioSubida .'/'. $nombreFichero) == true) {
         * //$msg .= 'Archivo guardado en: ' . $directorioSubida .'/'. $nombreFichero . ' <br />';
         * $msg ="Archivo guardado con exito";
         * } else {
         * $msg .= 'ERROR: Archivo no guardado correctamente <br />';
         * }
         * } else {
         * $msg .= 'ERROR: No es un directorio correcto o no se tiene permiso de escritura <br />';
         * }
         * }
         */
        //PRIMERO SUBO EL FICHERO Y LUEGO SI SE SUBE ALMACENO LOS DATOS EN EL JSON
        if(modelouserSubirfichero($directorioSubida, $nombreFichero, $tipoFichero, $tamanioFichero, $temporalFichero, $errorFichero, $msg)){
            if(modeloficheroAdd($id, $data)){$msg.="<br>Exito al almacenar datos";}}
    }
    modeloUserSave();
    ctlFileVerFicheros($msg);
}

function ctlFileBorrar($msg)
{
    $msg = "";
    $user = $_GET['id'];
    $nombre= $_GET['nombre'];
    echo $nombre;
    if (modeloUserDelfichero($user)) {
        $directorioSubida = "C:\Users\Bryan\Desktop\Prueba\\".$_SESSION["user"]."\\".$nombre;
        echo $directorioSubida;
        unlink($directorioSubida);
        
        $msg = "El archivo se borró correctamente.";
    } else {
        $msg = "No se pudo borrar el archivo.";
    }
    modeloUserSave();
    ctlFileVerFicheros($msg);
}

function ctlFileRenombrar($msg)
{
    $usuarios = modeloUserGetAll(); // almaceno dentro de $usuarios el contenido de la sesion usuarios
                                    // Invoco la vista
    include_once 'plantilla/verarchivos.php';
}

function ctlFileCompartir($msg)
{
    $usuarios = modeloUserGetAll(); // almaceno dentro de $usuarios el contenido de la sesion usuarios
                                    // Invoco la vista
    include_once 'plantilla/verarchivos.php';
}

function ctlFileUserCerrar($msg)
{
    session_destroy();
    modeloUserSave();
    header('Location:index.php');
}

function ctlFileDescargar($msg)
{
  
    $user = $_GET["id"];
    $datosusuario = $_SESSION["ficheros"][$user];
    $nombre = $datosusuario[0];
    $directorio = $datosusuario[1];
   
    
    modelouserDescargar($nombre,$directorio,$msg);
    ctlFileVerFicheros($msg);
}

function ctlFileModificar()
{
    $msg = "";

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if (isset($_POST['clave1']) && isset($_POST['email']) && isset($_POST['nombre']) && isset($_POST['plan'])) {
            $id = $_POST['iduser'];
            $nombre = $_POST['nombre'];
            $clave = $_POST['clave1'];
            $mail = $_POST['email'];
            $plan = $_POST['plan'];

            // Si el plan se modifica entonces el estado pasa a BLOQUEADO
            // ECHO $plan . " plan antiguo: " . $plan2;
            if ($plan != ($_SESSION["tusuarios"][$_SESSION["user"]][3])) {
                $estado = "B";
            } else {
                $estado = $_SESSION["tusuarios"][$_SESSION["user"]][4];
            }
            echo $estado;
            // CREO UN ARRAY DONDE ALMACENAR LA INFORMACION PARA LUEGO PASARLO COMO PARAMETRO
            $modificado = [
                $clave,
                $nombre,
                $mail,
                $plan,
                $estado
            ];

            // if (cumplecontra($_POST["clave1"], $_POST["clave2"],$_POST["iduser"],$_POST["email"])) {
            if (cumplerequisitos($_POST["clave1"], $_POST["clave2"], $_POST["iduser"], $_POST["email"], $msg)) {
                if (modeloUserUpdate($id, $modificado)) {
                    $msg = "El usuario fue modificado con éxito";
                }
            } else {
                $msg = "El usuario no pudo ser modificado";
            }
        }
    } else {

        // al pulsar en modificar le paso el id, con ese id sacamos los datos del id(usuario) para, que luego se mostraran a la hora de modificar
        $user = $_SESSION["user"];
        $datosusuario = $_SESSION["tusuarios"][$user];
        $clave = $datosusuario[0];
        $nombre = $datosusuario[1];
        $mail = $datosusuario[2];
        $plan = $datosusuario[3];
        $estado = $datosusuario[4];

        include_once 'plantilla/modificarficheros.php';
    }
    modeloUserSave();
    ctlFileVerFicheros($msg);
}

?>
