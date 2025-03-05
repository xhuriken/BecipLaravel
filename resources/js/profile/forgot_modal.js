document.addEventListener("DOMContentLoaded", function() {
    let openModalBtn = document.getElementById("open-change-password");

    if (openModalBtn) {
        openModalBtn.addEventListener("click", function() {
            let modal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
            modal.show();
        });
    }
});
