<?php

use App\CmsPhp\controllers\AuthController;
use App\CmsPhp\controllers\CommentController;
use App\CmsPhp\controllers\PostController;

require_once '../vendor/autoload.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$auth = new AuthController($db);

if(!$auth->isLoggedIn()){
    header("Location: login.php");
    exit;
}

$post = new PostController($db);
$comment = new CommentController($db);

$posts = $post->getAll();

$publishedPosts = array_filter($posts, function($post){
    return $post['status'] === 'published';
});

$draftPosts = array_filter($posts, function($post){
    return $post['status'] === 'draft';
});

$pageTitle = "Dashboard";
include_once '../app/views/header.php';
?>

<h2>Dashboard</h2>
<p>Bienvenido al panel de Administración</p>

<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Posts</h5>
                <p class="card-text"><?php echo count($posts); ?> publicaciones</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Publicados</h5>
                <p class="card-text"><?php echo count($publishedPosts); ?> publicaciones</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title">Borradores</h5>
                <p class="card-text"><?php echo count($draftPosts); ?> borradores</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3">
            <div class="card-body">
                <h5 class="card-title">Comentarios</h5>
                <p class="card-text"><?php echo $comment->getPendingCount(); ?> pendientes</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Posts Recientes</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach(array_slice($posts, 0,5) as $post): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="badge bg-<?php echo $post['status'] === 'published' ? 'success' : 'warning'; ?>">
                                <?php echo $post['status'] === 'published' ? 'Publicado' : 'Borrador'; ?>
                            </span>
                            <a href="post.php?id=<?php echo $post['id']; ?>"><?php echo $post['title']; ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Acciones Rápidas</h5>
            </div>
            <div class="card-body">
                <div class="d-grip gap-2">
                    <a href="create-post.php" class="btn btn-primary">Crear nuevo post</a>
                    <?php if($auth->isAdmin()): ?>
                        <a href="manage-comments.php" class="btn btn-secondary">Gestionar comentarios</a>
                    <?php endif; ?>
                    <a href="posts.php" class="btn btn-info">Ver blog</a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include_once '../app/views/footer.php'; ?>
