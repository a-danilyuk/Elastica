<?php
namespace Elastica6\Test\Query;

use Elastica6\Document;
use Elastica6\Query;
use Elastica6\Query\GeoDistance;
use Elastica6\Test\Base as BaseTest;

class GeoDistanceTest extends BaseTest
{
    /**
     * @group functional
     */
    public function testGeoPoint()
    {
        $index = $this->_createIndex();

        $type = $index->getType('test');

        // Set mapping
        $type->setMapping(['point' => ['type' => 'geo_point']]);

        // Add doc 1
        $doc1 = new Document(1);
        $doc1->addGeoPoint('point', 17, 19);
        $type->addDocument($doc1);

        // Add doc 2
        $doc2 = new Document(2);
        $doc2->addGeoPoint('point', 30, 40);
        $type->addDocument($doc2);

        $index->forcemerge();
        $index->refresh();

        // Only one point should be in radius
        $geoQuery = new GeoDistance('point', ['lat' => 30, 'lon' => 40], '1km');
        $query = new Query();
        $query->setPostFilter($geoQuery);

        $this->assertEquals(1, $type->count($query));

        // Both points should be inside
        $geoQuery = new GeoDistance('point', ['lat' => 30, 'lon' => 40], '40000km');
        $query = new Query();
        $query->setPostFilter($geoQuery);

        $this->assertEquals(2, $type->count($query));
    }

    /**
     * @group unit
     */
    public function testConstructLatlon()
    {
        $key = 'location';
        $location = [
            'lat' => 48.86,
            'lon' => 2.35,
        ];
        $distance = '10km';

        $query = new GeoDistance($key, $location, $distance);

        $expected = [
            'geo_distance' => [
                $key => $location,
                'distance' => $distance,
            ],
        ];

        $data = $query->toArray();

        $this->assertEquals($expected, $data);
    }

    /**
     * @group unit
     */
    public function testConstructGeohash()
    {
        $key = 'location';
        $location = 'u09tvqx';
        $distance = '10km';

        $query = new GeoDistance($key, $location, $distance);

        $expected = [
            'geo_distance' => [
                $key => $location,
                'distance' => $distance,
            ],
        ];

        $data = $query->toArray();

        $this->assertEquals($expected, $data);
    }

    /**
     * @group unit
     */
    public function testSetDistanceType()
    {
        $query = new GeoDistance('location', ['lat' => 48.86, 'lon' => 2.35], '10km');
        $distanceType = GeoDistance::DISTANCE_TYPE_ARC;
        $query->setDistanceType($distanceType);

        $data = $query->toArray();

        $this->assertEquals($distanceType, $data['geo_distance']['distance_type']);
    }
}
