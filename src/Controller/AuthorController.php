<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function PHPUnit\Framework\returnSelf;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/showauthor/{name}', name: 'show_author')]
    public function show($name)
    {
        return $this->render(
            "author/show.html.twig",
            array('nameAuthor' => $name)
        );
    }




    #[Route('/listauthors', name: 'list_authors')]
    public function listAuthor(AuthorRepository $repository, Request $request)
    {

        /* $minNbrBook = $request->query->get('minNbrBook');
        $maxNbrBook = $request->query->get('maxNbrBook');
*/
        $author = $repository->findAll(); // Replace with your own logic to get the authors

        if ($author === null) {
            $author = [];
        }

        usort($author, function ($a, $b) {
            return strcmp($a->getEmail(), $b->getEmail());
        });
        /*
        $filteredAuthors = array_filter($author, function ($author) use ($minNbrBook, $maxNbrBook) {
            return $author->nbrBook >= $minNbrBook && $author->nbrBook <= $maxNbrBook;
        });
*/


        return $this->render('author/authors.html.twig', [
            'author' => $author,
            // 'filteredAuthors' => $filteredAuthors,
        ]);
    }

    #[Route('/addauthor', name: 'add_author')]
    public function addAuthor(ManagerRegistry $managerRegistry)
    {
        $author = new Author();
        $author->setUsername("Victor Hugo");
        $author->setEmail("victo.hugo@gmail.com");
        $author->setDescriptions("");
        $author->setNbrBook(20);
        $em = $managerRegistry->getManager();
        $em->persist($author);
        $em->flush();
        return $this->redirectToRoute("list_authors");
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request, ManagerRegistry $managerRegistry)
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $managerRegistry->getManager();
            $em->persist($author);
            $em->flush();
            return $this->redirectToRoute("list_authors");
        }
        return $this->renderForm(
            "author/add.html.twig",
            array('authorForm' => $form)
        );
    }

    #[Route('/delete', name: 'delete')]
    public function deleteAuthor(AuthorRepository $repository, ManagerRegistry $registry)
    {
        $entityManager = $registry->getManager();

        $authorsWithZeroBooks = $repository->findBy(['nbrBook' => 0]);

        foreach ($authorsWithZeroBooks as $author) {
            $entityManager->remove($author);
        }

        $entityManager->flush();

        // Redirect back to the list of authors page to refresh the list
        return $this->redirectToRoute("list_authors");
    }


    #[Route('/update/{id}', name: 'update_author')]
    public function update(ManagerRegistry $managerRegistry, $id, AuthorRepository $repository, Request $request)
    {
        $author = $repository->find($id);
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $managerRegistry->getManager();
            $em->persist($author);
            $em->flush();
            return $this->redirectToRoute("list_authors");
        }
        return $this->renderForm(
            "author/update.html.twig",
            array('authorForm' => $form)
        );
    }
}
