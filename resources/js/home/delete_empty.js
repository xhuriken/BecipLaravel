document.addEventListener('DOMContentLoaded', function() {
    let deleteEmpty = document.querySelector("#delete-empty");
    if (deleteEmpty) {
        deleteEmpty.addEventListener('click', function() {
            let route = this.getAttribute('data-route');

            Swal.fire({
                title: "Êtes-vous sûr ?",
                text: "Tous les projets sans entreprise ni référent seront supprimés.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#0c9155",
                cancelButtonColor: "#d33",
                confirmButtonText: "Oui"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(route, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": window.csrf_token
                        },
                        body: JSON.stringify({})
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                data.deleted_ids.forEach(function(id) {
                                    let row = document.querySelector(`#project-row-${id}`);
                                    if (row) row.remove();
                                });
                                Swal.fire({
                                    title: "Affaires vides supprimées !",
                                    icon: "success",
                                    timer: 1200,
                                    timerProgressBar: true
                                });
                            } else {
                                Swal.fire({
                                    title: "Erreur",
                                    text: "Impossible de supprimer les affaires vides.",
                                    icon: "error"
                                });
                            }
                        })
                        .catch(error => {
                            console.error("Error deleting empty projects:", error);
                            Swal.fire({
                                title: "Erreur",
                                text: "Quelque chose s'est mal passé.",
                                icon: "error"
                            });
                        });
                }
            });
        });
    }
});
