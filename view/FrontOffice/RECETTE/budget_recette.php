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
    $apiUrl = "https://api.groq.com/openai/v1/chat/completions";
    $model = "llama-3.3-70b-versatile";

    $prompt = "Tu es un chef expert cuisine économique.
Crée 1 ou 2 recettes avec ce budget.
Budget : $budget $devise
Type repas : $type_repas
Personnes : $personnes
Préférences : $preferences

Réponds STRICTEMENT en JSON avec ce format :
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
      \"e�tapes\": [\"...\",\"...\"],
      \"conseil\": \"...\"
    }
  ]
}";

    $postData = [
        "model" => $model,
        "messages" => [
            [
                "role" => "system",
                "content" => "Tu es un assistant culinaire qui répond exclusivement en JSON."
            ],
            [
                "role" => "user",
                "content" => $prompt
            ]
        ],
        "temperature" => 0.7,
        "response_format" => ["type" => "json_object"]
    ];

    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Authorization: Bearer " . $apiKey
        ],
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_SSL_VERIFYPEER => false
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    $responseText = $result['choices'][0]['message']['content'] ?? null;

    if (!$responseText) {
        echo json_encode(["error" => "Erreur API Groq"]);
        exit;
    }

    $json = json_decode($responseText, true);

    if (!$json) {
        echo json_encode(["error" => "Erreur lecture JSON de l'IA"]);
        exit;
    }

    /* ========================= */
    /* IMAGE API PEXELS         */
    /* ========================= */

    $pexelsKey = "";

    if (isset($json["recipes"])) {

        foreach ($json["recipes"] as $k => $recipe) {
            $keyword = $recipe['nom'];

            $searchUrl = "https://api.pexels.com/v1/search?query=" . urlencode($keyword . " food") . "&per_page=1&orientation=landscape";
            $chImg = curl_init($searchUrl);
            curl_setopt($chImg, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($chImg, CURLOPT_HTTPHEADER, ["Authorization: $pexelsKey"]);
            $imgResponse = curl_exec($chImg);
            curl_close($chImg);

            $imgData = json_decode($imgResponse, true);
            $image = $imgData['photos'][0]['src']['large'] ?? "https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=800";
            
            // On injecte l'image dans le JSON pour le frontend
            $json["recipes"][$k]["image"] = $image;
        }
    }

    // renvoyer frontend
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>

