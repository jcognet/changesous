// Quelques constantes
var ISBN_INPUT_ID = 'livrebundle_rechercheIBSNLivre_isbn'; // ID du champ input de l'isbn
var ISBN_BUTTON_ID = 'livrebundle_rechercheIBSNLivre_btnAjouterLivre'; // ID du button qui "valide" le formulaire
var ISBN_DIV_ID = 'form_ajout'; // id de la div avec le champ ISBN
var LISTE_AJOUT_DIV_ID = 'liste_livre_ajout'; // id de la div après laquelle le nouveau livre sera ajouté
var ERREUR_DIV_ID = 'form_ajout_erreur'; // id de la div avec les erreurs
var MODAL_DETAIL_ID = 'modalDetail'; // id de la div de la modele

var ROUTE_AJOUT = 'livre_bibliotheque_ajout_ajax'; // Route Symfony gérant l'ajout
var ROUTE_DETAIL_POP_IN = 'livre_detail_pop_in';
var ROUTE_MODIFIE = 'livre_bibliotheque_modifie_ajax';
var ROUTE_SUPPRESSION = 'livre_bibliotheque_supprime_ajax';

var ISBN_INPUT = null;
var ISBN_BUTTON = null;
var ISBN_FORM_JS = null;
var LISTE_AJOUT_DIV = null;
var ERREUR_DIV = null;
var MODAL_DETAIL = null;
// Quelques variables globales à la page
var isbnInputTimer;
var dureeAttenteSaisie = 2000; // En seconde
var bufferLivre = new Array();
var ajoutLivreEnCours = false;
$(document).ready(function () {
    // Mise a jour des champs
    ISBN_INPUT = $('#' + ISBN_INPUT_ID);
    ISBN_BUTTON = $('#' + ISBN_BUTTON_ID);
    ISBN_FORM_JS = ISBN_INPUT.closest('form').get(0);
    LISTE_AJOUT_DIV = $('#' + LISTE_AJOUT_DIV_ID);
    ERREUR_DIV = $('#' + ERREUR_DIV_ID);
    MODAL_DETAIL = $('#' + MODAL_DETAIL_ID);

    // Evenements
    // saisi dans le champ ISBN
    ISBN_INPUT.on('keydown', function (e) {
        clearTimeout(isbnInputTimer);
        attenteInputISBN();
    });
    // Click sur le bouton
    ISBN_BUTTON.on('click', function (e) {
        clearTimeout(isbnInputTimer);
        gereInputISBN();
    });
    addEvent();

    ERREUR_DIV.hide();
    MODAL_DETAIL.hide();
});

// function ajout livre à la bibliotheque
function ajouteLivre() {
    ajoutLivreEnCours = true;
    ERREUR_DIV.slideUp();
    // On retire les mauvais caractères
    isbn = nettoieISBN(ISBN_INPUT.val());
    ISBN_INPUT.val(isbn);
    // Lancement de l'ajax
    setAjaxWorking(ISBN_DIV_ID);
    var formAjout = new FormData(ISBN_FORM_JS);
    url = Routing.generate(ROUTE_AJOUT);
    $.ajax({
        url: url,
        type: 'POST',
        data: formAjout,
        contentType: false,
        cache: false,
        processData: false,
        success: function (retour, statut) { // success est toujours en place, bien sûr !
            var code = retour.code;
            var html = retour.html;
            if (code == 200) {
                LISTE_AJOUT_DIV.append(html);
                ISBN_INPUT.val('');
                addEvent();
            } else {
                ERREUR_DIV.html(html);
                ERREUR_DIV.slideDown();
                ISBN_INPUT.trigger('blur');
            }
            unsetAjaxWorking(ISBN_DIV_ID);
            ajoutLivreEnCours = false;
        },

        error: function (resultat, statut, erreur) {
            unsetAjaxWorking(ISBN_DIV_ID);
        }
    });

}
// Gere le timer de l'input isbn
function attenteInputISBN() {
    isbnInputTimer = setTimeout(gereInputISBN, dureeAttenteSaisie);
}
// Gere la saiei de l'utilisateur
function gereInputISBN() {
    if (isISBN(ISBN_INPUT.val()) && !ajoutLivreEnCours ) {
        ajouteLivre();
    } else {
        console.log('no isbn')
    }
}
// Nettoie l'iSBN
function nettoieISBN(isbn) {
    return isbn.trim().replace(/[^\dX]/gi, '');
}

// Vérifie si une donnée est un code ISBN
// Source : https://neilang.com/articles/how-to-check-if-an-isbn-is-valid-in-javascript/
function isISBN(isbn) {
    isbn = nettoieISBN(isbn)
    if (isbn.length != 10) {
        return false;
    }
    var chars = isbn.split('');
    if (chars[9].toUpperCase() == 'X') {
        chars[9] = 10;
    }
    var sum = 0;
    for (var i = 0; i < chars.length; i++) {
        sum += ((10 - i) * parseInt(chars[i]));
    }
    return ((sum % 11) == 0);
}
// Affiche la modale de détail d'un livre
function afficheModalLivre(baseLivreId, divParent) {
    MODAL_DETAIL.modal('hide');
    // Cas où le livre n'est pas encore dans le buffer
    if (!(baseLivreId in bufferLivre)) {
        return rechercheLivre(baseLivreId, divParent);
    }
    afficheLivre(baseLivreId);
}
// Ajout les événements
function addEvent() {
    var rowLivre =$('#liste_livre .row');
    // Ajout de l'action pour apparaîte la pop in
    $('.lien_pop_in input').off('click');
    $('.lien_pop_in').on('click', function () {
        var rowParent = $(this).closest('.row');
        var livreId = rowParent.attr('data-base-livre-id');
        afficheModalLivre(livreId, rowParent);
    });
    // Mise en place du style alterné
    var iRowLivre = 0;
    rowLivre.each(function(e){
        $(this).removeClass('active');
        if(iRowLivre%2 == 0)
            $(this).addClass('active');
        iRowLivre++;

    });
    // Correction de la hauteur de la première ligne car elle n'a pas de input
    if(rowLivre.length>=2){
        rowLivre.get(0).style.height =rowLivre.get(1).offsetHeight+'px';
    }
    // Modification d'un livre
    $('#liste_livre input').off('change');
    $('#liste_livre input').on('change', function () {
        var rowParent = $(this).closest('.row');
        var livreId = rowParent.attr('data-livre-id');
        modifieLivre(livreId, rowParent);
    });
    // Suppression d'un livre
    $('#liste_livre .lnk_suppression_livre').off('click');
    $('#liste_livre .lnk_suppression_livre').on('click', function () {
        if(!confirm('Voulez-vous vraiment supprimer cet élément ? ')){
            e.preventDefault();
            return false;
        }

        var rowParent = $(this).closest('.row');
        var livreId = rowParent.attr('data-livre-id');
        supprimeLivre(livreId, rowParent);
    });
}
// Affiche un livre en pop in
function afficheLivre(baseLivreId) {
    if (baseLivreId in bufferLivre) {
        MODAL_DETAIL.find('.modal-title').html(bufferLivre[baseLivreId]['titre']);
        MODAL_DETAIL.find('.modal-body').html(bufferLivre[baseLivreId]['html']);
        MODAL_DETAIL.modal('show');
    }
    livreBuffer();
}
// Recherche le livre en baseLivreId
function rechercheLivre(baseLivreId, divParent) {
    setAjaxWorking(divParent.attr('id'));
    var url = Routing.generate(ROUTE_DETAIL_POP_IN, {'id': baseLivreId});
    $.ajax({
        url: url,
        type: 'POST',
        contentType: false,
        cache: false,
        processData: false,
        success: function (retour, statut) { // success est toujours en place, bien sûr !
            var titre = retour.titre;
            var html = retour.html;
            unsetAjaxWorking(divParent.attr('id'));
            bufferLivre[baseLivreId] = {'titre': titre, 'html': html};
            afficheLivre(baseLivreId);
        },

        error: function (resultat, statut, erreur) {
            unsetAjaxWorking(divParent.attr('id'));
        }
    });
}
// Affiche les livres connus dans le buffer
function livreBuffer(){
    // Affichage d'une icone avant chaque élément dans le buffer
    for (var livreId in bufferLivre) {
        var element =  $("div[data-base-livre-id="+livreId+"]");
        var caseName = $(element.children('.lien_pop_in').get(0));
        if(caseName.children('.glyphicon-record').length ==0)
            caseName.prepend('<span class="glyphicon glyphicon-record"></span>');
    }
}
// Modifie un livre
function modifieLivre(livreId, divParent){
    setAjaxWorking(divParent.attr('id'));

    //console.log(divParent.find('form').get(0));
    //var formModifieData = new FormData(divParent.find('form').get(0));
    var formModifieData = (divParent.find('form')).serialize();
    var url = Routing.generate(ROUTE_MODIFIE, {'id': livreId});
    $.ajax({
        url: url,
        type: 'POST',
        data: formModifieData,
        cache: false,
        success: function (block_html, statut) { // success est toujours en place, bien sûr !
            divParent.replaceWith(block_html);
            unsetAjaxWorking(divParent.attr('id'));
            addEvent();
        },

        error: function (resultat, statut, erreur) {
            unsetAjaxWorking(divParent.attr('id'));
            addEvent();
        }
    });
}
// Supprime un livre
function supprimeLivre(livreId, divParent){
    setAjaxWorking(divParent.attr('id'));
    var url = Routing.generate(ROUTE_SUPPRESSION, {'id': livreId});
    $.ajax({
        url: url,
        type: 'POST',
        cache: false,
        success: function (block_html, statut) { // success est toujours en place, bien sûr !
            unsetAjaxWorking(divParent.attr('id'));
            divParent.remove();
            addEvent();
        },

        error: function (resultat, statut, erreur) {
            unsetAjaxWorking(divParent.attr('id'));
            addEvent();
        }
    });
}