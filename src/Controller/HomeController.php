<?php


namespace App\Controller;


use App\RabbitMq\MessageHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */

    public function home()

    {
        return $this->render('home/home.html.twig', [
            'user' => $this->getUser()
        ]);
    }
    /**
     * @Route("/", name="start")
     */

    public function start()

    {
        return $this->redirect('/home');
    }

}