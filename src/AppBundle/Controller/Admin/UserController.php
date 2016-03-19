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

/**
 * @Route("/admin/user")
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class UserController extends Controller
{
    /**
     * @return array
     * @Route("/", name="admin_users")
     * @Method("GET")
     * @Template("AppBundle:admin/user:list.html.twig")
     */
    public function showAllAction()
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $users = $repository->findAll();

        return ['users' => $users];
    }

    /**
     * @param User $user
     * @return array
     * @Route("/set_user_role/{id}", requirements={"id": "\d+"}, name="set_user_role")
     * @ParamConverter("user", class="AppBundle:User")
     * @Method("GET")
     */
    public function setUserRoleAction(User $user)
    {
        $user->setRoles([]);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($this->generateUrl('admin_users'));
    }

    /**
     * @param User $user
     * @return array
     * @Route("/set_admin_role/{id}", requirements={"id": "\d+"}, name="set_admin_role")
     * @ParamConverter("user", class="AppBundle:User")
     * @Method("GET")
     */
    public function setAdminRoleAction(User $user)
    {
        $user->setRoles(['ROLE_ADMIN']);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($this->generateUrl('admin_users'));
    }

    /**
     * @param User $user
     * @return array
     * @Route("/switch_active/{id}", requirements={"id": "\d+"}, name="switch_active")
     * @ParamConverter("user", class="AppBundle:User")
     * @Method("GET")
     */
    public function switchActiveAction(User $user)
    {
        $user->setEnabled(!$user->isEnabled());
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($this->generateUrl('admin_users'));
    }
}