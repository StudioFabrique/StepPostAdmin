<?php

namespace App\Controller;

use App\ClassesOutils\FormatageObjet;
use App\Entity\Facteur;
use App\Form\FacteurType;
use App\Repository\ExpediteurRepository;
use App\Repository\FacteurRepository;
use DateTime;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'app_')]
#[IsGranted('ROLE_ADMIN')]
class FacteurController extends AbstractController
{
    #[Route('/facteurs', name: 'facteur')]
    public function showFacteurs(FacteurRepository $facteurRepo, PaginatorInterface $paginatorInterface, Request $request, ExpediteurRepository $expediteurRepository): Response
    {

        $facteurs = $paginatorInterface->paginate(
            $facteurRepo->findAll(),
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('facteur/index.html.twig', [
            'facteurs' => $facteurs,
            'expediteursInactifs' => $expediteurRepository->findAllInactive()
        ]);
    }

    #[Route('/nouveauFacteur', 'newFacteur')]
    public function newFacteur(FacteurRepository $facteurRepo, Request $request, ExpediteurRepository $expediteurRepository): Response
    {
        $form = ($this->createForm(FacteurType::class))->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $facteur = (new FormatageObjet)
                    ->stringToLowerObject(
                        $form->getData(),
                        Facteur::class,
                        array('createdAt', 'updatedAt')
                    );

                $facteurRepo->add(
                    $facteur->setRoles(['ROLE_FACTEUR'])
                        ->setCreatedAt(new DateTime('now'))
                        ->setUpdatedAt(new DateTime('now')),
                    true
                );
            } catch (UniqueConstraintViolationException $e) {
                return $this->redirectToRoute('app_facteur');
            }
            return $this->redirectToRoute('app_facteur');
        }

        return $this->renderForm('facteur/form.html.twig', [
            'form' => $form,
            'title' => 'Créer un facteur',
            'expediteursInactifs' => $expediteurRepository->findAllInactive()
        ]);
    }

    #[Route('/modifierFacteur', 'editFacteur')]
    public function editFacteur(FacteurRepository $facteurRepo, Request $request, EntityManagerInterface $em, ExpediteurRepository $expediteurRepository): Response
    {
        $ancienFacteur = $facteurRepo->find($request->get('id'));
        $form = $this->createForm(FacteurType::class, $ancienFacteur)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $facteur = (new FormatageObjet)->stringToLowerObject(
                $formData,
                Facteur::class,
                array('createdAt, updatedAt'),
                false
            );

            $facteur
                ->setRoles(['ROLE_FACTEUR'])
                ->setCreatedAt($ancienFacteur->getCreatedAt())
                ->setUpdatedAt(new DateTime())
                ->setPassword($ancienFacteur->getPassword());
            $em->persist($facteur);
            $em->flush();

            return $this->redirectToRoute('app_facteur');
        }

        return $this->renderForm('facteur/form.html.twig', [
            'form' => $form,
            'title' => 'Modifier le facteur',
            'expediteursInactifs' => $expediteurRepository->findAllInactive()
        ]);
    }

    #[Route('/supprimerFacteur', 'deleteFacteur')]
    public function deleteFacteur(Request $request, FacteurRepository $facteurRepo): Response
    {
        $idFacteur = $request->get('id');
        $facteurRepo->remove($facteurRepo->find($idFacteur), true);
        return $this->redirectToRoute('app_facteur');
    }
}
