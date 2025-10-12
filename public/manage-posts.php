<?php

use App\CmsPhp\controllers\AuthController;
use App\CmsPhp\controllers\PostController;
use App\CmsPhp\core\Helpers;

require_once '../vendor/autoload.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$auth = new AuthController($db);

if(!$auth->isLoggedIn() || !$auth->isAuthor()){
    header("Location: login.php");
    exit;
}

$postController = new PostController($db);
$posts = $postController->getAll();

// Eliminar post 
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    if($postController->delete($id)){
        header("Location: manage-posts.php?delete=1");
        exit;
    }
}

$pageTitle = "Gestionar Posts";
include_once '../app/views/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestionar Posts</h2>
    <a href="crear-post.php" class="btn btn-primary">Nuevo Post</a>
</div>

<?php if(isset($_GET['delete'])): ?>
    <div class="alert alert-success">Post eliminado correctamente</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($posts as $post): ?>
                        <tr>
                            <td><?php echo $post['title']; ?></td>
                            <td><?php echo $post['username']; ?></td>
                            <td>
                                <span class="badge bg-<?php echo $post['status'] === 'published' ? 'success' : 'warning'; ?>"><?php echo $post['status'] === 'published' ? 'Publicado' : 'Borrador'; ?></span>
                            </td>
                            <td><?php echo Helpers::formatDate($post['created_at']); ?></td>
                            <td>
                                <a href="edit-post.php?id=<?php echo $post['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="manage-posts.php?delete=<?php echo $post['id']; ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar este post?')">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include_once '../app/views/footer.php'; ?>
