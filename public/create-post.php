<?php

use App\CmsPhp\controllers\AuthController;
use App\CmsPhp\controllers\PostController;
use App\CmsPhp\core\Helpers;

require_once '../vendor/autoload.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$auth = new AuthController($db);

if (!$auth->isLoggedIn() || !$auth->isAuthor()) {
    header("Location: login.php");
    exit;
}

$postController = new PostController($db);

$error = '';
$success = '';

// Inicializar variables
$title = $content = $excerpt = '';
$status = 'draft';
$user_id = $_SESSION['user_id'];

if ($_POST) {
    $title = Helpers::sanitize($_POST['title' ?? '']);
    $content = $_POST['content'] ?? '';
    $excerpt = Helpers::sanitize($_POST['excerpt' ?? '']);
    $status = $_POST['status'] ?? 'draft';
    $user_id = $_SESSION['user_id'];

    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploadResult = Helpers::uploadImage($_FILES['image']);
        if (isset($uploadResult['error'])) {
            $error = $uploadResult['error'];
        } else {
            $image = $uploadResult['filename'];
        }
    }

    if (empty($error)) {
        if ($postController->create($title, $content, $excerpt, $image, $user_id, $status)) {
            $success = "Post creado correctamente";
            // Reset form
            $title = $content = $excerpt = '';
            $status = 'draft';
        } else {
            $error = "Error al crear el post";
        }
    } else {
        $error = "Error al subir la imagen";
    }
}

$pageTitle = "Crear Post";
include_once '../app/views/header.php';
?>


<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">Crear Nuevo Post</h3>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Titulo</label>
                        <input type="text" name="title" id="title" class="form-control" value="<?php echo $title ?? ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="excerpt" class="form-label">Resumen</label>
                        <textarea name="excerpt" id="excerpt" class="form-control" rows="3"><?php echo $excerpt ?? ''; ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Contenido</label>
                        <div id="editor" style="height: 400px;"><?php echo htmlspecialchars($content ?? ''); ?></div>
                        <textarea name="content" id="content" class="d-none"><?php echo htmlspecialchars($content ?? ''); ?></textarea>
                        <div id="content-error" class="invalid-feedback d-none">El contenido es requerido</div>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Imagen destacada</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Estado</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>Borrador</option>
                            <option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>Publicado</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Crear Post</button>
                    <a href="manage-posts.php" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>

    </div>

</div>
<?php include_once '../app/views/footer.php'; ?>

<script>
    const quill = new Quill('#editor', {
        theme: 'snow'
    });

    // Set initial content if any
    const contentTextarea = document.querySelector('textarea#content');
    if (contentTextarea.value) {
        quill.root.innerHTML = contentTextarea.value;
    }

    // Update hidden textarea with Quill content before form submission
    const form = document.querySelector('form');
    const contentError = document.getElementById('content-error');
    
    form.onsubmit = function(e) {
        // Get the HTML content from Quill
        const content = quill.root.innerHTML.trim();
        contentTextarea.value = content;
        
        // Validate that content is not empty (not just empty tags or spaces)
        if (content === '' || content === '<p><br></p>' || content === '<p></p>') {
            e.preventDefault();
            contentError.classList.remove('d-none');
            return false;
        }
        
        contentError.classList.add('d-none');
        return true;
    };
</script>