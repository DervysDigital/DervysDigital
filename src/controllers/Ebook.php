<?php

use app\Controller;

class Ebook extends Controller
{
    public function __construct()
    {
        session_start();
        if (empty($_SESSION['activo'])) {
            header("location: " . APP_URL);
        }
        parent::__construct();

        $id_user = $_SESSION['id_usuario'];
        $perm = $this->model->verificarPermisos($id_user, "Ebooks");

        if (!$perm && $id_user != 1) {
            $this->views->getView($this, "permisos");
            exit;
        }
    }

    // Vista principal
    public function index()
    {
        $this->views->getView($this, "index");
    }

    // Listar ebooks
    public function listar()
    {
        $data = $this->model->getAllEbooks();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    // Registrar o actualizar ebook
    public function registrar()
    {
        $titulo = strClean($_POST['titulo']);
        $idAutor = strClean($_POST['idAutor']);
        $isbn = strClean($_POST['isbn']);
        $cantidad = strClean($_POST['cantidad']);
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if (empty($titulo) || empty($idAutor) || empty($isbn) || empty($cantidad)) {
            $msg = array('msg' => 'Todos los campos son requeridos', 'icono' => 'warning');
            echo json_encode($msg);
            die();
        }

        // Registrar
        if ($id == 0) {
            $data = $this->model->insertEbook($titulo, $idAutor, $isbn, $cantidad);

            if ($data == "ok") {
                $msg = array('msg' => 'Ebook registrado con éxito', 'icono' => 'success');
            } elseif ($data == "existe") {
                $msg = array('msg' => 'El ebook ya existe', 'icono' => 'warning');
            } else {
                $msg = array('msg' => 'Error al registrar', 'icono' => 'error');
            }

        // Actualizar
        } else {
            $data = $this->model->updateEbook($titulo, $idAutor, $isbn, $cantidad, $id);

            if ($data == "modificado") {
                $msg = array('msg' => 'Ebook modificado con éxito', 'icono' => 'success');
            } else {
                $msg = array('msg' => 'Error al modificar', 'icono' => 'error');
            }
        }

        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }

    // Obtener un ebook por ID
    public function editar($id)
    {
        $data = $this->model->getEbookById($id);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    // Dar de baja (estado = prestado)
    public function eliminar($id)
    {
        $data = $this->model->stateEbook('prestado', $id);

        if ($data == 1) {
            $msg = array('msg' => 'Ebook marcado como prestado', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error al cambiar estado', 'icono' => 'error');
        }

        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }

    // Reingresar (estado = disponible)
    public function reingresar($id)
    {
        $data = $this->model->stateEbook('disponible', $id);

        if ($data == 1) {
            $msg = array('msg' => 'Ebook disponible nuevamente', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error al restaurar ebook', 'icono' => 'error');
        }

        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }

    // Eliminar físicamente
    public function destruir($id)
    {
        $data = $this->model->deleteEbook($id);

        if ($data == "eliminado") {
            $msg = array('msg' => 'Ebook eliminado permanentemente', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error al eliminar', 'icono' => 'error');
        }

        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
}
