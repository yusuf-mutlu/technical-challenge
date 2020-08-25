<?php

namespace AppBundle\Helper;

/**
 * A cut-down version of League/Geotools/Coordinate/Coordinate
 * The ellipsoid is always Airy, as it is dealing with OSGB.
 *
 * Note: this is a polar lat/long coordinate. We are likely to need a
 * Cartesian lat/long coordinate too, for the datum conversion process.
 */

class Coordinate implements CoordinateInterface
{
    /**
     * The latitude of the coordinate.
     *
     * @var double
     */

    public $latitude;

    /**
     * The longitude of the coordinate.
     *
     * @var double
     */

    public $longitude;

    /**
     * Set the latitude and the longitude of the coordinates.
     *
     * @param array $coordinates The latitude and longitude coordinates.
     * @throws InvalidArgumentException
     */

    public function __construct($coordinates)
    {
        if (is_array($coordinates) && count($coordinates) == 2) {
            list($lat, $long) = array_values($coordinates);
            $this->setLatitude($lat);
            $this->setLongitude($long);
        } else {
            throw new InvalidArgumentException(
                'Coordinate should be a an array; %s passed instead', gettype($coordinates)
            );
        }
    }

    /**
     * {@inheritDoc}
     */

    public function normalizeLatitude($latitude)
    {
        return (double) max(-90, min(90, $latitude));
    }

    /**
     * {@inheritDoc}
     */

    public function normalizeLongitude($longitude)
    {
        if (180 === $longitude % 360) {
            return 180.0;
        }

        $mod = fmod($longitude, 360);
        $longitude = $mod < -180 ? $mod + 360 : ($mod > 180 ? $mod - 360 : $mod);

        return (double) $longitude;
    }

    /**
     * {@inheritDoc}
     */

    public function setLatitude($latitude)
    {
        $this->latitude = $this->normalizeLatitude($latitude);
    }

    /**
     * {@inheritDoc}
     */

    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * {@inheritDoc}
     */

    public function setLongitude($longitude)
    {
        $this->longitude = $this->normalizeLongitude($longitude);
    }

    /**
     * {@inheritDoc}
     */

    public function getLongitude()
    {
        return $this->longitude;
    }
}

