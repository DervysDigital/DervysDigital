<?php

namespace app;

use PDO; //importamos la clase PDO para manejo de base de datos
use config\Connection; //importamos la clase Connection del espacio de nombres Config

// Clase Query para manejar consultas a la base de datos
class Query extends Connection {

    // Atributos para la conexion y consultas
    protected $pdo, $conn, $sql, $datos;

    // Constructor para inicializar la conexion
    public function __construct() {
        $this->pdo = new Connection();

        $this->conn = $this->pdo->conn;
    }

    // Métodos para ejecutar consultas
    public function select(string $sql)
    {
        $this->sql = $sql;
        $resul = $this->conn->prepare($this->sql);
        $resul->execute();
        $data = $resul->fetch(PDO::FETCH_ASSOC);
        return $data;
    }

    // Método para seleccionar múltiples registros
    public function selectAll(string $sql)
    {
        $this->sql = $sql;
        $resul = $this->conn->prepare($this->sql);
        $resul->execute();
        $data = $resul->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    // Método para ejecutar consultas de inserción, actualización o eliminación
    public function save(string $sql, array $datos)
    {
        $this->sql = $sql;
        $this->datos = $datos;
        $insert = $this->conn->prepare($this->sql);
        $data = $insert->execute($this->datos);
        if ($data) {
            $res = 1;
        }else{
            $res = 0;
        }
        return $res;
    }

    // Método para insertar un nuevo registro y obtener su ID
    public function insert(string $sql, array $datos)
    {
        $this->sql = $sql;
        $this->datos = $datos;
        $insert = $this->conn->prepare($this->sql);
        $data = $insert->execute($this->datos);
        if ($data) {
            $res = $this->conn->lastInsertId();;
        } else {
            $res = 0;
        }
        return $res;
    }
}