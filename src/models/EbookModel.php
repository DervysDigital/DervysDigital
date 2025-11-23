<?php

use app\Query;

// Modelo de Libros
class EbookModel extends Query{

    // Constructor
    public function __construct()
    {
        parent::__construct();
    }

   // Insertar libros
    public function insertEbook($titulo, $idAutor, $isbn, $cantidad)
    {
        // Validaciones
        if (empty($titulo) || empty($idAutor) || empty($isbn) || empty($cantidad)) {
            return "campos_vacios";
        }
        // Evitar inyección SQL usando parámetros
        $verificar = "SELECT * FROM libros WHERE titulo = ?";
        $existe = $this->select($verificar, [$titulo]);
        if (!empty($existe)) {
            return "existe";
        }
        $query = "INSERT INTO libros(titulo, id_autor, ISBN, cantidad, estado) VALUES (?,?,?,?,?)";
        $datos = array($titulo, $idAutor, $isbn, $cantidad, 'disponible');
        $data = $this->save($query, $datos);
        if ($data == 1) {
            return "ok";
        }
        return "error";
    }

    // Obtener libro por ID
    public function getEbookById($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return null;
        }
        $sql = "SELECT * FROM libros WHERE id_libro = ?";
        $res = $this->select($sql, [$id]);
        return $res;
    }

    // Listar todos los libros
    public function getAllEbooks()
    {
        $sql = "SELECT * FROM libros";
        $res = $this->selectAll($sql);
        return $res;
    }

    // Actualizar libro
    public function updateEbook($titulo, $idAutor, $isbn, $cantidad, $id)
    {
        if (empty($titulo) || empty($idAutor) || empty($isbn) || empty($cantidad) || empty($id)) {
            return "campos_vacios";
        }
        $query = "UPDATE libros SET titulo = ?, id_autor = ?, ISBN = ?, cantidad = ? WHERE id_libro = ?";
        $datos = array($titulo, $idAutor, $isbn, $cantidad, $id);
        $data = $this->save($query, $datos);
        $res = "error";
        if ($data == 1) {
            $res = "modificado";
        }
        return $res;
    }

    // Cambiar estado del libro (disponible/prestado)
    public function stateEbook($estado, $id)
    {
        $estadosValidos = ['disponible', 'prestado'];
        if (!in_array($estado, $estadosValidos) || !is_numeric($id) || $id <= 0) {
            return "estado_invalido";
        }
        $query = "UPDATE libros SET estado = ? WHERE id_libro = ?";
        $datos = array($estado, $id);
        $data = $this->save($query, $datos);
        return $data;
    }

    // Eliminar libro por ID
    public function deleteEbook($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return "id_invalido";
        }
        $sql = "DELETE FROM libros WHERE id_libro = ?";
        $datos = array($id);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            return "eliminado";
        }
        return "error";
    }

}