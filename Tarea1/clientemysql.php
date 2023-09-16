<?php
require_once("nusoap.php");

$namespace = "http://localhost/tarea1_soap_casierra/";
$serverScript = 'soapserver.php';
$metodoALlamar = isset($_POST['funcion']) ? $_POST['funcion'] : '';

try {
    $client = new nusoap_client("$namespace/$serverScript?wsdl", 'wsdl');

    $params = array(); 

    if ($metodoALlamar == 'creaContacto') {
        $params['nombre'] = isset($_POST['nombre']) ? $_POST['nombre'] : '';
        $params['apellidos'] = isset($_POST['apellidos']) ? $_POST['apellidos'] : '';
        $params['direccion'] = isset($_POST['direccion']) ? $_POST['direccion'] : '';
    } elseif ($metodoALlamar == 'buscarContacto') {
        $params['nombre'] = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    } elseif ($metodoALlamar == 'eliminarContacto') {
        $params['nombre'] = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    } elseif ($metodoALlamar == 'modificarContacto') {
        $params['nombre'] = isset($_POST['nombre']) ? $_POST['nombre'] : '';
        $result = $client->call("modificarContacto", array('nombre' => $params['nombre']), $namespace, $namespace . "$serverScript/modificarContacto");
        $datosActuales = json_decode($result, true);
    
        if ($datosActuales) {
            // Redirigir al usuario a la página de edición con los datos actuales
            $urlEditarContacto = "editarcontacto.php?nombre=" . urlencode($datosActuales['nombre']) .
                "&apellidos=" . urlencode($datosActuales['apellidos']) .
                "&direccion=" . urlencode($datosActuales['direccion']);
            header("Location: $urlEditarContacto");
            exit();
        }
    }elseif ($metodoALlamar == 'modificarContacto2') {
        $params['nombre_actual'] = isset($_POST['nombre_actual']) ? $_POST['nombre_actual'] : '';
        $params['apellidos_actual'] = isset($_POST['apellidos_actual']) ? $_POST['apellidos_actual'] : '';
        $params['direccion_actual'] = isset($_POST['direccion_actual']) ? $_POST['direccion_actual'] : '';
        $params['nombre'] = isset($_POST['nombre']) ? $_POST['nombre'] : '';
        $params['apellidos'] = isset($_POST['apellidos']) ? $_POST['apellidos'] : '';
        $params['direccion'] = isset($_POST['direccion']) ? $_POST['direccion'] : '';
    }
    $result = $client->call(
        "$metodoALlamar",
        $params,
        $namespace, 
        $namespace."$serverScript/$metodoALlamar" 
    );
    if ($metodoALlamar != 'modificarContacto') {
        
        echo $result . "<br><br><a href='crud.html'>Volver a formulario</a>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
