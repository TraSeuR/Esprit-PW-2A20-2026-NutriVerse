<?php
//verf 
if (isset($_POST['ingredients']) && isset($_POST['preferences'])) {

    $ingredients = htmlspecialchars($_POST['ingredients']);
    $preferences = htmlspecialchars($_POST['preferences']);

    $apiKey = "";
    $apiUrl = "https://api.groq.com/openai/v1/chat/completions";
    $model = "llama-3.3-70b-versatile";

    $prompt = "Tu es un chef expert nutritionniste.
Crée une recette personnalisée basée sur les ingrédients et préférences fournis.
Réponds UNIQUEMENT avec un objet JSON au format suivant :
{
  \"nom\": \"...\",
  \"categorie\": \"Healthy ou Vegan ou Cuisine Durable\",
  \"description\": \"...\",
  \"temps\": \"...\",
  \"ingredients\": [\"...\", \"...\"],
  \"etapes\": [\"...\", \"...\"],
  \"conseils\": [\"...\", \"...\"],
  \"photo_keyword\": \"un seul mot en anglais décrivant le plat principal\"
}

Ingrédients: $ingredients
Préférences: $preferences";

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
        echo "<p style='color:red;'>Erreur IA (Groq)</p>";
        exit;
    }

    $json = json_decode($responseText, true);

    if (!$json) {
        echo "<p>Erreur lecture JSON de l'IA</p>";
        exit;
    }

    $nom = $json['nom'];
    $categorie = $json['categorie'];
    $description = $json['description'];
    $temps = $json['temps'];
    $keyword = $json['photo_keyword'] ?? 'food';

    $ingredientsList = implode(", ", $json['ingredients']);
    $etapes = implode(" | ", $json['etapes']);
    $conseils = implode(" | ", $json['conseils']);

    $pexelsKey = "";
    $searchUrl = "https://api.pexels.com/v1/search?query=" . urlencode($keyword . " food") . "&per_page=1&orientation=landscape";

    $chImg = curl_init($searchUrl);
    curl_setopt($chImg, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chImg, CURLOPT_HTTPHEADER, ["Authorization: $pexelsKey"]);
    $imgResponse = curl_exec($chImg);
    curl_close($chImg);

    $imgData = json_decode($imgResponse, true);
    $image = $imgData['photos'][0]['src']['large'] ?? "https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=800";

    $link = "ai_recette_details.php?"
        . "nom=" . urlencode($nom)
        . "&categorie=" . urlencode($categorie)
        . "&desc=" . urlencode($description)
        . "&temps=" . urlencode($temps)
        . "&ing=" . urlencode($ingredientsList)
        . "&steps=" . urlencode($etapes)
        . "&tips=" . urlencode($conseils)
        . "&img=" . urlencode($image);

    echo '
    <a href="' . $link . '" class="card-link">
        <div class="card">
            <img src="' . $image . '" alt="' . htmlspecialchars($nom) . '">
            <div class="card-content">
                <div class="tags">
                    <span class="tag">' . htmlspecialchars($categorie) . '</span>
                    <span class="badge">IA Chef</span>
                </div>
                <h3>' . htmlspecialchars($nom) . '</h3>
            </div>
        </div>
    </a>
    ';
}
?>