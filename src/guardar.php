<?php

$host = getenv('DB_HOST');
$baseDatos = getenv('DB_NAME');
$usuario = getenv('DB_USER');
$contrasena = getenv('DB_PASSWORD');

$mensaje = "";
$errorListado = "";
$usuarios = [];
$conexion = null;

try {

    $conexion = new PDO(
        "mysql:host=$host;dbname=$baseDatos;charset=utf8mb4",
        $usuario,
        $contrasena,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    if($_SERVER["REQUEST_METHOD"] === "POST") {
        $nombre = trim($_POST["nombre"] ?? "");
        $email = trim($_POST["email"] ?? "");

        if($nombre === "" || $email === ""){
            $mensaje = "Debes completar todos los campos";
        } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $mensaje = "El correo electronico no es valido";
        } else {
            $sql = $conexion->prepare(
                "INSERT INTO datosUsuario(nombre, email) VALUES(?, ?)"
            );

            $sql->execute([$nombre, $email]);
            $mensaje = "Datos guardados correctamente";
        }

    }

    $usuarios = $conexion->query(
        "SELECT id, nombre, email, fecha FROM datosUsuario ORDER BY id DESC"
    )->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    error_log($e->getMessage());
    $mensaje = "Error: no se pudo conectar con la base de datos";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios registrados</title>
</head>
<body>

    <h1>Resultado del formulario</h1>

    <p><?= htmlspecialchars($mensaje, ENT_QUOTES, "UTF-8")?></p>

    <h2>Usuarios registrados</h2>

    <table border="1">
        <tr>
            <th>Id</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Fecha</th>
        </tr>
        <?php foreach($usuarios ?? [] as $usuario) { ?>
        
            <tr>
                <td><?= htmlspecialchars($usuario["id"], ENT_QUOTES, "UTF-8")?></td>
                <td><?= htmlspecialchars($usuario["nombre"], ENT_QUOTES, "UTF-8")?></td>
                <td><?= htmlspecialchars($usuario["email"], ENT_QUOTES, "UTF-8")?></td>
                <td><?= htmlspecialchars($usuario["fecha"], ENT_QUOTES, "UTF-8")?></td>
            </tr>

        <?php }?>
    </table>

    <p><a href="index.html">Volver al formulario</a></p>

</body>
</html>
