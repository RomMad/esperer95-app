<?php

namespace App\Controller;

use App\Entity\ServiceUser;
use App\Entity\User;
use App\Entity\UserSearch;

use App\Form\UserSearchType;
use App\Form\UserType;

use App\Repository\UserRepository;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    private $manager;
    private $repo;
    private $request;
    private $security;

    public function __construct(ObjectManager $manager, UserRepository $repo, Security $security)
    {
        $this->manager = $manager;
        $this->repo = $repo;
        $this->security = $security;
    }

    /**
     * Permet de rechercher un utilisateur
     * 
     * @Route("directory/list/users", name="list_users")
     * @Route("/new_support/search/user", name="new_support_search_user")
     * @return Response
     */
    public function listUsers(Request $request, UserSearch $userSearch = null, PaginatorInterface $paginator): Response
    {
        $userSearch = new UserSearch();

        $form = $this->createForm(UserSearchType::class, $userSearch);

        $form->handleRequest($request);

        if ($request->query->all()) {

            $users =  $paginator->paginate(
                $this->repo->findAllUsersQuery($userSearch),
                $request->query->getInt("page", 1), // page number
                20 // limit per page
            );
            // $users->setPageRange(5);
            $users->setCustomParameters([
                "align" => "right", // alignement de la pagination
            ]);
        }

        return $this->render("app/listUsers.html.twig", [
            "users" => $users ?? null,
            "userSearch" => $userSearch,
            "form" => $form->createView()
        ]);
        // return $this->pagination($userSearch, $request, $form, $paginator);
    }

    /**
     * Vérifie si le login est déjà utilisé
     * 
     * @Route("/user/check_username", name="user_check_username", methods="GET")
     * @param Request $request
     * @return Response
     */
    public function checkUsername(Request $request): Response
    {
        $user = $this->repo->findOneBy(["username" => $request->query->get("value")]);

        if ($user) {
            $exists = true;
        } else {
            $exists = false;
        }
        return $this->json(["response" => $exists], 200);
    }

    /**
     * Editer la fiche Utilisateur
     * 
     * @Route("/user/{id}", name="user_show", methods="GET|POST")
     *  @return Response
     */
    public function editUser(User $user, Request $request): Response
    {
        $this->denyAccessUnlessGranted("EDIT", $user);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // $user->setUpdatedAt(new \DateTime())
            //     ->setUpdatedBy($this->security->getUser());

            $this->manager->flush();

            $this->addFlash("success", "Les modifications ont été enregistrées.");
        }

        return $this->render("app/user.html.twig", [
            "form" => $form->createView(),
            "edit_mode" => true
        ]);
    }

    /**
     * Permet de trouver les utilisateurs par le mode de recherche instannée
     *
     * @Route("/search/user", name="search_user")
     * @param User $user
     * @param Request $request
     * @param UserRepository $repo
     * @return Response
     */
    public function searchUser(Request $request): Response
    {
        if ($request->query->get("search")) {
            $search = $request->query->get("search");
        } else {
            $search = null;
        }

        $users = $this->repo->findPeopleByResearch($search);
        $nbResults = count($users);

        if ($nbResults) {
            foreach ($users as $user) {
                $results[] = [
                    "id" => $user->getId(),
                    "lastname" => $user->getLastname(),
                    "firstname" => $user->getFirstname()
                ];
            }
            return $this->json([
                "nb_results" => $nbResults,
                "results" => $results
            ], 200);
        } else {
            return $this->json([
                "nb_results" => $nbResults,
                "results" => "Aucun résultat."
            ], 200);
        }
    }
}