<?php

namespace App\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GetImageController extends AbstractController
{
    #[Route('/get/{image}', name: 'app_get_image')]
    public function getImage(
        string $image,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        $imageProccess = $entityManager->getRepository(Task::class)->findOneBy(
            [
                'hash' => $image
            ]
        );

        if (empty($imageProccess)) {
            return new BinaryFileResponse(
                $request->server->get('DOCUMENT_ROOT') . '404.png'
            );
        }

        if (!empty($imageProccess->getOutputName())) {
            if (file_exists($request->server->get('DOCUMENT_ROOT') . 'out/' . $imageProccess->getOutputName())) {
                return new BinaryFileResponse(
                    $request->server->get('DOCUMENT_ROOT') . 'out/' . $imageProccess->getOutputName()
                );
            }
        }

        return new BinaryFileResponse(
            $request->server->get('DOCUMENT_ROOT') . 'no-image.jpg'
        );
    }
}
