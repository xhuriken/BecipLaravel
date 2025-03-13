document.addEventListener('DOMContentLoaded', function() {
    // update specific fields of specific fileId
    function updateFileField(fileId, field, value) {
        const updateUrl = `${window.fileUpdateRoute.replace('FILE_ID', fileId)}`;

        fetch(updateUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrf_token
            },
            body: JSON.stringify({ field, value })
        })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert(data.error || "Erreur lors de la mise à jour.");
                }
            })
            .catch(err => {
                console.error(err);
                alert("Une erreur s'est produite.");
            });
    }

    document.addEventListener('change', function(event) {
        let target = event.target;

        // 1) GESTION REVISION (is_last_index)
        if (target.classList.contains('update-last-index')) {
            const row = target.closest('tr');
            const fileId = row.getAttribute('data-id');
            const newValue = target.checked;
            updateFileField(fileId, 'is_last_index', newValue);
        }
        // 2) GESTION TYPE (file-type-select)
        if (target.classList.contains('file-type-select')) {
            const row = target.closest('tr');
            const fileId = row.getAttribute('data-id');
            const newValue = target.value;
            updateFileField(fileId, 'type', newValue);
        }
        // 4) GESTION VALIDATION (is-validated-checkbox)
        if (target.classList.contains('is-validated-checkbox')) {
            const row = target.closest('tr');
            const fileId = row.getAttribute('data-id');
            const newValue = target.checked;
            updateFileField(fileId, 'is_validated', newValue);
        }
    });

    document.addEventListener('blur', function(event) {
        let target = event.target;

        if (!document.querySelector('.comment-textarea')) {
            return;
        }

        // 2) GESTION TYPE (file-type-select)
        if (target.classList.contains('comment-textarea')) {
            const row = target.closest('tr');
            const fileId = row.getAttribute('data-id');
            const newValue = target.value;

            if(newValue !== "") {
                updateFileField(fileId, 'comment', newValue);
            }
        }
    }, true);
});
