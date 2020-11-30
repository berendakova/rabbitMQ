<?php


namespace App\Controller;


use App\Entity\Image;
use App\RabbitMq\MessageHandler;
use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */

    public function profile(UserInterface $user)
    {
        $repository = $this->getDoctrine()->getRepository(Image::class);
        $pictures = $repository->findBy(
            ['user' => $user->getId(), 'isProcessed' => 0]
        );

        $processedPictures = $repository->findBy(
            ['user' => $user->getId(), 'isProcessed' => 1]
        );

        return $this->render('profile/profile.html.twig', [
            'pictures' => $pictures, 'processedPictures' => $processedPictures, 'user' => $this->getUser()
        ]);
    }
}