document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-file-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const deleteUrl = this.getAttribute('data-delete-url');

            // Confirm with SweetAlert
            Swal.fire({
                title: "Êtes-vous sûr de vouloir supprimer ce fichier ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#0c9155",
                cancelButtonColor: "#d33",
                confirmButtonText: "Oui",
                cancelButtonText: "Non"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': window.csrf_token,
                            'Content-Type': 'application/json'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error("HTTP error " + response.status);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: "Fichier supprimé avec succès.",
                                    icon: "success",
                                    timer: 3000,
                                    timerProgressBar: true
                                }).then(() => {
                                    // let row = document.querySelector(`tr[data-id="${fileId}"]`);
                                    // if (row) row.remove();
                                });
                            } else {
                                Swal.fire({
                                    title: "Erreur lors de la suppression",
                                    text: data.error || "Inconnue",
                                    icon: "error"
                                });
                            }
                        })
                        .catch(error => {
                            console.error("Fetch error:", error);
                            Swal.fire({
                                title: "Une erreur est survenue.",
                                text: "Vérifiez votre connexion.",
                                icon: "error"
                            });
                        });
                }
            });
        });
    });
});
