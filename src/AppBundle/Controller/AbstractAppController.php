<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Mapper\NoteMapper;
use AppBundle\Mapper\TaskMapper;
use Goat\AccountBundle\Controller\AccountMapperAwareController;
use Goat\Mapper\Error\EntityNotFoundError;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractAppController extends Controller
{
    use AccountMapperAwareController;
    use RefererControllerTrait;

    /**
     * Get task mapper
     *
     * @return TaskMapper
     */
    final protected function getTaskMapper() : TaskMapper
    {
        return $this->getMapper('App:Task');
    }

    /**
     * Get task mapper
     *
     * @return TaskMapper
     */
    final protected function getNoteMapper() : NoteMapper
    {
        return $this->getMapper('App:Note');
    }

    /**
     * Get criteria for task that is owned by the current user
     *
     * @param int|string $id
     */
    final private function getTaskForUserCriteria($id)
    {
        if (!$id || !is_numeric($id)) {
            throw $this->createNotFoundException();
        }

        return ['t.id' => $id, 't.id_account' => $this->getUserAccountOrDie()->getId()];
    }

    /**
     * Die if task does not exists or I am not owner
     *
     * @param string $taskId
     */
    final protected function taskIsMineOrDie(string $id)
    {
        if (!$this->getTaskMapper()->exists($this->getTaskForUserCriteria($id))) {
            throw $this->createNotFoundException();
        }
    }

    /**
     * Get task
     *
     * @param string $id
     * @param bool $isOwner
     *
     * @return Task
     */
    final protected function getTaskOrDie(string $id, bool $isOwner = false) : Task
    {
        try {
            return $this->getTaskMapper()->findFirst($this->getTaskForUserCriteria($id), true);
        } catch (EntityNotFoundError $e) {
            throw $this->createNotFoundException();
        }
    }

    /**
     * Render the given view as page blocks in a JSON response
     *
     * @param string $name
     * @param array $context
     *
     * @return JsonResponse
     */
    final protected function renderJsonPage(string $name, array $context) : JsonResponse
    {
        /** @var \Twig_Environment $twig */
        $twig = $this->container->get('twig');

        /** @var \Twig_Template $template */
        $template = $twig->load($name);

        $data = [];
        foreach (['messages', 'menu', 'content'] as $block) {
            if ($template->hasBlock($block, $context)) {
                $data[$block] = $template->renderBlock($block, $context);
            }
        }

        return new JsonResponse($data);
    }
}
