//
// Download And Distribute button
//
document.addEventListener('DOMContentLoaded', function() {
    const downloadBtn = document.getElementById('download-btn');
    // Dodge error
    if (downloadBtn) {
        //Download button functionality
        downloadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            //get all checkbox checked
            const fileCheckboxes = document.querySelectorAll('input[name="download_files[]"]:checked');
            let fileIds = [];
            fileCheckboxes.forEach(function(checkbox) {
                fileIds.push(checkbox.value);
            });

            // if no one file selected, make alert
            if (fileIds.length === 0) {
                showAlert("Please select at least one file to download.", "error", 3000);
                return;
            }

            // make data{}
            const projectContainer = document.getElementById('project-container');
            const projectId = projectContainer.getAttribute('data-project-id');
            const data = {
                project_id: projectId,
                file_ids: fileIds,
                _token: window.csrf_token // je sais plus pourquoi
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
                    // If a ZIP file is returned, we redirect the browser to download it.
                    // For single file download, we may use window.location.href
                    if (response.status === 200) {
                        return response.blob();
                    }
                    throw new Error("Download failed.");
                })
                .then(blob => {
                    // Create a temporary URL and download the file
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    // Name the file as Date-ProjectName.zip or use original name for single file
                    a.download = `Download_${new Date().toISOString().slice(0,10)}_Project${projectId}.zip`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    showAlert("Download started.", "success", 3000);
                })
                .catch(error => {
                    console.error("Download error:", error);
                    showAlert("An error occurred during download.", "error", 3000);
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
                showAlert("Please select at least one file to distribute.", "error", 3000);
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
                            location.reload()
                            showAlert("Distribution email sent successfully!", "success", 3000);
                    } else {
                        showAlert(response.error || "Error during distribution.", "error", 3000);
                    }
                })
                .catch(error => {
                    console.error("Distribution error:", error);
                    showAlert("An error occurred during distribution.", "error", 3000);
                });
        });
    }
});
