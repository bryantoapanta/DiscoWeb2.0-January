<?php
session_start();
include_once 'app/config.php';
include_once 'app/controlerFile.php';
include_once 'app/controlerUser.php';
include_once 'app/modeloUser.php';

// Inicializo el modelo
modeloUserInit();

// Enrutamiento
// Relaci贸n entre peticiones y funci贸n que la va a tratar
// Versi贸n sin POO no manejo de Clases ni objetos
$rutasUser = [
    "Inicio"      => "ctlUserInicio",
    "Alta"        => "ctlUserAlta",
    "AltaUser"    => "ctlUserAltaUser",
    "Detalles"    => "ctlUserDetalles",
    "Modificar"   => "ctlUserModificar",
    "Borrar"      => "ctlUserBorrar",
    "Cerrar"      => "ctlUserCerrar",
    "VerUsuarios" => "ctlUserVerUsuarios"
];


// Si no hay usuario a Inicio
if (!isset($_SESSION['user'])){
    $procRuta = "ctlUserInicio";
} else {
    if ( $_SESSION['modo'] == GESTIONUSUARIOS){
        if (isset($_GET['orden'])){
            // La orden tiene una funcion asociada
            if ( isset ($rutasUser[$_GET['orden']]) ){
                $procRuta =  $rutasUser[$_GET['orden']];
            }
            else {
                // Error no existe funci贸n para la ruta
                header('Status: 404 Not Found');
                echo '<html><body><h1>Error 404: No existe la ruta <i>' .
                    $_GET['ctl'] .
                    '</p></body></html>';
                    exit;
            }
        }
        else {
            $procRuta = "ctlUserVerUsuarios";
        }
    }
    // Usuario Normal PRIMERA VERSION SIN ACCIONES
    else {
        $procRuta = "ctlUserInicio";
    }
}

if (isset($_GET['orden'])){
    // La orden tiene una funcion asociada
    if ( isset ($rutasUser[$_GET['orden']]) ){
        $procRuta =  $rutasUser[$_GET['orden']];
    }
}else{
    $procRuta = "ctlUserInicio";
}

//Gestion Ficheros

// Rutas en MODO GESTIONFICHEROS
$rutasFicheros = [
    "VerFicheros" => "ctlFileVerFicheros",
    "Nuevo"       => "ctlFileNuevo",
    "Borrar"      => "ctlFileBorrar",
    "Renombrar"   => "ctlFileRenombrar",
    "Compartir"   => "ctlFileCompartir",
    "Cerrar"      => "ctlUserCerrar",
    "Descargar"   => "ctlFileDescargar"
];

if ($_SESSION['modo'] == GESTIONFICHEROS){
    if (isset($_GET['operacion'])){
        // La orden tiene una funcion asociada
        if ( isset ($rutasFicheros[$_GET['operacion']]) ){
            $procRuta =  $rutasFicheros[$_GET['operacion']];
        }
        else {
            // Error no existe funci髇 para la ruta
            header('Status: 404 Not Found');
            echo '<html><body><h1>Error 404: No existe la ruta <i>' .
                $_GET['ctl'] .
                '</p></body></html>';
                exit;
        }
    }
    else {
        $procRuta = "ctlFileVerFicheros";
    }
}

// Llamo a la funci贸n seleccionada
$procRuta($msg);


//var_dump($_REQUEST);

