<?php


namespace App\Elasticsearch\QueryBuilder;


use App\Data\SortData;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchQuery;
use Elastica\Query\MultiMatch;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

class ProduitQueryBuilder
{
    private PaginatedFinderInterface $productFinder;

    public function __construct(PaginatedFinderInterface $productFinder){
        $this->productFinder = $productFinder;
    }


    public function getQuerySearch($categorie,SortData $search){
        $boolQuery = new BoolQuery();
        $mainQuery = new Query();

        $fieldQuery = new MatchQuery();
        $fieldQuery->setFieldQuery('categorie', $categorie);
        $boolQuery->addMust($fieldQuery);

        if (!empty($search->q)) {
            $titleQuery = new Query\MatchPhrasePrefix();
            $titleQuery->setParam('title', $search->q);
            $boolQuery->addMust($titleQuery);
        }

        if (!empty($search->min) || !empty($search->max)) {
            $min = !empty($search->min) ? $search->min : 0;
            $max = !empty($search->max) ? $search->max : 10000;
            $rangeQuery = new Query\Range();
            $rangeQuery->addField('price',  ['gte' => $min, 'lte' => $max], );
            $boolQuery->addFilter($rangeQuery);
        }

        if (!empty($search->revendeurs)) {
            $subBoolQuery = new BoolQuery();
            foreach ($search->revendeurs as $revendeur){
                $matchQuery = new MatchQuery();
                $matchQuery->setParam('revendeur', $revendeur->getEntreprise());
                $subBoolQuery->addShould($matchQuery);
            }
            $boolQuery->addMust($subBoolQuery);
        }

        $mainQuery->setQuery($boolQuery);
        return $this->productFinder->find($mainQuery, 1000);
    }
}
