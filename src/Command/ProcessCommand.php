<?php

namespace App\Command;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;


#[AsCommand(
    name: 'app:process',
    description: '',
)]
class ProcessCommand extends Command
{
    private array $mime = [
        'image/jpeg' => '.jpeg',
        'image/png' => '.png',
        'image/gif' => '.gif',
        'image/webp' => '.webp',
        'image/tiff' => '.tiff',
        'image/svg+xml' => '.svg'
    ];
    public function __construct(
        private EntityManagerInterface $entityManager,
        private KernelInterface $kernel
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $get = $this->entityManager->getRepository(Task::class)->createQueryBuilder("task")
            ->where('task.output_name IS NULL')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        foreach ($get as $item) {
            $location = $this->kernel->getProjectDir() . '/public/tmp/' . $item->getCachedName();
            $out = $this->kernel->getProjectDir() . '/public/out/' . $item->getHash();


            $imagine = new Imagine();
            $image = $imagine->open($location);

            if (!empty($item->getConvertFormat())) {
                $outExt = $out . '.' . $item->getConvertFormat();
                $image->save($outExt);
                $item->setOutputName($item->getHash() . '.' . $item->getConvertFormat());
                $this->entityManager->persist($item);
                $this->entityManager->flush();
            }

            if (!empty($item->getResizeType())) {
                if (!empty($item->getConvertFormat())) {
                    $location = $out . '.' . $item->getConvertFormat();
                    $image = $imagine->open($location);
                    $out = $out . '.' . $item->getConvertFormat();
                } else {
                    $out = $out . $this->mime[image_type_to_mime_type(exif_imagetype($location))];
                }

                if ($item->getResizeType() == 'width') {
                    $resizeParam = [
                        0 => (int)($item->getResizeParam()),
                        1 => 0
                    ];
                    $size = $image->getSize();
                    $c = $size->getWidth()/$size->getHeight();
                    $resizeParam[1] = ceil($resizeParam[0] / $c);
                    $image->resize(new Box($resizeParam[0], $resizeParam[1]))->save($out);
                } elseif ($item->getResizeType() == 'height') {
                    $resizeParam = [
                        0 => 0,
                        1 => (int)($item->getResizeParam())
                    ];
                    $size = $image->getSize();
                    $c = $size->getHeight()/$size->getWidth();
                    $resizeParam[0] = ceil($resizeParam[1] / $c);
                    $image->resize(new Box($resizeParam[0], $resizeParam[1]))->save($out);
                } elseif ($item->getResizeType() == 'crop') {
                    $resizeParamTmp = explode('*', $item->getResizeParam());
                    if ($resizeParamTmp[0] > $resizeParamTmp[1]) {
                        $resizeParam = [
                            0 => $resizeParamTmp[0],
                            1 => 0
                        ];
                        $size = $image->getSize();
                        $c = $size->getWidth()/$size->getHeight();
                        $resizeParam[1] = ceil($resizeParam[0] / $c);
                    } else {
                        $resizeParam = [
                            0 => 0,
                            1 => $resizeParamTmp[1]
                        ];
                        $size = $image->getSize();
                        $c = $size->getHeight()/$size->getWidth();
                        $resizeParam[0] = ceil($resizeParam[1] / $c);
                    }
                    $image->resize(new Box($resizeParam[0], $resizeParam[1]))
                        ->crop(new Point(0, 0), new Box($resizeParamTmp[0], $resizeParamTmp[1]))
                        ->save($out);
                }
            }
        }
        return Command::SUCCESS;
    }
}
