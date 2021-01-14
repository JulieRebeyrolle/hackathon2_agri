<?php

namespace App\Controller;

use App\Entity\Filter;
use App\Form\FilterType;
use App\Repository\BuyerRepository;
use App\Repository\CityRepository;
use App\Repository\FarmerRepository;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route ("/")
 */
class MapController extends AbstractController
{
    /**
     * @Route ("/", name="index_map", methods={"GET"})
     * @param Request $request
     * @param CityRepository $cityRepository
     * @param FarmerRepository $farmerRepository
     * @param BuyerRepository $buyerRepository
     * @return Response
     */
    public function indexMap(
        Request $request,
        CityRepository $cityRepository,
        FarmerRepository $farmerRepository,
        BuyerRepository $buyerRepository
    ): Response {
        $filter = new Filter();
        $form = $this->createForm(FilterType::class, $filter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filter = $form->getData();
            $farmers = $farmerRepository->getFilteredFarmers($filter);
            $buyers = $buyerRepository->getBuyers();

            return $this->render('map/map.html.twig', [
                'cities' => $farmers,
                'buyers' => $buyers,
                'form' => $form->createView()
            ]);
        }

        $cities = $farmerRepository->getFarmerWithData();
        $buyers = $buyerRepository->getBuyers();
        return $this->render('map/map.html.twig', [
            'cities' => $cities,
            'buyers' => $buyers,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/category/{category}", name="map_by_cereal")
     * @param TransactionRepository $transactionRepository
     * @param $category
     * @return Response
     */
    public function mapShowFarmersByCereal(TransactionRepository $transactionRepository, string $category): Response
    {
        $cities = $transactionRepository->getFarmersByProduct($category);
        return $this->render('map/map.html.twig', [
            'cities' => $cities,
        ]);
    }

    /**
     * @Route("/size/{size}", name="map_by_size")
     * @param FarmerRepository $farmerRepository
     * @param int $size
     * @return Response
     */
    public function mapShowFarmersByFarmSize(FarmerRepository $farmerRepository, int $size): Response
    {
        $cities = $farmerRepository->getFarmersByFarmSize($size);
        return $this->render('map/map.html.twig', [
            'cities' => $cities,
        ]);
    }































































































































































































































































































































































































    /**
     * @Route("scriptpirequejamais", name="nepasregarder")
     * @param FarmerRepository $farmerRepository
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function scriptpourriparcequeyapasgroupconcatdansDoctrine(FarmerRepository $farmerRepository, EntityManagerInterface $entityManager)
    {

        $comments = [
            'Presque parfait !!!!!!',
            'Comme d\'habitude ..impeccable ! Service, rapidité, efficacité . Bien à vous et à la prochaine campagne !!!',
            'Une équipe à nôtre écoute et prête à trouver le meilleur compromis pour nous satisfaire date de paiement respectée',
            'Parcours de la signature du contrat aux enlèvements et aux règlements très bien rodé. Bravo !',
            'Bonne prestation sur les produits classiques, amélioration possible et souhaitée sur les cultures moins courantes.','La seule chose qui leurs manquent : faire monter les cours le reste est parfait ',
            'RAS. je suis très comptant d\'avoir le mème contact avec qui échanger sur les ventes ou autre. Ne changé pas ce principe.',
            'personne facile a joindre bonne reactivité paiement dans les temps',
            'Je sais plus quoi écrire tellement vous êtes presque parfait ',
            'rien a signaler juste un peu de retard dans l enlevement de la marchandise',
            'Tout s\'est passé comme prévu',
            'pour la deuxième fois que je travail avec le comparateur toujours aussi satisfait du service et de l\'amabilité des personne',
            'RAS',
            'Bonjour votre entreprise est réactif à l\'écoute et évolutif il faut continuer dans la perspective de nous accompagner. Très bien',
        ];
      
        $farmers = $farmerRepository->findAll();
        $categories = [];
        foreach ($farmers as $farmer) {
            $products = [];
            $quantity = 0;
            $transactions = $farmer->getTransactions();
            foreach ($transactions as $transaction) {
                $label = $transaction->getProduct()->getCategory();
                if ($label === 'ble') {
                    $products[] = 'Blé';
                } elseif ($label === 'mais') {
                    $products[] = 'Maïs';
                } else {
                    $products[] = ucfirst($transaction->getProduct()->getCategory());
                }
                $quantity += $transaction->getQuantity();
                $farmer->setComment($comments[array_rand($comments)]);
                $farmer->setXpRate(rand(3,5));
            }

            $farmer->setCategory(implode(' ', array_unique($products)))
                ->setQuantitySold($quantity);
        }

        $entityManager->flush();
        return $this->redirectToRoute('index_map');

    }
}
