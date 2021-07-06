<?php


namespace App\Controller;


use App\Entity\Achat;
use App\Entity\AchatDetaille;
use App\Entity\Produit;
use App\Entity\User;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Exception;


class PaiementController extends AbstractController
{
    /**
     * @Route(path="/bag/add/{id}", name="bag_add")
     */
    public function add ($id, SessionInterface $session, $redisUrl) {

        try {
            $redis = RedisAdapter::createConnection($redisUrl);
        } catch (Exception $e) {
            return $this->redirectToRoute("categorie");
        }

        $user = $this->getUser();
        $redisKey = "pannier".$user->getId();

        $redis->lPush($redisKey, $id);
        $redis->expire($redisKey, 300);
        return $this->redirectToRoute("bag");
    }
    /**
     * @Route(path="/bag/remove/{id}", name="bag_remove")
     */
    public function remove($id, SessionInterface $session, $redisUrl) {

        try {
            $redis = RedisAdapter::createConnection($redisUrl);
        } catch (Exception $e) {
            return $this->redirectToRoute("categorie");
        }

        $user = $this->getUser();
        $redisKey = "pannier".$user->getId();
        $redis->lRem($redisKey, $id, 1);

        return $this->redirectToRoute("bag");
    }

    /**
     * @Route(path="/bag/information/save", name="bag_add_save")
     */
    public function save ($redisUrl, EntityManagerInterface $em) {
        try {
            $redis = RedisAdapter::createConnection($redisUrl);
        } catch (Exception $e) {
            return $this->redirectToRoute("bag");
        }

        $user = $this->getUser();
        $user = $em->getRepository(User::class)->find($user->getId());
        $redisKey = "pannier".$user->getId();
        $panier = $redis->lrange( "$redisKey", 0, -1);

        $ids = [];
        $panierInfo = [];
        foreach ($panier as $item){
            if(!in_array($item, $ids)){
                $panierInfo[$item] = [
                    "id" => $item,
                    "quantity"=>1,
                ];
                array_push($ids, $item);
            }else{
                $panierInfo[$item]["quantity"] = $panierInfo[$item]["quantity"] + 1;
            }
        }
        foreach ($panierInfo as $info){

            $id = $info["id"];
            $quantity = $info["quantity"];

            $produit = $em->getRepository(Produit::class)->find($id);

            $montant = $produit->getPrice() * $quantity;

            $achatDetaille = new AchatDetaille();
            $achatDetaille->setIdProduitFk($produit);
            $achatDetaille->setMontant($montant);

            $achat = new Achat();
            $achat->setIdUserFk($user);
            $achat->setDate(new \DateTime());
            $achat->setMontant($quantity);
            $achat->addAchatDetaille($achatDetaille);

            $em->persist($achat);
            $em->persist($achatDetaille);
        }
        $em->flush();

        $redis->del($redisKey);

        return $this->redirectToRoute("myprofil");
    }

    /**
     * @Route(path="/bag", name="bag")
     */
    public function bag(Environment $twig, SessionInterface $session, ProduitRepository $productRepository, $redisUrl)
    {
        try {
            $redis = RedisAdapter::createConnection($redisUrl);
        } catch (Exception $e) {
            return $this->redirectToRoute("categorie");
        }
        $user = $this->getUser();
        $redisKey = "pannier".$user->getId();
        $panier = $redis->lrange( "$redisKey", 0, -1);

        $allPanier = [];
        $panierWithData =[];
        foreach ($panier as $id => $value) {
            if(!in_array($value, $allPanier)){
                $panierWithData[$value] = [
                    'product'=> $productRepository->find($value),
                    'quantity'=> 1
                ];
                array_push($allPanier, $value);
            }else{
                $panierWithData[$value]["quantity"] = $panierWithData[$value]["quantity"] + 1;
            }
        }
        $total=0;
        foreach ($panierWithData as $item){
            $totalItem = $item['product']->getPrice() *$item['quantity'];
            $total += $totalItem;
        }
        $content = $twig->render('bag.html.twig',[
            'items'=>$panierWithData,
            'total'=>$total
        ]);
        return new Response($content);
    }

    /**
     * @Route(path="/bag/removeAll/{id}", name="bag_remove_all")
     */
    public function removeAll($id, SessionInterface $session, $redisUrl){
        try {
            $redis = RedisAdapter::createConnection($redisUrl);
        } catch (Exception $e) {
            return $this->redirectToRoute("categorie");
        }
        $user = $this->getUser();
        $redisKey = "pannier".$user->getId();

        $redis->lRem($redisKey, $id, 0);
        return $this->redirectToRoute("bag");
    }

    /**
     * @Route(path="/paiement", name="paiement")
     */
    public function paiement(Environment $twig,ProduitRepository $productRepository,  $redisUrl)
    {

        try {
            $redis = RedisAdapter::createConnection($redisUrl);
        } catch (Exception $e) {
            return $this->redirectToRoute("categorie");
        }
        $user = $this->getUser();
        $redisKey = "pannier".$user->getId();
        $panier = $redis->lrange( "$redisKey", 0, -1);

        $allPanier = [];
        $panierWithData =[];
        foreach ($panier as $id => $value) {
            if(!in_array($value, $allPanier)){
                $panierWithData[$value] = [
                    'product'=> $productRepository->find($value),
                    'quantity'=> 1
                ];
                array_push($allPanier, $value);
            }else{
                $panierWithData[$value]["quantity"] = $panierWithData[$value]["quantity"] + 1;
            }
        }
        $total=0;
        foreach ($panierWithData as $item){
            $totalItem = $item['product']->getPrice() *$item['quantity'];
            $total += $totalItem;
        }

        $content = $twig->render('commande/paiement.html.twig',[
            'total'=>$total
        ]);
        echo("Paiement accepté");
        return new Response($content);

    }
}





