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

  // DELETE BUTTON LOGIC (ADMIN ONLY)
const deleteButtons = document.querySelectorAll('.bms-delete-btn');

deleteButtons.forEach(function (button) {
    button.addEventListener('click', function (e) {
        e.preventDefault();

        const postId = this.getAttribute('data-post-id');

        if (!confirm('Are you sure you want to delete this blog post?')) {
            return;
        }

        fetch(bmsAjaxObj.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                'action': 'bms_delete_post',
                'post_id': postId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.data.message);
                window.location.reload();
            } else {
                alert('Error deleting post: ' + data.data);
            }
        })
        .catch(error => {
            alert('Request failed: ' + error.message);
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

    if (loaderOverlay) {
        loaderOverlay.style.display = 'flex';
    }
    form.classList.add('blur');

    const formData = new FormData(form);
    formData.append('action', 'bms_add_post');

    fetch(bmsAjaxObj.ajax_url, {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (loaderOverlay) {
                loaderOverlay.style.display = 'none';
            }
            form.classList.remove('blur');

            if (data.success) {
                if (formContainer && successContainer) {
                    formContainer.style.display = 'none';
                    successContainer.style.display = 'block';

                    setTimeout(function () {
                        const modal = document.getElementById('bms-modal');
                        if (modal) {
                            modal.style.display = 'none';
                        }

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
            if (loaderOverlay) {
                loaderOverlay.style.display = 'none';
            }
            form.classList.remove('blur');

            console.error('Fetch error:', error);
            alert('An unexpected error occurred.');
        });
});


});
