<?php

namespace App\Controller;

use App\Entity\FilmsSeries;
use App\Repository\FilmsSeriesRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/create', name: 'app_create_filmSerie')]
    public function addFilmSerie(Request $request, ManagerRegistry $doctrine): Response
    {
        $formData = $request->getContent();
        $data = json_decode($formData, true);

        $filmSerie = new FilmsSeries();
        $filmSerie->setName($data['name']);
        $filmSerie->setSynopsis($data['synopsis']);
        $filmSerie->setType($data['type']);
        $filmSerie->setCreatedDate(new \DateTime());

        $doctrine = $doctrine->getManager();
        $doctrine->persist($filmSerie);
        $doctrine->flush();

        return $this->json([$filmSerie,'status' => 'film or serie create successfully', 'message' => 'Le film ou serie a été créé avec succes'], Response::HTTP_CREATED, );
    }

    #[Route('/getAll', name: 'app_getAll_listing')]
    public function getAll(FilmsSeriesRepository $filmsSeriesRepository): Response
    {
        $listing = [];
        foreach ($filmsSeriesRepository->findAll() as $key => $value) {
            $listing[] = $value->getInfos();
        }

        if ($listing) {
            return $this->json([$listing, 'status' => 'success', 'message' => 'liste recupérer avec succes'], Response::HTTP_OK, );
        } else {
            return $this->json(['status' => 'error', 'message' => 'liste vide'], Response::HTTP_NOT_FOUND, );
        }
    }

    #[Route('/get/{id_item}', name: 'app_listing')]
    public function getItem($id_item, FilmsSeriesRepository $filmsSeriesRepository): Response
    {
        $listing = $filmsSeriesRepository->findOneBy(['id' => $id_item])->getInfos();
        
        if ($listing) {
            return $this->json([$listing, 'status' => 'success', 'message' => 'Item recupérer avec succes'], Response::HTTP_OK, );
        } else {
            return $this->json([$listing, 'status' => 'error', 'message' => 'L\'item n\'éxiste pas dans la base de donnée'], Response::HTTP_NOT_FOUND, );
        }
    }
}
