<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Usuario</title>
</head>
<body>
  <h2>Registro de Usuario</h2>
  <form action="registrar.php" method="POST">
    <label>Nombre de usuario:</label>
    <input type="text" name="usuario" required><br><br>

    <label>Correo electrónico:</label>
    <input type="email" name="correo" required><br><br>

    <label>Contraseña:</label>
    <input type="password" name="clave" required><br><br>

    <input type="submit" value="Registrar">
  </form>
</body>
</html>
