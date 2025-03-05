document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const nameEditBtn = document.getElementById('name-edit-btn');
    const nameDisplay = document.getElementById('name-display');
    const nameInput = document.getElementById('name-input');

    const emailEditBtn = document.getElementById('email-edit-btn');
    const emailDisplay = document.getElementById('email-display');
    const emailInput = document.getElementById('email-input');

    if (nameEditBtn) {

        // Handle name edits
        nameEditBtn.addEventListener('click', function(event) {
            // If in editing mode, we save
            if (nameEditBtn.classList.contains('editing')) {
                const newName = nameInput.value.trim();
                if (newName === "") {
                    Swal.fire({
                        title: "Le nom ne peut pas être vide.",
                        icon: "warning"
                    });
                    return;
                }
                nameEditBtn.disabled = true;

                fetch(window.updateProfileUrl, {
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
                            Swal.fire({
                                title: data.message || "Erreur lors de la sauvegarde.",
                                icon: "error"
                            });
                        }
                    })
                    .catch(() => {
                        Swal.fire({
                            title: "Une erreur est survenue lors de la sauvegarde.",
                            icon: "error"
                        });
                    })
                    .finally(() => {
                        nameEditBtn.disabled = false;
                    });

            } else {
                // Switch to editing mode
                toggleNameEditMode(true);
                event.stopImmediatePropagation();
            }
        });

        // Handle email edits
        emailEditBtn.addEventListener('click', function(event) {
            if (emailEditBtn.classList.contains('editing')) {
                const newEmail = emailInput.value.trim();
                if (newEmail === "") {
                    Swal.fire({
                        title: "L'email ne peut pas être vide.",
                        icon: "warning"
                    });
                    return;
                }
                emailEditBtn.disabled = true;

                fetch(window.updateProfileUrl, {
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
                            Swal.fire({
                                title: data.message || "Erreur lors de la sauvegarde.",
                                icon: "error"
                            });
                        }
                    })
                    .catch(() => {
                        Swal.fire({
                            title: "Une erreur est survenue lors de la sauvegarde.",
                            icon: "error"
                        });
                    })
                    .finally(() => {
                        emailEditBtn.disabled = false;
                    });

            } else {
                // Switch to editing mode
                toggleEmailEditMode(true);
                event.stopImmediatePropagation();
            }
        });

        // Toggle name editing
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

        // Toggle email editing
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
    }
});
