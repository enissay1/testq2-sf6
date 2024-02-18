<?php

namespace App\Service;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class UpdateArticleFromCsv
{

    public function __construct(private SerializerInterface $serializer, private ParameterBagInterface $params, private EntityManagerInterface $entityManager)
    {
    }
    public function updateDb(string $pathCsv, $flashed = true)
    {

        $csvData = file_get_contents($pathCsv);

        $articles = $this->serializer->deserialize($csvData, Article::class . '[]', 'csv', ["groups" => ["csv"]]);
        // dump($pathCsv, $articles);
        foreach ($articles as $article) {
            if ($article->getReference()) {
                $articledb = $this->entityManager->getRepository(Article::class)
                    ->findOneBy(['reference' => $article->getReference()]);
                if ($articledb) {
                    $articledb->setDesignation($article->getDesignation())->setQuantite($article->getQuantite())->setPrix($article->getPrix());
                    // Persistez l'objet Articledb dans la base de données
                    $this->entityManager->persist($articledb);
                } else {
                    // Persistez l'objet Article dans la base de données
                    $this->entityManager->persist($article);
                }
            }
        }
        // Flush pour sauvegarder les changements
        if ($flashed)
            $this->entityManager->flush();
    }
}
