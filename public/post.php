<?php
require_once '../vendor/autoload.php';
require_once '../config/database.php';

use App\CmsPhp\controllers\AuthController;
use App\CmsPhp\controllers\CommentController;
use App\CmsPhp\controllers\PostController;
use App\CmsPhp\core\Helpers;
use Parsedown;

$database = new Database();
$db = $database->getConnection();

$postController = new PostController($db);
$commentController = new CommentController($db);
$auth = new AuthController($db);

$post = null;
if(isset($_GET['slug'])) {
    $slug = $_GET['slug'];
    $post = $postController->getBySlug($slug);
}

if(!$post) {
    header("Location: posts.php");
    exit;
}

$comments = $commentController->getByPost($post['id']);

// Procesar nuevo comentario
if($_POST && isset($_POST['content'])) {
    $content = Helpers::sanitize($_POST['content']);
    $user_id = $auth->isLoggedIn() ? $_SESSION['user_id'] : null;
    
    if($user_id) {
        $commentController->create($post['id'], $user_id, $content, 'approved');
    } else {
        // Para usuarios no registrados, podrías pedir nombre y email
        // Por simplicidad, aquí solo permitimos usuarios registrados
        header("Location: login.php");
        exit;
    }
    
    // Recargar la página para mostrar el nuevo comentario
    header("Location: post.php?slug=" . $post['id']);
    exit;
}

$pageTitle = $post['title'];
include_once '../app/views/header.php';

// Usar Parsedown para convertir markdown a HTML
$parsedown = new Parsedown();
$content = $parsedown->text($post['content']);
?>

<article class="blog-post">
    <?php if($post['image']): ?>
    <img src="assets/uploads/<?php echo $post['image']; ?>" class="img-fluid rounded mb-4" alt="<?php echo $post['title']; ?>">
    <?php endif; ?>
    
    <h2 class="blog-post-title"><?php echo $post['title']; ?></h2>
    <p class="blog-post-meta text-muted">
        Por <?php echo $post['username']; ?> el <?php echo Helpers::formatDate($post['created_at']); ?>
    </p>
    
    <div class="blog-post-content">
        <?php echo $content; ?>
    </div>
</article>

<hr>

<div class="comments-section">
    <h3>Comentarios</h3>
    
    <?php if($auth->isLoggedIn()): ?>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Deja un comentario</h5>
            <form method="post">
                <div class="mb-3">
                    <textarea class="form-control" id="content" name="content" rows="3" required placeholder="Escribe tu comentario aquí..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Publicar comentario</button>
            </form>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-info">
        <a href="login.php">Inicia sesión</a> para dejar un comentario.
    </div>
    <?php endif; ?>
    
    <?php if(count($comments) > 0): ?>
        <?php foreach($comments as $comment): ?>
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <h6 class="card-subtitle mb-2 text-muted"><?php echo $comment['username']; ?></h6>
                    <small class="text-muted"><?php echo Helpers::formatDate($comment['created_at']); ?></small>
                </div>
                <p class="card-text"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aún no hay comentarios. ¡Sé el primero en comentar!</p>
    <?php endif; ?>
</div>

<?php
include_once '../app/views/footer.php';
?>