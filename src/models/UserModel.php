<?php

use app\Query;

// Modelo de Usuarios
class UserModel extends Query{

    // Atributos
    private $user, $name, $pass, $id;

    // Constructor
    public function __construct()
    {
        parent::__construct();
    }

    // Obtener usuario para login (usa password_hash / password_verify)
    public function getUser($user, $pass)
    {
        // Buscar por usuario (consulta parametrizada)
        $sql = "SELECT * FROM usuarios WHERE usuario = ?";
        $params = array($user);
        $data = $this->select($sql, $params); // Query::select debe aceptar params

        if (empty($data)) {
            return null;
        }

        $stored = $data;
        $storedPass = isset($stored['password']) ? $stored['password'] : (isset($stored['clave']) ? $stored['clave'] : null);

        if ($storedPass === null) {
            return null;
        }

        if (password_verify($pass, $storedPass) || $pass === $storedPass) {
            return $stored;
        }

        return null;
    }

    // Registrar usuario (usa consulta parametrizada y hash de contraseña)
    public function registerUser($user, $name, $pass)
    {
        $this->user = $user;
        $this->name = $name;
        // Hashear contraseña antes de guardar
        $this->pass = password_hash($pass, PASSWORD_DEFAULT);

        // Verificar existencia usando parámetro
        $verify = "SELECT * FROM usuarios WHERE usuario = ?";
        $exists = $this->select($verify, array($this->user));
        if (!empty($exists)) {
            return "existe";
        }

        // Insertar usuario (parametrizado)
        $sql = "INSERT INTO usuarios(usuario, nombre, password) VALUES (?,?,?)";
        $datos = array($this->user, $this->name, $this->pass);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            return "insertado";
        }
        return "error";
    }

    // Modificar usuario
    public function modifyUsuario($user, $name, $id)
    {
        $this->user = $user;
        $this->name = $name;
        $this->id = $id;
        $sql = "UPDATE usuarios SET usuario = ?, nombre = ? WHERE id_usuario = ?";
        $datos = array($this->user, $this->name, $this->id);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            return "modificado";
        }
        return "error";
    }

    // Actualizar contraseña
    public function actualizarPass($pass, $id)
    {
        $hashed = password_hash($pass, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET password = ? WHERE id_usuario = ?";
        $datos = array($hashed, $id);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            return "modificado";
        }
        return "error";
    }

    // Eliminar usuario por ID
    public function deleteUsuario($id)
    {
        $sql = "DELETE FROM usuarios WHERE id_usuario = ?";
        $datos = array($id);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            return "eliminado";
        }
        return "error";
    }
}
?>