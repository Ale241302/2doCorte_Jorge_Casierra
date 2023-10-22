<?php
require_once("conexion.php");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ejercicio";
$db = mysqli_connect($servername, $username, $password, $dbname);

if (!$db) {
    $response = ['error' => 'Error de conexión con la base de datos'];
    http_response_code(500);
} else {
    $funcion = isset($_REQUEST['funcion']) ? $_REQUEST['funcion'] : null;
    $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null;

    switch ($method) {
        case 'POST':
            switch ($funcion) {
                case 'creaContacto':
                    $response = crearContacto($db);
                    break;
                case 'buscarContacto':
                    $response = buscarContacto($db);
                    break;
                case 'modificarContacto':
                    $response = modificarContacto($db);
                    break;
                default:
                    $response = ['error' => 'Función no encontrada'];
                    http_response_code(400);
                    break;
            }
            break;
        case 'GET':
            switch ($funcion) {
                case 'buscarContacto':
                    $response = buscarContacto($db);
                    break;
                case 'mostrarTodosContactos':
                    $response = mostrarTodosContactos($db);
                    break;
                default:
                    $response = ['error' => 'Función no encontrada'];
                    http_response_code(400);
                    break;
            }
            break;
        case 'DELETE':
            if ($funcion === 'eliminarContacto') {
                $input_data = json_decode(file_get_contents('php://input'), true);
                $response = eliminarContacto($db, $input_data);
            } else {
                $response = ['error' => 'Función no encontrada'];
                http_response_code(400);
            }
            break;
        case 'PUT':
            if ($funcion === 'modificarContacto') {
                $input_data = json_decode(file_get_contents('php://input'), true);
                $response = modificarContacto($db, $input_data);
            } else {
                $response = ['error' => 'Función no encontrada'];
                http_response_code(400);
            }
            break;
        default:
            $response = ['error' => 'Método no admitido'];
            http_response_code(400);
            break;
    }
}

header('Content-Type: application/json');
echo json_encode($response);

function crearContacto($db) {
    $nombre = mysqli_real_escape_string($db, $_POST['nombre']);
    $apellidos = mysqli_real_escape_string($db, $_POST['apellidos']);
    $direccion = mysqli_real_escape_string($db, $_POST['direccion']);
    $sql = "INSERT INTO escritorio (nombre, apellidos, direccion) VALUES ('$nombre', '$apellidos', '$direccion')";
    $stmt = $db->prepare($sql);
    if ($stmt->execute()) {
        return ["message" => "Contacto creado exitosamente", "status_code" => 200];
    } else {
        return ["message" => "Ocurrió un error al crear el contacto", "status_code" => 500];
    }
}

function buscarContacto($db) {
    $nombre = mysqli_real_escape_string($db, $_GET['nombre']);
    $sql = 'SELECT * FROM escritorio WHERE nombre = ?';
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $nombre);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $contacto = $result->fetch_assoc();

        if ($contacto) {
            return [
                "message" => "Contacto encontrado exitosamente",
                "contacto" => $contacto,
                "status_code" => 200
            ];
        } else {
            return [
                "message" => "Contacto no encontrado",
                "status_code" => 404
            ];
        }
    } else {
        return [
            "message" => "Ocurrió un error interno del servidor",
            "status_code" => 500
        ];
    }
}

function eliminarContacto($db, $input_data) {
    if (isset($input_data['nombre'])) {
        $nombre = mysqli_real_escape_string($db, $input_data['nombre']);
        $sql_check = 'SELECT * FROM escritorio WHERE nombre = ?';
        $stmt_check = $db->prepare($sql_check);
        $stmt_check->bind_param('s', $nombre);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        if ($result_check->num_rows > 0) {
            $sql_delete = 'DELETE FROM escritorio WHERE nombre = ?';
            $stmt_delete = $db->prepare($sql_delete);
            $stmt_delete->bind_param('s', $nombre);

            if ($stmt_delete->execute()) {
                return ["message" => "Contacto eliminado exitosamente", "status_code" => 200];
            } else {
                return ["message" => "Ocurrió un error al eliminar el contacto", "status_code" => 500];
            }
        } else {
            return ["message" => "Contacto no encontrado", "status_code" => 404];
        }
    } else {
        return ["message" => "Solicitud no válida: el parámetro 'nombre' es obligatorio", "status_code" => 400];
    }
}

function modificarContacto($db, $input_data) {
    if (isset($input_data['nombre'])) {
        $nombre = mysqli_real_escape_string($db, $input_data['nombre']);
        $nuevo_nombre = mysqli_real_escape_string($db, $input_data['nuevo_nombre']);
        $nuevo_apellidos = mysqli_real_escape_string($db, $input_data['nuevo_apellidos']);
        $nuevo_direccion = mysqli_real_escape_string($db, $input_data['nuevo_direccion']);
        if (empty($nombre) || empty($nuevo_nombre)) {
            return ["message" => "Los campos 'nombre' y 'nuevo_nombre' son obligatorios.", "status_code" => 400];
        } else {
            $sql_select = 'SELECT nombre FROM escritorio WHERE nombre = ?';
            $stmt_select = $db->prepare($sql_select);
            $stmt_select->bind_param('s', $nombre);
            if (!$stmt_select->execute()) {
                $error = mysqli_stmt_error($stmt_select);
                return ["message" => "Error al verificar el contacto: " . $error, "status_code" => 500];
            }
            $result = $stmt_select->get_result();
            $contacto = $result->fetch_assoc();
            if (!$contacto) {
                return ["message" => "El contacto no existe", "status_code" => 400];
            } else {
                $sql_update = 'UPDATE escritorio SET nombre = ?, apellidos = ?, direccion = ? WHERE nombre = ?';
                $stmt_update = $db->prepare($sql_update);
                $stmt_update->bind_param('ssss', $nuevo_nombre, $nuevo_apellidos, $nuevo_direccion, $nombre);

                if ($stmt_update->execute()) {
                    return ["message" => "Contacto modificado exitosamente", "status_code" => 200];
                } else {
                    $error = mysqli_stmt_error($stmt_update);
                    return ["message" => "Error al modificar el contacto: " . $error, "status_code" => 500];
                }
            }
        }
    } else {
        return ["message" => "Solicitud no válida: el parámetro 'nombre' es obligatorio", "status_code" => 400];
    }
}

function mostrarTodosContactos($db) {
    $sql = 'SELECT * FROM escritorio';
    $result = $db->query($sql);
    if ($result) {
        $contactos = $result->fetch_all(MYSQLI_ASSOC);
        return [
            "message" => "Contactos encontrados exitosamente",
            "contactos" => $contactos,
            "status_code" => 200
        ];
    } else {
        return [
            "message" => "Ocurrió un error interno del servidor",
            "status_code" => 500
        ];
    }
}
