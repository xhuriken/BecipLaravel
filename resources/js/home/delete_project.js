document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-project-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const deleteUrl = this.getAttribute('data-delete-url');
            const projectId = this.getAttribute('data-project-id');

            // SweetAlert de confirmation
            Swal.fire({
                title: "Êtes-vous sûr de vouloir supprimer cette affaire ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#0c9155",
                cancelButtonColor: "#d33",
                confirmButtonText: "Oui",
                cancelButtonText: "Non"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Requête fetch pour supprimer l'affaire
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
                                // SweetAlert de succès
                                let projectTable = $('#project-table').DataTable();
                                let row = document.querySelector(`#project-row-${projectId}`);
                                if (row) {
                                    console.log(`Suppression de la ligne: project-row-${projectId}`);
                                    projectTable.row($(row)).remove().draw(false);
                                } else {
                                    console.warn(`Ligne non trouvée pour projectId: ${projectId}`);
                                }

                                Swal.fire({
                                    title: "Affaire supprimée avec succès.",
                                    icon: "success",
                                    timer: 3000,
                                    timerProgressBar: true
                                });
                            } else {
                                Swal.fire({
                                    title: "Erreur lors de la suppression",
                                    text: data.error || "Cause inconnue",
                                    icon: "error"
                                });
                            }
                        })
                        .catch(error => {
                            console.error("Erreur fetch:", error);
                            Swal.fire({
                                title: "Erreur",
                                text: "Une erreur est survenue. Vérifiez votre connexion.",
                                icon: "error"
                            });
                        });
                }
            });
        });
    });
});

