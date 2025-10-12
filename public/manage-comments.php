<?php

use App\CmsPhp\controllers\AuthController;
use App\CmsPhp\controllers\CommentController;
use App\CmsPhp\core\Helpers;

require_once '../vendor/autoload.php';
require_once '../config/database.php';


$database = new Database();
$db = $database->getConnection();

$auth = new AuthController($db);

if(!$auth->isLoggedIn() || !$auth->isAdmin()) {
    header("Location: login.php");
    exit;
}

$commentController = new CommentController($db);
$comments = $commentController->getAll();

// Aprobar comentario
if(isset($_GET['approve'])) {
    $id = $_GET['approve'];
    if($commentController->approve($id)) {
        header("Location: manage-comments.php?approved=1");
        exit;
    }
}

// Eliminar comentario
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if($commentController->delete($id)) {
        header("Location: manage-comments.php?deleted=1");
        exit;
    }
}

$pageTitle = "Gestionar Comentarios";
include_once '../app/views/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestionar Comentarios</h2>
</div>

<?php if(isset($_GET['approved'])): ?>
<div class="alert alert-success">Comentario aprobado correctamente.</div>
<?php endif; ?>

<?php if(isset($_GET['deleted'])): ?>
<div class="alert alert-success">Comentario eliminado correctamente.</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Post</th>
                        <th>Usuario</th>
                        <th>Comentario</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($comments as $comment): ?>
                    <tr>
                        <td><?php echo $comment['post_id']; ?></td>
                        <td><?php echo $comment['username']; ?></td>
                        <td><?php echo Helpers::truncate($comment['content'], 50); ?></td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $comment['status'] === 'approved' ? 'success' : 
                                    ($comment['status'] === 'pending' ? 'warning' : 'danger'); 
                            ?>">
                                <?php echo $comment['status'] === 'approved' ? 'Aprobado' : 
                                    ($comment['status'] === 'pending' ? 'Pendiente' : 'Spam'); 
                                ?>
                            </span>
                        </td>
                        <td><?php echo Helpers::formatDate($comment['created_at']); ?></td>
                        <td>
                            <?php if($comment['status'] === 'pending'): ?>
                            <a href="manage-comments.php?approve=<?php echo $comment['id']; ?>" class="btn btn-sm btn-success">Aprobar</a>
                            <?php endif; ?>
                            <a href="manage-comments.php?delete=<?php echo $comment['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este comentario?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
include_once '../app/views/footer.php';
?>