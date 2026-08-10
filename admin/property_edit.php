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
    
    $image_url = $_POST['current_image'] ?? '';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            $new_filename = uniqid() . '.' . $filetype;
            $upload_path = '../uploads/properties/' . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_url = 'uploads/properties/' . $new_filename;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Invalid file type. Only JPG, PNG, WEBP allowed.";
        }
    } elseif (!empty($_POST['image_url_fallback'])) {
        $image_url = $_POST['image_url_fallback'];
    }
    
    if (empty($error) && !empty($title) && !empty($image_url)) {
        $stmt = $pdo->prepare("UPDATE properties SET title=?, type=?, location=?, price=?, image_url=?, status=?, badge_status=?, badge_featured=?, bhk=?, size=?, highlights_json=?, connectivity_json=? WHERE id=?");
        
        if ($stmt->execute([$title, $type, $location, $price, $image_url, $status, $badge_status, $badge_featured, $bhk, $size, $highlights_json, $connectivity_json, $id])) {
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

$highlights_arr = json_decode($prop['highlights_json'], true);
$highlights_str = $highlights_arr ? implode("\n", $highlights_arr) : '';

$connectivity_arr = json_decode($prop['connectivity_json'], true);
$connectivity_str = $connectivity_arr ? implode("\n", $connectivity_arr) : '';

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
                <label>Upload New Image (PC) - Leave blank to keep current</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                <?php if($prop['image_url']): ?>
                <div style="margin-top: 10px;">
                    <img src="<?php echo strpos($prop['image_url'], 'http') === 0 ? $prop['image_url'] : '../'.$prop['image_url']; ?>" style="height: 60px; border-radius: 4px;">
                </div>
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
