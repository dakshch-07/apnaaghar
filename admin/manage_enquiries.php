<?php
require_once '../includes/db.php';
require_once 'includes/header.php';

// Handle Mark as Read
if (isset($_GET['mark_read'])) {
    $id = (int)$_GET['mark_read'];
    $pdo->prepare("UPDATE enquiries SET status = 'read' WHERE id = ?")->execute([$id]);
    echo "<script>window.location.href='manage_enquiries.php';</script>";
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM enquiries WHERE id = ?")->execute([$id]);
    echo "<script>window.location.href='manage_enquiries.php';</script>";
    exit;
}

// Fetch all enquiries
$stmt = $pdo->query("SELECT * FROM enquiries ORDER BY created_at DESC");
$enquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-content-inner">
    <div class="card fade-up" style="padding: 0; overflow: hidden; border-radius: 12px; box-shadow: 0 15px 35px rgba(0,0,0,0.05);">
        <div style="background: var(--sidebar-bg); padding: 1.5rem 2rem; border-bottom: 3px solid var(--primary); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <h3 style="margin: 0; color: #fff; font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; font-weight: 600; letter-spacing: 0.5px;">
                <i class="fa-solid fa-envelope-open-text" style="color: var(--primary); margin-right: 10px;"></i> User Enquiries
            </h3>
            <p style="margin: 0; color: rgba(255,255,255,0.7); font-size: 0.95rem;">View and manage all contact requests from the website.</p>
        </div>
        
        <div class="table-wrapper" style="overflow-x: auto; padding: 2rem;">
            <table>
                <thead>
                    <tr>
                        <th width="40">#</th>
                        <th>DATE</th>
                        <th>NAME</th>
                        <th>STATUS</th>
                        <th>MESSAGE</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($enquiries)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem;">No enquiries found.</td>
                        </tr>
                    <?php else: ?>
                        <?php $delay = 0.1; $counter = 1; foreach($enquiries as $enq): ?>
                            <tr class="<?php echo $enq['status'] === 'unread' ? 'unread-row' : ''; ?>" style="opacity: 0; animation: fadeUp 0.5s ease-out forwards; animation-delay: <?php echo $delay; ?>s;">
                                <td style="color: #222; font-weight: 500;"><?php echo $counter++; ?></td>
                                <td style="color: #222;"><?php echo date('M d, Y', strtotime($enq['created_at'])); ?><br><small style="color: #444; font-weight: 500;"><?php echo date('h:i A', strtotime($enq['created_at'])); ?></small></td>
                                <td><strong style="color: #222;"><?php echo htmlspecialchars($enq['name']); ?></strong></td>
                                <td>
                                    <?php if($enq['status'] === 'unread'): ?>
                                        <span class="badge" style="background-color: rgba(231, 76, 60, 0.1); color: #e74c3c; padding: 0.3rem 0.6rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem; border: 1px solid rgba(231, 76, 60, 0.3);">NEW</span>
                                    <?php else: ?>
                                        <span class="badge" style="background-color: rgba(199, 154, 74, 0.1); color: var(--primary); padding: 0.3rem 0.6rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem; border: 1px solid rgba(199, 154, 74, 0.3);">SEEN</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button onclick="viewMessage(<?php echo htmlspecialchars(json_encode([
                                        'name' => $enq['name'],
                                        'phone' => $enq['phone'],
                                        'email' => $enq['email'],
                                        'date' => date('M d, Y h:i A', strtotime($enq['created_at'])),
                                        'type' => $enq['property_type'],
                                        'budget' => $enq['budget'],
                                        'message' => $enq['message']
                                    ])); ?>)" class="btn btn-sm" title="View Message Details" style="background: rgba(199, 154, 74, 0.1); color: var(--primary); padding: 0.4rem 0.75rem; border: none; cursor: pointer; border-radius: 4px; display: inline-flex; align-items: center; gap: 5px;"><i class="fa-solid fa-eye"></i> View</button>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <!-- Mark Read Button -->
                                        <?php if($enq['status'] === 'unread'): ?>
                                            <a href="?mark_read=<?php echo $enq['id']; ?>" class="btn btn-sm" title="Mark as Read" style="background: rgba(39, 174, 96, 0.1); color: #27ae60; padding: 0.5rem 0.75rem; text-decoration: none;"><i class="fa-solid fa-check-double"></i></a>
                                        <?php endif; ?>

                                        <!-- Delete Button -->
                                        <button onclick="confirmDeleteEnquiry(<?php echo $enq['id']; ?>)" class="btn btn-sm" title="Delete" style="background: rgba(231, 76, 60, 0.1); color: #e74c3c; padding: 0.5rem 0.75rem; border: none; cursor: pointer;"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php $delay += 0.05; endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .unread-row {
        background-color: rgba(199, 154, 74, 0.08); /* Primary var with opacity */
    }
    .unread-row td {
        font-weight: 500;
    }
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(17, 34, 59, 0.8); /* Sidebar bg with opacity */
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        backdrop-filter: blur(4px);
    }
    .modal-content {
        background: #fff;
        padding: 0;
        border-radius: 12px;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3);
        width: 100%;
        max-width: 550px;
        position: relative;
        animation: scaleIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        overflow: hidden;
    }
    .modal-header-luxe {
        background: var(--sidebar-bg);
        color: var(--primary);
        padding: 1.5rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 3px solid var(--primary);
    }
    .modal-header-luxe h3 {
        margin: 0;
        color: #fff;
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.8rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .modal-close-luxe {
        background: rgba(255,255,255,0.1);
        border: none;
        color: #fff;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }
    .modal-close-luxe:hover {
        background: var(--primary);
        color: var(--sidebar-bg);
        transform: rotate(90deg);
    }
    .modal-body-luxe {
        padding: 2rem;
    }
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    .info-item {
        background: #f9f9f9;
        padding: 1rem;
        border-radius: 8px;
        border-left: 3px solid var(--primary);
    }
    .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #888;
        margin-bottom: 0.4rem;
        display: block;
    }
    .info-value {
        color: var(--sidebar-bg);
        font-weight: 600;
        font-size: 0.95rem;
        word-break: break-word;
    }
    .msg-box {
        background: #f4f6f8;
        padding: 1.5rem;
        border-radius: 8px;
        color: #333;
        font-size: 0.95rem;
        line-height: 1.6;
        border: 1px solid #e1e5eb;
    }
    .msg-box-header {
        font-size: 0.8rem;
        text-transform: uppercase;
        color: var(--primary);
        font-weight: 700;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    @keyframes scaleIn { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
</style>

<!-- View Message Modal -->
<div id="viewModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header-luxe">
            <h3><i class="fa-regular fa-envelope-open" style="margin-right: 10px; color: var(--primary);"></i> Enquiry Details</h3>
            <button class="modal-close-luxe" onclick="closeViewModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body-luxe">
            <div style="text-align: right; margin-bottom: 1rem; font-size: 0.85rem; color: #777;">
                <i class="fa-regular fa-clock"></i> Received: <span id="v-date" style="font-weight: 600; color: #333;"></span>
            </div>
            
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Sender Name</span>
                    <div class="info-value" id="v-name"></div>
                </div>
                <div class="info-item">
                    <span class="info-label">Phone Number</span>
                    <div class="info-value">
                        <a href="#" id="v-phone-link" style="color: inherit; text-decoration: none;"><i class="fa-solid fa-phone" style="font-size: 0.8rem; color: var(--primary); margin-right: 4px;"></i> <span id="v-phone"></span></a>
                    </div>
                </div>
                <div class="info-item">
                    <span class="info-label">Email Address</span>
                    <div class="info-value">
                        <a href="#" id="v-email-link" style="color: inherit; text-decoration: none;"><i class="fa-solid fa-envelope" style="font-size: 0.8rem; color: var(--primary); margin-right: 4px;"></i> <span id="v-email"></span></a>
                    </div>
                </div>
                <div class="info-item">
                    <span class="info-label">Property Interest</span>
                    <div class="info-value"><span id="v-type"></span> &bull; <span id="v-budget" style="color: #666; font-weight: normal;"></span></div>
                </div>
            </div>
            
            <div class="msg-box">
                <div class="msg-box-header"><i class="fa-solid fa-quote-left"></i> MESSAGE</div>
                <div id="v-message"></div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="modal-overlay" style="display: none;">
    <div class="modal-content" style="max-width: 400px; text-align: center; padding: 3rem 2rem;">
        <div style="font-size: 3.5rem; color: #e74c3c; margin-bottom: 1.5rem;">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h2 style="margin-bottom: 0.5rem; color: var(--sidebar-bg);">Delete Enquiry?</h2>
        <p style="margin-bottom: 2rem; color: #666; line-height: 1.5;">This action cannot be undone. Are you sure you want to permanently delete this enquiry?</p>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <button type="button" class="btn" style="background: white; border: 1px solid #ddd; color: #333;" onclick="closeDeleteModal()">Cancel</button>
            <a href="#" id="confirmDeleteBtn" class="btn" style="background: #e74c3c; border: 1px solid #e74c3c; color: white;">Yes, delete it!</a>
        </div>
    </div>
</div>

<script>
function viewMessage(data) {
    document.getElementById('v-name').textContent = data.name;
    document.getElementById('v-phone').textContent = data.phone;
    document.getElementById('v-phone-link').href = 'tel:' + data.phone;
    document.getElementById('v-email').textContent = data.email;
    document.getElementById('v-email-link').href = 'mailto:' + data.email;
    document.getElementById('v-date').textContent = data.date;
    document.getElementById('v-type').textContent = data.type;
    document.getElementById('v-budget').textContent = data.budget;
    document.getElementById('v-message').innerHTML = data.message ? data.message.replace(/\n/g, '<br>') : '<em style="color:#888;">No specific message provided.</em>';
    document.getElementById('viewModal').style.display = 'flex';
}
function closeViewModal() {
    document.getElementById('viewModal').style.display = 'none';
}

function confirmDeleteEnquiry(id) {
    document.getElementById('confirmDeleteBtn').href = '?delete=' + id;
    document.getElementById('deleteModal').style.display = 'flex';
}
function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}
</script>

<?php require_once 'includes/footer.php'; ?>
