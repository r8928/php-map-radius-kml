<?php
function radius($center, $radius_in_miles, $numberOfSegments = 38)
{
    $radius_in_meters = $radius_in_miles * 1000 / 0.62137119223733392;

    $flatCoordinates = [];
    for ($i = 0; $i < $numberOfSegments; $i++) {
        $bearing = 2 * M_PI * $i / $numberOfSegments;
        $flatCoordinates[] = offset($center, $radius_in_meters, $bearing);
    }
    $flatCoordinates[] = $flatCoordinates[0];


    $coords = '';
    foreach ($flatCoordinates as $v) {
        $coords .= "$v[0],$v[1],0.0\n";
    }
    return $coords;
}

function offset($center, $distance, $bearing)
{
    $lon1 = deg2rad($center[0]);
    $lat1 = deg2rad($center[1]);
    $dByR = $distance / 6378137; // convert dist to angular distance in radians

    $lat = asin(
        sin($lat1) * cos($dByR) +
            cos($lat1) * sin($dByR) * cos($bearing)
    );
    $lon = $lon1 + atan2(
        sin($bearing) * sin($dByR) * cos($lat1),
        cos($dByR) - sin($lat1) * sin($lat)
    );
    $lon = fmod(
        $lon + 3 * M_PI,
        2 * M_PI
    ) - M_PI;
    return [rad2deg($lon), rad2deg($lat)];
}
