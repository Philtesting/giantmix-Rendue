<?php


namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class CommandeController extends AbstractController
{
    /**
     * @Route(path="/myprofil", name="myprofil")
     */
    public function myprofil(Environment $twig, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $user = $em->getRepository(User::class)->find($user->getId());
        $achats = $user->getAchats();
        $content = $twig->render('commande/myprofil.html.twig', [
            "achats" => $achats
        ]);
        return new Response($content);

    }
}




