<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Note;
use Goat\Core\Query\Query;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Note controller
 */
class NoteController extends AbstractAppController
{
    /**
     * Note add form build and processing
     */
    private function noteAddForm(Request $request, $taskId) : FormBuilderInterface
    {
        $this->taskIsMineOrDie($taskId);

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
    public function partialAddAction($taskId)
    {
        /** @var \Symfony\Component\HttpFoundation\Request $request */
        $request = $this->get('request_stack')->getMasterRequest();

        $form = $this
            ->noteAddForm($request, $taskId)
            ->setAction($this->generateUrl('app_note_add', ['taskId' => $taskId]))
            ->getForm()
        ;

        $form->handleRequest($request);

        return $this->render('app/partialForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Add note
     */
    public function addAction(Request $request, $taskId)
    {
        $form = $this->noteAddForm($request, $taskId)->getForm();
        $account = $this->getUserAccountOrDie();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $values = $form->getData();

            // Add some defaults
            $values['id_account'] = $account->getId();
            $values['id_task'] = $taskId;

            $this
                ->getDatabase()
                ->insertValues('task_comment')
                ->columns(array_keys($values))
                ->values(array_values($values))
                ->execute()
            ;

            return $this->redirectToReferer($request, 'app_task_view', ['id' => $taskId]);
        }

        return $this->render('app/note/add.html.twig', [
            'taskId' => $taskId,
            'form' => $form->createView(),
        ]);
    }

    /**
     * View all notes for the given task action
     */
    public function viewAllAction($taskId)
    {
        $this->taskIsMineOrDie($taskId);

        $notes = $this
            ->getNoteMapper()
            ->createSelect()
            ->condition('n.id_task', $taskId)
            ->orderBy('n.ts_added', Query::ORDER_ASC)
            ->orderBy('n.id', Query::ORDER_ASC)
            ->execute([], Note::class)
        ;

        return $this->render('app/note/all.html.twig', [
            'taskId' => $taskId,
            'notes' => $notes,
        ]);
    }
}
