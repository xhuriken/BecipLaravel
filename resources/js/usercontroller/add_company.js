document.addEventListener('DOMContentLoaded', function () {
    const companyForm = document.getElementById('add-company-form');
    if (!companyForm) return;

    companyForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const nameInput = document.querySelector('#add-company-form input[name="name"]');
        const name = nameInput.value.trim();
        if (!name) {
            return Swal.fire({
                icon: 'error',
                title: 'Nom manquant',
                text: 'Veuillez entrer un nom d’entreprise.',
            });
        }

        fetch(window.addCompanyRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.csrf_token
            },
            body: JSON.stringify({ name })
        })
            .then(async res => {
                const body = await res.json().catch(() => ({}));
                if (!res.ok) {
                    throw new Error(body.message || 'Erreur HTTP ' + res.status);
                }
                return body;
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Entreprise ajoutée !',
                        text: 'L’entreprise a bien été ajoutée.',
                    });

                    nameInput.value = '';

                    const table = $('#companies-table').DataTable();

                    // Création de la nouvelle ligne
                    const $newRow = $(`
                    <tr data-company-id="${data.company.id}">
                        <td class="company-name">${data.company.name}</td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-company" data-route="${data.update_route}">
                                <i class="fa fa-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-company" data-route="${data.delete_route}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);

                    const insertedRow = table.row.add($newRow).draw(false).node();
                    console.log("✅ Entreprise ajoutée et visible immédiatement !", insertedRow);

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: data.error || "Une erreur est survenue lors de l'ajout.",
                    });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: "Impossible d'ajouter l'entreprise.",
                });
            });
    });
});
