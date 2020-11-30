<?php


namespace App\Service;


use App\Entity\Image;
use Symfony\Component\Security\Core\User\UserInterface;

class ImageService
{

    public static function saveImage(Image $picture, $form, UserInterface $user)
    {
        $file = $form->get('file')->getData();
        $fileName = substr($file,5). ".png";
        $picture->setImage($form->get('image')->getData());
        $picture->setFile($fileName);
        $picture->setUser($user);
        $picture->setIsProcessed(0);
        return ['picture' => $picture, 'file' => $file, 'fileName' => $fileName];
    }
}