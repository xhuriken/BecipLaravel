document.addEventListener('DOMContentLoaded', function() {
    const downloadBtn = document.getElementById('download-btn');
    const overlay = document.getElementById('download-overlay');
    const loader = document.querySelector('.loader');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Récupère toutes les checkboxes cochées
            const checkedBoxes = document.querySelectorAll('input[name="download_files[]"]:checked');
            if (checkedBoxes.length === 0) {
                Swal.fire({
                    title: "Veuillez sélectionner au moins un fichier à télécharger.",
                    icon: "error",
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: true,
                    confirmButtonText: "OK",
                    customClass: {
                        confirmButton: 'btn btn-success'
                    },
                    buttonsStyling: false
                });
                return;
            }

            // Prépare un tableau contenant l'URL et le nom de chaque fichier
            let fileInfos = [];
            checkedBoxes.forEach(cb => {
                let path = cb.getAttribute('data-file-path');
                let originalName = cb.getAttribute('data-filename');
                fileInfos.push({
                    path: path,
                    filename: originalName
                });
            });

            // Affiche l'overlay
            overlay.style.display = 'flex';
            loader.style.display = 'block';

            let index = 0;
            function downloadNextFile() {
                if (index >= fileInfos.length) {
                    // Tous les fichiers sont téléchargés
                    overlay.style.display = 'none';
                    loader.style.display = 'none';
                    return;
                }

                let info = fileInfos[index++];
                // Nettoie le nom (remove accents etc.)
                let cleanName = info.filename.normalize("NFD").replace(/[\u0300-\u036f]/g, "");

                // Création d'un lien invisible pour déclencher le téléchargement
                const a = document.createElement('a');
                a.href = encodeURI(info.path);
                a.download = cleanName;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);

                // Téléchargement suivant dans 1 seconde
                setTimeout(downloadNextFile, 1000);
            }

            // Lance le premier téléchargement
            downloadNextFile();
        });
    }


    // Distribute button functionality
    const distributeBtn = document.getElementById('distribute-btn');
    if (distributeBtn) {
        distributeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Collect all checked file IDs for distribution
            const fileCheckboxes = document.querySelectorAll('input[name="print_files[]"]:checked');
            let fileIds = [];
            fileCheckboxes.forEach(function(checkbox) {
                fileIds.push(checkbox.value);
            });

            if (fileIds.length === 0) {
                Swal.fire({
                    title: "Veuillez sélectionner au moins un fichier à distribuer.",
                    icon: "error",
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: true,
                    confirmButtonText: "OK",
                    customClass: {
                        confirmButton: 'btn btn-success'
                    },
                    buttonsStyling: false
                });
                return;
            }

            // Prepare data for distribution request
            const projectContainer = document.getElementById('project-container');
            const projectId = projectContainer.getAttribute('data-project-id');
            const data = {
                project_id: projectId,
                file_ids: fileIds,
                _token: window.csrf_token
            };

            // Send POST request to distribute route
            fetch(window.distributeProjectUrl, {
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
                            title: "Email de distribution envoyé avec succès !",
                            icon: "success",
                            timer: 3000,
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
                            title: response.error || "Erreur lors de la distribution.",
                            icon: "error",
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: true,
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: 'btn btn-success'
                            },
                            buttonsStyling: false
                        });
                    }
                })
                .catch(error => {
                    console.error("Distribution error:", error);
                    Swal.fire({
                        title: "Une erreur est survenue lors de la distribution.",
                        icon: "error",
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: true,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: 'btn btn-success'
                        },
                        buttonsStyling: false
                    });
                });
        });
    }
});
