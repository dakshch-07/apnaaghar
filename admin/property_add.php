<?php
require_once '../includes/db.php';
require_once 'includes/header.php';

$error = '';
$success = '';

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
    
    // Process Highlights and Connectivity (split by newline)
    $highlights = array_filter(array_map('trim', explode("\n", $_POST['highlights'] ?? '')));
    $connectivity = array_filter(array_map('trim', explode("\n", $_POST['connectivity'] ?? '')));
    
    $highlights_json = json_encode(array_values($highlights));
    $connectivity_json = json_encode(array_values($connectivity));
    
    // Image Upload Logic — Base64 encode and store in DB (Vercel has read-only filesystem)
    $image_url = '';
    $additional_images = [];
    
    if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $max_size = 5 * 1024 * 1024; // 5MB limit
        $file_count = count($_FILES['images']['name']);
        $limit = min($file_count, 10); // Max 10 images total
        
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
    }
    
    if (empty($image_url)) {
        // Fallback if they put a URL instead
        $image_url = $_POST['image_url_fallback'] ?? '';
    }
    
    $images_json = !empty($additional_images) ? json_encode($additional_images) : NULL;
    
    if (empty($error) && !empty($title) && !empty($image_url)) {
        $stmt = $pdo->prepare("INSERT INTO properties (title, type, location, price, image_url, images_json, status, badge_status, badge_featured, bhk, size, highlights_json, connectivity_json) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$title, $type, $location, $price, $image_url, $images_json, $status, $badge_status, $badge_featured, $bhk, $size, $highlights_json, $connectivity_json])) {
            $success = "Property added successfully!";
        } else {
            $error = "Database error occurred.";
        }
    } elseif(empty($error)) {
        $error = "Title and Main Image are required.";
    }
}
?>

<div class="card fade-up" style="padding: 0; overflow: hidden; border-radius: 12px; box-shadow: 0 15px 35px rgba(0,0,0,0.05);">
    <div style="background: var(--sidebar-bg); padding: 1.5rem 2rem; border-bottom: 3px solid var(--primary); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <h3 style="margin: 0; color: #fff; font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; font-weight: 600; letter-spacing: 0.5px;">
            <i class="fa-solid fa-plus-circle" style="color: var(--primary); margin-right: 10px;"></i> Add New Property
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
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            
            <div class="form-group">
                <label>Property Title</label>
                <input type="text" name="title" class="form-control" required placeholder="e.g. The Grand Horizon Residency">
            </div>
            
            <div class="form-group">
                <label>Property Type</label>
                <input type="text" name="type" class="form-control" required placeholder="e.g. Luxury Tower, Penthouse">
            </div>
            
            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" class="form-control" required placeholder="e.g. Shell Colony, Chembur">
            </div>
            
            <div class="form-group">
                <label>Price Display</label>
                <input type="text" name="price" class="form-control" required placeholder="e.g. ₹3.45 Cr">
            </div>
            
            <div class="form-group">
                <label>BHK</label>
                <input type="text" name="bhk" class="form-control" required placeholder="e.g. 3 BHK">
            </div>
            
            <div class="form-group">
                <label>Size / Area</label>
                <input type="text" name="size" class="form-control" required placeholder="e.g. 1,450 sq.ft">
            </div>
            
            <div class="form-group">
                <label>Status</label>
                <input type="text" name="status" class="form-control" required placeholder="e.g. OC Received">
            </div>
            
            <div class="form-group">
                <label>Upload Images (PC) - Max 10</label>
                <input type="file" name="images[]" multiple class="form-control" accept="image/*">
                <small style="color: var(--text-muted); display: block; margin-top: 5px;">First image will be the main thumbnail. You can upload up to 10 images at once. Or use Image URL below if not uploading.</small>
            </div>
            
            <div class="form-group">
                <label>Badge: For Sale / For Rent</label>
                <select name="badge_status" class="form-control">
                    <option value="FOR SALE">FOR SALE</option>
                    <option value="FOR RENT">FOR RENT</option>
                    <option value="">None</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Badge: Featured</label>
                <select name="badge_featured" class="form-control">
                    <option value="">None</option>
                    <option value="FEATURED">FEATURED</option>
                </select>
            </div>
            
            <div class="form-group" style="grid-column: span 2;">
                <label>Image URL Fallback (Leave blank if uploading a file)</label>
                <input type="text" name="image_url_fallback" class="form-control" placeholder="https://images.unsplash.com/...">
            </div>
            
            <div class="form-group">
                <label>Highlights (One per line)</label>
                <textarea name="highlights" class="form-control" rows="5" placeholder="Double Height Grand Entrance Lobby&#10;Fully Equipped Modern Gymnasium"></textarea>
            </div>
            
            <div class="form-group">
                <label>Connectivity (One per line)</label>
                <textarea name="connectivity" class="form-control" rows="5" placeholder="5 mins from Chembur Railway Station&#10;2 mins drive from Eastern Express Highway"></textarea>
            </div>
            
        </div>
        
        <button type="submit" class="btn btn" style="margin-top: 1rem;"><i class="fa-solid fa-save"></i> Save Property</button>
    </form>
    </div> <!-- End padding wrapper -->
</div>

<?php require_once 'includes/footer.php'; ?>
<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const fileInput = document.querySelector('input[type="file"]');
    if (fileInput && fileInput.files.length > 0) {
        let totalSize = 0;
        for (let i = 0; i < fileInput.files.length; i++) {
            totalSize += fileInput.files[i].size;
        }
        // Vercel payload limit is 4.5MB
        if (totalSize > 4 * 1024 * 1024) {
            e.preventDefault();
            alert('Total image size exceeds 4MB. Please compress your images or upload fewer images at once to bypass Vercel limits.');
        }
    }
});
</script>
