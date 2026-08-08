<?php
require_once '../includes/db.php';
require_once 'includes/header.php';

// Get real counts
$prop_count = $pdo->query("SELECT COUNT(*) FROM properties")->fetchColumn();
$gal_count = $pdo->query("SELECT COUNT(*) FROM gallery")->fetchColumn();

// Get real status breakdown
$status_stmt = $pdo->query("SELECT status, COUNT(*) as count FROM properties GROUP BY status");
$statuses = $status_stmt->fetchAll(PDO::FETCH_ASSOC);

$status_labels = [];
$status_counts = [];
foreach($statuses as $s) {
    $status_labels[] = $s['status'];
    $status_counts[] = $s['count'];
}

// Mock KPI Data for demo
$total_leads = 1284;
$total_revenue = 450; // in Cr
$active_listings = 18;

?>

<style>
    .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem; }
    .kpi-card { background: var(--card-bg); border-radius: 12px; border: 1px solid var(--card-border); padding: 1.5rem; box-shadow: var(--card-shadow); display: flex; align-items: center; justify-content: space-between; opacity: 0; transform: translateY(20px); animation: fadeUp 0.6s forwards; }
    .kpi-card:nth-child(1) { animation-delay: 0.1s; }
    .kpi-card:nth-child(2) { animation-delay: 0.2s; }
    .kpi-card:nth-child(3) { animation-delay: 0.3s; }
    .kpi-card:nth-child(4) { animation-delay: 0.4s; }
    
    .kpi-info h4 { font-size: 0.85rem; color: var(--text-body); font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem; }
    .kpi-value { font-size: 2rem; font-weight: 700; color: var(--text-heading); }
    .kpi-icon { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
    
    .charts-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem; }
    .chart-card { background: var(--card-bg); border-radius: 12px; border: 1px solid var(--card-border); padding: 1.5rem; box-shadow: var(--card-shadow); opacity: 0; transform: translateY(20px); animation: fadeUp 0.6s forwards; animation-delay: 0.5s; }
    .chart-card h3 { margin-bottom: 1.5rem; font-size: 1.1rem; color: var(--text-heading); }
    .chart-container { position: relative; height: 300px; width: 100%; }
</style>

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Total Properties</h4>
            <div class="kpi-value"><span id="kpi-props">0</span></div>
        </div>
        <div class="kpi-icon" style="background: rgba(15, 92, 74, 0.1); color: var(--primary);">
            <i class="fa-solid fa-city"></i>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Total Leads</h4>
            <div class="kpi-value"><span id="kpi-leads">0</span></div>
        </div>
        <div class="kpi-icon" style="background: rgba(224, 166, 62, 0.1); color: var(--status-pending);">
            <i class="fa-solid fa-users"></i>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Est. Revenue (Cr)</h4>
            <div class="kpi-value">?<span id="kpi-rev">0</span></div>
        </div>
        <div class="kpi-icon" style="background: rgba(46, 125, 50, 0.1); color: var(--status-active);">
            <i class="fa-solid fa-indian-rupee-sign"></i>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Gallery Assets</h4>
            <div class="kpi-value"><span id="kpi-gallery">0</span></div>
        </div>
        <div class="kpi-icon" style="background: rgba(59, 111, 160, 0.1); color: var(--status-info);">
            <i class="fa-solid fa-image"></i>
        </div>
    </div>
</div>

<div class="charts-grid">
    <div class="chart-card">
        <h3>Revenue & Sales Trend</h3>
        <div class="chart-container">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
    <div class="chart-card">
        <h3>Properties by Status</h3>
        <div class="chart-container">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
</div>

<div class="chart-card" style="animation-delay: 0.6s;">
    <h3>Enquiries Over Time</h3>
    <div class="chart-container" style="height: 250px;">
        <canvas id="leadsChart"></canvas>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Initialize CountUp Animations
    const options = { duration: 2.5, useEasing: true, useGrouping: true };
    new countUp.CountUp('kpi-props', <?php echo $prop_count; ?>, options).start();
    new countUp.CountUp('kpi-leads', <?php echo $total_leads; ?>, options).start();
    new countUp.CountUp('kpi-rev', <?php echo $total_revenue; ?>, options).start();
    new countUp.CountUp('kpi-gallery', <?php echo $gal_count; ?>, options).start();

    // 2. Chart Configurations
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = "#6A6A6A";

    // Revenue Area Chart
    const ctxRev = document.getElementById('revenueChart').getContext('2d');
    
    let gradient = ctxRev.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(15, 92, 74, 0.5)');
    gradient.addColorStop(1, 'rgba(15, 92, 74, 0.0)');

    new Chart(ctxRev, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Revenue (in Cr)',
                data: [15, 20, 25, 22, 45, 55, 60, 58, 65, 80, 75, 90],
                borderColor: '#0F5C4A',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#0F5C4A',
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#E8E3DA' } },
                x: { grid: { display: false } }
            },
            animation: { duration: 2000, easing: 'easeOutQuart' }
        }
    });

    // Status Donut Chart (Real Data)
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($status_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($status_counts); ?>,
                backgroundColor: ['#0F5C4A', '#E0A63E', '#2E7D32', '#3B6FA0', '#C0392B', '#8E44AD'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } }
            },
            animation: { duration: 2000, easing: 'easeOutBounce' }
        }
    });

    // Leads Bar Chart
    const ctxLeads = document.getElementById('leadsChart').getContext('2d');
    new Chart(ctxLeads, {
        type: 'bar',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7', 'Week 8'],
            datasets: [{
                label: 'New Enquiries',
                data: [45, 60, 55, 80, 95, 85, 110, 120],
                backgroundColor: '#0F5C4A',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#E8E3DA' } },
                x: { grid: { display: false } }
            },
            animation: { duration: 1500, delay: 500, easing: 'easeOutQuart' }
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
