<?php

use app\Controller;

class Prestamos extends Controller
{
    public function __construct()
    {
        session_start();
        if (empty($_SESSION['activo'])) {
            header("location: " .   APP_URL);
        }
        parent::__construct();

        // Verificar permisos
        $id_user = $_SESSION['id_usuario'];
        $perm = $this->model->verificarPermisos($id_user, "Prestamos");
        if (!$perm && $id_user != 1) {
            $this->views->getView($this, "permisos");
            exit;
        }
    }

    public function index()
    {
        $this->views->getView($this, "index");
    }

    // Listar todos los préstamos
    public function listar()
    {
        $data = $this->model->getPrestamos();
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['estado'] == 1) {
                $data[$i]['estado'] = '<span class="badge badge-secondary">Prestado</span>';
                $data[$i]['acciones'] = '<div>
                    <button class="btn btn-primary" onclick="btnEntregar(' . $data[$i]['id_prestamo'] . ')"><i class="fa fa-hourglass-start"></i></button>
                    <button class="btn btn-danger" onclick="btnEliminar(' . $data[$i]['id_prestamo'] . ')"><i class="fa fa-trash-o"></i></button>
                </div>';
            } else {
                $data[$i]['estado'] = '<span class="badge badge-primary">Devuelto</span>';
                $data[$i]['acciones'] = '<div>
                    <button class="btn btn-success" onclick="btnReingresar(' . $data[$i]['id_prestamo'] . ')"><i class="fa fa-reply-all"></i></button>
                </div>';
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    // Registrar préstamo
    public function registrar()
    {
        $libro = strClean($_POST['libro']);
        $estudiante = strClean($_POST['estudiante']);
        $cantidad = strClean($_POST['cantidad']);
        $fechaPrestamo = strClean($_POST['fecha_prestamo']);
        $fechaDevolucion = strClean($_POST['fecha_devolucion']);
        $observacion = strClean($_POST['observacion']);

        if (empty($libro) || empty($estudiante) || empty($cantidad) || empty($fechaPrestamo) || empty($fechaDevolucion)) {
            $msg = ['msg' => 'Todos los campos son requeridos', 'icono' => 'warning'];
        } else {
            $stock = $this->model->getCantLibro($libro);
            if ($stock['cantidad'] >= $cantidad) {
                $res = $this->model->insertPrestamo($estudiante, $libro, $cantidad, $fechaPrestamo, $fechaDevolucion, $observacion);
                if ($res > 0) {
                    $msg = ['msg' => 'Préstamo registrado correctamente', 'icono' => 'success', 'id' => $res];
                } elseif ($res == "existe") {
                    $msg = ['msg' => 'El libro ya está prestado', 'icono' => 'warning'];
                } else {
                    $msg = ['msg' => 'Error al registrar el préstamo', 'icono' => 'error'];
                }
            } else {
                $msg = ['msg' => 'Stock insuficiente', 'icono' => 'warning'];
            }
        }

        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }

    // Entregar libro (cambia estado a devuelto y actualiza stock)
    public function entregar($id)
    {
        $res = $this->model->updatePrestamo(0, $id);
        if ($res === "ok") {
            $msg = ['msg' => 'Libro recibido correctamente', 'icono' => 'success'];
        } else {
            $msg = ['msg' => 'Error al procesar la entrega', 'icono' => 'error'];
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }

    // Eliminar préstamo (marca estado = 0 y restaura stock si estaba activo)
    public function eliminar($id)
    {
        $res = $this->model->deletePrestamo($id);
        if ($res === "ok") {
            $msg = ['msg' => 'Préstamo eliminado correctamente', 'icono' => 'success'];
        } else {
            $msg = ['msg' => 'Error al eliminar el préstamo', 'icono' => 'error'];
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }

    // Reingresar préstamo (por si quieres reactivar un préstamo eliminado)
    public function reingresar($id)
    {
        $res = $this->model->updatePrestamo(1, $id);
        if ($res === "ok") {
            $msg = ['msg' => 'Préstamo reactivado correctamente', 'icono' => 'success'];
        } else {
            $msg = ['msg' => 'Error al reactivar el préstamo', 'icono' => 'error'];
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
}
?>
