document.addEventListener('DOMContentLoaded', function() {
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('file-input');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Au clic sur la dropzone, on ouvre l'input fichier caché
    dropzone.addEventListener('click', function() {
        fileInput.click();
    });

    // Gestion du dragover pour indiquer que l'utilisateur peut déposer
    dropzone.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropzone.style.backgroundColor = "#f0f0f0";
    });

    // Réinitialiser le style lors du dragleave
    dropzone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropzone.style.backgroundColor = "";
    });

    // Lorsque des fichiers sont déposés dans la zone
    dropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropzone.style.backgroundColor = "";
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            uploadFiles(files);
        }
    });

    // Quand l'utilisateur sélectionne des fichiers via l'input
    fileInput.addEventListener('change', function() {
        if (fileInput.files.length > 0) {
            uploadFiles(fileInput.files);
        }
    });

    function uploadFiles(files) {
        if (files.length === 0) {
            alert('Veuillez sélectionner au moins un fichier.');
            return;
        }

        // Récupère la route d'upload depuis le conteneur
        const projectContainer = document.getElementById('project-container');
        const route = projectContainer.getAttribute('data-route');

        const formData = new FormData();
        formData.append('_token', csrfToken);
        for (let i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }

        fetch(route, {
            method: 'POST',
            headers: {
                // Ne pas définir Content-Type avec FormData !
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Fichiers uploadés avec succès !');
                    // Optionnel : actualiser la liste des fichiers ou recharger la page
                } else {
                    alert(data.message || "Erreur lors de l'upload des fichiers.");
                }
            })
            .catch(error => {
                console.error("Erreur lors de l'upload :", error);
                alert("Une erreur est survenue lors de l'upload.");
            });
    }
});
