document.addEventListener('DOMContentLoaded', function() {
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('file-input');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    if(dropzone){

        //dropzone clic, hidden input launch
        dropzone.addEventListener('click', function() {
            fileInput.click();
        });

        //change style on dragover
        dropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.style.backgroundColor = "#f0f0f0";
        });

        // re init style on leave
        dropzone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.style.backgroundColor = "";
        });

        //launch upload file when drop
        dropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.style.backgroundColor = "";
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                uploadFiles(files);
            }
        });


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

            // get route
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
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Fichiers uploadés avec succès !', 'success', 3000)
                        location.reload();
                    } else {
                        alert(data.message || "Erreur lors de l'upload des fichiers.");
                    }
                })
                .catch(error => {
                    console.error("Erreur lors de l'upload :", error);
                    showAlert('Une erreur est survenue', 'error', 3000)
                });
        }
    }
});
