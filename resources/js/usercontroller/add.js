document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('adduser');

    if (!form) return;

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const name = document.getElementById('name').value.trim();
        const phone = document.getElementById('phone').value.trim().replace(/\s+/g, '');
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const role = document.getElementById('role').value;
        const company_id = document.getElementById('company_id').value;

        // Regexs
        const nameRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ]+ [A-Za-zÀ-ÖØ-öø-ÿ]+$/; //Need one Space in name
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Format X@X.X
        const minPasswordLength = 8;

        // Verify fields
        if (!nameRegex.test(name)) {
            return Swal.fire({
                icon: 'error',
                title: 'Nom invalide',
                text: 'Veuillez entrer un nom valide (ex: Prénom Nom).',
            });
        }

        //TODO: Requete verifier si l'email existe pas déjà
        //TODO: Requete verifier si l'email existe pas déjà
        //TODO: Requete verifier si l'email existe pas déjà
        //TODO: Requete verifier si l'email existe pas déjà
        //TODO: Requete verifier si l'email existe pas déjà

        if (!emailRegex.test(email)) {
            return Swal.fire({
                icon: 'error',
                title: 'Email invalide',
                text: 'Veuillez entrer un email valide (ex: exemple@domaine.com).',
            });
        }

        if (password.length < minPasswordLength) {
            return Swal.fire({
                icon: 'error',
                title: 'Mot de passe trop court',
                text: 'Le mot de passe doit contenir au moins 8 caractères.',
            });
        }

        if(phone.length !== 0){
            if (!/^\d+$/.test(phone)) {
                return Swal.fire({
                    icon: 'error',
                    title: 'Numéro invalide',
                    text: 'Le numéro ne doit contenir que des chiffres.',
                });
            }

            if (phone.length !== 10) {
                return Swal.fire({
                    icon: 'error',
                    title: 'Numéro invalide',
                    text: 'Le numéro doit contenir exactement 10 chiffres.',
                });
            }
        }

        //If everithing is good, SEND !
        fetch(window.addUserRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrf_token
            },
            body: JSON.stringify({ name, phone, email, password, role, company_id })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Utilisateur ajouté !',
                        text: 'L\'utilisateur a bien été ajouté.',
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: data.error || "Une erreur s'est produite lors de l'ajout.",
                    });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: "Impossible d'ajouter l'utilisateur. Email déjà utilisé.",
                });
            });
    });
});
