<?php
include 'KmlTemplate.php';
include 'radius.php';

function doKML($state_name)
{
    $locations = json_decode(file_get_contents('json/' . $state_name . '.json'));

    $kml = new KmlTemplate(
        $state_name
    );

    $kml->dontIncludeMarkers()
        ->radiusColor('ff4f0e88', '1f4f0e88');

    foreach ($locations as $location) {
        $kml->addPlacemarks(
            $location->name,
            radius([$location->longitude, $location->latitude], 1),
            [
                'Phone' => $location->Phone,
                'Street' => $location->Street,
                'City' => $location->City,
                'State' => $location->State,
                'ZIP' => $location->ZIP,
                'AttWeb' => $location->AttWeb,
                'GMap' => $location->GMap
            ],
            [$location->longitude, $location->latitude]
        );
    }

    $kml->save();
}

$state = $_GET['state'] ??
    die('bad bad bad');

doKML($state);
die($_GET['state']);
