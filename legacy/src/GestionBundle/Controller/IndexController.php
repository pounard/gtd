<?php

namespace GestionBundle\Controller;

class IndexController extends AbstractGestionController
{
    public function indexAction()
    {
        $this->getUserAccountOrDie();

        return $this->render('GestionBundle:index:index.html.twig');
    }

    public function letterTemplateAction()
    {
        $this->getUserAccountOrDie();

        return $this->render('GestionBundle:letter:template.html.twig');
    }

    public function etatDesLieuxAction()
    {
        $this->getUserAccountOrDie();

        return $this->render('GestionBundle:letter:etat-des-lieux.html.twig');
    }

    public function departLocataireAction()
    {
        $this->getUserAccountOrDie();

        return $this->render('GestionBundle:letter:depart-locataire.html.twig');
    }
}
