document.addEventListener('DOMContentLoaded', function() {

    // update specific fields of specific fileId
    function updateFileField(fileId, field, value) {
        // Construit l'URL en insérant l'ID du fichier
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

    // 1) GESTION REVISION (is_last_index)
    document.querySelectorAll('.update-last-index').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const row = this.closest('tr');
            const fileId = row.getAttribute('data-id');
            const newValue = this.checked;

            updateFileField(fileId, 'is_last_index', newValue);
        });
    });

    // 2) GESTION TYPE (file-type-select)

    //
    //
    //
    //CHANGE ALL
    //
    //
    //

    document.addEventListener('change', function(event) {
        let target = event.target;

        // console.log( target );

        if( !target.classList.contains('file-type-select') ) return;

        alert('e');
    });


    document.querySelectorAll('.file-type-select').forEach(select => {
        select.addEventListener('change', function() {
            const row = this.closest('tr');
            const fileId = row.getAttribute('data-id');
            const newValue = this.value;

            updateFileField(fileId, 'type', newValue);
        });
    });

    // 3) GESTION COMMENTAIRE (comment-textarea)
    document.querySelectorAll('.comment-textarea').forEach(textarea => {
        //blur = perte de focus du comm
        textarea.addEventListener('blur', function() {
            const row = this.closest('tr');
            const fileId = row.getAttribute('data-id');
            const newValue = this.value;

            if(newValue !== ""){
                updateFileField(fileId, 'comment', newValue);
            }
        });
    });

    // 4) GESTION VALIDATION (is-validated-checkbox)
    document.querySelectorAll('.is-validated-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const row = this.closest('tr');
            const fileId = row.getAttribute('data-id');
            const newValue = this.checked;

            updateFileField(fileId, 'is_validated', newValue);
        });
    });

});
