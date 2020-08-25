<?php

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class PostCodeController extends Controller
{
    /**
     * @Route("/api/search_post_code", name="post_codes_collection")
     * @param Request $request
     * @return JsonResponse
     */
    public function searchAction(Request $request)
    {
        $filter = $request->query->get('filter');

        if($filter)
        {
            $postCodeRepository = $this->getDoctrine()
                ->getManager()
                ->getRepository('AppBundle:PostCode');

            $postCodes = $postCodeRepository->search( ucwords($filter) );

            return new JsonResponse($postCodes,200);
        } else return new JsonResponse("Please enter a filter word.",403);
    }

    /**
     * @Route("/api/nearest", name="nearest_post_codes")
     * @param Request $request
     * @return JsonResponse
     */
    public function findNearestAction(Request $request)
    {
        $lat = $request->query->get('lat');
        $lon = $request->query->get('lon');
        $radius = $request->query->get('radius');

        $postCodeRepository = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:PostCode');
        $nearestPostCodes = $postCodeRepository->getNearestPostCodes($lat, $lon, $radius );

        return new JsonResponse($nearestPostCodes,200);
    }

}
