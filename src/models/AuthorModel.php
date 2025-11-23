<?php


use app\Query;

// Modelo de Autores
class AuthorModel extends Query {
    
    // Constructor
    public function __construct()
    {
        parent::__construct();
    }

    // Obtener todos los autores
    public function getAuthor()
    {
        $sql = "SELECT * FROM autores";
        $res = $this->selectAll($sql);
        return $res;
    }

    // Insertar un nuevo autor
    public function insertAuthor($author)
    {
        // Verificar existencia usando parámetro para evitar SQL injection
        $verificar = "SELECT * FROM autores WHERE nombre = ?";
        $existe = $this->select($verificar, array($author));
        if (!empty($existe)) {
            return "existe";
        }

        $query = "INSERT INTO autores(nombre) VALUES (?)";
        $datos = array($author);
        $data = $this->save($query, $datos);
        return $data === 1 ? "insertado correctamente" : "error";
    }

    // Obtener datos de un autor para editar
    public function editAuthor($id)
    {
        $sql = "SELECT * FROM autores WHERE id_autor = ?";
        $res = $this->select($sql, array($id));
        return $res;
    }

    // Actualizar datos de un autor
    public function updateAuthor($author,$id)
    {
        $query = "UPDATE autores SET nombre = ? WHERE id_autor = ?";
        $datos = array($author, $id);
        $data = $this->save($query, $datos);
        return $data == 1 ? "modificado" : "error";
    }

    // Eliminar un autor
    public function deleteAuthor($id)
    {
        $sql = "DELETE FROM autores WHERE id_autor = ?";
        $datos = array($id);
        $data = $this->save($sql, $datos);
        return $data;
    }
}
