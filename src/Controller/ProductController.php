<?php

namespace App\Controller;

use App\Elasticsearch\QueryBuilder\ProduitQueryBuilder;
use App\Form\SearchForm;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use App\Repository\ProduitRepository;
use App\Data\SortData;




class ProductController extends AbstractController
{
    /**
     * @Route(path="/categorie", name="categorie")
     */
    public function categorie(Environment $twig)
    {

        $content = $twig->render('product/categorie.html.twig');
        return new Response($content);
    }

    /**
     * @Route(path="/lister/{categorie}", name="lister")
     */
    public function liste_produit(ProduitRepository $produitRepository, $categorie, Request $request, ProduitQueryBuilder $produitQueryBuilder)
    {
        $data = new SortData();
        $form = $this->createForm(SearchForm::class, $data);
        $form->handleRequest($request);


        $produits = $produitQueryBuilder->getQuerySearch($categorie, $data);
        // $produits = $this->maCategorie($produitRepository, $id);
//        $produits = $produitRepository->findBySearch( $categorie, $data);



        return $this->render('product/articles.html.twig', [
            'products' => $produits, 'cat' => $categorie,
            'form' => $form->createView()
        ]);
    }


}
