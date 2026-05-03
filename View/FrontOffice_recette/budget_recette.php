<?php

header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // recevoir données frontend
    $budget      = $_POST['budget'];
    $devise      = $_POST['devise'];
    $type_repas  = $_POST['type_repas'];
    $preferences = $_POST['preferences'];
    $personnes   = $_POST['personnes'];

    // sécurité simple
    $budget      = htmlspecialchars($budget);
    $devise      = htmlspecialchars($devise);
    $type_repas  = htmlspecialchars($type_repas);
    $preferences = htmlspecialchars($preferences);
    $personnes   = htmlspecialchars($personnes);

    $apiKey = "";

    // 3 modèles Gemini
    $modelsToTry = [
        "gemini-1.5-flash",
        "gemini-2.0-flash",
        "gemini-2.5-flash"
    ];

    // prompt simple
    $prompt = "Tu es un chef expert cuisine économique.

Crée 1 ou 2 recettes avec ce budget.

Budget : $budget $devise
Type repas : $type_repas
Personnes : $personnes
Préférences : $preferences

Réponds STRICTEMENT en JSON :

{
  \"recipes\": [
    {
      \"nom\": \"...\",
      \"categorie\": \"Healthy ou Vegan ou autre\",
      \"description\": \"...\",
      \"temps\": \"25 min\",
      \"budget_total\": 8,
      \"ingredients\": [
        {\"nom\":\"Tomate\",\"quantite\":\"500g\",\"prix\":1}
      ],
      \"etapes\": [\"...\",\"...\"],
      \"conseil\": \"...\"
    }
  ]
}";

    $responseText = null;

    // tester plusieurs modèles
    foreach ($modelsToTry as $model) {

        $url = "https://generativelanguage.googleapis.com/v1beta/models/"
            . rawurlencode($model)
            . ":generateContent?key=" . $apiKey;

        $postData = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

        $response = curl_exec($ch);

        curl_close($ch);

        $result = json_decode($response, true);

        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $responseText = $result['candidates'][0]['content']['parts'][0]['text'];
            break;
        }
    }

    // erreur API
    if (!$responseText) {
        echo json_encode([
            "error" => "Erreur API Gemini"
        ]);
        exit;
    }

    // nettoyer markdown
    $responseText = trim($responseText);
    $responseText = preg_replace('/```json|```/', '', $responseText);

    $json = json_decode($responseText, true);

    // erreur JSON
    if (!$json) {
        echo json_encode([
            "error" => "Erreur lecture JSON"
        ]);
        exit;
    }

    
    /* IMAGE API PEXELS         */
   

    $pexelsKey = "";

    if (isset($json["recipes"])) {

        foreach ($json["recipes"] as $k => $recipe) {

            $keyword = $recipe["nom"] . " food";

            $searchUrl = "https://api.pexels.com/v1/search?query="
                . urlencode($keyword)
                . "&per_page=1&orientation=landscape";

            $ch = curl_init($searchUrl);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: " . $pexelsKey
            ]);

            $imgResponse = curl_exec($ch);

            curl_close($ch);

            $imgData = json_decode($imgResponse, true);

            if (isset($imgData['photos'][0]['src']['large'])) {
                $json["recipes"][$k]["image"] = $imgData['photos'][0]['src']['large'];
            } else {
                $json["recipes"][$k]["image"] = "default.jpg";
            }
        }
    }

    // renvoyer frontend
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>