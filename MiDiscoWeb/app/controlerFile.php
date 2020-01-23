<?php
include_once 'config.php';
include_once 'modeloUser.php';

function ctlFileVerFicheros($msg)
{
    $usuarios = modeloUserGetFiles(); // almaceno dentro de $usuarios el contenido de la sesion usuarios
                                      // Invoco la vista
    include_once 'plantilla/verarchivos.php';
}

function ctlFileNuevo($msg)
{
    // Invoco la vista
    include_once 'plantilla/subirfichero.php';
}

function ctlFileBorrar($msg)
{
    $usuarios = modeloUserGetAll(); // almaceno dentro de $usuarios el contenido de la sesion usuarios
                                    // Invoco la vista
    include_once 'plantilla/verarchivos.php';
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
    $usuarios = modeloUserGetAll(); // almaceno dentro de $usuarios el contenido de la sesion usuarios
                                    // Invoco la vista
    include_once 'plantilla/verarchivos.php';
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
            //ECHO $plan . " plan antiguo: " . $plan2;
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
            //if (cumplerequisitos($_POST["clave1"], $_POST["clave2"], $_POST["iduser"], $_POST["email"], $msg)) {
                if (modeloUserUpdate($id, $modificado)) {
                    $msg = "El usuario fue modificado con éxito";
                    // }
                }
             else {
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
