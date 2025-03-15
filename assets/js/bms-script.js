document.addEventListener('DOMContentLoaded', function () {

    // MODAL LOGIC FOR ADD POST FORM
    const modal = document.getElementById('bms-modal');
    const btn = document.getElementById('bms-add-blog-btn');
    const closeBtn = document.querySelector('.bms-close');

    if (btn && modal && closeBtn) {
        btn.addEventListener('click', function () {
            modal.style.display = 'block';

            const formContainer = document.getElementById('bms-add-post-form-container');
            const successContainer = document.getElementById('bms-add-post-success-container');
            const loader = document.getElementById('bms-form-loader');

            if (formContainer && successContainer && loader) {
                formContainer.style.display = 'block';
                successContainer.style.display = 'none';
                loader.style.display = 'none';
            }
        });

        closeBtn.addEventListener('click', function () {
            modal.style.display = 'none';
        });

        window.addEventListener('click', function (e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }

    // DELETE BUTTON LOGIC
    const deleteButtons = document.querySelectorAll('.bms-delete-btn');

    deleteButtons.forEach(function (button) {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const postId = this.getAttribute('data-post-id');

            if (!confirm('Are you sure you want to delete this blog post?')) {
                return;
            }

            fetch(`${bms_ajax_obj.rest_url}wp/v2/bms_blog/${postId}`, {
                method: 'DELETE',
                headers: {
                    'X-WP-Nonce': bms_ajax_obj.nonce
                }
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => { throw new Error(data.message); });
                    }
                    return response.json();
                })
                .then(() => {
                    alert('Post deleted successfully!');
                    window.location.reload();
                })
                .catch(error => {
                    alert('Error deleting post: ' + error.message);
                });
        });
    });

    // AJAX TO ADD BLOG WITH THE FORM
    const form = document.getElementById('bms-add-post');

    if (!form) return;

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const formContainer = document.getElementById('bms-add-post-form-container');
        const successContainer = document.getElementById('bms-add-post-success-container');
        const loaderOverlay = document.getElementById('bms-form-loader');

        // Show loader overlay and blur the form
        if (loaderOverlay) {
            loaderOverlay.style.display = 'flex';
        }
        form.classList.add('blur');

        const formData = new FormData(form);
        formData.append('action', 'bms_add_post');

        fetch(bmsAddPost.ajax_url, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                // Hide loader and remove blur
                if (loaderOverlay) {
                    loaderOverlay.style.display = 'none';
                }
                form.classList.remove('blur');

                if (data.success) {
                    // Hide form, show success container
                    if (formContainer && successContainer) {
                        formContainer.style.display = 'none';
                        successContainer.style.display = 'block';

                        // Auto-close the modal after 6 seconds
                        setTimeout(function () {
                            const modal = document.getElementById('bms-modal');
                            if (modal) {
                                modal.style.display = 'none';
                            }

                            // Reset the UI if modal opens again later
                            formContainer.style.display = 'block';
                            successContainer.style.display = 'none';
                            form.reset();

                        }, 6000);
                    }
                } else {
                    alert('Failed to submit post: ' + (data.data || 'Unknown error'));
                }
            })
            .catch(error => {
                // Hide loader and remove blur on error
                if (loaderOverlay) {
                    loaderOverlay.style.display = 'none';
                }
                form.classList.remove('blur');

                console.error('Fetch error:', error);
                alert('An unexpected error occurred.');
            });
    });

});
