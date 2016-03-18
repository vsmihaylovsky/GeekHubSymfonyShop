<?php
namespace AppBundle\Services;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class MediaHandler
{
    protected $cacheManager;

    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    public function fileUpload($file, $path)
    {
        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $filename = sha1(uniqid(mt_rand(), true)).'.'.$file->guessExtension();
        $file->move($this->getAbsolutePath().'/'.$path, $filename);

        return $filename;
    }

    public function clearCache($file, $path)
    {
        if ($file != null) {
            $absolutePath = $this->getAbsolutePath().'/'.$path.'/'.$file;
            if (file_exists($absolutePath)) {
                unlink($absolutePath);
            }
            $this->cacheManager->remove($path.'/'.$file);
        }
    }

    public function getAbsolutePath()
    {
        return __DIR__.'/../../../web';
    }

}