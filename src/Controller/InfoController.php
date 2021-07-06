<?php


namespace App\Controller;



use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use App\Entity\Contact;

class InfoController extends AbstractController
{
    /**
     * @Route(path="/about", name="about")
     */
    public function about(Environment $twig)
    {
        $content = $twig->render('infos/about.html.twig');
        return new Response($content);
    }

    /**
     * @Route(path="/contact", name="contact")
     */
    public function contact(Environment $twig, Request $request, \Swift_Mailer $mailer)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class);//on appelle le formulaire de contact

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message = (new \Swift_Message($contact->getSubject()))
                ->setFrom('marketplace12344@gmail.com')
                ->setTo('marketplace12344@gmail.com')
                ->setBody(
                    $this->renderView(
                    // templates/mail/mail_contact.html.twig
                        'mail/mail_contact.html.twig',
                        ['name' => $contact->getName(), 'lastname' => $contact->getLastName(), 'message' => $contact->getMessage(), 'mail' => $contact->getMail()]

                    ),
                    'text/html'
                );


            $mailer->send($message);//on envoit le mail
        }

        return $this->render('infos/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}




