<?php

use app\Query;

// Modelo de Prestamos
class PrestamosModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    // Obtener todos los prestamos
    public function getPrestamos()
    {
        $sql = "SELECT 
                    p.id_prestamo, p.id_usuario, p.id_libro, p.fecha_prestamo, p.fecha_devolucion, p.cantidad, p.observacion, p.estado,
                    u.id_usuario AS usuario_id, u.nombre,
                    l.id_libro AS libro_id, l.titulo
                FROM prestamos p
                INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
                INNER JOIN libros l ON p.id_libro = l.id_libro
                ORDER BY p.fecha_prestamo DESC";
        $res = $this->selectAll($sql);
        return $res;
    }

    // Insertar nuevo prestamo
    public function insertPrestamo ($user, $libro, $cantidad, string $fechaPrestamo, string $fechaDevolucion, string $observacion)
    {
        // Verificar existencia de préstamo activo
        $verificar = "SELECT * FROM prestamos WHERE id_libro = ? AND id_usuario = ? AND estado = 1";
        $existe = $this->select($verificar, array($libro, $user));
        if (!empty($existe)) {
            return "existe";
        }

        // Comprobar stock del libro
        $libroSql = "SELECT cantidad FROM libros WHERE id_libro = ?";
        $resLibro = $this->select($libroSql, array($libro));
        if (empty($resLibro) || $resLibro['cantidad'] < $cantidad) {
            return 0; // No hay stock suficiente
        }

        // Uso de transacción
        try {
            if (isset($this->conn) && method_exists($this->conn, 'beginTransaction')) {
                $this->conn->beginTransaction();
            }

            $query = "INSERT INTO prestamos(id_usuario, id_libro, fecha_prestamo, fecha_devolucion, cantidad, observacion) VALUES (?,?,?,?,?,?)";
            $datos = array($user, $libro, $fechaPrestamo, $fechaDevolucion, $cantidad, $observacion);
            $insertId = $this->insert($query, $datos);

            if ($insertId > 0) {
                $total = $resLibro['cantidad'] - $cantidad;
                $libroUpdate = "UPDATE libros SET cantidad = ? WHERE id_libro = ?";
                $datosLibro = array($total, $libro);
                $this->save($libroUpdate, $datosLibro);

                if (isset($this->conn) && method_exists($this->conn, 'commit')) {
                    $this->conn->commit();
                }
                return $insertId;
            }

            if (isset($this->conn) && method_exists($this->conn, 'rollBack')) {
                $this->conn->rollBack();
            }
            return 0;
        } catch (\Exception $e) {
            if (isset($this->conn) && method_exists($this->conn, 'rollBack')) {
                $this->conn->rollBack();
            }
            // Log $e->getMessage() según corresponda
            return 0;
        }
    }

    // Actualizar prestamo
    public function updatePrestamo ($estado, $id)
    {
        $sql = "UPDATE prestamos SET estado = ? WHERE id_prestamo = ?";
        $datos = array($estado, $id);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $prestamoSql = "SELECT * FROM prestamos WHERE id_prestamo = ?";
            $resPrestamo = $this->select($prestamoSql, array($id));
            $id_libro = $resPrestamo['id_libro'];
            $libroSql = "SELECT cantidad FROM libros WHERE id_libro = ?";
            $resLibro = $this->select($libroSql, array($id_libro));
            $total = $resLibro['cantidad'] + $resPrestamo['cantidad'];
            $libroUpdate = "UPDATE libros SET cantidad = ? WHERE id_libro = ?";
            $datosLibro = array($total, $id_libro);
            $this->save($libroUpdate, $datosLibro);
            return "ok";
        } else {
            return "error";
        }
    }

    // Obtener cantidad de libros
    public function getCantLibro($libro)
    {
        $sql = "SELECT * FROM libros WHERE id_libro = $libro";
        $res = $this->select($sql);
        return $res;
    }

    // Obtener prestamos pendientes
    public function selectPrestamoDebe()
    {
        $sql = "SELECT u.id_usuario, u.nombre, l.id_libro, l.titulo, p.id_prestamo, p.id_usuario, p.id_libro, p.fecha_prestamo, p.fecha_devolucion, p.cantidad, p.observacion, p.estado FROM usuarios u INNER JOIN libros l INNER JOIN prestamos p ON p.id_usuario = u.id_usuario WHERE p.id_libro = l.id_libro AND p.estado = 1 ORDER BY u.nombre ASC";
        $res = $this->selectAll($sql);
        return $res;
    }

    // Obtener prestamo por ID
    public function getPrestamoLibro($id_prestamo)
    {
        $sql = "SELECT u.id_usuario, u.nombre, l.id_libro, l.titulo, p.id_prestamo, p.id_usuario, p.id_libro, p.fecha_prestamo, p.fecha_devolucion, p.cantidad, p.observacion, p.estado FROM usuarios u INNER JOIN libros l INNER JOIN prestamos p ON p.id_usuario = u.id_usuario WHERE p.id_libro = l.id_libro AND p.id_prestamo = $id_prestamo";
        $res = $this->select($sql);
        return $res;
    }

    // Eliminar prestamo, marca estado = 0 y restaura stock si estaba activo
    public function deletePrestamo($id)
    {
        // Verificar que exista el prestamo
        $sql = "SELECT * FROM prestamos WHERE id_prestamo = ?";
        $res = $this->select($sql, array($id));
        if (empty($res)) {
            return "error"; // no existe
        }

        try {
            if (isset($this->conn) && method_exists($this->conn, 'beginTransaction')) {
                $this->conn->beginTransaction();
            }

            // Si el prestamo está activo, restaurar stock del libro
            if (isset($res['estado']) && $res['estado'] == 1) {
                $id_libro = $res['id_libro'];
                $cantidadPrestada = $res['cantidad'];

                $libroSql = "SELECT cantidad FROM libros WHERE id_libro = ?";
                $resLibro = $this->select($libroSql, array($id_libro));
                if (empty($resLibro)) {
                    if (isset($this->conn) && method_exists($this->conn, 'rollBack')) {
                        $this->conn->rollBack();
                    }
                    return "error";
                }

                $total = $resLibro['cantidad'] + $cantidadPrestada;
                $libroUpdate = "UPDATE libros SET cantidad = ? WHERE id_libro = ?";
                $this->save($libroUpdate, array($total, $id_libro));
            }

            // Marcar prestamo como eliminado (estado = 0)
            $updateSql = "UPDATE prestamos SET estado = ? WHERE id_prestamo = ?";
            $this->save($updateSql, array(0, $id));

            if (isset($this->conn) && method_exists($this->conn, 'commit')) {
                $this->conn->commit();
            }
            return "ok";
        } catch (\Exception $e) {
            if (isset($this->conn) && method_exists($this->conn, 'rollBack')) {
                $this->conn->rollBack();
            }
            // Loguear $e->getMessage() si es necesario
            return "error";
        }
    }
}
