//
//
// USELESS NOW (Thanks Sweetalert)
//
//

function showAlert(message, type = 'info', duration = 5000) {
    const alertEl = document.getElementById('universal-alert');
    const messageEl = document.getElementById('universal-alert-message');

    alertEl.classList.remove('success', 'error', 'info');
    alertEl.classList.add(type);

    messageEl.textContent = message;
    alertEl.style.display = 'block';
    alertEl.style.opacity = '0.95';

    // Masquer l'alerte après la durée indiquée
    setTimeout(() => {
        alertEl.style.opacity = '0';
        setTimeout(() => {
            alertEl.style.display = 'none';
        }, 500);
    }, duration);
}

window.showAlert = showAlert;
// exemple:
// showAlert("Opération réussie", "success", 5000);
// showAlert("Une erreur s'est produite", "error", 5000);

function showConfirm(message, onConfirm) {
    const modalEl = document.getElementById('universal-confirm-modal');
    const messageEl = document.getElementById('confirm-modal-message');
    const okBtn = document.getElementById('confirm-ok-btn');

    messageEl.textContent = message;

    const newOkBtn = okBtn.cloneNode(true);
    okBtn.parentNode.replaceChild(newOkBtn, okBtn);

    newOkBtn.addEventListener('click', function() {
        onConfirm();
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        modalInstance.hide();
    });

    const confirmModal = new bootstrap.Modal(modalEl);
    confirmModal.show();
}
// exemple
// showConfirm("Voulez-vous vraiment supprimer cet élément ?", function() {
//     // Code à exécuter après confirmation
//     console.log("Elément supprimé");
// });
window.showConfirm = showConfirm;
