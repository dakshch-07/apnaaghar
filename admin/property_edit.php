<?php
require_once '../includes/db.php';
require_once 'includes/header.php';

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    echo "<script>window.location.href='manage_properties.php';</script>";
    exit;
}

$id = (int)$_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? '';
    $type = $_POST['type'] ?? '';
    $location = $_POST['location'] ?? '';
    $price = $_POST['price'] ?? '';
    $status = $_POST['status'] ?? '';
    $badge_status = $_POST['badge_status'] ?? '';
    $badge_featured = $_POST['badge_featured'] ?? '';
    $bhk = $_POST['bhk'] ?? '';
    $size = $_POST['size'] ?? '';
    
    $highlights = array_filter(array_map('trim', explode("\n", $_POST['highlights'] ?? '')));
    $connectivity = array_filter(array_map('trim', explode("\n", $_POST['connectivity'] ?? '')));
    
    $highlights_json = json_encode(array_values($highlights));
    $connectivity_json = json_encode(array_values($connectivity));
    
    $stmtOld = $pdo->prepare("SELECT image_url, images_json FROM properties WHERE id = ?");
    $stmtOld->execute([$id]);
    $oldProp = $stmtOld->fetch();
    
    $image_url = $oldProp['image_url'] ?? '';
    $images_json = $oldProp['images_json'] ?? NULL;
    $additional_images = [];
    $new_images_uploaded = false;
    
    if (isset($_FILES['images']) && is_array($_FILES['images']['name']) && !empty($_FILES['images']['name'][0])) {
        $new_images_uploaded = true;
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $max_size = 5 * 1024 * 1024; // 5MB limit
        $file_count = count($_FILES['images']['name']);
        $limit = min($file_count, 10);
        
        for ($i = 0; $i < $limit; $i++) {
            if ($_FILES['images']['error'][$i] == 0) {
                $filename = $_FILES['images']['name'][$i];
                $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (in_array($filetype, $allowed) && $_FILES['images']['size'][$i] <= $max_size) {
                    $mime_types = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp', 'gif' => 'image/gif'];
                    $mime = $mime_types[$filetype] ?? 'image/jpeg';
                    $image_data = file_get_contents($_FILES['images']['tmp_name'][$i]);
                    if ($image_data !== false) {
                        $base64_str = 'data:' . $mime . ';base64,' . base64_encode($image_data);
                        if ($i === 0) {
                            $image_url = $base64_str;
                        } else {
                            $additional_images[] = $base64_str;
                        }
                    }
                }
            }
        }
        $images_json = !empty($additional_images) ? json_encode($additional_images) : NULL;
    } elseif (!empty($_POST['image_url_fallback'])) {
        $image_url = $_POST['image_url_fallback'];
        // If they use URL fallback, we might not touch additional images
    }
    
    if (empty($error) && !empty($title) && !empty($image_url)) {
        $stmt = $pdo->prepare("UPDATE properties SET title=?, type=?, location=?, price=?, image_url=?, images_json=?, status=?, badge_status=?, badge_featured=?, bhk=?, size=?, highlights_json=?, connectivity_json=? WHERE id=?");
        
        if ($stmt->execute([$title, $type, $location, $price, $image_url, $images_json, $status, $badge_status, $badge_featured, $bhk, $size, $highlights_json, $connectivity_json, $id])) {
            $success = "Property updated successfully!";
        } else {
            $error = "Database error occurred.";
        }
    } elseif(empty($error)) {
        $error = "Title and Image are required.";
    }
}

$stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->execute([$id]);
$prop = $stmt->fetch();

if (!$prop) {
    echo "<script>window.location.href='manage_properties.php';</script>";
    exit;
}

$highlights_arr = !empty($prop['highlights_json']) ? json_decode($prop['highlights_json'], true) : [];
$highlights_str = is_array($highlights_arr) ? implode("\n", $highlights_arr) : '';

$connectivity_arr = !empty($prop['connectivity_json']) ? json_decode($prop['connectivity_json'], true) : [];
$connectivity_str = is_array($connectivity_arr) ? implode("\n", $connectivity_arr) : '';

?>

<div class="card fade-up" style="padding: 0; overflow: hidden; border-radius: 12px; box-shadow: 0 15px 35px rgba(0,0,0,0.05);">
    <div style="background: var(--sidebar-bg); padding: 1.5rem 2rem; border-bottom: 3px solid var(--primary); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <h3 style="margin: 0; color: #fff; font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; font-weight: 600; letter-spacing: 0.5px;">
            <i class="fa-solid fa-pen-to-square" style="color: var(--primary); margin-right: 10px;"></i> Edit Property
        </h3>
        <a href="manage_properties.php" class="btn" style="background: rgba(255,255,255,0.1); color: #fff; border: none; font-weight: 600; letter-spacing: 0.5px;"><i class="fa-solid fa-arrow-left"></i> Back to Properties</a>
    </div>
    
    <div style="padding: 2rem;">
    
    <?php if(!empty($error)): ?>
        <div style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; border: 1px solid #fecaca;"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if(!empty($success)): ?>
        <div style="background: #d1fae5; color: #047857; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; border: 1px solid #a7f3d0;"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($prop['image_url']); ?>">
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            
            <div class="form-group">
                <label>Property Title</label>
                <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($prop['title']); ?>">
            </div>
            
            <div class="form-group">
                <label>Property Type</label>
                <input type="text" name="type" class="form-control" required value="<?php echo htmlspecialchars($prop['type']); ?>">
            </div>
            
            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" class="form-control" required value="<?php echo htmlspecialchars($prop['location']); ?>">
            </div>
            
            <div class="form-group">
                <label>Price Display</label>
                <input type="text" name="price" class="form-control" required value="<?php echo htmlspecialchars($prop['price']); ?>">
            </div>
            
            <div class="form-group">
                <label>BHK</label>
                <input type="text" name="bhk" class="form-control" required value="<?php echo htmlspecialchars($prop['bhk']); ?>">
            </div>
            
            <div class="form-group">
                <label>Size / Area</label>
                <input type="text" name="size" class="form-control" required value="<?php echo htmlspecialchars($prop['size']); ?>">
            </div>
            
            <div class="form-group">
                <label>Status</label>
                <input type="text" name="status" class="form-control" required value="<?php echo htmlspecialchars($prop['status']); ?>">
            </div>
            
            <div class="form-group">
                <label>Upload New Images (PC) - Max 10. Leave blank to keep current</label>
                <input type="file" name="images[]" multiple class="form-control" accept="image/*">
                <?php if($prop['image_url']): ?>
                <div style="margin-top: 10px; display: flex; gap: 10px; flex-wrap: wrap;">
                    <img src="../image.php?id=<?php echo $prop['id']; ?>" style="height: 60px; border-radius: 4px; border: 2px solid var(--primary);">
                    <?php 
                    if (!empty($prop['images_json'])) {
                        $add_imgs = !empty($prop['images_json']) ? json_decode($prop['images_json'], true) : [];
                        if (is_array($add_imgs)) {
                            foreach ($add_imgs as $idx => $img) {
                                echo '<img src="../image.php?id='.$prop['id'].'&idx='.$idx.'" style="height: 60px; border-radius: 4px; border: 1px solid #ccc;">';
                            }
                        }
                    }
                    ?>
                </div>
                <small style="color: var(--text-muted); display: block; margin-top: 5px;">Uploading new images will replace all current images.</small>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Badge: For Sale / For Rent</label>
                <select name="badge_status" class="form-control">
                    <option value="FOR SALE" <?php echo $prop['badge_status'] == 'FOR SALE' ? 'selected' : ''; ?>>FOR SALE</option>
                    <option value="FOR RENT" <?php echo $prop['badge_status'] == 'FOR RENT' ? 'selected' : ''; ?>>FOR RENT</option>
                    <option value="" <?php echo empty($prop['badge_status']) ? 'selected' : ''; ?>>None</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Badge: Featured</label>
                <select name="badge_featured" class="form-control">
                    <option value="" <?php echo empty($prop['badge_featured']) ? 'selected' : ''; ?>>None</option>
                    <option value="FEATURED" <?php echo $prop['badge_featured'] == 'FEATURED' ? 'selected' : ''; ?>>FEATURED</option>
                </select>
            </div>
            
            <div class="form-group" style="grid-column: span 2;">
                <label>Image URL Fallback (Leave blank if uploading a file)</label>
                <input type="text" name="image_url_fallback" class="form-control" placeholder="https://images.unsplash.com/..." value="<?php echo strpos($prop['image_url'], 'http') === 0 ? htmlspecialchars($prop['image_url']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Highlights (One per line)</label>
                <textarea name="highlights" class="form-control" rows="5"><?php echo htmlspecialchars($highlights_str); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Connectivity (One per line)</label>
                <textarea name="connectivity" class="form-control" rows="5"><?php echo htmlspecialchars($connectivity_str); ?></textarea>
            </div>
            
        </div>
        
        <button type="submit" class="btn btn" style="margin-top: 1rem;"><i class="fa-solid fa-save"></i> Update Property</button>
    </form>
    </div> <!-- End padding wrapper -->
</div>

<?php require_once 'includes/footer.php'; ?>
<script>
async function compressImage(file) {
    return new Promise((resolve) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;
                const max_dim = 1600;
                if (width > max_dim || height > max_dim) {
                    if (width > height) {
                        height = Math.round(height * (max_dim / width));
                        width = max_dim;
                    } else {
                        width = Math.round(width * (max_dim / height));
                        height = max_dim;
                    }
                }
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);
                
                let quality = 0.8;
                let dataUrl = canvas.toDataURL('image/jpeg', quality);
                
                while(dataUrl.length > 350000 && quality > 0.1) {
                    quality -= 0.1;
                    dataUrl = canvas.toDataURL('image/jpeg', quality);
                }
                
                fetch(dataUrl)
                    .then(res => res.blob())
                    .then(blob => {
                        resolve(new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", {type: 'image/jpeg'}));
                    });
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
}

document.querySelector('form').addEventListener('submit', async function(e) {
    const fileInput = document.querySelector('input[type="file"]');
    if (fileInput && fileInput.files.length > 0) {
        let totalSize = 0;
        for (let i = 0; i < fileInput.files.length; i++) {
            totalSize += fileInput.files[i].size;
        }
        if (totalSize > 3.5 * 1024 * 1024) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing images, please wait...';
            submitBtn.disabled = true;
            
            const dataTransfer = new DataTransfer();
            for (let i = 0; i < fileInput.files.length; i++) {
                const file = fileInput.files[i];
                if (file.type.startsWith('image/')) {
                    const compressed = await compressImage(file);
                    dataTransfer.items.add(compressed);
                } else {
                    dataTransfer.items.add(file);
                }
            }
            
            fileInput.files = dataTransfer.files;
            this.submit();
        }
    }
});
</script>
