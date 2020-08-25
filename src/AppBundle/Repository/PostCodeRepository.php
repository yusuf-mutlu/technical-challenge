<?php
namespace AppBundle\Repository;

use AppBundle\Entity\PostCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PostCodeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PostCode::class);
    }

    public function search($term)
    {
        return $this->createQueryBuilder('pc')
            ->select('pc.id','pc.post_code')
            ->where('pc.post_code like :searchTerm')
            ->setParameter('searchTerm', $term."%")
            ->getQuery()->getArrayResult();
    }

    public function getNearestPostCodes($lat, $lon, $radius){

         // Every lat|lon degree° is ~ 111Km
        $angle_radius = $radius / ( 111 * cos( $lat ) );

        $min_lat = $lat - $angle_radius;
        $max_lat = $lat + $angle_radius;
        $min_lon = $lon - $angle_radius;
        $max_lon = $lon + $angle_radius;

        $results = $this->createQueryBuilder('pc')
            ->where("(pc.latitude > $min_lat AND pc.latitude < $max_lat ) AND ( pc.longitude > $min_lon AND pc.longitude < $max_lon )")
            ->getQuery()->getArrayResult();

        $nearest_post_codes = [];

        foreach( $results as $result )
        {

            if( $this->getDistanceBetweenPointsNew( $lat, $lon, $result["latitude"], $result["longitude"], 'Km' ) <= $radius ) {
                $nearest_post_codes[] = $result;
            }
        }
        return $nearest_post_codes;
    }

    protected function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'Mi')
    {
        $theta = $longitude1 - $longitude2;
        $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))+
            (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta))));
        $distance = acos($distance); $distance = rad2deg($distance);
        $distance = $distance * 60 * 1.1515;

        switch($unit)
        {
            case 'Mi': break;
            case 'Km' : $distance = $distance * 1.609344;
        }
        return (round($distance,2));
    }

}