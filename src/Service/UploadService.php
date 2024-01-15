<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * Permet d'effectuer un upload
 */
class UploadService
{
    public function upload(UploadedFile $file): string
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

        // Retourne le nom du fichier
        return $newFileName;
    }
}