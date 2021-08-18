<?php
include 'KmlTemplate.php';
include 'radius.php';

function doKML($state_name)
{
    include $state_name . '.php';

    $locations = json_decode(file_get_contents('json/' . $state_name . '.json'));

    $kml = new KmlTemplate(
        $state_name
    );

    foreach ($locations as $location) {
        $kml->addPlacemark(
            $location->name,
            radius([$location->longitude, $location->latitude], 2),
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
