document.addEventListener('DOMContentLoaded', function() {
    const uploadBtn = document.getElementById('upload-files-btn');
    const fileInput = document.getElementById('file-input');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    if(uploadBtn){

        uploadBtn.addEventListener('click', function() {
            const files = fileInput.files;
            if (files.length === 0) {
                alert('Veuillez sélectionner au moins un fichier.');
                return;
            }

            // Récupère l'ID du projet et la route depuis le conteneur
            const projectContainer = document.getElementById('project-container');
            const projectId = projectContainer.getAttribute('data-project-id');
            const route = projectContainer.getAttribute('data-route');

            const formData = new FormData();
            formData.append('_token', csrfToken);
            for (let i = 0; i < files.length; i++) {
                formData.append('files[]', files[i]);
            }

            // Utilisation de Fetch pour envoyer les fichiers
            fetch(route, {
                method: 'POST',
                headers: {
                    // 'Content-Type' n'est pas défini ici car FormData s'en charge automatiquement
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Fichiers uploadés avec succès !');
                        // Optionnel : mettre à jour la liste des fichiers ou recharger la page
                    } else {
                        alert(data.message || "Erreur lors de l'upload des fichiers.");
                    }
                })
                .catch(error => {
                    console.error("Erreur lors de l'upload :", error);
                    alert("Une erreur est survenue lors de l'upload.");
                });
        });
    }
});
