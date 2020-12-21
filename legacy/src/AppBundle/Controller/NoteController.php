<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Note;
use AppBundle\Entity\Task;
use MakinaCorpus\ACL\Permission;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Note controller
 */
class NoteController extends AbstractAppController
{
    /**
     * Note add form build and processing
     */
    private function noteAddForm(Request $request, Task $task) : FormBuilderInterface
    {
        $this->denyAccessUnlessGranted(Permission::UPDATE, $task);

        $form = $this
            ->createFormBuilder()
            ->add('description', TextareaType::class, [
                'required'  => false,
                'label'     => false,
                'attr'      => ['placeholder' => 'Hey, write something!'],
            ])
            ->add('submit', SubmitType::class, [
                'label'     => "Comment",
                'attr'      => ['class' => 'btn-success'],
            ])
        ;

        return $form;
    }

    /**
     * Add note (form only, no view)
     */
    public function partialAddAction(Task $task)
    {
        /** @var \Symfony\Component\HttpFoundation\Request $request */
        $request = $this->get('request_stack')->getMasterRequest();

        $form = $this
            ->noteAddForm($request, $task)
            ->setAction($this->generateUrl('app_note_add', ['task' => $task->getId()]))
            ->getForm()
        ;

        $form->handleRequest($request);

        return $this->render('app/partialAjaxForm.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Add note
     */
    public function addAction(Request $request, Task $task)
    {
        $form = $this->noteAddForm($request, $task)->getForm();
        $account = $this->getUserAccountOrDie();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $values = $form->getData();

            // Add some defaults
            $values['id_account'] = $account->getId();
            $values['id_task'] = $task->getId();

            $this
                ->getDatabase()
                ->insertValues('task_comment')
                ->columns(array_keys($values))
                ->values(array_values($values))
                ->execute()
            ;

            if ($request->isXmlHttpRequest() && in_array('application/json', $request->getAcceptableContentTypes())) {
                return $this->renderJsonPage('app/task/view.html.twig', ['task' => $task]);
            } else {
                return $this->redirectToReferer($request, 'app_task_view', ['task' => $task->getId()]);
            }
        }

        return $this->render('app/note/add.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete form action
     */
    public function deleteFormAction(Request $request, Task $task, Note $note)
    {
        $form = $this
            ->createFormBuilder()
            ->add('submit', SubmitType::class, [
                'label'     => "Delete",
                'attr'      => ['class' => 'btn-danger btn-lg'],
            ])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getNoteMapper()->createDelete(['id' => $note->getId()])->execute();
            $this->addFlash('success', $this->get('translator')->trans("Note has been deleted!"));

            return $this->redirectToRoute('app_task_view', ['task' => $task->getId()]);
        }

        return $this->render('app/note/delete.html.twig', [
            'note' => $note,
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * View a single note
     */
    public function viewAction(Task $task, Note $note)
    {
        return $this->render('app/note/view.html.twig', [
            'task' => $task,
            'note' => $note,
        ]);
    }

    /**
     * View all notes for the given task with no additional security checks
     */
    public function viewAllPartialAction(Task $task)
    {
        $notes = $this->getNoteMapper()->paginateForTask($task->getId());

        return $this->render('app/note/all.html.twig', [
            'task'  => $task,
            'notes' => $notes,
        ]);
    }
}
