<?php

namespace app;

class User extends Controller
{
    public function __construct()
    {
        session_start();
        parent::__construct();
    }

    // Vista principal
    public function index()
    {
        if (empty($_SESSION['activo'])) {
            header("location: " . APP_URL);
            exit;
        }

        $this->views->getView($this, "index");
    }

    // LOGIN
    public function login()
    {
        $usuario = $_POST['usuario'] ?? null;
        $pass = $_POST['password'] ?? null;

        if (empty($usuario) || empty($pass)) {
            echo json_encode(["msg" => "Todos los campos son requeridos", "icono" => "warning"]);
            return;
        }

        // Usa password_verify según el modelo
        $data = $this->model->getUser($usuario, $pass);

        if ($data) {
            $_SESSION['id_usuario'] = $data['id_usuario'];
            $_SESSION['usuario'] = $data['usuario'];
            $_SESSION['nombre'] = $data['nombre'];
            $_SESSION['activo'] = true;

            echo json_encode("ok");
        } else {
            echo json_encode(["msg" => "Usuario o contraseña incorrecta", "icono" => "error"]);
        }
    }

    // REGISTRAR USUARIO
    public function registrar()
    {
        $usuario = $_POST['usuario'] ?? null;
        $nombre = $_POST['nombre'] ?? null;
        $clave = $_POST['password'] ?? null;

        if (empty($usuario) || empty($nombre) || empty($clave)) {
            echo json_encode(["msg" => "Todos los campos son requeridos", "icono" => "warning"]);
            return;
        }

        $resp = $this->model->registerUser($usuario, $nombre, $clave);

        echo json_encode($resp);
    }

    // EDITAR USUARIO (obtener usuario por ID)
    public function editar($id)
    {
        $data = $this->model->getUserById($id); // si quieres crear este método te lo hago
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    // MODIFICAR USUARIO
    public function modificar()
    {
        $usuario = $_POST['usuario'] ?? null;
        $nombre = $_POST['nombre'] ?? null;
        $id = $_POST['id'] ?? null;

        if (empty($usuario) || empty($nombre) || empty($id)) {
            echo json_encode(["msg" => "Campos vacíos", "icono" => "warning"]);
            return;
        }

        $resp = $this->model->modifyUsuario($usuario, $nombre, $id);
        echo json_encode($resp);
    }

    // ACTUALIZAR CONTRASEÑA
    public function actualizarPass()
    {
        $id = $_POST['id'] ?? null;
        $pass = $_POST['password'] ?? null;

        if (empty($id) || empty($pass)) {
            echo json_encode(["msg" => "Campos vacíos", "icono" => "warning"]);
            return;
        }

        $resp = $this->model->actualizarPass($pass, $id);
        echo json_encode($resp);
    }

    // ELIMINAR USUARIO (definitivo)
    public function eliminar($id)
    {
        $resp = $this->model->deleteUsuario($id);
        echo json_encode($resp);
    }

    // CERRAR SESIÓN
    public function salir()
    {
        session_destroy();
        header("location: " . APP_URL);
    }
}

?>
