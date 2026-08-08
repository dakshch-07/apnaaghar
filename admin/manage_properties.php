<?php
require_once '../includes/db.php';
require_once 'includes/header.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Fetch image path to delete file if it's local
    $stmt = $pdo->prepare("SELECT image_url FROM properties WHERE id = ?");
    $stmt->execute([$id]);
    $prop = $stmt->fetch();
    
    if ($prop && strpos($prop['image_url'], 'uploads/properties/') !== false) {
        $file_path = '../' . $prop['image_url'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    $pdo->prepare("DELETE FROM properties WHERE id = ?")->execute([$id]);
    echo "<script>window.location.href='manage_properties.php';</script>";
    exit;
}

$properties = $pdo->query("SELECT * FROM properties ORDER BY created_at DESC")->fetchAll();
?>

<div class="card fade-up">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3>Manage Properties</h3>
        <a href="property_add.php" class="btn"><i class="fa-solid fa-plus"></i> Add New Property</a>
    </div>
    
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th width="80">Image</th>
                    <th>Property Details</th>
                    <th>Status</th>
                    <th>Price</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($properties as $prop): ?>
                <tr>
                    <td>
                        <img src="<?php echo strpos($prop['image_url'], 'http') === 0 ? $prop['image_url'] : '../'.$prop['image_url']; ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                    </td>
                    <td>
                        <strong style="color: var(--text-heading); font-size: 1rem;"><?php echo htmlspecialchars($prop['title']); ?></strong><br>
                        <span style="color: var(--text-body); font-size: 0.85rem; margin-top: 4px; display: inline-block;">
                            <i class="fa-solid fa-location-dot" style="opacity: 0.7; margin-right: 4px;"></i> <?php echo htmlspecialchars($prop['location']); ?> &bull; <?php echo htmlspecialchars($prop['type']); ?>
                        </span>
                    </td>
                    <td>
                        <?php
                        $status = strtolower($prop['status']);
                        $badge_class = 'info';
                        if (strpos($status, 'sold') !== false || strpos($status, 'inactive') !== false) {
                            $badge_class = 'rejected';
                        } elseif (strpos($status, 'available') !== false || strpos($status, 'ready') !== false || strpos($status, 'oc received') !== false) {
                            $badge_class = 'active';
                        } elseif (strpos($status, 'under construction') !== false || strpos($status, 'expected') !== false) {
                            $badge_class = 'pending';
                        }
                        ?>
                        <span class="status-badge <?php echo $badge_class; ?>">
                            <?php echo htmlspecialchars($prop['status']); ?>
                        </span>
                    </td>
                    <td style="font-weight: 600; color: var(--text-heading);">
                        <?php echo htmlspecialchars($prop['price']); ?>
                    </td>
                    <td>
                        <a href="property_edit.php?id=<?php echo $prop['id']; ?>" class="btn btn-sm" style="background: rgba(15,92,74,0.1); color: var(--primary); padding: 0.5rem 0.75rem;"><i class="fa-solid fa-pen"></i></a>
                        <button onclick="confirmDelete('manage_properties.php?delete=<?php echo $prop['id']; ?>')" class="btn btn-sm btn-danger" style="padding: 0.5rem 0.75rem;"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(empty($properties)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-body);">No properties found. Add one to get started!</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
