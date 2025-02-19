$(document).ready(function() {
    if ($('#project-table tbody tr').length > 1) {
        new DataTable('#project-table', {
            language: {
                "decimal": ",",
                "thousands": ".",
                "sProcessing": "Traitement en cours...",
                "sSearch": "Rechercher :",
                "sLengthMenu": "Afficher _MENU_ éléments",
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
            }
        });
    }
    if ($('#files-table tbody tr').length > 1) {

        new DataTable('#files-table', {
            language: {
                "decimal": ",",
                "thousands": ".",
                "sProcessing": "Traitement en cours...",
                "sSearch": "Rechercher :",
                "sLengthMenu": "Afficher _MENU_ éléments",
                "sInfo": "Affichage de _START_ à _END_ sur _TOTAL_ éléments",
                "sInfoEmpty": "Affichage de 0 à 0 sur 0 éléments",
                "sInfoFiltered": "(filtré à partir de _MAX_ éléments au total)",
                "sInfoPostFix": "",
                "sLoadingRecords": "Chargement en cours...",
                "sZeroRecords": "Aucun élément à afficher",
                "sEmptyTable": "Aucune donnée disponible dans le tableau",
                // "oPaginate": {
                //     "sFirst":      "Premier",
                //     "sPrevious":   "Précédent",
                //     "sNext":       "Suivant",
                //     "sLast":       "Dernier"
                // },
                "oAria": {
                    "sSortAscending": ": activer pour trier la colonne par ordre croissant",
                    "sSortDescending": ": activer pour trier la colonne par ordre décroissant"
                }
            }
        });
    }
});

