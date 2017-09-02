<?php

namespace GestionBundle\Controller;

class IndexController extends AbstractGestionController
{
    public function indexAction()
    {
        $this->getUserAccountOrDie();

        return $this->render('GestionBundle:index:index.html.twig', [
            'contracts' => $this->getContratMapper()->paginateListDisplayWhere(['c.date_stop' => null], 100, 1),
        ]);
    }

    public function letterTemplateAction()
    {
        $this->getUserAccountOrDie();

        return $this->render('GestionBundle:letter:template.html.twig');
    }
}
