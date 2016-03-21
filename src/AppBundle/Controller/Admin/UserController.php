<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 3/19/16
 * Time: 9:49 PM
 */

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/user")
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class UserController extends Controller
{
    /**
     * @param Request $request
     * @return array
     * @Route("/", name="admin_users")
     * @Method("GET")
     * @Template("AppBundle:admin/user:list.html.twig")
     */
    public function showAllAction(Request $request)
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT u FROM AppBundle:User u";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1),/*page number*/
            10,/*limit per page*/
            ['defaultSortFieldName' => 'u.id', 'defaultSortDirection' => 'asc']
        );

        return ['pagination' => $pagination];
    }

    /**
     * @param Request $request
     * @param User $user
     * @return array
     * @Route("/set_user_role/{id}", requirements={"id": "\d+"}, name="set_user_role")
     * @ParamConverter("user", class="AppBundle:User")
     * @Method("GET")
     */
    public function setUserRoleAction(Request $request, User $user)
    {
        $user->setRoles([]);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($this->generateUrl('admin_users',
            [
                'sort' => $request->query->get('sort', 'u.id'),
                'direction' => $request->query->get('direction', 'asc'),
                'page' => $request->query->getInt('page', 1),
            ]));
    }

    /**
     * @param Request $request
     * @param User $user
     * @return array
     * @Route("/set_admin_role/{id}", requirements={"id": "\d+"}, name="set_admin_role")
     * @ParamConverter("user", class="AppBundle:User")
     * @Method("GET")
     */
    public function setAdminRoleAction(Request $request, User $user)
    {
        $user->setRoles(['ROLE_ADMIN']);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($this->generateUrl('admin_users',
        [
            'sort' => $request->query->get('sort', 'u.id'),
            'direction' => $request->query->get('direction', 'asc'),
            'page' => $request->query->getInt('page', 1),
        ]));
   }

    /**
     * @param Request $request
     * @param User $user
     * @return array
     * @Route("/switch_active/{id}", requirements={"id": "\d+"}, name="switch_active")
     * @ParamConverter("user", class="AppBundle:User")
     * @Method("GET")
     */
    public function switchActiveAction(Request $request, User $user)
    {
        $user->setEnabled(!$user->isEnabled());
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($this->generateUrl('admin_users',
        [
            'sort' => $request->query->get('sort', 'u.id'),
            'direction' => $request->query->get('direction', 'asc'),
            'page' => $request->query->getInt('page', 1),
        ]));
   }
}