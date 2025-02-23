document.addEventListener('DOMContentLoaded', function() {

    const editModalEl = document.getElementById('editProjectModal');
    if(editModalEl){
        const editModal = new bootstrap.Modal(editModalEl);
    }

    $('#editProjectModal').on('shown.bs.modal', function () {
        $('#edit-project-clients').select2({
            placeholder: "Sélectionner des clients",
            allowClear: true,
            dropdownParent: $('#editProjectModal'),
            width: '100%',
            templateResult: formatClient,  // Ajoute les checkboxes
            templateSelection: formatClientSelection // Garde l'affichage propre
        });
    });
    document.querySelectorAll('.edit-project').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();

            const projectId = this.getAttribute('data-project-id');
            const projectName = this.getAttribute('data-project-name'); // Exemple: "B23.045"
            const companyId = this.getAttribute('data-company-id');
            const referentId = this.getAttribute('data-referent-id') || ""; // Si null, mettre ""
            const address = this.getAttribute('data-address') || ""; // Idem pour adresse
            const comment = this.getAttribute('data-comment') || ""; // Idem pour commentaire
            const clients = JSON.parse(this.getAttribute('data-clients')) || []; // Récupérer clients sous forme de tableau

            // Décomposer le nom de l'affaire (B XX . XXX)
            let match = projectName.match(/^B(\d{2})\.(\d{3})$/);
            let year = match ? match[1] : "";
            let number = match ? match[2] : "";

            // Remplir les champs
            document.getElementById('edit-project-id').value = projectId;
            document.getElementById('edit-project-year').value = year;
            document.getElementById('edit-project-number').value = number;
            document.getElementById('edit-project-name').value = projectName; // Stocker le nom complet

            document.getElementById('edit-project-company').value = companyId;
            document.getElementById('edit-project-referent').value = referentId;
            document.getElementById('edit-project-address').value = address;
            document.getElementById('edit-project-comment').value = comment;

            // Sélectionner les clients existants
            $('#edit-project-clients').val(clients).trigger('change');

            // Afficher le modal
            editModal.show();
        });
    });

    function updateProjectName() {
        const year = document.getElementById('edit-project-year').value.padStart(2, '0');
        const number = document.getElementById('edit-project-number').value.padStart(3, '0');
        document.getElementById('edit-project-name').value = `B${year}.${number}`;
    }

    document.getElementById('edit-project-year').addEventListener("input", updateProjectName);
    document.getElementById('edit-project-number').addEventListener("input", updateProjectName);


    document.getElementById('save-project-btn').addEventListener('click', function() {
        const projectId = document.getElementById('edit-project-id').value;
        const projectName = document.getElementById('edit-project-name').value;
        const companyId = document.getElementById('edit-project-company').value || null;
        const referentId = document.getElementById('edit-project-referent').value || null;
        const address = document.getElementById('edit-project-address').value || null;
        const comment = document.getElementById('edit-project-comment').value || null;
        const clients = $('#edit-project-clients').val() || [];

        if (!projectName.match(/^B\d{2}\.\d{3}$/)) {
            showAlert("Nom du projet mal renseigné.", "error", 3000);
            return;
        }

        const data = {
            project_id: projectId,
            project_name: projectName,
            company_id: companyId,
            referent_id: referentId,
            address: address,
            comment: comment,
            clients: clients,
            _token: window.csrf_token
        };

        fetch(window.updateProjectUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrf_token
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(response => {
                if (response.success) {
                    showAlert("Affaire mise à jour avec succès !", "success", 3000);
                    editModal.hide();
                    location.reload();
                } else {
                    showAlert("Erreur lors de la mise à jour.", "error", 3000);
                }
            })
            .catch(() => {
                showAlert("Une erreur est survenue lors de la mise à jour.", "error", 3000);
            });
    });

    const addProjectModalEl = document.getElementById('addProjectModal');
    const addProjectModal = new bootstrap.Modal(addProjectModalEl);

    $('#addProjectModal').on('shown.bs.modal', function () {
        $('#add-project-clients').select2({
            placeholder: "Sélectionner des clients",
            allowClear: true,
            dropdownParent: $('#addProjectModal'),
            width: '100%',
            templateResult: formatClient,  // Ajoute les checkboxes
            templateSelection: formatClientSelection // Garde l'affichage propre
        });
    });

    function formatClient(client) {
        if (!client.id) {
            return client.text;
        }
        return $('<span><input type="checkbox" class="select2-checkbox"> ' + client.text + '</span>');
    }

    function formatClientSelection(client) {
        return client.text;
    }

    const toggleButton = document.getElementById('toggleButton');
    toggleButton.addEventListener('click', function(e) {
        e.preventDefault();
        addProjectModal.show();
    });

    // Buttun modal click
    const submitAddProjectBtn = document.getElementById('submit-add-project-btn');
    submitAddProjectBtn.addEventListener('click', function() {
        // get all input
        const companyId = document.getElementById('add-project-company').value;
        const engineerId = document.getElementById('add-project-engineer').value;
        const yearVal = document.getElementById('add-project-year').value;
        const numberVal = document.getElementById('add-project-number').value;
        const clientsSelect = document.getElementById('add-project-clients');
        const clients = Array.from(clientsSelect.selectedOptions).map(opt => opt.value);

        if (yearVal.length !== 2 || numberVal.length !== 3) {
            showAlert("Nom du projet mal renseigné.", "error", 3000);
            return;
        }

        // create project name
        const projectName = "B" + yearVal + "." + numberVal;
        document.getElementById('add-project-name').value = projectName;

        // prepare data
        const data = {
            company_id: companyId,
            engineer_id: engineerId,
            project_name: projectName,
            clients: clients,
            _token: window.csrf_token
        };

        // send request with fetch
        fetch(window.storeProjectUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrf_token
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(({ status, body }) => {
                if (status === 200 && body.success) {
                    showAlert("Affaire ajoutée avec succès !", "success", 3000);
                    addProjectModal.hide();
                    location.reload();
                } else {
                    if (body.error) {
                        showAlert(body.error, "error", 3000);
                    } else {
                        showAlert("Erreur lors de l'ajout de l'affaire.", "error", 3000);
                    }
                }
            })
            .catch(error => {
                console.error("Erreur fetch:", error);
                showAlert("Une erreur est survenue.", "error", 3000);
            });

    });

    document.querySelectorAll('.delete-project-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const deleteUrl = this.getAttribute('data-delete-url');

            showConfirm("Êtes-vous sûr de vouloir supprimer cette affaire ?", function () {
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
                            showAlert("Affaire supprimée avec succès.", "success", 3000);
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
