<?php

namespace App\Controller;

use App\Export\UserExport;
use App\Form\Model\UserSearch;
use App\Form\User\UserSearchType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    private $manager;
    private $security;
    private $repo;

    public function __construct(EntityManagerInterface $manager, Security $security, UserRepository $repo)
    {
        $this->manager = $manager;
        $this->security = $security;
        $this->repo = $repo;
    }

    /**
     * Permet de rechercher un utilisateur
     * 
     * @Route("directory/users", name="users")
     * @return Response
     */
    public function listUsers(Request $request, UserSearch $userSearch = null, PaginatorInterface $paginator): Response
    {
        $userSearch = new UserSearch();

        $form = $this->createForm(UserSearchType::class, $userSearch);

        $form->handleRequest($request);

        if ($userSearch->getExport()) {
            $users = $this->repo->findUsersToExport($userSearch);
            $export = new UserExport();
            return $export->exportData($users);
        }

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
     * Tableaud d'administration des utilisateurs
     * 
     * @Route("admin/users", name="admin_users")
     * @return Response
     */
    public function adminListUsers(Request $request, UserSearch $userSearch = null, PaginatorInterface $paginator): Response
    {
        $this->denyAccessUnlessGranted("ROLE_SUPER_ADMIN");

        $userSearch = new UserSearch();

        $form = $this->createForm(UserSearchType::class, $userSearch);
        $form->handleRequest($request);

        if ($userSearch->getExport()) {
            $users = $this->repo->findUsersToExport($userSearch);
            $export = new UserExport();
            return $export->exportData($users);
        }

        $users =  $paginator->paginate(
            $this->repo->findAllUsersQuery($userSearch),
            $request->query->getInt("page", 1), // page number
            20 // limit per page
        );
        // $users->setPageRange(5);
        $users->setCustomParameters([
            "align" => "right", // alignement de la pagination
        ]);


        return $this->render("app/admin/listUsers.html.twig", [
            "users" => $users,
            "userSearch" => $userSearch,
            "form" => $form->createView()
        ]);
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
}
