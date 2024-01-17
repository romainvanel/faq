<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * Permet d'effectuer un upload
 */
class UploadService
{
    public function upload(UploadedFile $file, string $oldFile = null): string
    {
        // Récupère le nom du fichier
        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // Sluggify le nom
        $slugger = new AsciiSlugger();
        $safeFileName = $slugger->slug($originalFileName);
        // Donne nom unique au fichier
        $newFileName = $safeFileName.'-'.uniqid().'.'.$file->guessExtension();

        // Envoie le fichier vers le dossier imgs/avatar - upload
        $file->move('avatars', $newFileName);

        // Supprime un ancien fichier
        // Instancie le composant Symfony Filesystem
        $filesystem = new Filesystem();
        // Si l'argument $oldFile est différent de null et que le fichier existe
        if ($oldFile !== null && $filesystem->exists($oldFile)) {
            // On supprime le nouveau nom du fichier
           $filesystem->remove($oldFile);
        }
        // Retourne le nom du fichier
        return $newFileName;
    }
}