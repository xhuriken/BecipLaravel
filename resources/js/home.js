document.addEventListener('DOMContentLoaded', function() {

    const editModalEl = document.getElementById('editProjectModal');
    const editModal = new bootstrap.Modal(editModalEl);

    document.querySelectorAll('.edit-project').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const projectId = this.getAttribute('data-project-id');
            const projectName = this.getAttribute('data-project-name');
            const companyId = this.getAttribute('data-company-id');

            // Remplir le formulaire du modal
            document.getElementById('edit-project-id').value = projectId;
            document.getElementById('edit-project-name').value = projectName;
            document.getElementById('edit-project-company').value = companyId;

            // Afficher le modal
            editModal.show();
        });
    });

    document.getElementById('save-project-btn').addEventListener('click', function() {
        const projectId = document.getElementById('edit-project-id').value;
        const projectName = document.getElementById('edit-project-name').value;
        const companyId = document.getElementById('edit-project-company').value;

        const data = {
            project_id: projectId,
            project_name: projectName,
            company_id: companyId,
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
                    // Optionnel : mettre à jour la ligne dans le tableau avec les nouvelles données
                    // Vous pouvez rechercher la ligne par data-project-id et mettre à jour ses cellules

                    // Fermer le modal
                    editModal.hide();
                    // Recharger la page ou afficher un message de succès
                    location.reload();
                } else {
                    alert(response.message || "Erreur lors de la mise à jour.");
                }
            })
            .catch(() => {
                alert("Une erreur est survenue lors de la mise à jour.");
            });
    });
});
