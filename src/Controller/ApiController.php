<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiController extends AbstractController
{
    #[Route('/api/new_article', name: 'app_api_article_new', methods: ['POST'])]
    public function createPost(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator): Response 
    {
        $body = $request->getContent();
        $article = $serializer->deserialize($body, Article::class, 'json');
        $user = $this->getUser();
        $article->setAuteur($user);

        $errors = $validator->validate($article);
        if(count($errors)>0){
            $errorMessages = [];
        }

        $em->persist($article);
        $em->flush();

        return $this->json([
            'status' => 201,
            'message' => 'la ressource a été créée',
            'data' => $article
        ],201,[],['group' => 'article']);
    }


    #[Route('/api/articles', name: 'app_api_article_index')]
    public function index(ArticleRepository $articleRepository): Response 
        {
            $article = $articleRepository->findAll();
            return $this->json([
                'status' => 200,
                'message' => 'la ressource a été trouvée',
                'data' => $article
            ],201,[],['group' => 'article']);
        }
            
}


    