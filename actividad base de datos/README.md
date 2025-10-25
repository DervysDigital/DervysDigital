# Sistema de Concesionario de Autos

## Objetivo del proyecto

Diseñar y documentar una base de datos para un **concesionario de autos**, registrando información de **clientes, autos, vendedores, ventas, comentarios, fotos, características y etiquetas**.
Se implementan relaciones clásicas y polimórficas, aplicando normalización hasta la **3FN**.

## Modelo de negocio

- **Clientes** compran **autos**.
- **Vendedores** registran las ventas.
- Cada **auto** puede venderse solo una vez.
- Los **comentarios, fotos y etiquetas** pueden asociarse a diferentes entidades mediante relaciones polimórficas.

## Análisis de requerimientos (simulación de entrevista)

| Pregunta | | Respuesta simulada |

| ¿Qué información necesitamos de los clientes? | | Nombre, apellido, teléfono |
| ¿Qué información necesitamos de los autos? | | Marca, modelo, año, color |
| ¿Qué información necesitamos de los vendedores? | | Nombre, apellido |
| ¿Qué información necesitamos de las ventas? | | Fecha, precio, auto, cliente, vendedor |
| ¿Qué relaciones existen? | | Cliente > Venta (N:1), Auto > Venta (1:1), Vendedor > Venta (N:1) |
| ¿Necesitamos relaciones polimórficas? | | Sí, para comentarios, fotos y etiquetas aplicables a varias entidades |

## Diseño de la Base de Datos

### -Entidades, atributos y tipos de datos

| Tabla       | Atributos                                      | Tipo             | Clave        | Descripción |

| Cliente     | id_cliente, nombre, apellido, telefono        | INT, VARCHAR    | PK          | Identificador único del cliente y datos de contacto |
| Vendedor    | id_vendedor, nombre, apellido                 | INT, VARCHAR    | PK          | Identificador único del vendedor |
| Auto        | id_auto, marca, modelo, ano, color            | INT, VARCHAR, INT | PK        | Identificador único del auto y sus características |
| Placa       | id_placa, numero_placa, id_auto               | INT, VARCHAR, INT | PK, FK     | Cada auto tiene una placa única (1:1) |
| Venta       | id_venta, id_auto, id_cliente, id_vendedor, fecha_venta, precio | INT, INT, INT, INT, DATE, DECIMAL | PK, FK | Registro de ventas; relaciones con Auto, Cliente y Vendedor |
| Caracteristica | id_caracteristica, descripcion             | INT, VARCHAR    | PK          | Descripción de características de autos |
| Auto_Caracteristica | id_auto, id_caracteristica             | INT, INT        | PK (combinada), FK | Relación N:M entre Auto y Característica |
| Comentario  | id_comentario, texto, comentable_id, comentable_type | INT, TEXT, INT, VARCHAR | PK | Comentarios polimórficos (1:N o N:1) |
| Foto        | id_foto, url, fotoable_id, fotoable_type      | INT, VARCHAR, INT, VARCHAR | PK | Fotos polimórficas (1:1) |
| Etiqueta    | id_etiqueta, descripcion                       | INT, VARCHAR    | PK          | Lista de etiquetas posibles |
| Etiqueta_Asignada | etiqueta_id, etiquetable_id, etiquetable_type | INT, INT, VARCHAR | PK compuesta | Relación N:M polimórfica para etiquetas |

### -Relaciones

| Tipo de relación | Ejemplo en autos | Implementación |

| 1 a 1           | Auto <-> Placa | FK `id_auto` en `Placa` con UNIQUE |
| N a 1           | Venta > Cliente | FK `id_cliente` en `Venta` |
| N a N           | Auto <-> Característica | Tabla intermedia `Auto_Caracteristica` |
| 1 a 1 polimórfica | Foto > Auto o Cliente | UNIQUE sobre `(fotoable_id, fotoable_type)` |
| N a 1 polimórfica | Comentario > Auto o Cliente | `comentable_id` y `comentable_type` |
| N a N polimórfica | Etiqueta <-> Auto o Cliente | Tabla `Etiqueta_Asignada` con `etiquetable_type` |

## Normalización (3FN)

1. **1FN:** Cada columna contiene un solo valor atómico.
2. **2FN:** Todos los atributos dependen completamente de la clave primaria de cada tabla.
3. **3FN:** No hay dependencias transitivas; toda información redundante se guarda en su propia tabla.

## Script SQL

```sql
-- Tabla Cliente
CREATE TABLE Cliente (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    telefono VARCHAR(20)
);

-- Tabla Vendedor
CREATE TABLE Vendedor (
    id_vendedor INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL
);

-- Tabla Auto
CREATE TABLE Auto (
    id_auto INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(50) NOT NULL,
    modelo VARCHAR(50) NOT NULL,
    ano INT NOT NULL,
    color VARCHAR(20)
);

-- Relación 1 a 1: Auto ↔ Placa
CREATE TABLE Placa (
    id_placa INT AUTO_INCREMENT PRIMARY KEY,
    numero_placa VARCHAR(20) NOT NULL,
    id_auto INT UNIQUE,
    FOREIGN KEY (id_auto) REFERENCES Auto(id_auto)
);

-- Relación N a 1: Venta → Cliente y Vendedor, 1:1 con Auto
CREATE TABLE Venta (
    id_venta INT AUTO_INCREMENT PRIMARY KEY,
    id_auto INT UNIQUE,
    id_cliente INT NOT NULL,
    id_vendedor INT NOT NULL,
    fecha_venta DATE NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_auto) REFERENCES Auto(id_auto),
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id_cliente),
    FOREIGN KEY (id_vendedor) REFERENCES Vendedor(id_vendedor)
);

-- Relación N:M: Auto ↔ Característica
CREATE TABLE Caracteristica (
    id_caracteristica INT AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE Auto_Caracteristica (
    id_auto INT NOT NULL,
    id_caracteristica INT NOT NULL,
    PRIMARY KEY(id_auto, id_caracteristica),
    FOREIGN KEY(id_auto) REFERENCES Auto(id_auto),
    FOREIGN KEY(id_caracteristica) REFERENCES Caracteristica(id_caracteristica)
);

-- Relaciones polimórficas

-- 1 a 1 polimórfica: Foto
CREATE TABLE Foto (
    id_foto INT AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(255) NOT NULL,
    fotoable_id INT NOT NULL,
    fotoable_type VARCHAR(50) NOT NULL,
    UNIQUE(fotoable_id, fotoable_type)
);

-- N a 1 polimórfica: Comentario
CREATE TABLE Comentario (
    id_comentario INT AUTO_INCREMENT PRIMARY KEY,
    texto TEXT NOT NULL,
    comentable_id INT NOT NULL,
    comentable_type VARCHAR(50) NOT NULL
);

-- N a N polimórfica: Etiqueta ↔ Auto o Cliente
CREATE TABLE Etiqueta (
    id_etiqueta INT AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(50) NOT NULL
);

CREATE TABLE Etiqueta_Asignada (
    etiqueta_id INT NOT NULL,
    etiquetable_id INT NOT NULL,
    etiquetable_type VARCHAR(50) NOT NULL,
    PRIMARY KEY(etiqueta_id, etiquetable_id, etiquetable_type),
    FOREIGN KEY(etiqueta_id) REFERENCES Etiqueta(id_etiqueta)
);
