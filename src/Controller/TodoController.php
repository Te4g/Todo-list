<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Form\NewType;
use App\Form\TodoType;
use App\Form\UpdateType;
use App\Repository\TodoRepository;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/todo")
 */
class TodoController extends AbstractController
{
    /**
     * @Route("/", name="todo_index")
     * @param TodoRepository $todoRepository
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function index(TodoRepository $todoRepository, Request $request): Response
    {
        $todo = new Todo();
        $form = $this->createForm(NewType::class, $todo);
        $form->handleRequest($request);
        $updateForm = $this->createForm(TodoType::class, $todo);
        $userId = $this->getUser();


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $todo->setCreatedAt(new DateTime('now'));
            $todo->setIsCompleted(false);
            $todo->setUser($this->getUser());
            $entityManager->persist($todo);
            $entityManager->flush();
        }
        return $this->render('todo/index.html.twig', [
            'todos' => $todoRepository->findByUserId($userId),
            'oldTodos' => $todoRepository->findByUserIdOld($userId),
            'form' => $form->createView(),
            'updateForm' => $updateForm->createView(),
            'userId' => $userId,
        ]);
    }


    /**
     * @Route("/{id}/edit", name="todo_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Todo $todo): Response
    {
        $form = $this->createForm(UpdateType::class, $todo);
        $form->handleRequest($request);
        if(isset($_POST['todo']['title'])) {
            $title = $_POST['todo']['title'];
            $todo->setTitle($title);
        }  if(isset($_POST['todo']['detail'])) {
            $detail = $_POST['todo']['detail'];
            $todo->setDetail($detail);
        }  if(isset($_POST['todo']['end_at']) && !empty($_POST['todo']['end_at'])) {
            $date = DateTime::createFromFormat('Y-m-d', ($_POST['todo']['end_at']));
            $todo->setEndAt($date);
        }
        if(isset($_POST['todo']['is_completed'])) {
            $todo->setIsCompleted($_POST['todo']['is_completed']);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($todo);
        $em->flush();

        return $this->redirectToRoute('todo_index');


    }

    /**
     * @Route("/{id}", name="todo_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Todo $todo): Response
    {
        if ($this->isCsrfTokenValid('delete' . $todo->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($todo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('todo_index');
    }

    /**
     * @param Request $request
     * @param Todo $todo
     * @return Response
     * @Route("/{id}/old", name="todo_old")
     */
    public function old(Request $request, Todo $todo): Response
    {
        $em = $this->getDoctrine()->getManager();
        $todo->setIsCompleted(true);
        $em->persist($todo);
        $em->flush();

        return $this->redirectToRoute('todo_index');
    }
    /**
     * @param Request $request
     * @param Todo $todo
     * @return Response
     * @Route("/{id}/renew", name="todo_renew")
     */
    public function renew(Request $request, Todo $todo): Response
    {
        $em = $this->getDoctrine()->getManager();
        $todo->setIsCompleted(false);
        $em->persist($todo);
        $em->flush();

        return $this->redirectToRoute('todo_index');
    }
}
