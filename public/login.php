<?php

require_once '../vendor/autoload.php';
require_once '../config/database.php';

use App\CmsPhp\controllers\AuthController;


$database = new Database();
$db = $database->getConnection();

$auth = new AuthController($db);

if($auth->isLoggedIn()){
    header("Location: dashboard.php");
    exit;
}

$error = "";

if($_POST){
    $email = $_POST['email'];
    $password = $_POST['password'];

    if($auth->login($email, $password)){
        header("Location: dashboard.php");
        exit;
    }
    else{
        $error = "Email o contraseña incorrectos";
    }
}

$pageTitle = "Iniciar Sesión";
include_once '../app/views/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card mb-5">
            <div class="card-header">
                <h3 class="text-center">Iniciar Sesión</h3>
            </div>
        </div>

        <div class="card-body">
            <?php if($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" name="password" id="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
            </form>

            <div class="text-center mt-3">
                <p>¿No tienes cuenta? <a href="register.php">Regístrate</a></p>
            </div>
        </div>
    </div>
</div>

<?php include_once '../app/views/footer.php'; ?>
