function showAlert(message, type = 'info', duration = 5000) {
    const alertEl = document.getElementById('universal-alert');
    const messageEl = document.getElementById('universal-alert-message');

    // Appliquer la classe correspondant au type
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
// Exemple d'utilisation:
// showAlert("Opération réussie", "success", 5000);
// showAlert("Une erreur s'est produite", "error", 5000);

function showConfirm(message, onConfirm) {
    const modalEl = document.getElementById('universal-confirm-modal');
    const messageEl = document.getElementById('confirm-modal-message');
    const okBtn = document.getElementById('confirm-ok-btn');

    // Mettre à jour le message de confirmation
    messageEl.textContent = message;

    // Retirer d'éventuels événements antérieurs sur le bouton
    const newOkBtn = okBtn.cloneNode(true);
    okBtn.parentNode.replaceChild(newOkBtn, okBtn);

    // Ajouter l'événement pour confirmer
    newOkBtn.addEventListener('click', function() {
        onConfirm();
        // Fermer le modal
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        modalInstance.hide();
    });

    // Afficher le modal de confirmation
    const confirmModal = new bootstrap.Modal(modalEl);
    confirmModal.show();
}

// showConfirm("Voulez-vous vraiment supprimer cet élément ?", function() {
//     // Code à exécuter après confirmation
//     console.log("Elément supprimé");
// });
window.showConfirm = showConfirm;
