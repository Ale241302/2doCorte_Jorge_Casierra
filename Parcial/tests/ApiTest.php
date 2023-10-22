<?php
use PHPUnit\Framework\TestCase;
require_once './src/conexion.php';
require_once './src/Api.php';
class ApiTest extends TestCase
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = new mysqli('localhost', 'root', '', 'ejercicio');
        if ($this->db->connect_error) {
            die('Error de conexión con la base de datos de prueba: ' . $this->db->connect_error);
        }
    }

    public function testCrearContacto()
    {
        $data = [
            'nombre' => 'John Doe',
            'apellidos' => 'Doe',
            'direccion' => '123 Main St',
        ];

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_REQUEST['funcion'] = 'creaContacto';
        $_POST = $data;

        $response = crearContacto($this->db);

        $this->assertEquals('Contacto creado exitosamente', $response['message']);
        $this->assertEquals(200, $response['status_code']);
    }

    public function testBuscarContacto()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_REQUEST['funcion'] = 'buscarContacto';
        $_GET['nombre'] = 'John Doe';

        $response = buscarContacto($this->db);

        $this->assertEquals('Contacto encontrado exitosamente', $response['message']);
        $this->assertEquals(200, $response['status_code']);
    }

    public function testEliminarContacto()
{
    $data = [
        'nombre' => 'John Doe',
    ];

    $_SERVER['REQUEST_METHOD'] = 'DELETE';
    $_REQUEST['funcion'] = 'eliminarContacto';
    $_DELETE = $data;

    $response = eliminarContacto($this->db, $data);  // Pasa los datos como segundo argumento

    $this->assertEquals('Contacto eliminado exitosamente', $response['message']);
    $this->assertEquals(200, $response['status_code']);
}

public function testModificarContacto()
{
    $data = [
        'nombre' => 'Alejo',
        'nuevo_nombre' => 'Alejo',
        'nuevo_apellidos' => 'Doe',
        'nuevo_direccion' => '456 Elm St',
    ];

    $_SERVER['REQUEST_METHOD'] = 'PUT';
    $_REQUEST['funcion'] = 'modificarContacto';
    $_PUT = $data;

    $response = modificarContacto($this->db, $data);  // Pasa los datos como segundo argumento

    $this->assertEquals('Contacto modificado exitosamente', $response['message']);
    $this->assertEquals(200, $response['status_code']);
}
    public function __destruct()
    {
        $this->db->close();
    }
}
?>