document.addEventListener('DOMContentLoaded', function(event) {

    document.querySelector("#select-all").addEventListener('change', function () {
        let isChecked = this.checked;
        document.querySelectorAll(".delete-checkbox").forEach(checkbox => {
            checkbox.checked = isChecked;
        });
    });

    document.querySelector("#delete-selected").addEventListener('click', function(event) {
        let route = this.getAttribute('data-route');
        let selectedProjects = [];

        document.querySelectorAll(".delete-checkbox:checked").forEach(checkbox => {
            selectedProjects.push(checkbox.getAttribute('data-project-id'));
        });

        if (selectedProjects.length === 0) {
            alert("Aucune affaire sélectionnée.");
            //pop up perso ?
            return;
        }

        if (!confirm("Voulez-vous vraiment supprimer les affaires sélectionnées ?")) {
            //confirm perso ?
            return;
        }

        fetch(route, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ selected_projects: selectedProjects })
        })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                location.reload();
            })
            .catch(error => console.error("Erreur lors de la suppression :", error));
    });


    $(document).ready(function() {
        $('#search-clients').select2({
            placeholder: "Sélectionner des clients",
            allowClear: true,
            closeOnSelect: false, // Ne pas fermer la liste après sélection
            templateResult: formatOption, // Personnalisation de l'affichage des options
            templateSelection: formatSelection, // Personnalisation de l'affichage des éléments sélectionnés
            width: '100%'
        });

        // Fonction pour afficher les checkboxes à côté de chaque client
        function formatOption(option) {
            if (!option.id) {
                return option.text;
            }

            // Création de la checkbox
            var checkbox = $('<input type="checkbox" class="client-checkbox" style="margin-right: 8px;">');
            checkbox.val(option.id);

            // Vérifie si l'option est déjà sélectionnée
            if ($('#search-clients').find('option[value="' + option.id + '"]').prop('selected')) {
                checkbox.prop('checked', true);
            }

            var $option = $('<span></span>').text(option.text);
            return $('<span style="display: flex; align-items: center;"></span>').append(checkbox).append($option);
        }

        // Fonction pour afficher correctement les éléments sélectionnés
        function formatSelection(selection) {
            return selection.text;
        }

        // Gestion du clic sur les checkboxes
        $(document).on('click', '.client-checkbox', function(e) {
            var value = $(this).val();

            // Met à jour la sélection dans Select2
            var isSelected = $('#search-clients').find('option[value="' + value + '"]').prop('selected');

            $('#search-clients').find('option[value="' + value + '"]').prop('selected', !isSelected);
            $('#search-clients').trigger('change'); // Met à jour Select2 visuellement

            e.stopPropagation(); // Empêche la fermeture de Select2 lors du clic
        });

        // Met à jour les checkboxes lorsqu'un élément est sélectionné depuis Select2
        $('#search-clients').on('select2:select select2:unselect', function() {
        $('.client-checkbox').each(function() {
                var value = $(this).val();
                $(this).prop('checked', $('#search-clients').find('option[value="' + value + '"]').prop('selected'));
            });
        });
    });

    const project_year = document.getElementById("project_year");
    const project_number = document.getElementById("project_number");
    const project_name = document.getElementById("project_name");

    function updateNomDossier() {
        const year = project_year.value.padStart(2, '0');
        const number = project_number.value.padStart(3, '0');
        project_name.value = `B${year}.${number}`;
    }

    function enforceNumericInput(event) {
        if (!/^\d$/.test(event.key)) {
            event.preventDefault();
        }
    }

    project_year.addEventListener("input", updateNomDossier);
    project_number.addEventListener("input", updateNomDossier);
    project_year.addEventListener("keypress", enforceNumericInput);
    project_number.addEventListener("keypress", enforceNumericInput);

});
