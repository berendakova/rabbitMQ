<?php


namespace App\Controller;


use App\Entity\Image;
use App\Form\ImageFormType;
use App\RabbitMq\MessageHandler;
use App\Service\ImageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ImageController extends AbstractController
{
    public const IMAGE_DIR = "/var/www/RabbitMQ/rabbitMQ/public/images";

    /**
     * @Route("/add", name="add-picture")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function addPictures(Request $request): Response {
        $messageHandler = new MessageHandler();
        $success = "image not added";
        $picture = new Image();
        $form = $this->createForm(ImageFormType::class, $picture);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $picture = ImageService::saveImage($picture, $form, $this->getUser());
            $picture['file']->move(
                self::IMAGE_DIR,
                $picture['fileName']
            );
            $messageHandler->addMessage(  $picture['fileName']);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($picture['picture']);
            $entityManager->flush();
            $success = "image add";
        }

        return $this->render('image/add_pictures.html.twig', [
            'imageForm' => $form->createView(), 'success' => $success, 'user' => $this->getUser()
        ]);
    }
}