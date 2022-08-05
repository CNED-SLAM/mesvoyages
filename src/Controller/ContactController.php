<?php
namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * Description of ContactController
 *
 * @author emds
 */
class ContactController extends AbstractController {
    
    const PAGE_CONTACT = "pages/contact.html.twig";
    const PAGE_EMAIL = "pages/_email.html.twig";
    const ROUTE_CONTACT = "contact";
    
    /**
     * @Route("/contact", name="contact")
     * @return Response
     */
    public function index(Request $request, MailerInterface $mailer): Response{
        $contact = new Contact();
        $formContact = $this->createForm(ContactType::class, $contact);
        $formContact->handleRequest($request);

        if($formContact->isSubmitted() && $formContact->isValid()){
            // envoi du mail
            $this->sendEmail($mailer, $contact);
            $this->addFlash('succes', 'message envoyé');
            return $this->redirectToRoute(self::ROUTE_CONTACT);
        }

        return $this->render(self::PAGE_CONTACT, [
            'contact' => $contact,
            'formcontact' => $formContact->createView()
        ]);
    }

    /**
     * Envoi de mail
     * @param MailerInterface $mailer
     * @param Contact $contact
     */
    public function sendEmail(MailerInterface $mailer, Contact $contact){
        $email = (new Email())
            ->from($contact->getEmail())
            ->to('contact@mesvoyages.com')
            ->subject('Message du site de voyages')
            ->html($this->renderView(
                    self::PAGE__EMAIL, [
                        'contact' => $contact
                    ]
                ),
                'utf-8'
            )
        ;
        $mailer->send($email);
    }
    
}
