document.addEventListener('DOMContentLoaded', function(event) {
    document.querySelectorAll('.delete-file-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const deleteUrl = this.getAttribute('data-delete-url');

            showConfirm("Êtes-vous sûr de vouloir supprimer ce fichier ?", function () {
                fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': window.csrf_token,
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error("Erreur HTTP " + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showAlert("fichier supprimé avec succès.", "success", 3000);
                            location.reload();
                        } else {
                            showAlert("Erreur lors de la suppression : " + (data.error || "Inconnue"), "error", 3000);
                        }
                    })
                    .catch(error => {
                        console.error("Erreur fetch:", error);
                        showAlert("Une erreur est survenue. Vérifiez votre connexion.", "error", 3000);
                    });
            });
        });
    });
});
