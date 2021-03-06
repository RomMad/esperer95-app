<?php

namespace App\Controller;

use App\Entity\Pole;
use App\Form\Pole\PoleType;
use App\Repository\PoleRepository;
use App\Service\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PoleController extends AbstractController
{
    private $manager;
    private $repo;

    public function __construct(EntityManagerInterface $manager, PoleRepository $repo)
    {
        $this->manager = $manager;
        $this->repo = $repo;
    }

    /**
     * Liste des pôles
     * 
     * @Route("/poles", name="poles")
     * @param Request $request
     * @param Pagination $pagination
     * @return Response
     */
    public function listPole(Request $request, Pagination $pagination): Response
    {
        $poles =  $pagination->paginate($this->repo->findAllPolesQuery(), $request);

        return $this->render("app/pole/listPoles.html.twig", [
            "poles" => $poles ?? null
        ]);
    }

    /**
     * Nouveau pôle
     * 
     * @Route("/pole/new", name="pole_new", methods="GET|POST")
     *  @return Response
     */
    public function newPole(Pole $pole = null, Request $request): Response
    {
        $this->denyAccessUnlessGranted("ROLE_SUPER_ADMIN");

        $pole = new Pole();

        $form = $this->createForm(PoleType::class, $pole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->createPole($pole);
        }

        return $this->render("app/pole/pole.html.twig", [
            "form" => $form->createView(),
            "edit_mode" => false
        ]);
    }

    /**
     * Modification d'un pôle
     * 
     * @Route("/pole/{id}", name="pole_edit", methods="GET|POST")
     *  @return Response
     */
    public function editPole(Pole $pole, Request $request): Response
    {
        $this->denyAccessUnlessGranted("EDIT", $pole);

        $form = $this->createForm(PoleType::class, $pole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->updatePole($pole);
        }

        return $this->render("app/pole/pole.html.twig", [
            "form" => $form->createView(),
            "edit_mode" => true
        ]);
    }

    /**
     * Crée un pôle
     *
     * @param Pole $pole
     */
    protected function createPole(Pole $pole)
    {

        $now = new \DateTime();

        $pole->setCreatedAt($now)
            ->setCreatedBy($this->getUser())
            ->setUpdatedAt($now)
            ->setUpdatedBy($this->getUser());

        $this->manager->persist($pole);
        $this->manager->flush();

        $this->addFlash("success", "Le pôle a été créé.");

        return $this->redirectToRoute("pole_edit", ["id" => $pole->getId()]);
    }

    /**
     * Met à jour un pôle
     *
     * @param Pole $pole
     */
    protected function updatePole(Pole $pole)
    {
        $pole->setUpdatedAt(new \DateTime())
            ->setUpdatedBy($this->getUser());

        $this->manager->flush();

        $this->addFlash("success", "Les modifications ont été enregistrées.");
    }
}
