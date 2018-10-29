<?php

define('VILLE_DEPART', 'Rodez');
define('VILLE_ARRIVEE', 'Tarbes');

define('VILLES', [
    'Rodez' => [
        'Albi' => 90,
        'Montpellier' => 170,
        'Montauban' => 150,
        'Clermont-Ferrand' => 250
    ],
    'Montpellier' => [
        'Narbonne' => 120,
        'Marseille' => 200,
        'Rodez' => 170
    ],
    'Albi' => [
        'Rodez' => 90,
        'Toulouse' => 90,
        'Montauban' => 85
    ],
    'Toulouse' => [
        'Albi' => 90,
        'Montauban' => 70,
        'Auch' => 120,
        'Narbonne' => 220,
        'Tarbes' => 230
    ],
    'Montauban' => [
        'Albi' => 85,
        'Toulouse' => 70,
        'Rodez' => 150,
        'Agen' => 80
    ],
    'Agen' => [
        'Montauban' => 80,
        'Auch' => 90
    ],
    'Auch' => [
        'Toulouse' => 120,
        'Agen' => 90,
        'Tarbes' => 120
    ],
    'Clermont-Ferrand' => [
        'Rodez' => 250
    ],
    'Marseille' => [
        'Montpellier' => 200
    ],
    'Narbonne' => [
        'Montpellier' => 120,
        'Toulouse' => 220
    ],
    'Tarbes' => [
        'Auch' => 120,
        'Toulouse' => 230
    ]
]);

/************************************************************************************************
 ****************************** HEURE DE DEBUT DU SCRIPT ****************************************
 ************************************************************************************************/

$temps_millisecond = microtime(true);

/************************************************************************************************
 ****************************** INITIALISATION DES VILLES ***************************************
 ************************************************************************************************/
$villes = [];
foreach (VILLES as $ville => $villes_liees) {
    $villes[$ville] = [
        'distance_du_depart' => 999999999,
        'routes' => $villes_liees,
        'chemin' => []
    ];
}
$villes[VILLE_DEPART]['distance_du_depart'] = 0;


/************************************************************************************************
 ****************** FUNCTION DE COMPARAISON DES DISTANCES ***************************************
 ***********************************************************************************************/
$comparaison = function (array $a, array $b): int {
    if ($a['distance_du_depart'] < $b['distance_du_depart']) {
        return -1;
    }

    return 1;
};

/************************************************************************************************
 ********************************* ALGO DE DIJKSTRA *********************************************
 ***********************************************************************************************/
$chemin = [];

while ($villes) {
    uasort($villes, $comparaison);


    // On récupère la premiere clé car c'est la ville avec la plus faible distance
    $ville_parente = array_keys($villes)[0];
    $ville_parente_routes = $villes[$ville_parente]['routes'];
    $ville_parente_distance = $villes[$ville_parente]['distance_du_depart'];
    $ville_parente_chemin = $villes[$ville_parente]['chemin'];
    $ville_parente_chemin[] = $ville_parente;
    unset($villes[$ville_parente]);


    // On sort de la boucle si aucun chemin ne mène à destination
    if ($ville_parente_distance == 999999999) {
        break;
    }


    // YEAH !! La destination est la ville avec la plus faible distance ! On a donc trouvé le chemin le plus court !
    if ($ville_parente == VILLE_ARRIVEE) {
        $chemin = ['distance' => $ville_parente_distance, 'villes' => $ville_parente_chemin];
        break;
    }


    // On boucle pour check si on est au plus prêt des villes voisines par rapport à la ville de départ.
    // Si oui, on y assigne la liste des villes et la distance du chemin emprunté depuis la ville de départ !
    foreach ($ville_parente_routes as $ville_voisine => $distance) {
        $distance_du_depart = $ville_parente_distance + $distance;

        if (!isset($villes[$ville_voisine]) || $villes[$ville_voisine]['distance_du_depart'] < $distance_du_depart) {
            continue;
        }

        $villes[$ville_voisine]['distance_du_depart'] = $distance_du_depart;
        $villes[$ville_voisine]['chemin'] = $ville_parente_chemin;
    }
}

/************************************************************************************************
 *************** CALCUL EN MILLISECONDE DU TEMPS D'EXECUTION DU SCRIPT **************************
 ************************************************************************************************/
$temps_millisecond = round((microtime(true) - $temps_millisecond) / 1000, 3);

/************************************************************************************************
 ******************************* ON AFFICHE LE RESULTAT *****************************************
 ***********************************************************************************************/
if (!$chemin) {
    echo '<p>Aucun chemin n\'est possible entre la ville de départ et celle d\'arrivée.</p>';
} else {
    echo '<p>Un chemin de ' . $chemin['distance'] . 'km a été trouvé en '.$temps_millisecond.'ms : ' . implode(' > ', $chemin['villes']);
}