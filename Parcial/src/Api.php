<?php
//header('Content-Type: application/json');
require_once("conexion.php");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ejercicio";
$db = mysqli_connect($servername, $username, $password, $dbname);

if (!$db) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Error de conexión con la base de datos']);
    exit;
}

$funcion = $_POST['funcion'];

switch ($funcion) {
    case 'creaContacto':
        crearContacto($db);
        break;
    case 'buscarContacto':
        buscarContacto($db);
        break;
    case 'eliminarContacto':
        eliminarContacto($db);
        break;
    case 'modificarContacto':
        modificarContacto($db);
        break;
    case 'mostrarTodosContactos':
        mostrarTodosContactos($db);
        break;
    default:
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Función no encontrada']);
        break;
}

function crearContacto($db) {
    $nombre = mysqli_real_escape_string($db, $_POST['nombre']);
    $apellidos = mysqli_real_escape_string($db, $_POST['apellidos']);
    $direccion = mysqli_real_escape_string($db, $_POST['direccion']);
    $sql = "INSERT INTO escritorio (nombre, apellidos, direccion) VALUES ('$nombre', '$apellidos', '$direccion')";
    $stmt = $db->prepare($sql);
    if ($stmt->execute()) {
        $response = [
            "message" => "Contacto creado exitosamente"
        ];
        header('HTTP/1.1 200 OK');
        echo json_encode($response);
    } else {
        $response = [
            "message" => "Ocurrió un error al crear el contacto"
        ];
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode($response);
    }
}

function buscarContacto($db) {
    $nombre = mysqli_real_escape_string($db, $_POST['nombre']);
    $sql = 'SELECT * FROM escritorio WHERE nombre = ?';
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $nombre);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $contacto = $result->fetch_assoc();
        if ($contacto) {
            $response = [
                "message" => "Contacto encontrado exitosamente",
                "contacto" => $contacto
            ];
            header('HTTP/1.1 200 OK');
            echo json_encode($response); 
        } else {
            $response = [
                "message" => "Contacto no encontrado"
            ];
            header('HTTP/1.1 404 Not Found'); 
            echo json_encode($response); // Enviar la respuesta como JSON
        }
    } else {
        $response = [
            "message" => "Ocurrió un error interno del servidor"
        ];
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode($response);
    }
}


function eliminarContacto($db) {
    $nombre = mysqli_real_escape_string($db, $_POST['nombre']);
    $sql_select = 'SELECT nombre FROM escritorio WHERE nombre = ?';
    $stmt_select = $db->prepare($sql_select);
    $stmt_select->bind_param('s', $nombre);
    if ($stmt_select->execute()) {
        $result = $stmt_select->get_result();
        $contacto = $result->fetch_assoc();
        if ($contacto) {
            $sql_delete = 'DELETE FROM escritorio WHERE nombre = ?';
            $stmt_delete = $db->prepare($sql_delete);
            $stmt_delete->bind_param('s', $nombre);
            if ($stmt_delete->execute()) {
                $response = [
                    "message" => "Contacto eliminado exitosamente"
                ];
                header('HTTP/1.1 200 OK');
                echo json_encode($response);
            } else {
                $error = mysqli_stmt_error($stmt_delete);
                $response = [
                    "message" => "Error al eliminar el contacto: " . $error
                ];
                header('HTTP/1.1 500 Internal Server Error');
                echo json_encode($response);
            }
        } else {
            $response = [
                "message" => "El contacto no existe"
            ];
            header('HTTP/1.1 400 Bad Request');
            echo json_encode($response);
        }
    } else {
        $error = mysqli_stmt_error($stmt_select);
        $response = [
            "message" => "Error al verificar el contacto: " . $error
        ];
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode($response);
    }
}

function modificarContacto($db) {
    $nombre = mysqli_real_escape_string($db, $_POST['nombre']);
    $nuevo_nombre = mysqli_real_escape_string($db, $_POST['nuevo_nombre']);
    $nuevo_apellidos = mysqli_real_escape_string($db, $_POST['nuevo_apellidos']);
    $nuevo_direccion = mysqli_real_escape_string($db, $_POST['nuevo_direccion']);
    $sql_select = 'SELECT nombre FROM escritorio WHERE nombre = ?';
    $stmt_select = $db->prepare($sql_select);
    $stmt_select->bind_param('s', $nombre);
    if ($stmt_select->execute()) {
        $result = $stmt_select->get_result();
        $contacto = $result->fetch_assoc();
        if ($contacto) {
            if ($nombre !== $nuevo_nombre) {
                $sql_update_nombre = 'UPDATE escritorio SET nombre = ? WHERE nombre = ?';
                $stmt_update_nombre = $db->prepare($sql_update_nombre);
                $stmt_update_nombre->bind_param('ss', $nuevo_nombre, $nombre);
                if (!$stmt_update_nombre->execute()) {
                    $response = [
                        "message" => "Ocurrió un error interno al actualizar el nombre"
                    ];
                    header('HTTP/1.1 500 Internal Server Error');
                    echo json_encode($response);
                    return;
                }
            }
            $sql = 'UPDATE escritorio SET apellidos = ?, direccion = ? WHERE nombre = ?';
            $stmt = $db->prepare($sql);
            $stmt->bind_param('sss', $nuevo_apellidos, $nuevo_direccion, $nuevo_nombre);
            if ($stmt->execute()) {
                $response = [
                    "message" => "Contacto modificado exitosamente"
                ];
                header('HTTP/1.1 200 OK');
                echo json_encode($response);
            } else {
                $response = [
                    "message" => "Ocurrió un error interno del servidor"
                ];
                header('HTTP/1.1 500 Internal Server Error');
                echo json_encode($response);
            }
        } else {
            $response = [
                "message" => "El contacto no existe"
            ];
            header('HTTP/1.1 400 Bad Request');
            echo json_encode($response);
        }
    } else {
        $error = mysqli_stmt_error($stmt_select);
        $response = [
            "message" => "Error al verificar el contacto: " . $error
        ];
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode($response);
    }
}

function mostrarTodosContactos($db) {
    $sql = 'SELECT * FROM escritorio';
    $result = $db->query($sql);
    if ($result) {
        $contactos = $result->fetch_all(MYSQLI_ASSOC);
        $response = [
            "message" => "Contactos encontrados exitosamente",
            "contactos" => $contactos
        ];
        header('HTTP/1.1 200 OK');
        echo json_encode($response);
    } else {
        $response = [
            "message" => "Ocurrió un error interno del servidor"
        ];
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode($response);
    }
}
?>
