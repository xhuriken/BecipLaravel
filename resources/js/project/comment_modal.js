document.addEventListener('DOMContentLoaded', function() {
    const commentModalEl = document.getElementById('commentModal');
    if (commentModalEl) {
        const commentModal = new bootstrap.Modal(commentModalEl);
        const commentText = document.getElementById('commentModalText');

        document.querySelectorAll('.view-comment').forEach(link => {
            link.addEventListener('click', function(event) {
                commentText.textContent = this.getAttribute('data-comment');
                commentModal.show();
            });
        });
    }
});
