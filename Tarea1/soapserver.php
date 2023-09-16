<?php
require_once("nusoap.php");

// Configuración del servidor SOAP
$namespace = "http://localhost/tarea1_soap_casierra/";
$server = new soap_server();
$server->configureWSDL("WSDLTST", $namespace);
$server->soap_defencoding = 'UTF-8';
$server->wsdl->schemaTargetNamespace = $namespace;

function creaContacto($nombre, $apellidos, $direccion) {
    require_once("conexion.php");
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
    if (!$conn) {
        throw new SoapFault("Server", "Error de conexión con la base de datos");
    }

    $nombre = mysqli_real_escape_string($conn, $nombre);
    $apellidos = mysqli_real_escape_string($conn, $apellidos);
    $direccion = mysqli_real_escape_string($conn, $direccion);

    $sql = "INSERT INTO escritorio (nombre, apellidos, direccion) VALUES ('$nombre', '$apellidos', '$direccion')";

    if (mysqli_query($conn, $sql)) {
        $msg = "Se introdujo un nuevo registro en la BD: $nombre";
    } else {
        $msg = "Error al insertar en la base de datos: " . mysqli_error($conn);
    }

    mysqli_close($conn);

    return new soapval('return', 'xsd:string', $msg);
}


function buscarContacto($nombre) {
   
    require_once("conexion.php");
    
    // Conectar a la base de datos
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
    if (!$conn) {
        throw new SoapFault("Server", "Error de conexión con la base de datos");
    }

    
    $nombre = mysqli_real_escape_string($conn, $nombre);

    
    $sql = "SELECT * FROM escritorio WHERE nombre='$nombre'";
    $resultado = mysqli_query($conn, $sql);

    // Generar una tabla HTML con los resultados
    $listado = "<table border='1'><tr><td>ID</td><td>Nombre</td><td>Apellidos</td><td>Direccion</td></tr>";

    while ($fila = mysqli_fetch_array($resultado)) {
        $listado .= "<tr><td>" . $fila['idescritorio'] . "</td><td>" . $fila['nombre'] . "</td><td>" . $fila['apellidos'] . "</td><td>" . $fila['direccion'] . "</td></tr>";
    }

    $listado .= "</table>";

    // Cerrar la conexión a la base de datos
    mysqli_close($conn);

    return new soapval('return', 'xsd:string', $listado);
}
function modificarContacto($nombre) {
    require_once("conexion.php");
    
    // Conectar a la base de datos
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
    if (!$conn) {
        throw new SoapFault("Server", "Error de conexión con la base de datos");
    }

    $nombre = mysqli_real_escape_string($conn, $nombre);

    // Realizar una consulta para obtener los datos actuales del contacto
    $sql = "SELECT * FROM escritorio WHERE nombre='$nombre'";
    $resultado = mysqli_query($conn, $sql);

    $datosActuales = array();

    if ($fila = mysqli_fetch_array($resultado)) {
        $datosActuales['nombre'] = $fila['nombre'];
        $datosActuales['apellidos'] = $fila['apellidos'];
        $datosActuales['direccion'] = $fila['direccion'];
    } else {
        mysqli_close($conn);
        return new soapval('return', 'xsd:string', 'No se encontró el contacto');
    }

    // Cierra la conexión a la base de datos
    mysqli_close($conn);

    // Devuelve los datos actuales del contacto como parte del resultado
    return new soapval('return', 'xsd:string', json_encode($datosActuales));
}

function eliminarContacto($nombre) {
    require_once("conexion.php");
    
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
    if (!$conn) {
        throw new SoapFault("Server", "Error de conexión con la base de datos");
    }

    $nombre = mysqli_real_escape_string($conn, $nombre);

    $sql = "DELETE FROM escritorio WHERE nombre='$nombre'";
    if (mysqli_query($conn, $sql)) {
        $msg = "Se eliminó el contacto: $nombre";
    } else {
        $msg = "Error al eliminar el contacto: " . mysqli_error($conn);
    }

    mysqli_close($conn);

    return new soapval('return', 'xsd:string', $msg);
}


// Función para mostrar todos los contactos
function mostrarTodosContactos() {
    // Establecer la configuración de la base de datos
    require_once("conexion.php");
    
    // Conectar a la base de datos
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
    if (!$conn) {
        throw new SoapFault("Server", "Error de conexión con la base de datos");
    }

    // Consultar la base de datos
    $sql = "SELECT * FROM escritorio";
    $resultado = mysqli_query($conn, $sql);

    // Generar una tabla HTML con todos los contactos
    $listado = "<table border='1'><tr><td>ID</td><td>Nombre</td><td>Apellidos</td><td>Direccion</td></tr>";

    while ($fila = mysqli_fetch_array($resultado)) {
        $listado .= "<tr><td>" . $fila['idescritorio'] . "</td><td>" . $fila['nombre'] . "</td><td>" . $fila['apellidos'] . "</td><td>" . $fila['direccion'] . "</td></tr>";
    }

    $listado .= "</table>";

    // Cerrar la conexión a la base de datos
    mysqli_close($conn);

    return new soapval('return', 'xsd:string', $listado);
}
function modificarContacto2($nombre_actual, $apellidos_actual, $direccion_actual, $nombre, $apellidos, $direccion) {
    require_once("conexion.php");
    
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
    if (!$conn) {
        throw new SoapFault("Server", "Error de conexión con la base de datos");
    }

    $nombre_actual = mysqli_real_escape_string($conn, $nombre_actual);
    $apellidos_actual = mysqli_real_escape_string($conn, $apellidos_actual);
    $direccion_actual = mysqli_real_escape_string($conn, $direccion_actual);
    $nombre = mysqli_real_escape_string($conn, $nombre);
    $apellidos = mysqli_real_escape_string($conn, $apellidos);
    $direccion = mysqli_real_escape_string($conn, $direccion);

    // Actualiza los datos del contacto en la base de datos
    $sql = "UPDATE escritorio SET nombre='$nombre', apellidos='$apellidos', direccion='$direccion' WHERE nombre='$nombre_actual' AND apellidos='$apellidos_actual' AND direccion='$direccion_actual'";

    if (mysqli_query($conn, $sql)) {
        $msg = "Se modificó el contacto: $nombre_actual";
    } else {
        $msg = "Error al modificar el contacto: " . mysqli_error($conn);
    }

    mysqli_close($conn);

    return new soapval('return', 'xsd:string', $msg);
}


// Registrar las funciones en el servidor SOAP
$server->register('creaContacto', array('nombre' => 'xsd:string', 'apellidos' => 'xsd:string', 'direccion' => 'xsd:string'), array('return' => 'xsd:string'), $namespace, false, 'rpc', 'encoded', 'Función que crea un contacto');
$server->register('buscarContacto', array('nombre' => 'xsd:string'), array('return' => 'xsd:string'), $namespace, false, 'rpc', 'encoded', 'Función que busca un contacto por nombre');
$server->register('mostrarTodosContactos', array(), array('return' => 'xsd:string'), $namespace, false, 'rpc', 'encoded', 'Función que muestra todos los contactos');
$server->register('eliminarContacto', array('nombre' => 'xsd:string'), array('return' => 'xsd:string'), $namespace, false, 'rpc', 'encoded', 'Función que elimina un contacto por nombre');
$server->register('modificarContacto', array('nombre' => 'xsd:string'), array('return' => 'xsd:string'), $namespace, false, 'rpc', 'encoded', 'Función que busca para modificar un contacto por nombre');
$server->register('modificarContacto2', array('nombre_actual' => 'xsd:string','apellidos_actual' => 'xsd:string', 'direccion_actual' => 'xsd:string','nombre' => 'xsd:string', 'apellidos' => 'xsd:string', 'direccion' => 'xsd:string'), array('return' => 'xsd:string'), $namespace, false, 'rpc', 'encoded', 'Función que modifica un contacto');
// Manejo de solicitudes SOAP
if (!isset($HTTP_RAW_POST_DATA)) {
    $HTTP_RAW_POST_DATA = file_get_contents('php://input');
}

$server->service($HTTP_RAW_POST_DATA);
?>
