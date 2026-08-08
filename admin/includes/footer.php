    </div> <!-- End content-area -->
</div> <!-- End main-content -->

<!-- External Libraries -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.8.0/countUp.umd.js"></script>

<script>
    // SweetAlert2 Delete Confirmation
    function confirmDelete(url) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0F5C4A',
            cancelButtonColor: '#C0392B',
            confirmButtonText: 'Yes, delete it!'
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
                
                const previewContainer = document.createElement('div');
                previewContainer.className = 'file-preview-container';
                previewContainer.style.display = 'none';
                
                const previewImg = document.createElement('img');
                previewImg.className = 'file-preview-image';
                
                const label = document.createElement('label');
                label.className = 'custom-file-label';
                label.innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i> <span>Choose an image to upload</span>';
                
                input.parentNode.insertBefore(wrapper, input);
                wrapper.appendChild(label);
                label.appendChild(input);
                
                previewContainer.appendChild(previewImg);
                wrapper.appendChild(previewContainer);

                // Preview Logic
                input.addEventListener('change', function(e) {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImg.src = e.target.result;
                            previewContainer.style.display = 'block';
                            label.querySelector('span').textContent = input.files[0].name;
                        }
                        reader.readAsDataURL(this.files[0]);
                    } else {
                        previewContainer.style.display = 'none';
                        label.querySelector('span').textContent = 'Choose an image to upload';
                    }
                });
            }
        });
    });
</script>
</body>
</html>
