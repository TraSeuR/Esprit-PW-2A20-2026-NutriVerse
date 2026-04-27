<?php

if (isset($_POST['ingredients']) && isset($_POST['preferences'])) {

    $ingredients = htmlspecialchars($_POST['ingredients']);
    $preferences = htmlspecialchars($_POST['preferences']);

    $apiKey = ""; 

    // 3 modeles en cas ou
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

        if (curl_errno($ch)) {
            echo "Erreur CURL : " . curl_error($ch);
        }

        curl_close($ch);

        $result = json_decode($response, true);

        
       

        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $responseText = $result['candidates'][0]['content']['parts'][0]['text'];
            break;
        }
    }

    //  si api mamachech
    if (!$responseText) {
        echo "<p style='color:red;'>Erreur API (quota ou clé)</p>";
        exit;
    }

    
    $responseText = trim($responseText);
    $responseText = preg_replace('/```json|```/', '', $responseText);


    $json = json_decode($responseText, true);

    if (!$json) {
        echo "<p>Erreur lecture JSON</p>";
        exit;
    }

    //  recup donne
    $nom = $json['nom'];
    $categorie = $json['categorie'];
    $description = $json['description'];
    $temps = $json['temps'];
    $ingredientsList = implode(", ", $json['ingredients']);
    $etapes = implode(" | ", $json['etapes']);
    $conseils = implode(" | ", $json['conseils']);

    $image = "https://source.unsplash.com/300x200/?food," . urlencode($nom);

    
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
    <a href="'.$link.'" class="card-link">

    <div class="card">

        <img src="'.$image.'" alt="">

        <div class="card-content">

            <div class="tags">
                <span class="tag">'.$categorie.'</span>
            </div>

            <h3>'.$nom.'</h3>

            

        </div>

    </div>

    </a>
    ';
}
?>