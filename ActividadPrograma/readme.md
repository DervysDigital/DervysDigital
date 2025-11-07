# Mini Programa: Registro de Usuario con Validación, Condicional y Ciclo

## Descripción general

Este mini programa fue desarrollado en **PHP** y tiene como propósito **registrar un usuario a través de un formulario web**, aplicando **validaciones de entrada** y **buenas prácticas de seguridad** antes de guardar los datos en una base de datos MySQL.

El código combina **una estructura condicional** (`if`) y **un ciclo** (`while`), cumpliendo los requisitos básicos de control de flujo en programación, al tiempo que resuelve un problema común en entornos empresariales: **la validación y sanitización de datos de usuario antes del registro**.

---

## ⚙️ Características principales

- Formulario HTML para ingresar **nombre de usuario**, **correo electrónico** y **contraseña**.
- Validación de los datos del usuario antes de almacenarlos.
- Uso de un **ciclo `while`** para limitar los intentos de registro.
- Uso de **condicionales `if/else`** para verificar que los datos sean válidos.
- **Sanitización de entradas** con funciones seguras (`htmlspecialchars`, `filter_var`, `trim`).
- **Encriptación de contraseñas** usando `password_hash()` antes de guardarlas.
- Inserción segura a base de datos mediante **consultas preparadas (`prepare`, `bind_param`)**, evitando inyecciones SQL.

---

## 🧠 Lógica del programa

1. El usuario llena el formulario (`formulario.html`) y envía los datos mediante el método `POST`.
2. El archivo `registrar.php` recibe los datos.
3. Se limpian y validan los campos:
   - El usuario no debe estar vacío.
   - El correo debe tener un formato válido.
   - La contraseña debe tener al menos 6 caracteres.
4. Si los datos son correctos:
   - Se encripta la contraseña.
   - Se insertan los datos en la base de datos.
5. Si son incorrectos:
   - Se muestra un mensaje de error y se incrementa el contador de intentos.
6. Después de 3 intentos fallidos, el registro se bloquea temporalmente.

---

## 🗄️ Configuración de la base de datos

Antes de ejecutar el programa, crea la base de datos y la tabla correspondiente en **MySQL**:

```sql
CREATE DATABASE empresa;
USE empresa;

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(50) NOT NULL,
  correo VARCHAR(100) NOT NULL UNIQUE,
  clave VARCHAR(255) NOT NULL
);
