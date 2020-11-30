<?php


namespace App\Console;


use App\Controller\ImageController;
use App\Entity\Image;
use App\RabbitMq\MessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use ImagickDraw;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RabbitCommand extends Command
{
    protected static $defaultName = 'app:read-message';
    public const USERNAME = "admin";
    public const PASSWORD = "password";
    public const PORT = 5672;
    public const HOST = "localhost";

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Read message from RMQ');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = new AMQPStreamConnection(self::HOST, self::PORT, self::USERNAME, self::PASSWORD);
        $channel = $connection->channel();
        $channel->queue_declare(MessageHandler::PICTURE_QUEUE, false, false, false, false);
        $channel->basic_consume(
            MessageHandler::PICTURE_QUEUE,
            '',
            false,
            true,
            false,
            false,
            array($this, 'processOrder')
        );


        while (count($channel->callbacks)) {
            try {
                $channel->wait();
            } catch (\ErrorException $e) {
            }
        }
        $channel->close();
        $connection->close();
        return Command::SUCCESS;
    }

    public function processOrder($msg)
    {
        $fileName = $msg->body;

        $imageFolder = ImageController::IMAGE_DIR . "/" . $fileName;

        $imagick = new \Imagick($imageFolder);
        $borderColor = "green";
        $imagick->borderImage($borderColor,10,10);
        $imagick->setCompressionQuality(80);
        file_put_contents($imageFolder ,$imagick);

        $repository = $this->entityManager->getRepository("App:Image");
        $picture = $repository->findOneBy(
            ['file' => $fileName]
        );
        $picture->setIsProcessed(1);
        $this->entityManager->persist($picture);
        $this->entityManager->flush();

        echo $fileName;
    }

}