document.addEventListener('DOMContentLoaded', function () {
    const projectContainer = document.getElementById('project-container');
    // for dodge errors
    if (!projectContainer) return;

    const projectId = projectContainer.getAttribute('data-project-id');
    //first checkbox
    const maskValidedCheckbox = document.querySelector('input[data-label="mask-valid"]');
    //second checkbox
    const maskDistributedCheckbox = document.querySelector('input[data-label="mask-distrib"]');

    function updateMaskValidated() {
        fetch(window.maskValidedRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrf_token
            },
            body: JSON.stringify({
                project_id: projectId,
                is_mask_valided: maskValidedCheckbox.checked ? 1 : 0
            })
        }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("Mask validated updated");
                }
            }).catch(error => console.error("Error updating mask validated:", error));
    }

    function updateMaskDistributed() {
        fetch(window.maskDistributedRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrf_token
            },
            body: JSON.stringify({
                project_id: projectId,
                is_mask_distributed: maskDistributedCheckbox.checked ? 1 : 0
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        }).catch(error => console.error("Error updating mask distributed:", error));
    }

    //update when click
    if (maskValidedCheckbox) maskValidedCheckbox.addEventListener('change', updateMaskValidated);
    if (maskDistributedCheckbox) maskDistributedCheckbox.addEventListener('change', updateMaskDistributed);
});
