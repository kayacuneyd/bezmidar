<?php

function getCityCoordinates($city)
{
    $defaults = [
        'berlin' => ['lat' => 52.52, 'lng' => 13.405],
        'münchen' => ['lat' => 48.1351, 'lng' => 11.5820],
        'munchen' => ['lat' => 48.1351, 'lng' => 11.5820],
        'hamburg' => ['lat' => 53.5511, 'lng' => 9.9937],
        'frankfurt' => ['lat' => 50.1109, 'lng' => 8.6821],
        'köln' => ['lat' => 50.9375, 'lng' => 6.9603],
        'koln' => ['lat' => 50.9375, 'lng' => 6.9603],
        'stuttgart' => ['lat' => 48.7758, 'lng' => 9.1829],
        'düsseldorf' => ['lat' => 51.2277, 'lng' => 6.7735],
        'dusseldorf' => ['lat' => 51.2277, 'lng' => 6.7735],
        'dortmund' => ['lat' => 51.5136, 'lng' => 7.4653],
        'essen' => ['lat' => 51.4556, 'lng' => 7.0116],
        'bremen' => ['lat' => 53.0793, 'lng' => 8.8017],
        'leipzig' => ['lat' => 51.3397, 'lng' => 12.3731],
        'nürnberg' => ['lat' => 49.4521, 'lng' => 11.0767],
        'nurnberg' => ['lat' => 49.4521, 'lng' => 11.0767],
        'hannover' => ['lat' => 52.3759, 'lng' => 9.7320],
        'bonn' => ['lat' => 50.7374, 'lng' => 7.0982],
        'mannheim' => ['lat' => 49.4875, 'lng' => 8.4660],
        'karlsruhe' => ['lat' => 49.0069, 'lng' => 8.4037],
        'aachen' => ['lat' => 50.7753, 'lng' => 6.0839]
    ];

    $key = strtolower(trim($city ?? ''));

    if (isset($defaults[$key])) {
        return $defaults[$key];
    }

    return ['lat' => 52.52, 'lng' => 13.405];
}
