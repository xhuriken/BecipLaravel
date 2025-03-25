$(document).ready(function() {
    let companiesTable = $('#companies-table').DataTable({
        destroy: true,
        responsive: true,
        language: {
            "decimal": ",",
            "thousands": ".",
            "sProcessing": "Traitement en cours...",
            "sSearch": "Rechercher :",
            "sLengthMenu": "_MENU_",
            "sInfo": "Affichage de _START_ à _END_ sur _TOTAL_ éléments",
            "sInfoEmpty": "Affichage de 0 à 0 sur 0 éléments",
            "sInfoFiltered": "(filtré à partir de _MAX_ éléments au total)",
            "sInfoPostFix": "",
            "sLoadingRecords": "Chargement en cours...",
            "sZeroRecords": "Aucun élément à afficher",
            "sEmptyTable": "Aucune donnée disponible dans le tableau",
            "oAria": {
                "sSortAscending": ": activer pour trier la colonne par ordre croissant",
                "sSortDescending": ": activer pour trier la colonne par ordre décroissant"
            }
        },
        columnDefs: [
            { orderable: false, targets: 1 }
        ]
    });

    let usersTable = $('#users-table').DataTable({
        destroy: true,
        responsive: true,
        scrollX: true,
        autoWidth: false,
        language: {
            "decimal": ",",
            "thousands": ".",
            "sProcessing": "Traitement en cours...",
            "sSearch": "Rechercher :",
            "sLengthMenu": "_MENU_",
            "sInfo": "Affichage de _START_ à _END_ sur _TOTAL_ éléments",
            "sInfoEmpty": "Affichage de 0 à 0 sur 0 éléments",
            "sInfoFiltered": "(filtré à partir de _MAX_ éléments au total)",
            "sInfoPostFix": "",
            "sLoadingRecords": "Chargement en cours...",
            "sZeroRecords": "Aucun élément à afficher",
            "sEmptyTable": "Aucune donnée disponible dans le tableau",
            "oAria": {
                "sSortAscending": ": activer pour trier la colonne par ordre croissant",
                "sSortDescending": ": activer pour trier la colonne par ordre décroissant"
            }
        },
        columnDefs: [
            { orderable: false, targets: 4 }
        ]
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const roles = window.allRoles;

    // Edit Company
    document.addEventListener('click', function(event) {
        if(event.target.closest('.edit-company')) {
            event.stopImmediatePropagation();

            const btn = event.target.closest('.edit-company');
            const $row = $(btn).closest('tr');
            const $nameCell = $row.find('.company-name');
            const currentName = $nameCell.text().trim();

            // replace text by an input
            $nameCell.html(`<input type="text" class="form-control" value="${currentName}" />`);

            // change btn to save mode
            btn.classList.remove('edit-company', 'btn-primary');
            btn.classList.add('save-company', 'btn-warning');
            btn.innerHTML = '<i class="fa fa-floppy-o"></i>';
        }
    });

    // Save Company
    document.addEventListener('click', function(event) {
        if(event.target.closest('.save-company')) {
            event.stopImmediatePropagation();
            const btn = event.target.closest('.save-company');
            const route = btn.getAttribute('data-route');
            const $row = $(btn).closest('tr');
            const companyId = $row.data('company-id');
            const newName = $row.find('.company-name input').val();

            if (newName === "") {
                Swal.fire({
                    title: "Le nom ne peut pas être vide.",
                    icon: "warning"
                });
                return;
            }

            fetch(route, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrf_token
                },
                body: JSON.stringify({ company_id: companyId, name: newName })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $row.find('.company-name').text(newName);
                        btn.classList.remove('save-company', 'btn-warning');
                        btn.classList.add('edit-company', 'btn-primary');
                        btn.innerHTML = '<i class="fa fa-pencil"></i>';
                        Swal.fire({
                            title: "Entreprise modifiée avec succès.",
                            icon: "success",
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: true,
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: 'btn btn-success'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: data.message || 'Erreur inconnue',
                            icon: "error"
                        }).then(() => {
                            location.reload();
                        });
                    }
                })
                .catch(() => {
                    Swal.fire({
                        title: "Une erreur est survenue lors de la sauvegarde.",
                        icon: "error"
                    }).then(() => {
                        location.reload();
                    });
                });
        }
    });

    // Delete Company
    document.addEventListener('click', function(event) {
        if(event.target.closest('.delete-company')) {
            event.stopImmediatePropagation();
            const btn = event.target.closest('.delete-company');
            const route = btn.getAttribute('data-route');
            const $row = $(btn).closest('tr');
            const companyId = $row.data('company-id');

            Swal.fire({
                title: "Supprimer cette entreprise ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#0c9155",
                cancelButtonColor: "#d33",
                confirmButtonText: "Oui",
                cancelButtonText: "Non"
            }).then((result) => {
                if(result.isConfirmed) {
                    fetch(route, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.csrf_token
                        },
                        body: JSON.stringify({ company_id: companyId })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const companiesTable = $('#companies-table').DataTable();
                                companiesTable.row($row).remove().draw();
                                Swal.fire({
                                    title: "Entreprise supprimée avec succès.",
                                    icon: "success",
                                    timer: 2000,
                                    timerProgressBar: true,
                                    showConfirmButton: true,
                                    confirmButtonText: "OK",
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    },
                                    buttonsStyling: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: data.message || 'Erreur inconnue',
                                    icon: "error"
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        })
                        .catch(() => {
                            Swal.fire({
                                title: "Une erreur est survenue lors de la suppression.",
                                icon: "error"
                            }).then(() => {
                                location.reload();
                            });
                        });
                }
            });
        }
    });

    // edit User
    document.addEventListener('click', function(event) {
        if(event.target.closest('.edit-user')) {
            event.stopImmediatePropagation();

            const btn = event.target.closest('.edit-user');
            const $row = $(btn).closest('tr');

            const $nameCell = $row.find('.user-name');
            const $emailCell = $row.find('.user-email');
            const $roleCell = $row.find('.user-role');
            const $companyCell = $row.find('.user-company');

            const currentName = $nameCell.text().trim();
            const currentEmail = $emailCell.text().trim();
            const currentRole = $roleCell.attr('data-role'); // Get current role (in english)
            const currentCompany = $companyCell.text().trim();

            if (!currentRole) {
                console.error("Problème avec currentRole, valeur vide :", $roleCell);
            }

            // Change to edit input mode
            $nameCell.html(`<input type="text" class="form-control" value="${currentName}" />`);
            $emailCell.html(`<input type="text" class="form-control" value="${currentEmail}" />`);

            // Replace with Select and pre-select current role
            const roleOptions = Object.keys(window.allRoles).map(role =>
                `<option value="${role}" ${role === currentRole ? 'selected' : ''}>${window.allRoles[role]}</option>`
            ).join('');
            $roleCell.html(`<select class="form-control">${roleOptions}</select>`);

            // Replace with Company Select
            let companyOptions = `<option value="">Aucune</option>`;
            allCompanies.forEach(c => {
                companyOptions += `<option value="${c.id}" ${c.name === currentCompany ? 'selected' : ''}>${c.name}</option>`;
            });
            $companyCell.html(`<select class="form-control">${companyOptions}</select>`);

            // Change btn to save mode
            btn.classList.remove('edit-user', 'btn-primary');
            btn.classList.add('save-user', 'btn-warning');
            btn.innerHTML = '<i class="fa fa-floppy-o"></i>';
        }
    });

    // Save User
    document.addEventListener('click', function(event) {
        if(event.target.closest('.save-user')) {
            event.stopImmediatePropagation();

            const btn = event.target.closest('.save-user');
            const route = btn.getAttribute('data-route');
            const $row = $(btn).closest('tr');
            const userId = $row.data('user-id');

            const newName = $row.find('.user-name input').val().trim();
            const newEmail = $row.find('.user-email input').val().trim();
            const newRole = $row.find('.user-role select').val();
            const newCompanyId = $row.find('.user-company select').val();

            // verify if name isnt empty
            if (newName === "") {
                Swal.fire({
                    title: "Le nom ne peut pas être vide.",
                    icon: "warning"
                });
                return;
            }

            // verify email valid
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(newEmail)) {
                Swal.fire({
                    title: "Veuillez entrer une adresse email valide (exemple : x@x.x).",
                    icon: "warning"
                });
                return;
            }

            const data = {
                user_id: userId,
                name: newName,
                email: newEmail,
                role: newRole,
                company_id: newCompanyId
            };

            fetch(route, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrf_token
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $row.find('.user-name').text(newName);
                        $row.find('.user-email').text(newEmail);
                        $row.find('.user-role')
                            .text(window.allRoles[newRole])
                            .attr('data-role', newRole);
                        const selectedCompany = allCompanies.find(c => c.id == newCompanyId);
                        $row.find('.user-company').text(selectedCompany ? selectedCompany.name : 'Aucune');

                        btn.classList.remove('save-user', 'btn-warning');
                        btn.classList.add('edit-user', 'btn-primary');
                        btn.innerHTML = '<i class="fa fa-pencil"></i>';

                        Swal.fire({
                            title: "Utilisateur modifié avec succès.",
                            icon: "success",
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: true,
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: 'btn btn-success'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: data.message || 'Erreur inconnue',
                            icon: "error"
                        }).then(() => {
                            location.reload();
                        });
                    }
                })
                .catch(() => {
                    Swal.fire({
                        title: "Une erreur est survenue lors de la sauvegarde.",
                        icon: "error"
                    }).then(() => {
                        location.reload();
                    });
                });
        }
    });

    // Delete User
    document.addEventListener('click', function(event) {
        if(event.target.closest('.delete-user')) {
            event.stopImmediatePropagation();
            const btn = event.target.closest('.delete-user');
            const route = btn.getAttribute('data-route');
            const $row = $(btn).closest('tr');
            const userId = $row.data('user-id');

            Swal.fire({
                title: "Supprimer cet utilisateur ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#0c9155",
                cancelButtonColor: "#d33",
                confirmButtonText: "Oui",
                cancelButtonText: "Non"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(route, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.csrf_token
                        },
                        body: JSON.stringify({ user_id: userId })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const usersTable = $('#users-table').DataTable();
                                usersTable.row($row).remove().draw();
                                Swal.fire({
                                    title: "Utilisateur supprimé avec succès.",
                                    icon: "success",
                                    timer: 2000,
                                    timerProgressBar: true,
                                    showConfirmButton: true,
                                    confirmButtonText: "OK",
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    },
                                    buttonsStyling: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: data.message || 'Erreur inconnue',
                                    icon: "error"
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        })
                        .catch(() => {
                            Swal.fire({
                                title: "Une erreur est survenue lors de la suppression.",
                                icon: "error"
                            }).then(() => {
                                location.reload();
                            });
                        });
                }
            });
        }
    });
});
