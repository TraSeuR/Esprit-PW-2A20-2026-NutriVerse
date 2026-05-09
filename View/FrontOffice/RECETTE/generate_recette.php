<?php
//verf 
if (isset($_POST['ingredients']) && isset($_POST['preferences'])) {

    $ingredients = htmlspecialchars($_POST['ingredients']);
    $preferences = htmlspecialchars($_POST['preferences']);

    $apiKey = "";

    $modelsToTry = [
        "gemini-1.5-flash",
        "gemini-2.0-flash",
        "gemini-2.5-flash"
    ];

    $prompt = "Tu es un chef expert nutritionniste.

Crée une recette personnalisée avec ce format JSON STRICT :

{
  \"nom\": \"...\",
  \"categorie\": \"Healthy ou Vegan ou Cuisine Durable\",
  \"description\": \"...\",
  \"temps\": \"...\",
  \"ingredients\": [\"...\", \"...\"],
  \"etapes\": [\"...\", \"...\"],
  \"conseils\": [\"...\", \"...\"]
}

Ingrédients: $ingredients
Préférences: $preferences

IMPORTANT: réponds uniquement en JSON.";

    $responseText = null;

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

    if (!$responseText) {
        echo "<p style='color:red;'>Erreur API</p>";
        exit;
    }

    $responseText = trim($responseText);
    $responseText = preg_replace('/```json|```/', '', $responseText);

    preg_match('/\{.*\}/s', $responseText, $match);

    $responseClean = $match[0] ?? '';

    $json = json_decode($responseClean, true);

    if (!$json) {
        echo "<p>Erreur lecture JSON</p>";
        exit;
    }

    $nom = $json['nom'];
    $categorie = $json['categorie'];
    $description = $json['description'];
    $temps = $json['temps'];

    $ingredientsList = implode(", ", $json['ingredients']);
    $etapes = implode(" | ", $json['etapes']);
    $conseils = implode(" | ", $json['conseils']);

    /* IMAGE */
    $pexelsKey = "";

    $keyword = $nom;

    $searchUrl = "https://api.pexels.com/v1/search?query="
        . urlencode($keyword . " food")
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
        $image = $imgData['photos'][0]['src']['large'];
    } else {
        $image = "default.jpg";
    }

    /* lien details */
    $link = "recette_ai_details.php?"
        . "nom=" . urlencode($nom)
        . "&categorie=" . urlencode($categorie)
        . "&description=" . urlencode($description)
        . "&temps=" . urlencode($temps)
        . "&ingredients=" . urlencode($ingredientsList)
        . "&etapes=" . urlencode($etapes)
        . "&conseils=" . urlencode($conseils)
        . "&image=" . urlencode($image);

    echo '
    <a href="' . $link . '" class="card-link">

        <div class="card">

            <img src="' . $image . '" alt="">

            <div class="card-content">

                <div class="tags">
                    <span class="tag">' . $categorie . '</span>
                </div>

                <h3>' . $nom . '</h3>

            </div>

        </div>

    </a>
    ';
}
?>