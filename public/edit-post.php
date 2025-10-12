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

// INICIALIZAR VARIABLES
$error = '';
$success = '';
$title = '';
$content = '';
$excerpt = '';
$status = 'draft';
$current_image = '';

// OBTENER EL POST A EDITAR
$post = null;
if(isset($_GET['id'])) {
    $post_id = $_GET['id'];
    $post = $postController->getById($post_id);
    
    if(!$post) {
        header("Location: manage-posts.php");
        exit;
    }
    
    // Cargar datos del post en las variables
    $title = $post['title'] ?? '';
    $content = $post['content'] ?? '';
    $excerpt = $post['excerpt'] ?? '';
    $status = $post['status'] ?? 'draft';
    $current_image = $post['image'] ?? '';
} else {
    header("Location: manage-posts.php");
    exit;
}

// PROCESAR ACTUALIZACIÓN
if($_POST){
    $title = Helpers::sanitize($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $excerpt = Helpers::sanitize($_POST['excerpt'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    $user_id = $_SESSION['user_id'];

    $image = $current_image; // Mantener la imagen actual por defecto
    
    // Procesar nueva imagen si se subió
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
        $uploadResult = Helpers::uploadImage($_FILES['image']);
        if(isset($uploadResult['error'])){
            $error = $uploadResult['error'];
        }
        else{
            $image = $uploadResult['filename'];
            // Opcional: eliminar la imagen anterior
            if($current_image && file_exists("../public/assets/uploads/" . $current_image)) {
                unlink("../public/assets/uploads/" . $current_image);
            }
        }
    }
    
    // Checkbox para eliminar imagen actual
    if(isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
        if($current_image && file_exists("../public/assets/uploads/" . $current_image)) {
            unlink("../public/assets/uploads/" . $current_image);
        }
        $image = '';
    }

    if(empty($error)){
        if($postController->update($post_id, $title, $content, $excerpt, $image, $user_id, $status)){
            $success = "Post actualizado correctamente";
        }
        else{
            $error = "Error al actualizar el post";
        }
    }
}

$pageTitle = "Editar Post";
include_once '../app/views/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">Editar Post</h3>
            </div>
            <div class="card-body">
                <?php if($error): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <?php if($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Título</label>
                        <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($title); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="excerpt" class="form-label">Resumen</label>
                        <textarea name="excerpt" id="excerpt" class="form-control" rows="3"><?php echo htmlspecialchars($excerpt); ?></textarea>
                        <div class="form-text">Breve descripción que aparecerá en la lista de posts.</div>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Contenido</label>
                        <div id="editor" style="height: 400px;">
                            <?php echo htmlspecialchars($content ?? ''); ?>
                        </div>
                        <textarea name="content" id="content" class="d-none" rows="10"><?php echo htmlspecialchars($content); ?></textarea>
                        <div id="content-error" class="invalid-feedback d-none">El contenido es requerido</div>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Imagen destacada</label>
                        
                        <?php if($current_image): ?>
                        <div class="mb-2">
                            <p>Imagen actual:</p>
                            <img src="../public/assets/uploads/<?php echo htmlspecialchars($current_image); ?>" 
                                 alt="Imagen actual" 
                                 class="img-thumbnail" 
                                 style="max-height: 200px;">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                                <label class="form-check-label" for="remove_image">
                                    Eliminar imagen actual
                                </label>
                            </div>
                        </div>
                        <p class="text-muted">O subir nueva imagen:</p>
                        <?php endif; ?>
                        
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Estado</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>Borrador</option>
                            <option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>Publicado</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Actualizar Post</button>
                    <a href="manage-posts.php" class="btn btn-secondary">Cancelar</a>
                    <a href="post.php?slug=<?php echo $post_id; ?>" class="btn btn-info" target="_blank">Vista previa</a>
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

