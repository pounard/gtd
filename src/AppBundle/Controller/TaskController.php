<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class TaskController extends AbstractAppController
{
    /**
     * Update task, die if disallowed
     *
     * @param Request $request
     * @param int|string $id
     * @param array $sets
     */
    private function updateTaskOrDieIfDisallowed(Request $request, $id, array $sets)
    {
        if (!$request->isMethod('POST')) {
            throw new MethodNotAllowedHttpException(['POST']);
        }

        $this->taskIsMineOrDie($id);

        $this
            ->getDatabase()
            ->update('task')
            ->sets($sets)
            ->condition('id', $id)
            ->execute()
        ;
    }

    /**
     * Single task view action
     */
    public function viewAction(Request $request, $id)
    {
        return $this->render('app/task/view.html.twig', [
            'task' => $this->getTaskOrDie($id, true),
        ]);
    }

    /**
     * Star action
     */
    public function starAction(Request $request, $id)
    {
        $this->updateTaskOrDieIfDisallowed($request, $id, ['is_starred' => true]);

        return $this->redirectToReferer($request);
    }

    /**
     * Unstar action
     */
    public function unstarAction(Request $request, $id)
    {
        $this->updateTaskOrDieIfDisallowed($request, $id, ['is_starred' => false]);

        return $this->redirectToReferer($request);
    }

    /**
     * Unhide action
     */
    public function unhideAction(Request $request, $id)
    {
        $this->updateTaskOrDieIfDisallowed($request, $id, [
            'is_hidden' => false,
            'ts_unhide' => null,
        ]);

        return $this->redirectToReferer($request);
    }

    /**
     * Hide form action
     */
    public function hideFormAction(Request $request, $id)
    {
        $this->taskIsMineOrDie($id);

        $form = $this
            ->createFormBuilder()
            ->add('time', ChoiceType::class, [
                'label'     => "For how long?",
                'required'  => true,
                'multiple'  => false,
                'choices'   => [
                    "10 minutes" => '10m',
                    "1 hour" => '1h',
                    "2 hours" => '2h',
                    "Tomorrow at 8:00" => 't8',
                    "Tomorrow at 12:00" => 't12',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label'     => "Hide it!",
                'attr'      => ['class' => 'btn-danger'],
            ])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $values = $form->getData();

            $unhideDate = null;
            switch ($values['time']) {

                case '10m':
                    $unhideDate = new \DateTime("now +10 minutes");
                    break;

                case '1h':
                    $unhideDate = new \DateTime("now +1 hour");
                    break;

                case '2h':
                    $unhideDate = new \DateTime("now +2 hours");
                    break;

                case 't8':
                    $unhideDate = new \DateTime("tomorrow 8am");
                    break;

                default:
                    $unhideDate = new \DateTime("tomorrow noon");
                    break;
            }

            $this
                ->getDatabase()
                ->update('task')
                ->condition('id', $id)
                ->sets([
                    'is_hidden' => true,
                    'ts_unhide' => $unhideDate,
                ])
                ->execute()
            ;

            return $this->redirectToReferer($request);
        }

        return $this->render('app/task/hide.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Add task action
     */
    public function addAction(Request $request)
    {
        $account = $this->getUserAccountOrDie();

        $form = $this
            ->createFormBuilder()
            ->add('title', TextType::class, [
                'label'     => "Title",
                'required'  => true,
                'attr'      => ['placeholder' => 'Go to grocery store and buy eggs'],
            ])
            ->add('description', TextareaType::class, [
                'label'     => "Description",
                'required'  => false,
                'attr'      => ['placeholder' => 'Help yourself and write all what you need...'],
            ])
            ->add('ts_deadline', DateTimeType::class, [
                'data'      => new \DateTime(),
                'required'  => false,
            ])
            ->add('priority', ChoiceType::class, [
                'label'     => "Priority",
                'multiple'  => false,
                'required'  => true,
                'data'      => 0,
                'choices'   => [
                    "Immediate"     => 3,
                    "Very high"     => 2,
                    "high"          => 1,
                    "Normal"        => 0,
                    "Low"           => -1,
                    "Very low"      => -2,
                    "I don't care"  => -3,
                ],
            ])
            ->add('is_done', CheckboxType::class, [
                'label'     => "Done",
                'required'  => false,
            ])
            ->add('is_starred', CheckboxType::class, [
                'label'     => "Starred",
                'required'  => false,
            ])
            ->add('submit', SubmitType::class, [
                'label'     => "Save",
                'attr'      => ['class' => 'btn-primary'],
            ])
            ->getForm()
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $values = $form->getData();

            // Add some defaults
            $values['id_account'] = $account->getId();

            $this
                ->getDatabase()
                ->insertValues('task')
                ->columns(array_keys($values))
                ->values(array_values($values))
                ->execute()
            ;

            return $this->redirectToReferer($request);
        }

        return $this->render('app/task/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
