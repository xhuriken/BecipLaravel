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
        scrollX: true,
        autoWidth: false,
        lengthMenu: [
            [10, 50, 100, 500, -1],
            [10, 50, 100, 500, 'All']
        ],
        pageLength: 100,
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
        ],
        fnInitComplete: function () {
            $("tfoot").next().hide();
        },
        fnDrawCallback: function (oSettings) {
            $("tfoot").next().hide();
        }

    });


    const filetable = $('#files-table').DataTable({
        destroy: true,
        responsive: true,
        ordering: true,
        scrollX: true,
        autoWidth: false,
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

    $('#fileTypeFilter').on('change', function () {
        const val = $(this).val();
        // Colonne "Type" => index à adapter si besoin
        const typeColIndex = 3; // attention : index 0-based !
        if (val) {
            filetable.column(typeColIndex).search('^' + val + '$', true, false).draw();
        } else {
            filetable.column(typeColIndex).search('').draw();
        }
    });
});
