document.addEventListener('DOMContentLoaded', function() {
    // ---------------------------
    // EDIT PROJECT MODAL
    // ---------------------------
    const editModalEl = document.getElementById('editProjectModal');
    let editModal = null;
    if (editModalEl) {
        editModal = new bootstrap.Modal(editModalEl);
    }

    // Initialize select2 for edit-project-clients when the edit modal is shown
    if (document.getElementById('editProjectModal')) {
        $('#editProjectModal').on('shown.bs.modal', function () {
            $('#edit-project-clients').select2({
                placeholder: "Select clients",
                allowClear: true,
                dropdownParent: $('#editProjectModal'),
                width: '100%',
                templateResult: formatClient,       // Adds checkboxes to options
                templateSelection: formatClientSelection // Clean display for selected option
            });
        });
    }

    // Add click event to all edit buttons if they exist
    const editButtons = document.querySelectorAll('.edit-project');
    if (editButtons) {
        editButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                // Retrieve data attributes from the clicked button
                const projectId = this.getAttribute('data-project-id');
                const projectName = this.getAttribute('data-project-name'); // e.g. "B23.045"
                const companyId = this.getAttribute('data-company-id');
                const referentId = this.getAttribute('data-referent-id') || "";
                const address = this.getAttribute('data-address') || "";
                const comment = this.getAttribute('data-comment') || "";
                let clients = [];
                try {
                    clients = JSON.parse(this.getAttribute('data-clients')) || [];
                } catch(e) {
                    clients = [];
                }

                // Split project name into year and number using regex
                let match = projectName.match(/^B(\d{2})\.(\d{3})$/);
                let year = match ? match[1] : "";
                let number = match ? match[2] : "";

                // Fill in the modal form fields if they exist
                document.getElementById('edit-project-id').value = projectId;
                document.getElementById('edit-project-year').value = year;
                document.getElementById('edit-project-number').value = number;
                document.getElementById('edit-project-name').value = projectName; // Stocker le nom complet

                document.getElementById('edit-project-company').value = companyId;
                document.getElementById('edit-project-referent').value = referentId;
                document.getElementById('edit-project-address').value = address;
                document.getElementById('edit-project-comment').value = comment;

                // Set selected clients in the select2 element
                $('#edit-project-clients').val(clients).trigger('change');

                // Show the modal
                editModal.show();
            });
        });
    }

    // Update project name in edit modal when year/number fields change
    function updateProjectName() {
        const yearInput = document.getElementById('edit-project-year');
        const numberInput = document.getElementById('edit-project-number');
        const nameInput = document.getElementById('edit-project-name');
        if (!yearInput || !numberInput || !nameInput) return;
        const year = yearInput.value.padStart(2, '0');
        const number = numberInput.value.padStart(3, '0');
        // Only update if both fields have the proper length
        if (yearInput.value.length === 2 && numberInput.value.length === 3) {
            nameInput.value = `B${year}.${number}`;
        } else {
            nameInput.value = "";
        }
    }
    if (document.getElementById('edit-project-year') && document.getElementById('edit-project-number')) {
        document.getElementById('edit-project-year').addEventListener("input", updateProjectName);
        document.getElementById('edit-project-number').addEventListener("input", updateProjectName);
    }

    // Save changes from the edit modal
    const saveProjectBtn = document.getElementById('save-project-btn');
    if (saveProjectBtn) {
        saveProjectBtn.addEventListener('click', function() {
            const projectId = document.getElementById('edit-project-id') ? document.getElementById('edit-project-id').value : "";
            const projectName = document.getElementById('edit-project-name') ? document.getElementById('edit-project-name').value : "";
            const companyId = document.getElementById('edit-project-company') ? document.getElementById('edit-project-company').value : null;
            const referentId = document.getElementById('edit-project-referent') ? document.getElementById('edit-project-referent').value : null;
            const address = document.getElementById('edit-project-address') ? document.getElementById('edit-project-address').value : null;
            const comment = document.getElementById('edit-project-comment') ? document.getElementById('edit-project-comment').value : null;
            const clients = $('#edit-project-clients').val() || [];

            // Validate project name format "BXX.XXX"
            if (!projectName.match(/^B\d{2}\.\d{3}$/)) {
                showAlert("Project name is invalid. Please fill it in correctly (e.g. B23.045).", "error", 3000);
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
                        showAlert("Project updated successfully!", "success", 3000);
                        if (editModal) { editModal.hide(); }
                        location.reload();
                    } else {
                        showAlert("Error updating project.", "error", 3000);
                    }
                })
                .catch(() => {
                    showAlert("An error occurred while updating the project.", "error", 3000);
                });
        });
    }

    // ---------------------------
    // ADD PROJECT MODAL
    // ---------------------------
    const addProjectModalEl = document.getElementById('addProjectModal');
    let addProjectModal = null;
    if (addProjectModalEl) {
        addProjectModal = new bootstrap.Modal(addProjectModalEl);
    }

    $('#addProjectModal').on('shown.bs.modal', function () {
        $('#add-project-clients').select2({
            placeholder: "Select clients",
            allowClear: true,
            dropdownParent: $('#addProjectModal'),
            width: '100%',
            templateResult: formatClient,       // Add checkboxes to options
            templateSelection: formatClientSelection // Clean display for selection
        });
    });

    // Functions for formatting select2 options
    function formatClient(client) {
        if (!client.id) return client.text;
        return $('<span><input type="checkbox" class="select2-checkbox"> ' + client.text + '</span>');
    }
    function formatClientSelection(client) {
        return client.text;
    }

    // Toggle Add Project Modal when button is clicked
    const toggleButton = document.getElementById('toggleButton');
    if (toggleButton && addProjectModal) {
        toggleButton.addEventListener('click', function(e) {
            e.preventDefault();
            addProjectModal.show();
        });
    }

    // Update project name in add modal based on year and number inputs
    const yearA = document.getElementById("add-project-year");
    const numberA = document.getElementById("add-project-number");
    const projectNameA = document.getElementById("add-project-name");

    function updateProjectNameA() {
        if (!yearA || !numberA || !projectNameA) return;
        const yearVal = yearA.value;
        const numberVal = numberA.value;
        // Only update if both fields have correct length
        if (yearVal.length !== 2 || numberVal.length !== 3) {
            projectNameA.value = "";
            return;
        }
        projectNameA.value = `B${yearVal}.${numberVal}`;
    }
    if (yearA && numberA && projectNameA) {
        yearA.addEventListener("input", updateProjectNameA);
        numberA.addEventListener("input", updateProjectNameA);
        yearA.addEventListener("keypress", function(e) {
            if (!/^\d$/.test(e.key)) e.preventDefault();
        });
        numberA.addEventListener("keypress", function(e) {
            if (!/^\d$/.test(e.key)) e.preventDefault();
        });
    }

    // Submit Add Project Modal form
    const submitAddProjectBtn = document.getElementById('submit-add-project-btn');
    if (submitAddProjectBtn) {
        submitAddProjectBtn.addEventListener('click', function() {
            const companyId = document.getElementById('add-project-company') ? document.getElementById('add-project-company').value : "";
            const engineerId = document.getElementById('add-project-engineer') ? document.getElementById('add-project-engineer').value : "";
            const yearVal = document.getElementById('add-project-year') ? document.getElementById('add-project-year').value : "";
            const numberVal = document.getElementById('add-project-number') ? document.getElementById('add-project-number').value : "";
            const clientsSelect = document.getElementById('add-project-clients');
            const clients = clientsSelect ? Array.from(clientsSelect.selectedOptions).map(opt => opt.value) : [];

            if (yearVal.length !== 2 || numberVal.length !== 3) {
                showAlert("Project name is invalid. Please fill in correctly (e.g. B23.045).", "error", 3000);
                return;
            }

            const projectName = `B${yearVal}.${numberVal}`;
            if (projectNameA) {
                projectNameA.value = projectName;
            }

            const data = {
                company_id: companyId,
                engineer_id: engineerId,
                project_name: projectName,
                clients: clients,
                _token: window.csrf_token
            };

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
                        showAlert("Project added successfully!", "success", 3000);
                        if (addProjectModal) { addProjectModal.hide(); }
                        location.reload();
                    } else {
                        if (body.error) {
                            showAlert(body.error, "error", 3000);
                        } else {
                            showAlert("Error adding project.", "error", 3000);
                        }
                    }
                })
                .catch(error => {
                    console.error("Fetch error:", error);
                    showAlert("An error occurred.", "error", 3000);
                });
        });
    }
});

//document.addEventListener('DOMContentLoaded', function() {
//
//     const editModalEl = document.getElementById('editProjectModal');
//     if(editModalEl){
//         const editModal = new bootstrap.Modal(editModalEl);
//     }
//
//     $('#editProjectModal').on('shown.bs.modal', function () {
//         $('#edit-project-clients').select2({
//             placeholder: "Sélectionner des clients",
//             allowClear: true,
//             dropdownParent: $('#editProjectModal'),
//             width: '100%',
//             templateResult: formatClient,  // Ajoute les checkboxes
//             templateSelection: formatClientSelection // Garde l'affichage propre
//         });
//     });
//     document.querySelectorAll('.edit-project').forEach(btn => {
//         btn.addEventListener('click', function(e) {
//             e.preventDefault();
//
//             const projectId = this.getAttribute('data-project-id');
//             const projectName = this.getAttribute('data-project-name'); // Exemple: "B23.045"
//             const companyId = this.getAttribute('data-company-id');
//             const referentId = this.getAttribute('data-referent-id') || ""; // Si null, mettre ""
//             const address = this.getAttribute('data-address') || ""; // Idem pour adresse
//             const comment = this.getAttribute('data-comment') || ""; // Idem pour commentaire
//             const clients = JSON.parse(this.getAttribute('data-clients')) || []; // Récupérer clients sous forme de tableau
//
//             // Décomposer le nom de l'affaire (B XX . XXX)
//             let match = projectName.match(/^B(\d{2})\.(\d{3})$/);
//             let year = match ? match[1] : "";
//             let number = match ? match[2] : "";
//
//             // Remplir les champs
//             document.getElementById('edit-project-id').value = projectId;
//             document.getElementById('edit-project-year').value = year;
//             document.getElementById('edit-project-number').value = number;
//             document.getElementById('edit-project-name').value = projectName; // Stocker le nom complet
//
//             document.getElementById('edit-project-company').value = companyId;
//             document.getElementById('edit-project-referent').value = referentId;
//             document.getElementById('edit-project-address').value = address;
//             document.getElementById('edit-project-comment').value = comment;
//
//             // Sélectionner les clients existants
//             $('#edit-project-clients').val(clients).trigger('change');
//
//             // Afficher le modal
//             editModal.show();
//         });
//     });
//
//     function updateProjectName() {
//         const year = document.getElementById('edit-project-year').value.padStart(2, '0');
//         const number = document.getElementById('edit-project-number').value.padStart(3, '0');
//         document.getElementById('edit-project-name').value = B${year}.${number};
//     }
//
//     if(editModalEl){
//         document.getElementById('edit-project-year').addEventListener("input", updateProjectName);
//         document.getElementById('edit-project-number').addEventListener("input", updateProjectName);
//     }
//
//
//     document.getElementById('save-project-btn').addEventListener('click', function() {
//         const projectId = document.getElementById('edit-project-id').value;
//         const projectName = document.getElementById('edit-project-name').value;
//         const companyId = document.getElementById('edit-project-company').value || null;
//         const referentId = document.getElementById('edit-project-referent').value || null;
//         const address = document.getElementById('edit-project-address').value || null;
//         const comment = document.getElementById('edit-project-comment').value || null;
//         const clients = $('#edit-project-clients').val() || [];
//
//         if (!projectName.match(/^B\d{2}\.\d{3}$/)) {
//             showAlert("Nom du projet mal renseigné.", "error", 3000);
//             return;
//         }
//
//         const data = {
//             project_id: projectId,
//             project_name: projectName,
//             company_id: companyId,
//             referent_id: referentId,
//             address: address,
//             comment: comment,
//             clients: clients,
//             _token: window.csrf_token
//         };
//
//         fetch(window.updateProjectUrl, {
//             method: 'POST',
//             headers: {
//                 'Content-Type': 'application/json',
//                 'X-CSRF-TOKEN': window.csrf_token
//             },
//             body: JSON.stringify(data)
//         })
//             .then(response => response.json())
//             .then(response => {
//                 if (response.success) {
//                     showAlert("Affaire mise à jour avec succès !", "success", 3000);
//                     editModal.hide();
//                     location.reload();
//                 } else {
//                     showAlert("Erreur lors de la mise à jour.", "error", 3000);
//                 }
//             })
//             .catch(() => {
//                 showAlert("Une erreur est survenue lors de la mise à jour.", "error", 3000);
//             });
//     });
//
//     const addProjectModalEl = document.getElementById('addProjectModal');
//     const addProjectModal = new bootstrap.Modal(addProjectModalEl);
//
//     $('#addProjectModal').on('shown.bs.modal', function () {
//         $('#add-project-clients').select2({
//             placeholder: "Sélectionner des clients",
//             allowClear: true,
//             dropdownParent: $('#addProjectModal'),
//             width: '100%',
//             templateResult: formatClient,  // Ajoute les checkboxes
//             templateSelection: formatClientSelection // Garde l'affichage propre
//         });
//     });
//
//     function formatClient(client) {
//         if (!client.id) {
//             return client.text;
//         }
//         return $('<span><input type="checkbox" class="select2-checkbox"> ' + client.text + '</span>');
//     }
//
//     function formatClientSelection(client) {
//         return client.text;
//     }
//
//     const toggleButton = document.getElementById('toggleButton');
//     toggleButton.addEventListener('click', function(e) {
//         e.preventDefault();
//         addProjectModal.show();
//     });
//
//     // Buttun modal click
//     const submitAddProjectBtn = document.getElementById('submit-add-project-btn');
//     submitAddProjectBtn.addEventListener('click', function() {
//         // get all input
//         const companyId = document.getElementById('add-project-company').value;
//         const engineerId = document.getElementById('add-project-engineer').value;
//         const yearVal = document.getElementById('add-project-year').value;
//         const numberVal = document.getElementById('add-project-number').value;
//         const clientsSelect = document.getElementById('add-project-clients');
//         const clients = Array.from(clientsSelect.selectedOptions).map(opt => opt.value);
//
//         if (yearVal.length !== 2 || numberVal.length !== 3) {
//             showAlert("Nom du projet mal renseigné.", "error", 3000);
//             return;
//         }
//
//         // create project name
//         const projectName = "B" + yearVal + "." + numberVal;
//         document.getElementById('add-project-name').value = projectName;
//
//         // prepare data
//         const data = {
//             company_id: companyId,
//             engineer_id: engineerId,
//             project_name: projectName,
//             clients: clients,
//             _token: window.csrf_token
//         };
//
//         // send request with fetch
//         fetch(window.storeProjectUrl, {
//             method: 'POST',
//             headers: {
//                 'Content-Type': 'application/json',
//                 'X-CSRF-TOKEN': window.csrf_token
//             },
//             body: JSON.stringify(data)
//         })
//             .then(response => response.json().then(data => ({ status: response.status, body: data })))
//             .then(({ status, body }) => {
//                 if (status === 200 && body.success) {
//                     showAlert("Affaire ajoutée avec succès !", "success", 3000);
//                     addProjectModal.hide();
//                     location.reload();
//                 } else {
//                     if (body.error) {
//                         showAlert(body.error, "error", 3000);
//                     } else {
//                         showAlert("Erreur lors de l'ajout de l'affaire.", "error", 3000);
//                     }
//                 }
//             })
//             .catch(error => {
//                 console.error("Erreur fetch:", error);
//                 showAlert("Une erreur est survenue.", "error", 3000);
//             });
//
//     });
// });
