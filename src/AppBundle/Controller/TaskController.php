<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
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
     * Star action
     */
    public function doneAction(Request $request, $id)
    {
        $this->updateTaskOrDieIfDisallowed($request, $id, ['is_done' => true, 'ts_done' => new \DateTime()]);

        return $this->redirectToReferer($request);
    }

    /**
     * Unstar action
     */
    public function undoneAction(Request $request, $id)
    {
        $this->updateTaskOrDieIfDisallowed($request, $id, ['is_done' => false]);

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
                'data'      => '2h',
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
                'attr'      => ['class' => 'btn-success btn-lg'],
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
     * Delete form action
     */
    public function deleteFormAction(Request $request, $id)
    {
        $task = $this->getTaskOrDie($id, true);

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

            $this->getTaskMapper()->createDelete(['id' => $id])->execute();
            $this->addFlash('success', $this->get('translator')->trans("Task has been deleted!"));

            return $this->redirectToRoute('app_tasks');
        }

        return $this->render('app/task/delete.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit task action
     */
    public function editFormAction(Request $request, $id)
    {
        $task = $this->getTaskOrDie($id, true);

        $form = $this
            ->createFormBuilder()
            ->add('title', TextType::class, [
                'label'     => "Title",
                'required'  => true,
                'attr'      => ['placeholder' => 'Go to grocery store and buy eggs'],
                'data'      => $task->getTitle(),
            ])
            ->add('description', TextareaType::class, [
                'label'     => "Description",
                'required'  => false,
                'attr'      => ['placeholder' => 'Help yourself and write all what you need...'],
                'data'      => $task->getDescription(),
            ])
            ->add('ts_deadline_date', DateType::class, [
                'label'     => "Date",
                'data'      => new \DateTime(),
                'html5'     => true,
                'required'  => false,
                'data'      => $task->deadlinesAt(),
            ])
            ->add('ts_deadline_time', TimeType::class, [
                'label'     => "Time",
                'data'      => new \DateTime(),
                'html5'     => true,
                'required'  => false,
                'data'      => $task->deadlinesAt(),
            ])
            ->add('priority', ChoiceType::class, [
                'label'     => "Priority",
                'multiple'  => false,
                'required'  => true,
                'data'      => $task->getPriority(),
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
            ->add('submit', SubmitType::class, [
                'label'     => "Save",
                'attr'      => ['class' => 'btn-primary'],
            ])
            ->getForm()
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $values = $form->getData();

            // Not proud of this one, but it is working.
            /** @var \DateTime $date */
            $date = $values['ts_deadline_date'];
            /** @var \DateTime $time */
            $time = $values['ts_deadline_time'];
            $date->modify($time->format("+H \\h\\o\\u\\r i\\m\\i\\n\\u\\t\\e"));
            unset($values['ts_deadline_date'], $values['ts_deadline_time']);
            $values['ts_deadline'] = $date;

            $this
                ->getDatabase()
                ->update('task')
                ->condition('id', $id)
                ->sets($values)
                ->execute()
            ;

            return $this->redirectToReferer($request);
        }

        return $this->render('app/task/edit.html.twig', [
            'task' => $task,
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
            ->add('ts_deadline_date', DateType::class, [
                'label'     => "Date",
                'data'      => new \DateTime(),
                'html5'     => true,
                'required'  => false,
            ])
            ->add('ts_deadline_time', TimeType::class, [
                'label'     => "Time",
                'data'      => new \DateTime(),
                'html5'     => true,
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

            // Not proud of this one, but it is working.
            /** @var \DateTime $date */
            $date = $values['ts_deadline_date'];
            /** @var \DateTime $time */
            $time = $values['ts_deadline_time'];
            $date->modify($time->format("+H \\h\\o\\u\\r i\\m\\i\\n\\u\\t\\e"));
            unset($values['ts_deadline_date'], $values['ts_deadline_time']);
            $values['ts_deadline'] = $date;

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
