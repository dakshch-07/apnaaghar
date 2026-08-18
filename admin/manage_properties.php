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

<div class="card fade-up" style="padding: 0; overflow: hidden; border-radius: 12px; box-shadow: 0 15px 35px rgba(0,0,0,0.05);">
    <div class="card-header-flex" style="background: var(--sidebar-bg); padding: 1.5rem 2rem; border-bottom: 3px solid var(--primary); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <h3 style="margin: 0; color: #fff; font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; font-weight: 600; letter-spacing: 0.5px;">
            <i class="fa-solid fa-building" style="color: var(--primary); margin-right: 10px;"></i> Manage Properties
        </h3>
          <div class="search-container" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
              <div style="position: relative; width: 100%;">
                  <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #666;"></i>
                  <input type="text" id="propertySearch" class="form-control" placeholder="Search properties by title, location, status..." style="padding: 0.6rem 1rem 0.6rem 36px; width: 350px; border-radius: 8px; border: none; outline: none; background: rgba(255,255,255,0.95); color: #333; font-size: 0.95rem; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
              </div>
              <a href="property_add.php" class="btn" style="background: var(--primary); color: #fff; border: none; font-weight: 600; letter-spacing: 0.5px; white-space: nowrap;"><i class="fa-solid fa-plus"></i> Add New Property</a>
          </div>
    </div>
    
    <div class="table-wrapper" style="overflow-x: auto; padding: 2rem;">
        <table>
            <thead>
                <tr>
                    <th width="40">#</th>
                    <th width="80">Image</th>
                    <th>Property Details</th>
                    <th>Status</th>
                    <th>Price</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
            <tbody id="propertyTableBody">
                <?php $serial = 1; foreach($properties as $prop): ?>
                <tr class="property-row">
                    <td style="color: var(--text-body); font-weight: 500;"><?php echo $serial++; ?></td>
                    <td style="width: 80px;">
                        <img src="../image.php?id=<?php echo $prop['id']; ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
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
                        <a href="property_edit.php?id=<?php echo $prop['id']; ?>" class="btn btn-sm" title="Edit" style="background: rgba(199, 154, 74, 0.1); color: var(--primary); padding: 0.5rem 0.75rem;"><i class="fa-solid fa-pen"></i></a>
                        <button onclick="confirmDelete('manage_properties.php?delete=<?php echo $prop['id']; ?>')" class="btn btn-sm btn-danger" title="Delete" style="padding: 0.5rem 0.75rem;"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(empty($properties)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-body);">No properties found. Add one to get started!</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('propertySearch');
    const tableRows = document.querySelectorAll('.property-row');

    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        
        tableRows.forEach(row => {
            const textContent = row.textContent.toLowerCase();
            if(textContent.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>

