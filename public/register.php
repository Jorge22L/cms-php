<?php

use App\CmsPhp\controllers\AuthController;

require_once '../vendor/autoload.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$auth = new AuthController($db);

if($auth->isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$success = '';

if($_POST) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validaciones básicas
    if(empty($username) || empty($email) || empty($password)) {
        $error = "Todos los campos son obligatorios";
    }
    elseif($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden";
    }
    elseif(strlen($password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres";
    }
    else {
        if($auth->register($username, $email, $password)) {
            $success = "Cuenta creada correctamente. Ahora puedes iniciar sesión.";
            // Limpiar formulario
            $username = $email = '';
        } else {
            $error = "Error al crear la cuenta. El email o usuario ya existen.";
        }
    }
}

$pageTitle = "Registrarse";
include_once '../app/views/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">Crear Cuenta</h3>
            </div>
            <div class="card-body">
                <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="post">
                    
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Nombre de usuario</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="form-text">Mínimo 6 caracteres</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmar contraseña</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Registrarse</button>
                </form>
                
                <div class="text-center mt-3">
                    <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../app/views/footer.php'; ?>