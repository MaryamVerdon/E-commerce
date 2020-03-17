<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Commande;
use App\Entity\Article;
use App\Entity\Client;
use App\Entity\Taille;
use App\Entity\Adresse;
use App\Entity\QuantiteTaille;
use App\Entity\LigneDeCommande;
use App\Form\ArticleType;
use App\Form\CommandeType;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/article", name="admin_article")
     */
    public function indexArticle()
    {
        return $this->render('admin/article/index.html.twig', [
        ]);
    }
   
    /**
     * @Route("/admin/article/get", name="admin_article_get")
     */
    public function getArticle(Request $request){
        $parameters = $request->query->all();

        $page = 1;
        if(isset($parameters['page'])){
            $page = $parameters['page'];
        }

        $nbMaxParPage = 20;
        if(isset($parameters['nb_max_par_page'])){
            $nbMaxParPage = $parameters['nb_max_par_page'];
        }

        $paginator = $this->getDoctrine()
        ->getRepository(Article::class)
        ->findByParametersPagine($page, $nbMaxParPage, $parameters);
        //dd($commande);

        $articles = [];
        // dd($paginator->getIterator()->getArrayCopy());
        foreach($paginator->getIterator()->getArrayCopy() as $article){
            $articles[] = [
                "id" => $article->getId(),
                "libelle" => $article->getLibelle(),
                "image" => $article->getImage(),
                "description" => $article->getDescription(),
                "prix_u" => $article->getPrixU()
            ];
        }
        $result = [
            "articles" => $articles,
            "pagination" => [
                "page" => $page,
                "nbPages" => (ceil(count($paginator) / $nbMaxParPage))
            ]
        ];
        return new JsonResponse($result);
    }
    /**
     * @Route("/admin/article/{id}", name="admin_article_show", requirements={"id"="\d+"})
     */
    public function show($id)
    {
        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->find($id);
        return $this->render('admin/article/show.html.twig', [
            'controller_name' => 'ArticleController',
            'article' => $article
        ]);
    }
    /**
     * @Route("/admin/client/{id}", name="admin_client_show", requirements={"id"="\d+"})
     */
    public function showclient($id)
    {
        $client = $this->getDoctrine()
            ->getRepository(Client::class)
            ->find($id);
        $commandes = $this->getDoctrine()
            ->getRepository(Commande::class)
            ->findLastsByClient($client);
        $adresses = $this->getDoctrine()
            ->getRepository(Adresse::class)
            ->findByClient($client);
        //dd($commandes);
        //dd($adresses);
        
        return $this->render('admin/client/show.html.twig', [
            'controller_name' => 'ClientController',
            'client' => $client,
            'commandes' => $commandes,
            'adresse' => $adresses
        ]);
    }
    
    /**
     * @Route("/admin/commande", name="admin_commande")
     */
    public function indexCommande(){
        /*
        $commande = $this->getDoctrine()
        ->getRepository(Commande::class)
        ->findAll();
        */
        //dd($commande);
        return $this->render('admin/commande/index.html.twig',[
            //'commandes' => $commande
        ]);
    }

    /**
     * @Route("/admin/commande/get", name="admin_commande_get")
     */
    public function getCommande(Request $request){ 
        $parameters = $request->query->all();

        $page = 1;
        if(isset($parameters['page'])){
            $page = $parameters['page'];
        }

        $nbMaxParPage = 20;
        if(isset($parameters['nb_max_par_page'])){
            $nbMaxParPage = $parameters['nb_max_par_page'];
        }

        $paginator = $this->getDoctrine()
            ->getRepository(Commande::class)
            ->findByParametersPagine($page, $nbMaxParPage, $parameters);
        //dd($commande);

        
        $commandes = [];
        // dd($paginator->getIterator()->getArrayCopy());
        foreach($paginator->getIterator()->getArrayCopy() as $commande){
            $commandes[] = [
                "id" => $commande->getId(),
                "date" => $commande->getDate(),
                "client" => ($commande->getClient()->getNom() . " " . $commande->getClient()->getPrenom()),
                "mode_paiement" => $commande->getModePaiement()->getLibelle(),
                "statut_commande" => $commande->getStatutCommande()->getLibelle(),
                "adresse" => ($commande->getAdresse()->getAdresse() . " " . $commande->getAdresse()->getCp() . " " . $commande->getAdresse()->getVille()),
                "nb_articles" => $commande->getNbArticles(),
                "total" => $commande->getPrixTotalWithShipping()
            ];
        }
        $result = [
            "commandes" => $commandes,
            "pagination" => [
                "page" => $page,
                "nbPages" => (ceil(count($paginator) / $nbMaxParPage))
            ]
        ];
        // dd(json_encode($result));
        return new JsonResponse($result);
    }

    /**
     * @Route("/admin/client/commande/{id}", name="admin_client_commande", requirements={"id"="\d+"})
     */
    public function indexCommandeClient($id){
        /*
        $commande = $this->getDoctrine()
        ->getRepository(Commande::class)
        ->findByclient($id);
        */
        //dd($commande);
        return $this->render('admin/client/commande.html.twig',[
            'clientId' => $id
        ]);
    }

    /**
     * @Route("/admin/commande/show/{id}", name="admin_commande_show", requirements={"id"="\d+"})
     */
    public function showCommandeClient($id){
        $commande = $this->getDoctrine()
        ->getRepository(Commande::class)
        ->find($id);
        //dd($commande);
        return $this->render('admin/commande/show.html.twig',[
            'controller_name'=> 'clientController',
            'commande' => $commande
        ]);
    }
    /**
     * @Route("/admin/article/new", name="admin_article_new")
     */
    public function new(Request $request, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            foreach($article->getQuantiteTailles() as $quantite_taille){
                $quantite_taille->setArticle($article);
            }
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('admin_article_show', ['id' => $article->getId()]);
        }

        
        return $this->render('admin/article/new.html.twig', [
            'controller_name' => 'ArticleController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/article/{id}/edit", name="admin_article_edit")
     */
    public function edit($id, Request $request, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (null === $article = $entityManager->getRepository(Article::class)->find($id)) {
            throw $this->createNotFoundException('Aucun article pour l \'id '.$id);
        }

        $originalQuantiteTailles = new ArrayCollection();

        foreach($article->getQuantiteTailles() as $quantite_taille){
            $originalQuantiteTailles->add($quantite_taille);
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            foreach($originalQuantiteTailles as $quantite_taille){
                if(false === $article->getQuantiteTailles()->contains($quantite_taille)){
                    // Remove quantitetaille
                    $entityManager->remove($quantite_taille);
                }
            }
            foreach($article->getQuantiteTailles() as $quantite_taille){
                $quantite_taille->setArticle($article);
            }
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('admin_article_show', ['id' => $article->getId()]);
        }

        
        return $this->render('admin/article/new.html.twig', [
            'controller_name' => 'ArticleController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/article/{id}/remove", name="admin_article_remove")
     */
    public function remove($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em = $this->getDoctrine()
            ->getManager();
        $article = $em->getRepository(Article::class)
            ->find($id);
        $em->remove($article);
        $em->flush();
        
        return $this->redirectToRoute('admin_article');
    }

    /**
     * @Route("admin/client", name="admin_client")
     */
    public function indexClient()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
            //dd($client);
    
        return $this->render('admin/client/index.html.twig',[
        ]);
    }

    /**
     * @Route("admin/client/get", name="admin_client_get")
     */
    public function getClient(Request $request)
    { 
        $parameters = $request->query->all();


        $page = 1;
        if(isset($parameters['page'])){
            $page = $parameters['page'];
        }

        $nbMaxParPage = 20;
        if(isset($parameters['nb_max_par_page'])){
            $nbMaxParPage = $parameters['nb_max_par_page'];
        }

        $paginator = $this->getDoctrine()
            ->getRepository(Client::class)
            ->findByParametersPagine($page, $nbMaxParPage, $parameters);
        //dd($commande);

        
        $clients = [];
        // dd($paginator->getIterator()->getArrayCopy());
        foreach($paginator->getIterator()->getArrayCopy() as $client){
            $clients[] = [
                "id" => $client->getId(),
                "nom" => $client->getNom(),
                "prenom" => $client->getPrenom(),
                "email" => $client->getEmail()
            ];
        }
        $result = [
            "clients" => $clients,
            "pagination" => [
                "page" => $page,
                "nbPages" => (ceil(count($paginator) / $nbMaxParPage))
            ]
        ];
        // dd(json_encode($result));
        return new JsonResponse($result);
    }

    /**
     * @Route("/admin/commande/{id}/edit", name="admin_commande_edit")
     */
    public function commande_edit($id, Request $request, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (null === $commande = $entityManager->getRepository(Commande::class)->find($id)) {
            throw $this->createNotFoundException('Aucune commande pour l \'id '.$id);
        }

        $originalLignesDeCommande = new ArrayCollection();

        foreach($commande->getLignesDeCommande() as $ligne_de_commande){
            $originalLignesDeCommande->add($ligne_de_commande);
        }

        $form = $this->createForm(CommandeType::class, $commande);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            foreach($originalLignesDeCommande as $ligne_de_commande){
                if(false === $commande->getLignesDeCommande()->contains($ligne_de_commande)){
                    // Remove quantitetaille
                    $entityManager->remove($ligne_de_commande);
                }
            }
            foreach($commande->getLignesDeCommande() as $ligne_de_commande){
                $ligne_de_commande->setCommande($commande);
            }
            $entityManager->persist($commande);
            $entityManager->flush();

            return $this->redirectToRoute('admin_commande_show', ['id' => $commande->getId()]);
        }

        
        return $this->render('admin/commande/edit.html.twig', [
            'controller_name' => 'ArticleController',
            'form' => $form->createView(),

        ]);
    }

     /**
     * @Route("/admin", name="admin")
     */
    public function ten_last_commande()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $commandes = $this->getDoctrine()
            ->getRepository(Commande::class)
            ->findTenLastCommandes();
            
        $clients = $this->getDoctrine()
            ->getRepository(Client::class)
            ->findTenLastClients();

        $articles = $this->getDoctrine()
        ->getRepository(Article::class)
        ->findTenLastArticles();
        
        //dd($clients);
        //dd($commandes);
        //dd($articles);
        return $this->render('admin/index.html.twig',[
            'controller_name' => 'AdminController',
            'commandes' => $commandes,
            'clients' => $clients,
            'articles' => $articles
        ]);
    }
     
    /**
     * @Route("/admin", name="admin_client")
     */
    /*
     public function ten_last_client()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $clients = $this->getDoctrine()
            ->getRepository(Client::class)
            ->findTenLastClient();
        
        //dd($clients);
    
        return $this->render('admin/index.html.twig',[
            'controller_name' => 'AdminController',
            'clients' => $clients
        ]);
    }*/
}
