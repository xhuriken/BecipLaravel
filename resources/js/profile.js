document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const updateProfileUrl = window.updateProfileUrl;

    // Récupération des éléments pour le nom
    const nameEditBtn = document.getElementById('name-edit-btn');
    const nameDisplay = document.getElementById('name-display');
    const nameInput = document.getElementById('name-input');

    // Récupération des éléments pour l'email
    const emailEditBtn = document.getElementById('email-edit-btn');
    const emailDisplay = document.getElementById('email-display');
    const emailInput = document.getElementById('email-input');

    // Gestion du bouton d'édition du nom
    nameEditBtn.addEventListener('click', function(event) {
        // Si le bouton est déjà en mode "édition" (classe "editing"), on sauvegarde
        if (nameEditBtn.classList.contains('editing')) {
            const newName = nameInput.value.trim();
            if (newName === "") {
                alert("Le nom ne peut pas être vide.");
                return;
            }
            nameEditBtn.disabled = true; // désactiver le bouton pour éviter les doublons

            fetch(updateProfileUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    field: 'name',
                    value: newName
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        nameDisplay.textContent = newName;
                        toggleNameEditMode(false);
                    } else {
                        alert(data.message || "Erreur lors de la sauvegarde.");
                    }
                })
                .catch(() => {
                    alert("Une erreur est survenue lors de la sauvegarde.");
                })
                .finally(() => {
                    nameEditBtn.disabled = false;
                });
        } else {
            // Passage en mode édition
            toggleNameEditMode(true);
            // On stoppe la propagation pour éviter un double déclenchement
            event.stopImmediatePropagation();
        }
    });

    // Gestion du bouton d'édition de l'email
    emailEditBtn.addEventListener('click', function(event) {
        if (emailEditBtn.classList.contains('editing')) {
            const newEmail = emailInput.value.trim();
            if (newEmail === "") {
                alert("L'email ne peut pas être vide.");
                return;
            }
            emailEditBtn.disabled = true;
            fetch(updateProfileUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    field: 'email',
                    value: newEmail
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        emailDisplay.textContent = newEmail;
                        toggleEmailEditMode(false);
                    } else {
                        alert(data.message || "Erreur lors de la sauvegarde.");
                    }
                })
                .catch(() => {
                    alert("Une erreur est survenue lors de la sauvegarde.");
                })
                .finally(() => {
                    emailEditBtn.disabled = false;
                });
        } else {
            toggleEmailEditMode(true);
            event.stopImmediatePropagation();
        }
    });

    // Fonctions pour activer/désactiver le mode édition pour le nom
    function toggleNameEditMode(editing) {
        if (editing) {
            nameDisplay.style.display = 'none';
            nameInput.style.display = 'inline-block';
            nameEditBtn.classList.add('editing');
            nameEditBtn.innerHTML = '<i class="fa fa-floppy-o"></i>';
        } else {
            nameDisplay.style.display = 'inline-block';
            nameInput.style.display = 'none';
            nameEditBtn.classList.remove('editing');
            nameEditBtn.innerHTML = '<i class="fa-solid fa-pen-to-square"></i>';
        }
    }

    // Fonctions pour activer/désactiver le mode édition pour l'email
    function toggleEmailEditMode(editing) {
        if (editing) {
            emailDisplay.style.display = 'none';
            emailInput.style.display = 'inline-block';
            emailEditBtn.classList.add('editing');
            emailEditBtn.innerHTML = '<i class="fa fa-floppy-o"></i>';
        } else {
            emailDisplay.style.display = 'inline-block';
            emailInput.style.display = 'none';
            emailEditBtn.classList.remove('editing');
            emailEditBtn.innerHTML = '<i class="fa-solid fa-pen-to-square"></i>';
        }
    }
});
