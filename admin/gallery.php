<?php
require_once '../includes/db.php';
require_once 'includes/header.php';

$error = '';
$success = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Fetch image path to delete file if it's local
    $stmt = $pdo->prepare("SELECT image_url FROM gallery WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    
    if ($item && strpos($item['image_url'], 'uploads/gallery/') !== false) {
        $file_path = '../' . $item['image_url'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    $pdo->prepare("DELETE FROM gallery WHERE id = ?")->execute([$id]);
    echo "<script>window.location.href='gallery.php';</script>";
    exit;
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $action = $_POST['action'];
    $id = (int)($_POST['id'] ?? 0);
    
    if ($action == 'add' || $action == 'edit') {
        $image_url = $_POST['existing_image'] ?? '';
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            $filename = $_FILES['image']['name'];
            $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $max_size = 5 * 1024 * 1024; // 5MB limit
            
            if (!in_array($filetype, $allowed)) {
                $error = "Invalid file type. Only JPG, PNG, WEBP, GIF allowed.";
            } elseif ($_FILES['image']['size'] > $max_size) {
                $error = "Image too large. Maximum size is 5MB.";
            } else {
                $mime_types = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp', 'gif' => 'image/gif'];
                $mime = $mime_types[$filetype] ?? 'image/jpeg';
                $image_data = file_get_contents($_FILES['image']['tmp_name']);
                if ($image_data !== false) {
                    $image_url = 'data:' . $mime . ';base64,' . base64_encode($image_data);
                } else {
                    $error = "Failed to read uploaded image.";
                }
            }
        } elseif(empty($image_url)) {
            $image_url = $_POST['image_url_fallback'] ?? '';
        }
        
        if (empty($error) && !empty($title) && !empty($category) && !empty($image_url)) {
            if ($action == 'add') {
                $stmt = $pdo->prepare("INSERT INTO gallery (title, category, image_url) VALUES (?, ?, ?)");
                if ($stmt->execute([$title, $category, $image_url])) {
                    $success = "Image added to gallery!";
                } else {
                    $error = "Database error occurred.";
                }
            } else {
                $stmt = $pdo->prepare("UPDATE gallery SET title=?, category=?, image_url=? WHERE id=?");
                if ($stmt->execute([$title, $category, $image_url, $id])) {
                    $success = "Image updated successfully!";
                    echo "<script>window.location.href='gallery.php';</script>";
                    exit;
                } else {
                    $error = "Database error occurred.";
                }
            }
        } elseif(empty($error)) {
            $error = "Title, Category, and Image are required.";
        }
    }
}

$edit_item = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM gallery WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit_item = $stmt->fetch();
}

$gallery_items = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC")->fetchAll();
?>

<div style="display: grid; grid-template-columns: 350px 1fr; gap: 2rem;" class="fade-up">
    
    <!-- Add/Edit Form -->
    <div class="card" style="height: fit-content; position: sticky; top: 100px;">
        <h3 style="margin-bottom: 1.5rem;"><?php echo $edit_item ? 'Edit Image' : 'Add New Image'; ?></h3>
        
        <?php if(!empty($error)): ?>
            <div style="background: rgba(192, 57, 43, 0.1); color: var(--status-rejected); padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; font-size: 0.9rem;"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if(!empty($success)): ?>
            <div style="background: rgba(46, 125, 50, 0.1); color: var(--status-active); padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; font-size: 0.9rem;"><i class="fa-solid fa-check"></i> <?php echo $success; ?></div>
        <?php endif; ?>
        
        <form action="gallery.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="<?php echo $edit_item ? 'edit' : 'add'; ?>">
            <?php if($edit_item): ?>
                <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
                <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($edit_item['image_url']); ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label>Image Title</label>
                <input type="text" name="title" class="form-control" required placeholder="e.g. Modern Kitchen" value="<?php echo $edit_item ? htmlspecialchars($edit_item['title']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Category</label>
                <select name="category" class="form-control" required>
                    <?php
                    $cats = ['Interiors', 'Exteriors', 'Amenities', 'Floor Plans', 'Construction Updates'];
                    $current_cat = $edit_item ? $edit_item['category'] : '';
                    foreach($cats as $c) {
                        $sel = ($c == $current_cat) ? 'selected' : '';
                        echo "<option value=\"$c\" $sel>$c</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label><?php echo $edit_item ? 'Upload New Image (Optional)' : 'Upload Image (PC)'; ?></label>
                <input type="file" name="image" accept="image/*">
                <?php if($edit_item && $edit_item['image_url']): ?>
                    <div style="margin-top: 10px;">
                        <img src="<?php echo strpos($edit_item['image_url'], 'uploads/') === 0 ? '../'.$edit_item['image_url'] : $edit_item['image_url']; ?>" style="width:100%; border-radius:8px; border:1px solid var(--card-border);">
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Or Image URL Fallback</label>
                <input type="text" name="image_url_fallback" class="form-control" placeholder="https://..." value="<?php echo ($edit_item && strpos($edit_item['image_url'], 'http') === 0) ? htmlspecialchars($edit_item['image_url']) : ''; ?>">
            </div>
            
            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn" style="flex:1;"><i class="fa-solid <?php echo $edit_item ? 'fa-floppy-disk' : 'fa-plus'; ?>"></i> <?php echo $edit_item ? 'Update' : 'Add to Gallery'; ?></button>
                <?php if($edit_item): ?>
                    <a href="gallery.php" class="btn" style="background:#f1f5f9; color:var(--text-main);"><i class="fa-solid fa-xmark"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <!-- Gallery Grid -->
    <div class="card" style="animation-delay: 0.2s;">
        <h3 style="margin-bottom: 1.5rem;">Manage Gallery</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.5rem;">
            <?php foreach($gallery_items as $item): ?>
            <div style="border: 1px solid var(--card-border); border-radius: 8px; overflow: hidden; position: relative; transition: all 0.2s; background: #fff;" onmouseover="this.style.boxShadow='0 10px 20px rgba(0,0,0,0.05)'" onmouseout="this.style.boxShadow='none'">
                <img src="<?php echo strpos($item['image_url'], 'uploads/') === 0 ? '../'.$item['image_url'] : $item['image_url']; ?>" style="width: 100%; height: 160px; object-fit: cover; display: block;">
                
                <div style="position: absolute; top: 10px; right: 10px; display:flex; gap:5px;">
                    <a href="gallery.php?edit=<?php echo $item['id']; ?>" class="btn btn-sm" style="background: var(--primary); color: white; padding: 6px 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.3); border: none;"><i class="fa-solid fa-pen"></i></a>
                    <button onclick="confirmDelete('gallery.php?delete=<?php echo $item['id']; ?>')" class="btn btn-sm btn-danger" style="padding: 6px 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);"><i class="fa-solid fa-trash"></i></button>
                </div>
                
                <div style="padding: 1rem;">
                    <h5 style="margin-bottom: 0.25rem; font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--text-heading);"><?php echo htmlspecialchars($item['title']); ?></h5>
                    <span style="font-size: 0.75rem; color: var(--sidebar-muted); text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;"><?php echo htmlspecialchars($item['category']); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if(empty($gallery_items)): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--text-muted);">
                No images in gallery yet.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

