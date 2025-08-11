document.addEventListener('DOMContentLoaded', function() {
    // Responsive sidebar toggle
    const menuToggle = document.getElementById('menu-toggle');
    const adminWrapper = document.querySelector('.admin-wrapper');

    if (menuToggle && adminWrapper) {
        menuToggle.addEventListener('click', function() {
            adminWrapper.classList.toggle('sidebar-closed');
        });
    }

    // Settings form AJAX submission
    const settingsForm = document.querySelector('form[action="settings.php"]');
    if (settingsForm) {
        settingsForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            const clickedButton = e.submitter;
            if (clickedButton && clickedButton.name) {
                formData.append(clickedButton.name, clickedButton.value);
            }

            fetch('settings.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please check the console.');
            });
        });
    }

    // Gallery Upload Form AJAX
    const galleryUploadForm = document.querySelector('.gallery-upload-form');
    if (galleryUploadForm) {
        galleryUploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('gallery.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') {
                        window.location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    }

    // Gallery Delete Forms AJAX
    const galleryDeleteForms = document.querySelectorAll('.gallery-item form');
    galleryDeleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this image?')) {
                const formData = new FormData(this);
                fetch('gallery.php', { method: 'POST', body: formData })
                    .then(response => response.json())
                    .then(data => {
                        alert(data.message);
                        if (data.status === 'success') {
                            window.location.reload();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    });

    // Add Menu Form AJAX
    const addMenuForm = document.getElementById('add-menu-form');
    if (addMenuForm) {
        addMenuForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const messageDiv = document.getElementById('ajax-message');

            fetch('menu-add.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    messageDiv.textContent = data.message;
                    messageDiv.className = 'message ' + (data.status === 'success' ? 'success' : 'error');
                    messageDiv.style.display = 'block';

                    if (data.status === 'success') {
                        addMenuForm.reset(); // Clear the form
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    messageDiv.textContent = 'A network error occurred.';
                    messageDiv.className = 'message error';
                    messageDiv.style.display = 'block';
                });
        });
    }

    // Gallery media type switcher
    const mediaTypeRadios = document.querySelectorAll('input[name="media_type"]');
    const uploadField = document.getElementById('upload-field');
    const embedField = document.getElementById('embed-field');

    if (mediaTypeRadios.length) {
        mediaTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'video_embed') {
                    uploadField.style.display = 'none';
                    embedField.style.display = 'block';
                } else {
                    uploadField.style.display = 'block';
                    embedField.style.display = 'none';
                }
            });
        });
    }
});
