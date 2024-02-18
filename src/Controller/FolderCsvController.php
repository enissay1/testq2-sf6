<?php

namespace App\Controller;

use App\Entity\Article;
use App\Service\UpdateArticleFromCsv;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class FolderCsvController extends AbstractController
{
    #[Route('/folder/csv', name: 'app_folder_csv')]
    public function index(UpdateArticleFromCsv $updateArticleFromCsv, ParameterBagInterface $params, EntityManagerInterface $entityManager): Response
    {

        $folderPath = $params->get('kernel.project_dir') . '/public/csvfolder';

        $finder = new Finder();
        $finder->in($folderPath)->files()->name('*.csv');
        // Vérifier s'il y a au moins un fichier CSV
        if ($finder->hasResults()) {
            // Traiter les fichiers CSV
            foreach ($finder as $file) {
                //dump($file->getRealPath());
                $updateArticleFromCsv->updateDb($file->getRealPath());
            }
        }
        // Flush pour sauvegarder les changements
        //$entityManager->flush();

        return $this->json(('ok'));
    }
}
