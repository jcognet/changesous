<?php

namespace LivreBundle\Controller;

use LivreBundle\Entity\Livre;
use LivreBundle\Form\ListeLivreType;
use LivreBundle\Form\LivreType;
use LivreBundle\Form\RechercheISBNLivreType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BibliothequeController extends Controller
{
    public function listeAction(Request $request)
    {
        //TODO : modifier le prix d'un livre
        //TODO : Supprimer un livre
        //TODO : notion d'un lieu
        //TODO : empêcher accès à cette page sans user

        // Formulaire pour modifier les dernières entrées de la bibliotheque
        $formBibliotheque = $this->createForm(ListeLivreType::class, $this->getUser());
        // Formulaire pour ajouter un livre
        $formAjout = $this->createForm(RechercheISBNLivreType::class)
            ->add('btnAjouterLivre', ButtonType::class, array(
                'label' => 'Ajouter >>',
            ));

        return $this->render('LivreBundle:Bibliotheque:liste.html.twig', array(
            'form_bibliotheque' => $formBibliotheque->createView(),
            'form_ajout'        => $formAjout->createView()
        ));
    }

    /**
     * Ajoute un livre à la bibliotheque de l'utilisateur courant
     * @param Request $request
     * @return JsonResponse
     */
    public function ajoutAction(Request $request)
    {
        //TODO : vérifier la présence d'un utilisateur
        $formAjout = $this->createForm(RechercheISBNLivreType::class)
            ->add('btnAjouterLivre', ButtonType::class, array(
                'label' => 'Ajouter >>',
            ));
        $formAjout->handleRequest($request);
        $listeErreurs = array();
        $livre = null;

        if ($formAjout->isSubmitted() && $formAjout->isValid()) {
            $isbn = $formAjout->get('isbn')->getData();
            // On recherche si le livre est en base
            $baseLivre = $this->getDoctrine()->getRepository('LivreBundle:BaseLivre')->findOneByIsbn($isbn);
            // S'il n'existe pas, on appelle google
            if (true === is_null($baseLivre)) {
                // On demande à google
                $baseLivre = $this->get('livre.google_get_livre_service')->rechercheLivreParISBN($isbn);
            }
            // On a trouvé un livre... Youhou \o/
            $em = $this->getDoctrine()->getManager();
            if (false === is_null($baseLivre)) {
                // mais est-ce que l'utilisateur courant l'a-t-il déjà ?
                if(false === $em->getRepository('LivreBundle:Livre')->utilisateurPossedeLivre($baseLivre, $this->getUser())) {
                    $livre = new Livre();
                    $livre->setAction('ajout')
                        ->setDateAction(new \DateTime())
                        ->setDateAjout(new \DateTime())
                        ->setPrix($baseLivre->getPrix())
                        ->setBaseLivre($baseLivre)
                        ->setProprietaire($this->getUser());

                    $em->persist($livre);
                    $em->flush();
                }else{
                    $listeErreurs[] = "Vous possédez déjà ce livre, petit coquinou !";
                }
            } else {
                $listeErreurs[] = "Le livre avec l'ISBN " . $isbn . " n'a psa été trouvé par google. ";
            }

        } else {
            // Affichage de tous les erreurs
            foreach ($formAjout->getErrors(true, true) as $erreur) {
                $listeErreurs[] = $erreur->getMessage();
            }
        }
        // Code de retours
        $contentHtml = '';
        $codeRetour  = '';
        // Création du nouveau formulaire
        $formLivre = null;
        if (false === is_null($livre) && count($listeErreurs) === 0) {
            // Succes
            $formLivre  = $this->createForm(LivreType::class, $livre);
            $codeRetour = '200';
            $content    = $this->renderView('LivreBundle:Block:bibliotheque_ajout_livre.html.twig', array(
                'form_livre' => $formLivre->createView(),
            ));
        } else {
            // Dommage, on a des problemes
            $codeRetour = '500';
            $content    = implode('<br/>', $listeErreurs);
        }
        // On retourne le tout !
        return new JsonResponse(
            array(
                'code' => $codeRetour,
                'html' => $content
            )
        );
    }

    public function supprimeAction(Request $request)
    {
        return $this->render('LivreBundle:Bibliotheque:supprime.html.twig', array(// ...
        ));
    }

}
