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
                templateResult: formatClient,
                templateSelection: formatClientSelection
            });
        });
    }

    // Add click event to all edit buttons if they exist
    const editButtons = document.querySelectorAll('.edit-project');
    if (editButtons) {
        editButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const projectId = this.getAttribute('data-project-id');
                const projectName = this.getAttribute('data-project-name'); // "B23.045"
                const namelong = this.getAttribute('data-project-namelong'); // "Beauvais mairie..."
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

                // Split project name BXX.XXX
                let match = projectName.match(/^B(\d{2})\.(\d{3})$/);
                let year = match ? match[1] : "";
                let number = match ? match[2] : "";

                document.getElementById('edit-project-id').value = projectId;
                document.getElementById('edit-project-year').value = year;
                document.getElementById('edit-project-number').value = number;
                document.getElementById('edit-project-name').value = projectName;
                document.getElementById('edit-project-namelong').value = namelong;
                document.getElementById('edit-project-company').value = companyId;
                document.getElementById('edit-project-referent').value = referentId;
                document.getElementById('edit-project-address').value = address;
                document.getElementById('edit-project-comment').value = comment;

                $('#edit-project-clients').val(clients).trigger('change');
                editModal.show();
            });
        });
    }

    // Update project name in edit modal
    function updateProjectName() {
        const yearInput = document.getElementById('edit-project-year');
        const numberInput = document.getElementById('edit-project-number');
        const nameInput = document.getElementById('edit-project-name');
        if (!yearInput || !numberInput || !nameInput) return;
        const year = yearInput.value.padStart(2, '0');
        const number = numberInput.value.padStart(3, '0');
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
            const projectId = document.getElementById('edit-project-id')?.value || "";
            const projectName = document.getElementById('edit-project-name')?.value || "";
            const namelong = document.getElementById('edit-project-namelong')?.value || "";
            const companyId = document.getElementById('edit-project-company')?.value || null;
            const referentId = document.getElementById('edit-project-referent')?.value || null;
            const address = document.getElementById('edit-project-address')?.value || null;
            const comment = document.getElementById('edit-project-comment')?.value || null;
            const clients = $('#edit-project-clients').val() || [];

            // Validate name
            if (!projectName.match(/^B\d{2}\.\d{3}$/)) {
                Swal.fire({
                    title: "Nom de l'affaire invalide (ex: B23.045).",
                    icon: "error",
                    timer: 3000,
                    timerProgressBar: true
                });
                return;
            }

            const data = {
                project_id: projectId,
                project_name: projectName,
                project_namelong: namelong,
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
                        Swal.fire({
                            title: "Affaire mise à jour avec succès.",
                            icon: "success",
                            timer: 3000,
                            timerProgressBar: true
                        }).then(() => {
                            if (editModal) editModal.hide();
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: "Erreur lors de la mise à jour de l'affaire.",
                            icon: "error",
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                })
                .catch(() => {
                    Swal.fire({
                        title: "Une erreur s'est produite lors de la mise à jour de l'affaire.",
                        icon: "error",
                        timer: 3000,
                        timerProgressBar: true
                    });
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
            templateResult: formatClient,
            templateSelection: formatClientSelection
        });
    });

    function formatClient(client) {
        if (!client.id) return client.text;
        return $('<span><input type="checkbox" class="select2-checkbox"> ' + client.text + '</span>');
    }
    function formatClientSelection(client) {
        return client.text;
    }

    // Toggle Add Project Modal
    const toggleButton = document.getElementById('toggleButton');
    if (toggleButton && addProjectModal) {
        toggleButton.addEventListener('click', function(e) {
            e.preventDefault();
            addProjectModal.show();
        });
    }

    // Update project name in add modal
    const yearA = document.getElementById("add-project-year");
    const numberA = document.getElementById("add-project-number");
    const projectNameA = document.getElementById("add-project-name");

    function updateProjectNameA() {
        if (!yearA || !numberA || !projectNameA) return;
        const yearVal = yearA.value;
        const numberVal = numberA.value;
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
            const companyId = document.getElementById('add-project-company')?.value || "";
            const engineerId = document.getElementById('add-project-engineer')?.value || "";
            const yearVal = document.getElementById('add-project-year')?.value || "";
            const numberVal = document.getElementById('add-project-number')?.value || "";
            const namelong = document.getElementById('add-project-namelong')?.value || "";
            const clientsSelect = document.getElementById('add-project-clients');
            const clients = clientsSelect ? Array.from(clientsSelect.selectedOptions).map(opt => opt.value) : [];

            if (yearVal.length !== 2 || numberVal.length !== 3) {
                Swal.fire({
                    title: "Nom de l'affaire invalide (ex: B23.045).",
                    icon: "error",
                    timer: 3000,
                    timerProgressBar: true
                });
                return;
            }

            const projectName = `B${yearVal}.${numberVal}`;
            if (projectNameA) {
                projectNameA.value = projectName;
            }

            const data = {
                company_id: companyId,
                engineer_id: engineerId,
                project_namelong: namelong,
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
                .then(response => response.json().then(r => ({ status: response.status, body: r })))
                .then(({ status, body }) => {
                    if (status === 200 && body.success) {
                        Swal.fire({
                            title: "Affaire ajoutée avec succès.",
                            icon: "success",
                            timer: 3000,
                            timerProgressBar: true
                        }).then(() => {
                            if (addProjectModal) addProjectModal.hide();
                            location.reload();
                        });
                    } else {
                        if (body.error) {
                            Swal.fire({
                                title: body.error,
                                icon: "error",
                                timer: 3000,
                                timerProgressBar: true
                            });
                        } else {
                            Swal.fire({
                                title: "Erreur lors de l'ajout de l'affaire.",
                                icon: "error",
                                timer: 3000,
                                timerProgressBar: true
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error("Fetch error:", error);
                    Swal.fire({
                        title: "Une erreur s'est produite.",
                        icon: "error",
                        timer: 3000,
                        timerProgressBar: true
                    });
                });
        });
    }
});
