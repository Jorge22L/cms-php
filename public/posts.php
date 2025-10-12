<?php
require_once '../vendor/autoload.php';
require_once '../config/database.php';

use App\CmsPhp\controllers\PostController;
use App\CmsPhp\core\Helpers;

$database = new Database();
$db = $database->getConnection();

$postController = new PostController($db);
$posts = $postController->getPublished();

$pageTitle = "Blog";
include_once '../app/views/header.php';
?>

<h2 class="mb-4">Nuestro Blog</h2>

<div class="row">
    <?php foreach($posts as $post): ?>
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <?php if($post['image']): ?>
            <img src="assets/uploads/<?php echo $post['image']; ?>" class="card-img-top" alt="<?php echo $post['title']; ?>" style="height: 250px; object-fit: cover;">
            <?php endif; ?>
            <div class="card-body">
                <h5 class="card-title"><?php echo $post['title']; ?></h5>
                <p class="card-text"><?php echo $post['excerpt'] ?: Helpers::truncate($post['content']); ?></p>
                <p class="text-muted">Por: <?php echo $post['username']; ?> el <?php echo Helpers::formatDate($post['created_at']); ?></p>
            </div>
            <div class="card-footer">
                <a href="post.php?slug=<?php echo $post['id']; ?>" class="btn btn-primary">Leer más</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php
include_once '../app/views/footer.php';
?>