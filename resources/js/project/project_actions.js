document.addEventListener('DOMContentLoaded', function() {
    // Download button functionality
    const downloadBtn = document.getElementById('download-btn');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Get all checked download file checkboxes
            const fileCheckboxes = document.querySelectorAll('input[name="download_files[]"]:checked');
            let fileIds = [];
            fileCheckboxes.forEach(function(checkbox) {
                fileIds.push(checkbox.value);
            });

            // If no file is selected, show error alert
            if (fileIds.length === 0) {
                Swal.fire({
                    title: "Veuillez sélectionner au moins un fichier à télécharger.",
                    icon: "error",
                    timer: 3000,
                    timerProgressBar: true
                });
                return;
            }

            // Build data object for download request
            const projectContainer = document.getElementById('project-container');
            const projectId = projectContainer.getAttribute('data-project-id');
            const data = {
                project_id: projectId,
                file_ids: fileIds,
                _token: window.csrf_token
            };

            // Send POST request to download files route
            fetch(window.downloadProjectUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrf_token
                },
                body: JSON.stringify(data)
            })
                .then(response => {
                    if (response.status === 200) {
                        return response.blob();
                    }
                    throw new Error("Download failed.");
                })
                .then(blob => {
                    // Create a temporary URL and trigger download
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = `Download_${new Date().toISOString().slice(0,10)}_Project${projectId}.zip`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    Swal.fire({
                        title: "Le téléchargement a démarré.",
                        icon: "success",
                        timer: 3000,
                        timerProgressBar: true
                    });
                })
                .catch(error => {
                    console.error("Download error:", error);
                    Swal.fire({
                        title: "Une erreur est survenue lors du téléchargement.",
                        icon: "error",
                        timer: 3000,
                        timerProgressBar: true
                    });
                });
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
                    timerProgressBar: true
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
                            timerProgressBar: true
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: response.error || "Erreur lors de la distribution.",
                            icon: "error",
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                })
                .catch(error => {
                    console.error("Distribution error:", error);
                    Swal.fire({
                        title: "Une erreur est survenue lors de la distribution.",
                        icon: "error",
                        timer: 3000,
                        timerProgressBar: true
                    });
                });
        });
    }
});
