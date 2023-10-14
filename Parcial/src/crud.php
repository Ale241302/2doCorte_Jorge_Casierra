<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Servicios web</title>
    <link rel="stylesheet" type="text/css" href="style.css">
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
    <form action="" method="post">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" />
        <input type="text" name="funcion" value="buscarContacto" hidden />
        <input type="submit" value="Buscar Contacto" />
    </form>
    <hr/>
    <form action="" method="post" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este contacto?')">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" />
        <input type="text" name="funcion" value="eliminarContacto" hidden />
        <input type="submit" value="Eliminar Contacto" />
    </form>    
    <hr/>
    <form action="" method="post">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" />
        <label for="nombre">Nuevo Nombre:</label>
        <input type="text" name="nuevo_nombre" />
        <label for="nombre">Nuevo Apellidos:</label>
        <input type="text" name="nuevo_apellidos" />
        <label for="nombre">Nueva Direccion:</label>
        <input type="text" name="nuevo_direccion" />
        <input type="text" name="funcion" value="modificarContacto" hidden />
        <input type="submit" value="Modificar Contacto" />
    </form>
    <hr/>
    <form action="" method="post">
        <input type="text" name="funcion" value="mostrarTodosContactos" hidden />
        <input type="submit" value="Mostrar todos los contactos" />
    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $api_url = "http://localhost/2corte/Parcial/src/Api.php";
        $funcion = $_POST['funcion'];
        $data = $_POST;
        $response = '';
        $ch = curl_init();
        switch ($funcion) {
            case 'creaContacto':
                curl_setopt($ch, CURLOPT_URL, $api_url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;

            case 'buscarContacto':
                curl_setopt($ch, CURLOPT_URL, $api_url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;

            case 'eliminarContacto':
                curl_setopt($ch, CURLOPT_URL, $api_url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;

            case 'modificarContacto':
                curl_setopt($ch, CURLOPT_URL, $api_url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;

            case 'mostrarTodosContactos':
                curl_setopt($ch, CURLOPT_URL, $api_url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;

            default:
                echo 'Función no encontrada';
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
                echo '<th>ID</th>';
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
            echo 'Error desconocido';
        }
        
    }
    ?>
</body>
</html>
