    </div> <!-- End content-area -->
</div> <!-- End main-content -->

<!-- External Libraries -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.8.0/countUp.umd.js"></script>

<style>
    .swal2-popup { border-radius: 12px !important; border: 1px solid var(--card-border) !important; padding: 2rem !important; }
    .swal2-title { font-family: 'Inter', sans-serif !important; font-size: 1.5rem !important; }
    .swal2-html-container { font-family: 'Inter', sans-serif !important; color: var(--text-body) !important; }
    .swal2-confirm { font-family: 'Inter', sans-serif !important; border-radius: 8px !important; padding: 0.8rem 1.5rem !important; font-weight: 500 !important; background-color: #C0392B !important; color: #fff !important; box-shadow: none !important; }
    .swal2-confirm:hover { background-color: #922B21 !important; }
    .swal2-cancel { font-family: 'Inter', sans-serif !important; border-radius: 8px !important; padding: 0.8rem 1.5rem !important; font-weight: 500 !important; background-color: #7F8C8D !important; color: #fff !important; box-shadow: none !important; }
    .swal2-cancel:hover { background-color: #606A6B !important; }
</style>
<script>
    // SweetAlert2 Delete Confirmation
    function confirmDelete(url) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            background: '#FFFFFF',
            color: '#11223b',
            iconColor: '#C79A4A',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    // Custom File Upload Preview Logic
    document.addEventListener("DOMContentLoaded", () => {
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            // Check if we need to wrap the input for custom UI
            if (!input.closest('.custom-file-upload')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'custom-file-upload';
                input.parentNode.insertBefore(wrapper, input);
                wrapper.appendChild(input);

                const label = document.createElement('label');
                label.className = 'custom-file-label';
                label.innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i><span>Click to upload or drag &amp; drop</span>';
                wrapper.appendChild(label);
            }

            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;
                const labelSpan = this.closest('.custom-file-upload')?.querySelector('span');
                if (labelSpan) labelSpan.textContent = file.name;
                
                // Show inline image preview
                const wrapper = this.closest('.custom-file-upload');
                if (wrapper) {
                    let preview = wrapper.querySelector('.file-preview-image');
                    if (!preview) {
                        const previewContainer = document.createElement('div');
                        previewContainer.className = 'file-preview-container';
                        preview = document.createElement('img');
                        preview.className = 'file-preview-image';
                        previewContainer.appendChild(preview);
                        wrapper.appendChild(previewContainer);
                    }
                    const reader = new FileReader();
                    reader.onload = e => { preview.src = e.target.result; }
                    reader.readAsDataURL(file);
                }
            });
        });
    });

    // Mobile Sidebar Controls
    function openSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const closeBtn = document.getElementById('sidebarClose');
        sidebar.classList.add('open');
        overlay.classList.add('active');
        if (closeBtn) closeBtn.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const closeBtn = document.getElementById('sidebarClose');
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        if (closeBtn) closeBtn.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Close sidebar when a nav link is clicked on mobile
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) closeSidebar();
        });
    });

    // On desktop resize, reset sidebar state
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('active');
            document.body.style.overflow = '';
        }
    });
</script>
</body>
</html>
