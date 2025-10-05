<?php

use App\CmsPhp\controllers\AuthController;
use App\CmsPhp\controllers\PostController;
use App\CmsPhp\core\Helpers;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();

$auth = new AuthController($db);
$postController = new PostController($db);

$posts = $postController->getPublished();

$pageTitle = 'Inicio';
include_once '../app/views/header.php';

?>

<div class="jumbotron bg-light p-5 rounded mb-4">
    <h1 class="display-4">Sistema de Gestión de Contenidos</h1>
    <p class="lead">Este es un CMS desarrollado con PHP, MySQL y Bootstrap para la asignatura de Desarrollo de Aplicaciones Web II</p>
    <hr class="my-4">
    <p>Explora nuestro blog o regístrate para comenzar a gestionar contenido.</p>
    <a href="posts.php" class="btn btn-primary btn-lg">Ver Blog</a>
    <?php if(!$auth->isLoggedIn()): ?>
        <a href="register.php" class="btn btn-success btn-lg">Regístrate</a>
    <?php endif; ?>
</div>

<h2 class="mb-4">Últimos posts</h2>
<div class="row">
    <?php foreach(array_slice($posts, 0, 3) as $post): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <?php if($post['image']): ?>
                    <img src="assets/uploads/<?php echo $post['image']; ?>" alt="<?php echo $post['title']; ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $post['title']; ?></h5>
                    <p class="card-text"><?php echo Helpers::truncate($post['excerpt']) ? : $post['content']; ?></p>
                    <p class="text-muted">Por: <?php echo $post['username']; ?> el <?php echo Helpers::formatDate($post['created_at']); ?></p>
                </div>
                <div class="card-footer">
                    <a href="post.php?slug=<?php echo $post['id']; ?>" class="btn btn-primary">Leer más</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include_once '../app/views/footer.php'; ?>
