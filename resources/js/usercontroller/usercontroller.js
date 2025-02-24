$(document).ready(function() {
    let companiesTable = $('#companies-table').DataTable({
        language: {
            "decimal": ",",
            "thousands": ".",
            "sProcessing": "Traitement en cours...",
            "sSearch": "Rechercher :",
            "sLengthMenu": "Afficher _MENU_ éléments",
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
        }
    });

    let usersTable = $('#users-table').DataTable({
        language: {
            "decimal": ",",
            "thousands": ".",
            "sProcessing": "Traitement en cours...",
            "sSearch": "Rechercher :",
            "sLengthMenu": "Afficher _MENU_ éléments",
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
        }
    });
});
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const roles = window.allRoles;
    // Supposons que companiesTable est déjà initialisé via DataTables
    // -------------------------------
    // a) Éditer une entreprise
    document.addEventListener('click', function(event) {
        if(event.target.closest('.edit-company')) {
            event.stopImmediatePropagation(); // Empeche le double evenement (pour le bouton et le <i>)

            const btn = event.target.closest('.edit-company');
            const $row = $(btn).closest('tr');
                    // Récupération de la cellule contenant le nom
            const $nameCell = $row.find('.company-name');
            const currentName = $nameCell.text().trim();

            // Remplacer le texte par un input
            $nameCell.html(`<input type="text" class="form-control" value="${currentName}" />`);

            // Transformer le bouton en mode sauvegarde (disquette)
            btn.classList.remove('edit-company', 'btn-primary');
            btn.classList.add('save-company', 'btn-warning');
            btn.innerHTML = '<i class="fa fa-floppy-o"></i>';
        }
    });

    // b) Sauvegarder une entreprise avec Fetch
    document.addEventListener('click', function(event) {
        if(event.target.closest('.save-company')) {
            event.stopImmediatePropagation(); // Empeche le double evenement (pour le bouton et le <i>)
            const btn = event.target.closest('.save-company');
            const route = btn.getAttribute('data-route'); // URL définie dans l'attribut data-route
            const $row = $(btn).closest('tr');
            const companyId = $row.data('company-id');
            const newName = $row.find('.company-name input').val();

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
                        // Remettre la cellule en mode lecture
                        $row.find('.company-name').text(newName);
                        // Revenir au bouton "éditer"
                        btn.classList.remove('save-company', 'btn-warning');
                        btn.classList.add('edit-company', 'btn-primary');
                        btn.innerHTML = '<i class="fa fa-pencil"></i>';
                    } else {
                        alert(data.message || 'Erreur inconnue');
                    }
                })
                .catch(() => {
                    alert('Une erreur est survenue lors de la sauvegarde.');
                });
        }
    });

    // c) Supprimer une entreprise avec Fetch
    document.addEventListener('click', function(event) {
        if(event.target.closest('.delete-company')) {

            if(!confirm('Supprimer cette entreprise ?')) return;

            const btn = event.target.closest('.delete-company');
            const route = btn.getAttribute('data-route'); // URL définie dans l'attribut data-route
            const $row = $(btn).closest('tr');
            const companyId = $row.data('company-id');

            fetch(route, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ company_id: companyId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Suppression de la ligne dans le DataTable
                        const companiesTable = $('#companies-table').DataTable();
                        companiesTable.row($row).remove().draw();
                    } else {
                        alert(data.message || 'Erreur inconnue');
                    }
                })
                .catch(() => {
                    alert('Une erreur est survenue lors de la suppression.');
                });
        }
    });

    document.addEventListener('click', function(event) {
        if(event.target.closest('.edit-user')) {
            event.stopImmediatePropagation(); // Empeche le double evenement (pour le bouton et le <i>)

            const btn = event.target.closest('.edit-user');
            const $row = $(btn).closest('tr');

            // Récupération des cellules
            const $nameCell = $row.find('.user-name');
            const $emailCell = $row.find('.user-email');
            const $roleCell = $row.find('.user-role');
            const $companyCell = $row.find('.user-company');

            const currentName = $nameCell.text().trim();
            const currentEmail = $emailCell.text().trim();
            const currentRole = $roleCell.text().trim();
            const currentCompany = $companyCell.text().trim();

            // Transformation en champs éditables
            $nameCell.html(`<input type="text" class="form-control" value="${currentName}" />`);
            $emailCell.html(`<input type="text" class="form-control" value="${currentEmail}" />`);

            // Select pour le rôle

            //TODO: replace by french word roles

            const roleOptions = roles.map(role =>
                `<option value="${role}" ${role === currentRole ? 'selected' : ''}>${role}</option>`
            ).join('');
            $roleCell.html(`<select class="form-control">${roleOptions}</select>`);

            // Select pour l'entreprise
            let companyOptions = `<option value="">Aucune</option>`;
            allCompanies.forEach(c => {
                companyOptions += `<option value="${c.id}" ${c.name === currentCompany ? 'selected' : ''}>${c.name}</option>`;
            });
            $companyCell.html(`<select class="form-control">${companyOptions}</select>`);

            // Passage du bouton en mode sauvegarde
            btn.classList.remove('edit-user', 'btn-primary');
            btn.classList.add('save-user', 'btn-warning');
            btn.innerHTML = '<i class="fa fa-floppy-o"></i>';
        }
    });

    // b) Sauvegarder un utilisateur avec Fetch
    document.addEventListener('click', function(event) {
        if(event.target.closest('.save-user')) {
            event.stopImmediatePropagation(); // Empeche le double evenement (pour le bouton et le <i>)

            const btn = event.target.closest('.save-user');
            const route = btn.getAttribute('data-route'); // URL définie dans data-route
            const $row = $(btn).closest('tr');
            const userId = $row.data('user-id');

            const newName = $row.find('.user-name input').val();
            const newEmail = $row.find('.user-email input').val();
            const newRole = $row.find('.user-role select').val();
            const newCompanyId = $row.find('.user-company select').val();

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
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $row.find('.user-name').text(newName);
                        $row.find('.user-email').text(newEmail);
                        $row.find('.user-role').text(newRole);
                        const selectedCompany = allCompanies.find(c => c.id == newCompanyId);
                        $row.find('.user-company').text(selectedCompany ? selectedCompany.name : 'Aucune');

                        btn.classList.remove('save-user', 'btn-warning');
                        btn.classList.add('edit-user', 'btn-primary');
                        btn.innerHTML = '<i class="fa fa-pencil"></i>';
                    } else {
                        alert(data.message || 'Erreur inconnue');
                    }
                })
                .catch(() => {
                    alert('Une erreur est survenue lors de la sauvegarde.');
                });
        }
    });

    // c) Supprimer un utilisateur avec Fetch
    document.addEventListener('click', function(event) {
        if(event.target.closest('.delete-user')) {
            if(!confirm('Supprimer cet utilisateur ?')) return;
            const btn = event.target.closest('.delete-user');
            const route = btn.getAttribute('data-route'); // URL définie dans data-route
            const $row = $(btn).closest('tr');
            const userId = $row.data('user-id');

            fetch(route, {
                method: 'POST', // ou DELETE, selon ta configuration
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ user_id: userId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const usersTable = $('#users-table').DataTable();
                        usersTable.row($row).remove().draw();
                    } else {
                        alert(data.message || 'Erreur inconnue');
                    }
                })
                .catch(() => {
                    alert('Une erreur est survenue lors de la suppression.');
                });
        }
    });
});
