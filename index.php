<?php 
    require 'vendor/autoload.php';
    require 'config.php';
    use GuzzleHttp\Client;

    $weight = null;

    // On vérifie si on a bien reçu le Authentification token 
    if(!empty($_GET['code'])){

        // On setup le client (on ajoute un certificat car bug ..)
        $client = new Client([
            'timeout'  => 2.0,
            'verify' => __DIR__ . './cacert.pem'
        ]);

        // On récupère l'acces token
        try {
            $response = $client->request('POST', 'https://wbsapi.withings.net/v2/oauth2', [
                'form_params' => [
                    'code' => $_GET['code'],
                    'client_id' => CLIENT_ID,
                    'client_secret' => SECRET,
                    'action' => 'requesttoken',
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => 'http://localhost/withings_test'

                ]
            ]);

            
            if(property_exists(json_decode($response->getBody()), 'error')) {
                dd(json_decode($response->getBody())->error);
            }

            $access_token = json_decode($response->getBody())->body->access_token;                        
        } catch (\Throwable $th) {
            dd($th);
        }

        // On récupère les données
        try {
            $response = $client->request('POST', 'https://wbsapi.withings.net/measure', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $access_token
                ],
                'form_params' => [
                    'action' => 'getmeas',
                    'meastype' => 1,
                    'category' => 1,
                ]
            ]);

            if(property_exists(json_decode($response->getBody()), 'error')) {
                dd(json_decode($response->getBody())->error);
            }

            // On récupère la dernière mesure effectuée (on suppose qu'il s'agit de la première)
            $response = json_decode($response->getBody());
            $last_measure = $response->body->measuregrps[0]->measures;

            // On calcule le poids
            $weight = $last_measure[0]->value * pow(10,$last_measure[0]->unit);
            
        } catch (\Throwable $th){
            dd($th);
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Test Withings</title>
</head>
<body>
    <h1>Récuperer son dernier poid enregistré</h1>
    <a href="http://account.withings.com/oauth2_user/authorize2?response_type=code&client_id=<?= CLIENT_ID ?>&state=a_random_value&scope=user.metrics&redirect_uri=<?= urlencode('http://localhost/withings_test')?>&mode=demo">se connecter</a>
    
    <!-- Si le poids n'est pas null donc il a été initialisé et on affiche -->
    <?php 
        if($weight){
            echo ("<p>Votre poid est de : " . $weight . "kg </p>");
        }
    ?>
</body>
</html>
