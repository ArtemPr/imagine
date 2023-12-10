<?php

namespace App\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiController extends AbstractController
{
    private array $validImageFormat = [
        'png', 'jpg', 'gif', 'webp', 'svg', 'tiff'
    ];
    private array $validResizeType = [
        'width', 'height', 'crop'
    ];

    #[Route('/add', name: 'app_add', methods: ['POST'])]
    function add(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        if (empty($request->request->all())) {
            $data = json_decode(file_get_contents("php://input"), true);
        } else {
            $data = $request->request->all();
        }

        $out = [];

        foreach ($data as $key => $value) {
            if (!empty($value['location'])) {

                $format = (in_array($value['format'], $this->validImageFormat)) ? $value['format'] : NULL;
                $resizeParam = (preg_match("/[0-9]{1,5}\*[0-9]{1,5}/", $value['resizeParam'])) ? $value['resizeParam'] : NULL;
                $resizeType = (in_array($value['resizeType'], $this->validResizeType)) ? $value['resizeType'] : NULL;

                $hash = md5(
                    $value['location'] .
                    (string)$format.
                    (string)$resizeParam.
                    (string)$resizeType
                );

                $get = $entityManager->getRepository(Task::class)->findBy(
                    [
                        'hash' => $hash
                    ]
                );
                if (empty($get)) {
                    $task = new Task();
                    $task->setLocation($value['location']);
                    $task->setConvertFormat($format);
                    $task->setResizeParam($resizeParam);
                    $task->setResizeType($resizeType);
                    $task->setHash($hash);
                    $entityManager->persist($task);
                    $entityManager->flush();
                    unset($task);
                }
                $out[$key] = 'https://' . $request->server->get('HTTP_HOST') . '/get/' . $hash;
            } else {
                $out[$key] = 'error';
            }
        }

        return $this->json($out);
    }
}
