$(function () { // DOM ready
    // interception du clic sur les liens
    $('.btn-content').click(function (event) {
        // évite d'aller sur la page du lien
        event.preventDefault();

        // objet jquery sur le lien cliqué
        var $btn = $(this);

        // Appel ajax en GET
        $.get(
            // page appelée en ajax : page dont l'url est dans l'attribut href du lien
            $btn.attr('href'),
            // fonction de callback qui traite la réponse retournée par la page appelée en ajax
            function (response) {
                var $modal = $('#modal-content');

                // intégration du contenu de la réponse dans la modale
                $modal.find('.modal-body').html(response);

                // affiche la modale
                $modal.modal('show');
            }
        );
    });
});
