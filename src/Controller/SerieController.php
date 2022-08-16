<?php
namespace App\Controller;
use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/series", name="serie_")
 */
class SerieController extends AbstractController
{
    /**
     * @Route("", name="list")
     */
    public function list(SerieRepository $serieRepository): Response
    {
        // récupération de tous les éléments dans la BDD (toutes les lignes)
        //$series = $serieRepository->findAll();


        //récupération d'éléments dans la bdd avec filtre existant
        //$series = $serieRepository->findBy([], ['popularity' => 'DESC', 'vote' => 'DESC'], 50);


        //récupération avec une requete personnalisée
        $series = $serieRepository->findBestSeries();

        return $this->render('serie/list.html.twig', [
            "series" => $series
        ]);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(int $id, SerieRepository $serieRepository): Response
    {
        $serie = $serieRepository->find($id);

        return $this->render('serie/details.html.twig',[ "serie" => $serie ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        //création d'une instance de serie
        $serie = new Serie();
        $serie->setDateCreated(new \DateTime());//attribut nécessaire pour envoi bdd mais retiré du form
        $serieForm = $this -> createForm(SerieType::class, $serie);

        dump($serie);//permet de verifier si un objet est hydraté
        $serieForm -> handleRequest($request);
        dump($serie);//on voit a present que mon objet serie à des arguments grace à handleRequest

        if($serieForm->isSubmitted()){
            $entityManager->persist($serie);
            $entityManager->flush();

            //on crée un message flash pour signaler à l'utilisateur
            $this->addFlash('success', 'Serie added! Good job.');

            // on va à présent rediriger pour cela on utilise return
            return $this->redirectToRoute('serie_details',['id' => $serie->getId()]);
        }

        //passage à twig pour déclencher l'affichage du formulaire
        return $this->render('serie/create.html.twig', [
            'serieForm' => $serieForm ->createView()
        ]);
    }

    /**
     * @Route("/demo", name="em-demo")
     */
    public function demo(EntityManagerInterface $entityManager): Response
    {
        // créé une instance de mon entité
        $serie = new Serie();
        // hydrate toutes les propriétés
        $serie->setName('pif');
        $serie->setBackdrop('dafsd');
        $serie->setPoster('dafsd');
        $serie->setDateCreated(new \DateTime());
        $serie->setFirstAirDate(new \DateTime("-1 year"));
        $serie->setLastAirDate(new \DateTime("-6 month"));
        $serie->setGenres('drama');
        $serie->setOverview('bla bla bla');
        $serie->setPopularity(123.00);
        $serie->setVote(8.2);
        $serie->setStatus('Canceled');
        $serie->setTmdbId(329432);
        dump($serie);
        // $entityManager = $this->getDoctrine()->getManager()
        $entityManager->persist($serie);
        $entityManager->flush();
        dump($serie);
        //$entityManager->remove($serie);
        $serie->setGenres('comedy');
        $entityManager->flush();
        return $this->render('serie/create.html.twig');

    }
}