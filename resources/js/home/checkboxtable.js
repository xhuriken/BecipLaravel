document.addEventListener('DOMContentLoaded', function() {
    let selectAll       = document.querySelector("#select-all");
    let deleteSelected  = document.querySelector("#delete-selected");

    // Toggle all checkboxes
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            let isChecked = this.checked;
            document.querySelectorAll(".delete-checkbox").forEach(checkbox => {
                checkbox.checked = isChecked;
            });
        });
    }

    // Delete selected items
    if (deleteSelected) {
        deleteSelected.addEventListener('click', function() {
            let route = this.getAttribute('data-route');
            let selectedProjects = [];

            document.querySelectorAll(".delete-checkbox:checked").forEach(checkbox => {
                selectedProjects.push(checkbox.getAttribute('data-project-id'));
            });

            if (selectedProjects.length === 0) {
                Swal.fire({
                    title: "Aucune affaire sélectionnée !",
                    icon: "warning",
                    timer: 1200,
                    timerProgressBar: true
                });
                return;
            }

            // Confirm delete
            Swal.fire({
                title: "Êtes-vous sûr ?",
                text: "Cette action est irréversible !",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#0c9155",
                cancelButtonColor: "#d33",
                confirmButtonText: "Oui"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(route, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": window.csrf_token
                        },
                        body: JSON.stringify({ selected_projects: selectedProjects })
                    })
                        .then(response => response.json())
                        .then(data => {

                            let projectTable = $('#project-table').DataTable();

                            // Remove deleted items in DOM
                            selectedProjects.forEach(projectId => {
                                let row = document.querySelector(`#project-row-${projectId}`);
                                if (row) {
                                    projectTable.row(row).remove().draw();
                                }
                            });

                            Swal.fire({
                                title: "Affaires supprimées !",
                                icon: "success",
                                timer: 1200,
                                timerProgressBar: true
                            });
                        })
                        .catch(error => {
                            console.error("Error deleting projects:", error);
                            Swal.fire({
                                title: "Erreur",
                                text: "Quelque chose s'est mal passé.",
                                icon: "error"
                            });
                        });
                }
            });
        });
    }

    // Initialize select2
    $(document).ready(function() {
        $('#search-clients').select2({
            placeholder: "Select clients",
            allowClear: true,
            closeOnSelect: false,
            templateResult: formatOption,
            templateSelection: formatSelection,
            width: '100%'
        });

        function formatOption(option) {
            if (!option.id) {
                return option.text;
            }
            let checkbox = $('<input type="checkbox" class="client-checkbox" style="margin-right: 8px;">');
            checkbox.val(option.id);

            if ($('#search-clients').find('option[value="' + option.id + '"]').prop('selected')) {
                checkbox.prop('checked', true);
            }
            let $option = $('<span></span>').text(option.text);

            return $('<span style="display: flex; align-items: center;"></span>')
                .append(checkbox)
                .append($option);
        }

        function formatSelection(selection) {
            return selection.text;
        }

        // Handle checkbox clicks
        $(document).on('click', '.client-checkbox', function(e) {
            let value = $(this).val();
            let isSelected = $('#search-clients').find('option[value="' + value + '"]').prop('selected');
            $('#search-clients').find('option[value="' + value + '"]').prop('selected', !isSelected);
            $('#search-clients').trigger('change');
            e.stopPropagation();
        });

        // Update checkboxes when select2 changes
        $('#search-clients').on('select2:select select2:unselect', function() {
            $('.client-checkbox').each(function() {
                let value = $(this).val();
                $(this).prop(
                    'checked',
                    $('#search-clients').find('option[value="' + value + '"]').prop('selected')
                );
            });
        });
    });
});
