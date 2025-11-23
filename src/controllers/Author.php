<?php

namespace app;
use app\Controller;

class AuthorController extends Controller
{
    public function __construct() {
        parent::__construct();

        session_start();
        if (empty($_SESSION['activo'])) {
            header("location: " . APP_URL);
        }
    }

    // Página principal de autores
    public function index()
    {
        $this->views->render($this, "index");
    }

    // Obtener lista de autores
    public function list()
    {
        $data = $this->model->getAuthor();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        return;
    }

    // Registrar nuevo autor
    public function register ()
    {
        $author = $_POST['author'];

        $respuesta = $this->model->insertAuthor($author);

        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        die();
    }

    // Obtener datos de un autor según ID
    public function edit($id)
    {
        $data = $this->model->editAuthor($id);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    // Actualizar autor existente
    public function update()
    {
        $id = $_POST['id'];
        $author = $_POST['author'];

        $respuesta = $this->model->updateAuthor($author, $id);

        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        die();
    }

    // Eliminar autor
    public function delete($id)
    {
        $respuesta = $this->model->deleteAuthor($id);
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        die();
    }
}

