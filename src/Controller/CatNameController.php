<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CatNameController extends AbstractController
{

    //region Cat Data Choice

    #[Route('/cat', name: 'app_cat_name')]
    public function index(Request $request)
    : Response
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.thecatapi.com/v1/breeds');

        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            throw new \Exception('Failed to fetch cat data from API');
        }

        $catData = $response->toArray();

        if ($request->isMethod('POST')) {
            $selectedCatId = $request->request->get('catDropdown');
            $selectedCat = null;

            foreach ($catData as $cat) {
                if ($cat['id'] === $selectedCatId) {
                    $selectedCat = $cat;
                    break;
                }
            }

            if ($selectedCat) {
                return $this->redirectToRoute('app_cat_data', ['id' => $selectedCat['reference_image_id']]);
            }
        }

        return $this->render('cat_name/index.html.twig', [
            'cat_data' => $catData,
            'selected_cat' => null,
        ]);
    }
    //endregion

    //region Cat Data by ID

    #[Route('/cat/{id}', name: 'app_cat_data')]
    public function cat_data(string $id)
    : Response
    {


        $client = HttpClient::create();
        $response = $client->request(
            'GET',
            'https://api.thecatapi.com/v1/images/' . $id
        );

        $statusCode = $response->getStatusCode();

        if ($statusCode !== 200) {
            throw new \Exception('Failed to fetch cat data from API');
        }
        $content = $response->toArray();

        return $this->render('cat_name/cat_data.html.twig', [
            'cat_data' => $content,
        ]);
    }
    //endregion

}
