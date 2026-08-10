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
                label.innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i><span>Click to upload or drag & drop</span>';
                wrapper.appendChild(label);
            }

            input.addEventListener('change', function(e) {
                const fileName = e.target.files[0] ? e.target.files[0].name : 'Click to upload or drag & drop';
                const labelSpan = this.nextElementSibling.querySelector('span');
                if (labelSpan) {
                    labelSpan.textContent = fileName;
                }
            });
        });
    });
</script>
</body>
</html>
