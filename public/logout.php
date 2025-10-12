<?php

use App\CmsPhp\controllers\AuthController;

require_once '../vendor/autoload.php';
require_once '../config/database.php';



$database = new Database();
$db = $database->getConnection();

$auth = new AuthController($db);

// Obtener el nombre de usuario antes de cerrar sesión
$username = $_SESSION['username'] ?? 'Usuario';

// Cerrar sesión
$auth->logout();

$pageTitle = "Sesión cerrada";
include_once '../app/views/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                </div>
                <h3 class="card-title">Sesión cerrada</h3>
                <p class="card-text">Has cerrado sesión correctamente. ¡Hasta pronto, <?php echo htmlspecialchars($username); ?>!</p>
                <div class="mt-4">
                    <a href="index.php" class="btn btn-primary me-2">Ir al inicio</a>
                    <a href="login.php" class="btn btn-outline-primary">Iniciar sesión nuevamente</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../app/views/footer.php'; ?>