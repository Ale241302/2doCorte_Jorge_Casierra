<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Servicios web</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <form action="" method="post">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" /><br />
        <label for="apellidos">Apellidos:</label>
        <input type="text" name="apellidos" /><br />
        <label for="direccion">Direccion:</label>
        <input type="text" name="direccion" /><br />
        <input type="text" name="funcion" value="creaContacto" hidden />
        <input type="submit" value="Crear nuevo usuario" />
    </form>
    <hr/>
    <form action="" method="GET">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" />
        <input type="text" name="funcion" value="buscarContacto" hidden />
        <input type="submit" value="Buscar Contacto" />
    </form>
    <hr/>
    <form action="" method="DELETE">
    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre" required />
    <input type="button" value="Eliminar Contacto" onclick="deleteContact();" />
    </form>
    <hr/>
    <form action="" method="PUT">
    <label for="nombre_update">Nombre:</label>
    <input type="text" id="nombre_update" name="nombre" />
    <label for="nuevo_nombre">Nuevo Nombre:</label>
    <input type="text" id="nuevo_nombre" name="nuevo_nombre" />
    <label for="nuevo_apellidos">Nuevo Apellidos:</label>
    <input type="text" id="nuevo_apellidos" name="nuevo_apellidos" />
    <label for="nuevo_direccion">Nueva Direccion:</label>
    <input type="text" id="nuevo_direccion" name="nuevo_direccion" />
    <input type="button" value="Modificar Contacto" onclick="updateContact();" />
    </form>
    <hr/>
    <form action="" method="GET">
        <input type="text" name="funcion" value="mostrarTodosContactos" hidden />
        <input type="submit" value="Mostrar todos los contactos" />
    </form>
    <script>
    function deleteContact() {
    const nombre = document.getElementById('nombre').value;
    const funcion = "eliminarContacto";

    if (!nombre) {
        alert('Por favor, ingresa un nombre antes de eliminar el contacto.');
        return;
    }

    if (confirm('¿Estás seguro de que deseas eliminar este contacto?')) {
        const xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState === 4) {
                if (this.status === 200) {
                    alert('Contacto eliminado con éxito');
                    // Puedes realizar acciones adicionales aquí, como actualizar la página.
                } else {
                    alert('Error al eliminar el contacto');
                }
            }
        };

        xhttp.open("DELETE", `http://localhost/2corte/Parcial/src/Api.php?funcion=${funcion}&nombre=${nombre}`, true);
        xhttp.setRequestHeader('Content-Type', 'application/json');
        xhttp.send(JSON.stringify({ nombre: nombre }));
    }
}

function updateContact() {
    const nombre = document.getElementById('nombre_update').value;
    const nuevo_nombre = document.getElementById('nuevo_nombre').value;
    const nuevo_apellidos = document.getElementById('nuevo_apellidos').value;
    const nuevo_direccion = document.getElementById('nuevo_direccion').value;
    const funcion = "modificarContacto";

    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState === 4) {
            if (this.status === 200) {
                alert('Contacto modificado exitosamente');
            } else {
                alert('Error al modificar el contacto');
            }
        }
    };

    xhttp.open("PUT", `http://localhost/2corte/Parcial/src/Api.php?funcion=${funcion}&nombre=${nombre}`, true);
    xhttp.setRequestHeader('Content-Type', 'application/json');
    xhttp.send(JSON.stringify({ 
        nombre: nombre,
        nuevo_nombre: nuevo_nombre,
        nuevo_apellidos: nuevo_apellidos,
        nuevo_direccion: nuevo_direccion
    }));
}
</script>
    <?php
$api_url = "http://localhost/2corte/Parcial/src/Api.php";
$funcion = $_REQUEST['funcion']; 
$data = $_REQUEST; 
$response = '';
$ch = curl_init();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        break;

        case 'GET':
            // Agregar la función y el nombre como parámetros en la URL
            $data['funcion'] = $funcion; // Agregar la función a los datos
            curl_setopt($ch, CURLOPT_URL, $api_url . '?' . http_build_query($data));
            break;
    default:
        echo 'Método HTTP no admitido';
        break;
}

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($status_code === 200) {
    $result = json_decode($response, true);

    if ($funcion === 'mostrarTodosContactos' && isset($result['contactos'])) {
        echo '<h2>Contactos</h2>';
        echo '<table border="1">';
        echo '<tr>';
        echo '<th>Nombre</th>';
        echo '<th>Apellidos</th>';
        echo '<th>Dirección</th>';
        echo '</tr>';

        foreach ($result['contactos'] as $contacto) {
            echo '<tr>';
            echo '<td>' . $contacto['nombre'] . '</td>';
            echo '<td>' . $contacto['apellidos'] . '</td>';
            echo '<td>' . $contacto['direccion'] . '</td>';
            echo '</tr>';
        }

        echo '</table>';
    } elseif ($funcion === 'buscarContacto' && isset($result['contacto'])) {
        echo '<h2>Contacto Encontrado</h2>';
        echo '<table border="1">';
        echo '<tr>';
        echo '<th>Nombre</th>';
        echo '<th>Apellidos</th>';
        echo '<th>Dirección</th>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>' . $result['contacto']['nombre'] . '</td>';
        echo '<td>' . $result['contacto']['apellidos'] . '</td>';
        echo '<td>' . $result['contacto']['direccion'] . '</td>';
        echo '</tr>';
        echo '</table>';
    } else {
        echo 'Respuesta: ' . $result['message'];
    }
} elseif ($status_code === 400) {
    echo 'Error 400: Bad Request';
} elseif ($status_code === 500) {
    echo 'Error 500: Internal Server Error';
} else {
    echo 'No Existe el Contacto';
}
?>

</body>
</html>
