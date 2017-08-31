<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Alarm;
use AppBundle\Entity\Task;
use MakinaCorpus\ACL\Permission;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Alarm controller
 */
class AlarmController extends AbstractAppController
{
    /**
     * Note add form build and processing
     */
    private function alarmAddForm(Request $request, Task $task) : FormBuilderInterface
    {
        $this->denyAccessUnlessGranted(Permission::UPDATE, $task);

        $form = $this
            ->createFormBuilder()
            ->add('ts_trigger_date', DateType::class, [
                'label'     => "Triggers on",
                'data'      => new \DateTime(),
                'html5'     => true,
                'required'  => false,
            ])
            ->add('ts_trigger_time', TimeType::class, [
                'label'     => "At",
                'data'      => new \DateTime(),
                'html5'     => true,
                'required'  => false,
            ])
            ->add('repeat', CheckboxType::class, [
                'label'     => "Repeat",
                'required'  => false,
            ])
            ->add('duration', ChoiceType::class, [
                'label'     => "Repeat interval",
                'required'  => true,
                'multiple'  => false,
                'data'      => '15m',
                'choices'   => [
                    "10 minutes" => '10m',
                    "15 minutes" => '15m',
                    "30 minutes" => '30mn',
                    "1 hour" => '1h',
                    "2 hour" => '2h',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label'     => "Submit",
                'attr'      => ['class' => 'btn-success'],
            ])
        ;

        return $form;
    }

    /**
     * List alarms for task
     */
    public function allAction(Task $task)
    {
        return $this->render('app/alarm/all.html.twig', [
            'task'    => $task,
            'alarms'  => $this->getAlarmMapper()->paginateForTask($task->getId()),
        ]);
    }

    /**
     * Add note
     */
    public function addAction(Request $request, Task $task)
    {
        $form = $this->alarmAddForm($request, $task)->getForm();
        $account = $this->getUserAccountOrDie();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $values = $form->getData();

            // Add some defaults
            $values['id_account'] = $account->getId();
            $values['id_task'] = $task->getId();
            $values['repeat'] = (bool)$values['repeat'];

            // Not proud of this one, but it is working.
            /** @var \DateTime $date */
            $date = $values['ts_trigger_date'];
            /** @var \DateTime $time */
            $time = $values['ts_trigger_time'];
            $date->modify($time->format("+H \\h\\o\\u\\r i\\m\\i\\n\\u\\t\\e"));
            unset($values['ts_trigger_date'], $values['ts_trigger_time']);
            $values['ts_trigger'] = $date;

            switch ($values['duration']) {

                case '10m':
                    $values['duration'] = new \DateInterval("PT10M");
                    break;

                case '15m':
                    $values['duration'] = new \DateInterval("PT15M");
                    break;

                case '30mn':
                    $values['duration'] = new \DateInterval("PT30M");
                    break;

                case '1h':
                    $values['duration'] = new \DateInterval("PT1H");
                    break;

                case '2h':
                    $values['duration'] = new \DateInterval("PT2H");
                    break;
            }

            $this
                ->getDatabase()
                ->insertValues('task_alarm')
                ->columns(array_keys($values))
                ->values(array_values($values))
                ->execute()
            ;

            if ($request->isXmlHttpRequest() && in_array('application/json', $request->getAcceptableContentTypes())) {
                return $this->renderJsonPage('app/task/view.html.twig', ['task' => $task]);
            } else {
                return $this->redirectToReferer($request, 'app_task_view', ['id' => $task->getId()]);
            }
        }

        return $this->render('app/alarm/add.html.twig', [
            'task' => $task->getId(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete form action
     */
    public function deleteFormAction(Request $request, Task $task, Alarm $alarm)
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

            $this->getAlarmMapper()->createDelete(['id' => $alarm->getId()])->execute();
            $this->addFlash('success', $this->get('translator')->trans("Note has been deleted!"));

            return $this->redirectToRoute('app_task_view', ['task' => $task->getId()]);
        }

        return $this->render('app/alarm/delete.html.twig', [
            'alarm' => $alarm,
            'task'  => $task,
            'form'  => $form->createView(),
        ]);
    }
}
