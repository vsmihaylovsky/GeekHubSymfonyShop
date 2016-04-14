<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="admin_homepage")
     * @Template("AppBundle:admin:index.html.twig")
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * @Route("/tables", name="admin_tables")
     * @Template("AppBundle:admin:tables.html.twig")
     */
    public function tablesAction()
    {
        return [];
    }

    /**
     * @Route("/forms", name="admin_forms")
     * @Template("AppBundle:admin:forms.html.twig")
     */
    public function formsAction()
    {
        return [];
    }

}
