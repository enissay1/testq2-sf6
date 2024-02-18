<?php

namespace App\Controller;

use App\Service\UpdateArticleFromCsv;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class FolderCsvController extends AbstractController
{
    #[Route('/folder/listcsv', name: 'app_import_listcsv')]
    public function index(ParameterBagInterface $params): Response
    {

        $folderPath = $params->get('kernel.project_dir') . '/public/csvfolder';
        //$files = scandir($folderPath);
        // Utiliser Finder pour trouver les fichiers CSV dans le dossier
        $finder = new Finder();
        $finder->in($folderPath)->files()->name('*.csv');

        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                $baseName[] = ['name' => $file->getBasename('.csv'), 'date' => date('Y-m-d H:i:s', $file->getMTime())];
            }
        }
        // dd($baseName);
        return $this->renderForm('import/index.html.twig', [
            'files' => $baseName,
        ]);
    }
    #[Route('/folder/csv', name: 'app_folder_csv')]
    public function importFolder(UpdateArticleFromCsv $updateArticleFromCsv, ParameterBagInterface $params, EntityManagerInterface $entityManager): Response
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

        return $this->json(('All files are uploaded'));
    }
    #[Route('/folder/csv/{nameFile}', name: 'app_file_csv')]
    public function importFile(string $nameFile, UpdateArticleFromCsv $updateArticleFromCsv, ParameterBagInterface $params): Response
    {
        // dd($nameFile);
        $folderPath = $params->get('kernel.project_dir') . '/public/csvfolder';

        $finder = new Finder();
        $finder->in($folderPath)->files()->name('*.csv');
        // Vérifier s'il y a au moins un fichier CSV
        if ($finder->hasResults()) {

            // Traiter les fichiers CSV
            foreach ($finder as $file) {
                $baseName = $file->getBasename('.csv');
                //dd($baseName);
                //dump($file->getRealPath());
                if ($baseName == $nameFile) {
                    $updateArticleFromCsv->updateDb($file->getRealPath());
                }
            }
        }
        // Flush pour sauvegarder les changements
        //$entityManager->flush();
        if ($updateArticleFromCsv->getFlashed()) {
            $msg = "Article update successfully";
        } else $msg = "Article update failed";

        return $this->json(($msg));
    }
}
