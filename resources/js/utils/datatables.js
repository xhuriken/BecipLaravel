jQuery(document).ready(function($) {

    function getNonOrderableColumns(tableSelector) {
        let nonOrderable = [];
        $(`${tableSelector} thead th`).each(function(index) {
            if ($(this).attr("data-orderable") === "false") {
                nonOrderable.push(index);
            }
        });
        return nonOrderable;
    }

    $('#project-table').DataTable({
        destroy: true,
        responsive: true,
        language: {
            "decimal": ",",
            "thousands": ".",
            "sProcessing": "Traitement en cours...",
            "sSearch": "Rechercher :",
            "sLengthMenu": "_MENU_",
            "sInfo": "Affichage de _START_ à _END_ sur _TOTAL_ éléments",
            "sInfoEmpty": "Affichage de 0 à 0 sur 0 éléments",
            "sInfoFiltered": "(filtré à partir de _MAX_ éléments au total)",
            "sInfoPostFix": "",
            "sLoadingRecords": "Chargement en cours...",
            "sZeroRecords": "Aucun élément à afficher",
            "sEmptyTable": "Aucune donnée disponible dans le tableau",
            "oAria": {
                "sSortAscending": ": activer pour trier la colonne par ordre croissant",
                "sSortDescending": ": activer pour trier la colonne par ordre décroissant"
            }
        },
        columnDefs: [
            { orderable: false, targets: getNonOrderableColumns("#project-table") }
        ]

    });


    $('#files-table').DataTable({
        destroy: true,
        responsive: true,
        ordering: true,
        language: {
            "decimal": ",",
            "thousands": ".",
            "sProcessing": "Traitement en cours...",
            "sSearch": "Rechercher :",
            "sLengthMenu": "_MENU_",
            "sInfo": "Affichage de _START_ à _END_ sur _TOTAL_ éléments",
            "sInfoEmpty": "Affichage de 0 à 0 sur 0 éléments",
            "sInfoFiltered": "(filtré à partir de _MAX_ éléments au total)",
            "sInfoPostFix": "",
            "sLoadingRecords": "Chargement en cours...",
            "sZeroRecords": "Aucun élément à afficher",
            "sEmptyTable": "Aucune donnée disponible dans le tableau",
            "oAria": {
                "sSortAscending": ": activer pour trier la colonne par ordre croissant",
                "sSortDescending": ": activer pour trier la colonne par ordre décroissant"
            }
        },
        columnDefs: [
            { orderable: false, targets: getNonOrderableColumns("#files-table") }
        ]
    });
});
