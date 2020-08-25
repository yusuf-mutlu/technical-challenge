<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\UniqueConstraint;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostCodeRepository")
 * @ORM\Table(name="post_codes", indexes={@Index(name="search_idx", columns={"latitude", "longitude"})}, uniqueConstraints={@UniqueConstraint(name="user_unique",columns={"post_code"})})
 * @ORM\HasLifecycleCallbacks()
 */
class PostCode
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $post_code;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8)
     */
    private $latitude;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8)
     */
    private $longitude;

    /**
     * @return mixed
     */
    public function getPostCode()
    {
        return $this->post_code;
    }

    /**
     * @param mixed $post_code
     */
    public function setPostCode($post_code): void
    {
        $this->post_code = $post_code;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude): void
    {
        $this->longitude = $longitude;
    }


}