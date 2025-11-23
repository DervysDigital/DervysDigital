# Biblioteca - Sistema de Gestión

## Descripción

Este proyecto es un **sistema de gestión de biblioteca** desarrollado en **PHP** y **MySQL**, utilizando **programación orientada a objetos (POO)** y la arquitectura **MVC**.
Permite administrar y gestionar:

- Libros
- Usuarios
- Préstamos
- Autores

Actualmente, la parte principal del sistema como lo es la logica de negocio esta creada, con la ausencia de las vistas.

## Tecnologías utilizadas

- PHP 7+
- MySQL
- HTML, CSS, JavaScript (para las vistas)
- Arquitectura MVC
- Programación orientada a objetos (POO)

## Características principales

- Gestión de libros: agregar, editar, eliminar y listar libros.
- Gestión de usuarios: registro, actualización, eliminación y listado de usuarios.
- Gestión de autores: agregar, editar, eliminar y listar autores.
- Gestión de préstamos: registrar préstamos, devoluciones y control de estado de los libros.
- Seguridad básica mediante sesiones y control de acceso a los módulos.

## Estructura del proyecto

/config
app.php
server.php
connection.php
/src
 /app
   Controller.php
   Query.php
   Views.php
 /Controllers
   Author.php
   Ebook.php
   Prestamos.php
   User.php
 /Models
   AuthorModel.php
   UserModel.php
   PrestamosModel.php
   EbookModel.php
index.php
README.md
/vendor