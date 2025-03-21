document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function setupEditButton(editBtn, displayElem, inputElem, field) {
        editBtn.addEventListener('click', function() {
            if (editBtn.classList.contains('editing')) {
                let newValue = inputElem.value.trim();

                if (newValue === "") {
                    Swal.fire({ title: `${field} ne peut pas être vide.`, icon: "warning" });
                    return;
                }

                // Validation spécifique pour téléphone
                if (field === 'phone') {
                    const cleanedValue = newValue.replace(/\s+/g, '');
                    if (cleanedValue.length !== 10 || !/^\d+$/.test(cleanedValue)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Numéro de téléphone invalide',
                            text: 'Le numéro doit contenir exactement 10 chiffres sans lettre.',
                        });
                        return;
                    }
                    newValue = cleanedValue;
                }

                editBtn.disabled = true;

                fetch(window.updateProfileUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ field: field, value: newValue })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            displayElem.textContent = newValue;
                            toggleEditMode(editBtn, displayElem, inputElem, false);
                        } else {
                            Swal.fire({ title: data.message || "Erreur lors de la sauvegarde.", icon: "error" });
                        }
                    })
                    .catch(() => Swal.fire({ title: "Une erreur est survenue.", icon: "error" }))
                    .finally(() => editBtn.disabled = false);
            } else {
                toggleEditMode(editBtn, displayElem, inputElem, true);
            }
        });
    }


    function toggleEditMode(editBtn, displayElem, inputElem, editing) {
        if (editing) {
            displayElem.classList.add('d-none');
            inputElem.classList.remove('d-none');
            editBtn.classList.replace('btn-primary', 'btn-warning');
            editBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i>';
            editBtn.classList.add('editing');
        } else {
            displayElem.classList.remove('d-none');
            inputElem.classList.add('d-none');
            editBtn.classList.replace('btn-warning', 'btn-primary');
            editBtn.innerHTML = '<i class="fa-solid fa-pen-to-square"></i>';
            editBtn.classList.remove('editing');
        }
    }

    const nameEditBtn = document.getElementById('name-edit-btn');
    const nameDisplay = document.getElementById('name-display');
    const nameInput = document.getElementById('name-input');

    const emailEditBtn = document.getElementById('email-edit-btn');
    const emailDisplay = document.getElementById('email-display');
    const emailInput = document.getElementById('email-input');

    const phoneEditBtn = document.getElementById('phone-edit-btn');
    const phoneDisplay = document.getElementById('phone-display');
    const phoneInput = document.getElementById('phone-input');

    if (phoneEditBtn) setupEditButton(phoneEditBtn, phoneDisplay, phoneInput, 'phone');
    if (nameEditBtn) setupEditButton(nameEditBtn, nameDisplay, nameInput, 'name');
    if (emailEditBtn) setupEditButton(emailEditBtn, emailDisplay, emailInput, 'email');
});
