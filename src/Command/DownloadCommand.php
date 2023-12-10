<?php

namespace App\Command;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'app:download',
    description: 'System command download file',
)]
class DownloadCommand extends Command
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
            ->where('task.cached_name IS NULL')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        if (!empty($get)) {
            foreach($get as $get_value) {
                if (!empty($get_value->getLocation())) {
                    $header = get_headers($get_value->getLocation(), true);
                    if (strstr($header[0], 200)) {
                        $ext = $this->mime[$header['Content-Type']] ?? false;
                        if (!empty($ext)) {
                            $newName = time() + mt_rand(1111111111, 9999999999) . $ext;
                            $downloadLocation = $this->kernel->getProjectDir() . '/public/tmp/' . $newName;
                            if (copy($get_value->getLocation(), $downloadLocation)) {
                                $get_value->setCachedName($newName);
                                $this->entityManager->persist($get_value);
                                $this->entityManager->flush();
                            }
                        }
                    }
                }
            }
        }
        return Command::SUCCESS;
    }
}
