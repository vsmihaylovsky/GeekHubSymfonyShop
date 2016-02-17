<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="admin_homepage")
     * @Template("AppBundle:admin:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        return [];
    }

    /**
     * @Route("/tables", name="admin_tables")
     * @Template("AppBundle:admin:tables.html.twig")
     */
    public function tablesAction(Request $request)
    {
        return [];
    }

    /**
     * @Route("/forms", name="admin_forms")
     * @Template("AppBundle:admin:forms.html.twig")
     */
    public function formsAction(Request $request)
    {
        return [];
    }

}
