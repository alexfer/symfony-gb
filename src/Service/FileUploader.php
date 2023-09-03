<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Attach;

class FileUploader
{

    /**
     * 
     * @var string|null
     */
    private ?string $target;

    /**
     * 
     * @param string $targetDirectory
     * @param SluggerInterface $slugger
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
            private string $targetDirectory,
            private SluggerInterface $slugger,
    )
    {
        
    }

    /**
     * 
     * @param UploadedFile $file
     * @return self
     * @throws \Exception
     */
    public function upload(UploadedFile $file): self
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $this->fileName = sprintf("%s-%s.%s", $safeFilename, uniqid(), $file->guessExtension());

        try {
            $target = $file->move($this->getTargetDirectory(), $this->fileName);
        } catch (FileException $e) {
            throw new \Exception($e->getMessage());
        }

        $this->target = $target;
        return $this;
    }

    /**
     * 
     * @return int|null
     */
    private function getSize(): ?int
    {
        return filesize($this->target);
    }

    /**
     * 
     * @return string|null
     */
    private function getMimeType(): ?string
    {
        return mime_content_type($this->target);
    }

    /**
     * 
     * @param Entity $entity
     * @return void
     */
    public function handle(): ?Attach
    {
        $attach = new Attach();
        $attach->setName($this->fileName)
                ->setSize($this->getSize())
                ->setMime($this->getMimeType());

        return $attach;
    }

    /**
     * 
     * @return string
     */
    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
