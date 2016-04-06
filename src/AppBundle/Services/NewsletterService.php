<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 4/6/16
 * Time: 7:20 PM
 */

namespace AppBundle\Services;

use AppBundle\Entity\Newsletter;
use AppBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Bundle\TwigBundle\TwigEngine;

class NewsletterService
{
    private $formFactory;
    private $router;
    private $tokenStorage;
    private $mailer;
    private $noreplyEmailAddress;
    private $noreplySenderName;
    private $templating;

    public function __construct(
        FormFactory $formFactory,
        Router $router,
        TokenStorage $tokenStorage,
        \Swift_Mailer $mailer,
        TwigEngine $templating,
        $noreplyEmailAddress,
        $noreplySenderName
    ) {
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->noreplyEmailAddress = $noreplyEmailAddress;
        $this->noreplySenderName = $noreplySenderName;
    }

    /**
     * @param Newsletter $newsletter
     * @return \Symfony\Component\Form\Form
     */
    public function getSendNewsletterForm(Newsletter $newsletter)
    {
        /** @var FormBuilder $formBuilder */
        $formBuilder = $this->formFactory->createBuilder();

        return $formBuilder
            ->setAction($this->router->generate('send_newsletter', ['id' => $newsletter->getId()]))
            ->setMethod('POST')
            ->add('sendTestEmail', EmailType::class,
                [
                    'label' => 'newsletter.test_send_email',
                    'data' => $this->tokenStorage->getToken()->getUser()->getEmail(),
                ])
            ->add('sendTest', SubmitType::class, ['label' => 'newsletter.test.send'])
            ->add('send', SubmitType::class, ['label' => 'newsletter.send'])
            ->getForm();
    }

    /**
     * @param Newsletter $newsletter
     * @param array $users
     */
    public function sendNewsletter(Newsletter $newsletter, array $users)
    {
        /** @var User $user */
        foreach ($users as $user) {
            $message = \Swift_Message::newInstance()
                ->setSubject($newsletter->getSubject())
                ->setFrom([$this->noreplyEmailAddress => $this->noreplySenderName])
                ->setTo($user->getEmail(), $user->getUsername())
                ->setBody(
                    $this->templating->render(
                        'AppBundle:Emails:newsletter.html.twig',
                        [
                            'message' => $newsletter->getMessage()
                        ]
                    ),
                    'text/html');

            $this->mailer->send($message);
        }
    }
}