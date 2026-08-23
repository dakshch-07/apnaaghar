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
    $description = $_POST['description'] ?? '';
    
    $highlights_post = $_POST['highlights'] ?? [];
    $highlights = is_array($highlights_post) ? $highlights_post : array_filter(array_map('trim', explode("\n", $highlights_post)));
    $connectivity = array_filter(array_map('trim', explode("\n", $_POST['connectivity'] ?? '')));
    
    $highlights_json = json_encode(array_values($highlights));
    $connectivity_json = json_encode(array_values($connectivity));
    
    $stmtOld = $pdo->prepare("SELECT image_url, images_json FROM properties WHERE id = ?");
    $stmtOld->execute([$id]);
    $oldProp = $stmtOld->fetch();
    
    $final_images = [];
    $final_main_image = '';

    // 1. Keep selected existing images
    if (isset($_POST['keep_main_image']) && $_POST['keep_main_image'] == '1') {
        $final_main_image = $oldProp['image_url'] ?? '';
    }
    if (isset($_POST['keep_images']) && is_array($_POST['keep_images'])) {
        $old_extra = !empty($oldProp['images_json']) ? json_decode($oldProp['images_json'], true) : [];
        if (is_array($old_extra)) {
            foreach ($_POST['keep_images'] as $idx) {
                if (isset($old_extra[$idx])) {
                    $final_images[] = $old_extra[$idx];
                }
            }
        }
    }
    
    // 2. Add newly uploaded images
    if (isset($_FILES['images']) && is_array($_FILES['images']['name']) && !empty($_FILES['images']['name'][0])) {
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
                        if (empty($final_main_image)) {
                            $final_main_image = $base64_str;
                        } else {
                            $final_images[] = $base64_str;
                        }
                    }
                }
            }
        }
    }
    
    $image_url = $final_main_image;
    $images_json = !empty($final_images) ? json_encode($final_images) : NULL;
    
    if (empty($error) && !empty($title) && !empty($image_url)) {
        try {
            $stmt = $pdo->prepare("UPDATE properties SET title=?, type=?, location=?, price=?, image_url=?, images_json=?, status=?, badge_status=?, badge_featured=?, bhk=?, size=?, description=?, highlights_json=?, connectivity_json=? WHERE id=?");
            
            if ($stmt->execute([$title, $type, $location, $price, $image_url, $images_json, $status, $badge_status, $badge_featured, $bhk, $size, $description, $highlights_json, $connectivity_json, $id])) {
                $success = "Property updated successfully!";
            } else {
                $error = "Database error occurred.";
            }
        } catch(PDOException $ex) {
            // Auto fallback if description column missing
            if (strpos($ex->getMessage(), 'description') !== false || $ex->getCode() == '42S22') {
                try {
                    $pdo->exec("ALTER TABLE properties ADD COLUMN description LONGTEXT NULL AFTER size");
                    $stmt = $pdo->prepare("UPDATE properties SET title=?, type=?, location=?, price=?, image_url=?, images_json=?, status=?, badge_status=?, badge_featured=?, bhk=?, size=?, description=?, highlights_json=?, connectivity_json=? WHERE id=?");
                    if ($stmt->execute([$title, $type, $location, $price, $image_url, $images_json, $status, $badge_status, $badge_featured, $bhk, $size, $description, $highlights_json, $connectivity_json, $id])) {
                        $success = "Property updated successfully!";
                    } else {
                        $error = "Database error occurred.";
                    }
                } catch(PDOException $e2) {
                    $error = "Database error: " . $e2->getMessage();
                }
            } else {
                $error = "Database error: " . $ex->getMessage();
            }
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
    <div class="card-header-flex" style="background: var(--sidebar-bg); padding: 1.5rem 2rem; border-bottom: 3px solid var(--primary); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <h3 style="margin: 0; color: #fff; font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; font-weight: 600; letter-spacing: 0.5px;">
            <i class="fa-solid fa-pen-to-square" style="color: var(--primary); margin-right: 10px;"></i> Edit Property
        </h3>
        <a href="manage_properties.php" class="btn" style="background: rgba(255,255,255,0.1); color: #fff; border: none; font-weight: 600; letter-spacing: 0.5px;"><i class="fa-solid fa-arrow-left"></i> Back to Properties</a>
    </div>
    
    <div class="admin-card-body" style="padding: 2rem;">
    
    <?php if(!empty($error)): ?>
        <div style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; border: 1px solid #fecaca;"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if(!empty($success)): ?>
        <div style="background: #d1fae5; color: #047857; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; border: 1px solid #a7f3d0;"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($prop['image_url']); ?>">
        
        <div class="admin-form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            
            <div class="form-group">
                <label>Property Title</label>
                <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($prop['title']); ?>">
            </div>
            
            <div class="form-group">
                <label>Property Type</label>
                <select name="type" class="form-control" required>
                    <option value="" disabled <?php echo empty($prop['type']) ? 'selected' : ''; ?>>Select Property Type</option>
                    <?php
                    $known_types = ['flat', 'house', 'townhouse', 'open plot', 'commercial'];
                    $current_type = strtolower(trim($prop['type']));
                    $is_known = in_array($current_type, $known_types);
                    if (!$is_known && !empty($current_type)) {
                        echo '<option value="'.htmlspecialchars($prop['type']).'" selected>'.htmlspecialchars($prop['type']).'</option>';
                    }
                    ?>
                    <option value="Flat" <?php echo ($current_type == 'flat') ? 'selected' : ''; ?>>Flat</option>
                    <option value="House" <?php echo ($current_type == 'house') ? 'selected' : ''; ?>>House</option>
                    <option value="Townhouse" <?php echo ($current_type == 'townhouse') ? 'selected' : ''; ?>>Townhouse</option>
                    <option value="Open Plot" <?php echo ($current_type == 'open plot') ? 'selected' : ''; ?>>Open Plot</option>
                    <option value="Commercial" <?php echo ($current_type == 'commercial') ? 'selected' : ''; ?>>Commercial</option>
                </select>
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
                <label>Carpet Area</label>
                <input type="text" name="size" class="form-control" required value="<?php echo htmlspecialchars($prop['size']); ?>">
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label>Property Description</label>
                <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($prop['description'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="" disabled <?php echo empty($prop['status']) ? 'selected' : ''; ?>>Select Status</option>
                    <?php
                    $known_statuses = ['ready to move', 'oc received', 'under construction'];
                    $current_status = strtolower(trim($prop['status']));
                    $is_known_status = in_array($current_status, $known_statuses);
                    if (!$is_known_status && !empty($current_status)) {
                        echo '<option value="'.htmlspecialchars($prop['status']).'" selected>'.htmlspecialchars($prop['status']).'</option>';
                    }
                    ?>
                    <option value="Ready to move" <?php echo ($current_status == 'ready to move') ? 'selected' : ''; ?>>Ready to move</option>
                    <option value="OC received" <?php echo ($current_status == 'oc received') ? 'selected' : ''; ?>>OC received</option>
                    <option value="Under construction" <?php echo ($current_status == 'under construction') ? 'selected' : ''; ?>>Under construction</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Upload New Images (PC) - Max 10. Leave blank to keep current</label>
                <input type="file" name="images[]" multiple class="form-control" accept="image/*">
                <?php if($prop['image_url']): ?>
                <div style="margin-top: 10px; display: flex; gap: 10px; flex-wrap: wrap;" id="existing-images-container">
                    
                    <div class="existing-img-wrapper" style="position: relative; display: inline-block;">
                        <input type="hidden" name="keep_main_image" value="1" class="keep-img-input">
                        <img src="../image.php?id=<?php echo $prop['id']; ?>&t=<?php echo time(); ?>" style="height: 80px; border-radius: 6px; border: 2px solid var(--primary);">
                        <button type="button" class="delete-existing-img-btn" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"><i class="fa-solid fa-xmark"></i></button>
                    </div>

                    <?php 
                    if (!empty($prop['images_json'])) {
                        $add_imgs = !empty($prop['images_json']) ? json_decode($prop['images_json'], true) : [];
                        if (is_array($add_imgs)) {
                            foreach ($add_imgs as $idx => $img) {
                                ?>
                                <div class="existing-img-wrapper" style="position: relative; display: inline-block;">
                                    <input type="hidden" name="keep_images[]" value="<?php echo $idx; ?>" class="keep-img-input">
                                    <img src="../image.php?id=<?php echo $prop['id']; ?>&idx=<?php echo $idx; ?>&t=<?php echo time(); ?>" style="height: 80px; border-radius: 6px; border: 1px solid #ccc;">
                                    <button type="button" class="delete-existing-img-btn" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                                <?php
                            }
                        }
                    }
                    ?>
                </div>
                <small style="color: var(--text-muted); display: block; margin-top: 5px;">Click the <span style="color:red;font-weight:bold;">X</span> on any image to delete it. New uploads will be added to the remaining images!</small>
                
                <script>
                document.querySelectorAll('.delete-existing-img-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const wrapper = this.closest('.existing-img-wrapper');
                        // Remove the hidden input so it won't be sent to server as 'kept'
                        wrapper.querySelector('.keep-img-input').remove();
                        // Hide the wrapper visually
                        wrapper.style.display = 'none';
                    });
                });
                </script>
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
                <label>Amenities</label>
                <div class="amenities-container">
                    <?php 
                    $amenities_list = ['Meditation Center', 'Gym', 'Community Hall', 'Club House', 'Kids Play Area', 'Roof Top', 'Meeting Hall', 'Turf', 'Parking', 'Balcony', 'Basement', 'Cable TV', 'Ceiling Fan', 'Lift', 'Fitness Center', 'Online Application', 'Portal', 'Package Service', 'Pet Park', 'Refugee Area', 'Residents Lounge', 'Storage', 'Wheel Chair Access'];
                    sort($amenities_list);
                    foreach($amenities_list as $amenity): 
                        $is_checked = in_array($amenity, $highlights_arr) ? 'checked' : '';
                    ?>
                        <label class="amenity-pill">
                            <input type="checkbox" name="highlights[]" value="<?php echo htmlspecialchars($amenity); ?>" <?php echo $is_checked; ?>>
                            <span class="pill-text"><i class="fa-solid fa-plus icon-plus"></i><i class="fa-solid fa-check icon-check" style="display:none;"></i> <?php echo htmlspecialchars($amenity); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <style>
                    .amenities-container { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
                    .amenity-pill input { display: none; }
                    .amenity-pill { cursor: pointer; }
                    .pill-text { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: #f0f2f5; border: 1px solid #e4e6eb; border-radius: 50px; font-size: 0.9rem; color: #4b4f56; transition: all 0.2s; }
                    .pill-text i { font-size: 0.8rem; }
                    .amenity-pill input:checked + .pill-text { background: var(--primary); color: #fff; border-color: var(--primary); }
                    .amenity-pill input:checked + .pill-text .icon-plus { display: none; }
                    .amenity-pill input:checked + .pill-text .icon-check { display: inline-block !important; }
                </style>
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
