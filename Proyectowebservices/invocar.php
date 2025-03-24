<?php

require_once "vendor/autoload.php";
require_once "vendor/econea/nusoap/src/nusoap.php";
require_once "servidor.php";




$namespace = "urn:miserviciowsdl";
$server = new nusoap_server();

$server->configureWSDL("MiServicioWeb", $namespace);
$server->wsdl->schemaTargetNamespace = $namespace;

// Definir el tipo complejo Producto


// Registrar métodos con los tipos de datos correctos


$server->register(
    "Servidor.Login",
    [
        "usuario" => "xsd:string",
        "contraseña"=> "xsd:string",
        "token"=> "xsd:string",
    ],
    [
        "return"=> "xsd:string"
    ],
    $namespace,
    false,
    "rpc",
    "encoded",  
    "Verifica las credenciales de usuario y devuelve un token o mensaje"
);

$server->register(
    "Servidor.ObtenerUsuario", // Nombre de la función
    ["token" => "xsd:string"], // Recibe solo el token
    ["return" => "xsd:string"], // Devuelve XML en formato string
    $namespace,
    false,
    "rpc",
    "encoded",
    "Muestra los usuarios en la BD en formato XML"
);

$server->register(
    "Servidor.CrearUsuario",
    [
    "nombre" => "xsd:string",
    "usuario" => "xsd:string",
    "contraseña" => "xsd:string",
    "token"=> "xsd:string",
    ],
    ["return" => "xsd:string"], // Devuelve XML en formato string
    $namespace, false,"rpc","encoded","Crea un usuario nuevo en la bd"
    );

$server->register(
    "Servidor.EliminarUsuario",

    [
        "id" => "xsd:integer",
        "token" => "xsd:string"
    ],
    [
        "return"=> "xsd:string"
    ],
    $namespace,
    false,
    "rpc",
    "encoded",
    "Eliminar un Usuario"
    );


$server->register(
    "Servidor.ActualizarUsuario",
    [
        "id"=> "xsd:integer",
        "nombre" => "xsd:string",
        "usuario" => "xsd:string",
        "contraseña" => "xsd:string",
        "token"=> "xsd:string",
    ],
    [
        "return"=> "xsd:string"
    ],
    $namespace,
    false,
    "rpc",
    "encoded",
    "Actualizar usuario"
    );

$server->service(file_get_contents("php://input"));

?>