<?php

require './vendor/autoload.php';
use PHPUnit\Framework\TestCase;
final class ApiTest extends TestCase  {
    public function testCrearContacto() {
        $_POST['funcion'] = 'creaContacto';
        $_POST['nombre'] = 'John';
        $_POST['apellidos'] = 'Casierra';
        $_POST['direccion'] = '2';


        require_once('./src/Api.php');
        ob_start();
        crearContacto($db);
        $output = ob_get_clean();
        $this->assertJson($output);
        $data = json_decode($output, true);
        $this->assertEquals("Contacto creado exitosamente", $data['message']);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('nombre', $data);
        $this->assertArrayHasKey('apellidos', $data);
        $this->assertArrayHasKey('direccion', $data);
        $this->assertEquals('John', $data['nombre']);
    }
    public function testBuscarContacto() {
        $_POST['funcion'] = 'buscarContacto';
        $_POST['nombre'] = 'John';
        require_once('./src/Api.php');
        ob_start();
        buscarContacto($db);
        $output = ob_get_clean();
        $this->assertJson($output);
        $data = json_decode($output, true);
        $this->assertEquals("Contacto encontrado exitosamente", $data['message']);
        $this->assertArrayHasKey('nombre', $data);
        $this->assertArrayHasKey('apellidos', $data);
        $this->assertArrayHasKey('direccion', $data);
        $this->assertEquals('John', $data['nombre']);
    }
    public function testEliminarContacto() {
        $_POST['funcion'] = 'eliminarContacto';
        $_POST['nombre'] = 'John';
        require_once('./src/Api.php');
        ob_start();
        eliminarContacto($db);
        $output = ob_get_clean();
        $this->assertJson($output);
        $data = json_decode($output, true);
        $this->assertEquals("Contacto eliminado exitosamente", $data['message']);
    }
    public function testModificarContacto() {
        $_POST['funcion'] = 'modificarContacto';
        $_POST['nombre'] = 'John';
        $_POST['nuevo_nombre'] = 'Jane';
        $_POST['nuevo_apellidos'] = 'Casanova';
        $_POST['nuevo_direccion'] = 'J3';
        require_once('./src/Api.php');
        ob_start();
        modificarContacto($db);
        $output = ob_get_clean();
        $this->assertJson($output);
        $data = json_decode($output, true);
        $this->assertEquals("Contacto modificado exitosamente", $data['message']);
    }
    public function testMostrarTodosContactos() {
        $_POST['funcion'] = 'mostrarTodosContactos';
        require_once('./src/Api.php');
        ob_start();
        mostrarTodosContactos($db);
        $output = ob_get_clean();
        $this->assertJson($output);
        $data = json_decode($output, true);
        $this->assertArrayHasKey('contactos', $data);
        $this->assertNotEmpty($data['contactos']);
    } 
}
?>