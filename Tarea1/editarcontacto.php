<?php
$nombre = isset($_GET['nombre']) ? urldecode($_GET['nombre']) : '';
$apellidos = isset($_GET['apellidos']) ? urldecode($_GET['apellidos']) : '';
$direccion = isset($_GET['direccion']) ? urldecode($_GET['direccion']) : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Contacto</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>Editar Contacto</h1>
    <form action="clientemysql.php" method="post">
        <input type="hidden" name="nombre_actual" value="<?php echo $nombre; ?>" />
        <input type="hidden" name="apellidos_actual" value="<?php echo $apellidos; ?>" />
        <input type="hidden" name="direccion_actual" value="<?php echo $direccion; ?>" />
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" value="<?php echo $nombre; ?>" />
        <br>
        <label for="apellidos">Apellidos:</label>
        <input type="text" name="apellidos" value="<?php echo $apellidos; ?>"/>
        <br>

        <label for="nueva_direccion">Dirección:</label>
        <input type="text" name="direccion" value="<?php echo $direccion; ?>"/>
        <br>

        <input type="text" name="funcion" value="modificarContacto2" hidden />
        <input type="submit" value="Modificar Contacto" />
    </form>
</body>
</html>
