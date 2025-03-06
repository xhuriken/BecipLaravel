document.addEventListener('DOMContentLoaded', function() {
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('file-input');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    if (dropzone) {

        // When dropzone is clicked, trigger hidden file input
        dropzone.addEventListener('click', function() {
            fileInput.click();
        });

        // Change style on dragover
        dropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.style.backgroundColor = "#f0f0f0";
        });

        // Reset style on dragleave
        dropzone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.style.backgroundColor = "";
        });

        // On drop, start file upload
        dropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.style.backgroundColor = "";
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                uploadFiles(files);
            }
        });

        // When files are selected via input
        fileInput.addEventListener('change', function() {
            if (fileInput.files.length > 0) {
                uploadFiles(fileInput.files);
            }
        });

        function uploadFiles(files) {
            if (files.length === 0) {
                Swal.fire({
                    title: "Veuillez sélectionner au moins un fichier.",
                    icon: "warning"
                });
                return;
            }

            // Get the upload route from the project container
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
                        Swal.fire({
                            title: "Fichiers uploadés avec succès !",
                            icon: "success",
                            timer: 3000,
                            timerProgressBar: true
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: data.message || "Erreur lors de l'upload des fichiers.",
                            icon: "error"
                        }).then(() => {
                            location.reload();
                        });
                    }
                })
                .catch(error => {
                    console.error("Erreur lors de l'upload :", error);
                    Swal.fire({
                        title: "Une erreur est survenue",
                        icon: "error",
                        timer: 3000,
                        timerProgressBar: true
                    }).then(() => {
                        location.reload();
                    });
                });
        }
    }
});
