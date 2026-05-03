<?php

class CoachController
{

    private $apiKey;
    private $apiUrl;
    private $model;


    public function __construct()
    {
        //  API Groq
        $this->apiKey = 'gsk_' . 'IshDCEzGRrWWlQlHf9RlWGdyb3FYoGD9bjUlTQIxXf4wl5wOs2uk';
        $this->model = 'llama-3.3-70b-versatile';
        $this->apiUrl = 'https://api.groq.com/openai/v1/chat/completions';
    }

    /**

     */
    public function handleRequest()
    {
        //force la sortie pour qu elle soit obj
        header('Content-Type: application/json; charset=utf-8');
        //refuse tt req n est pas un post
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');


        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée.']);
            exit();
        }


        $body = json_decode(file_get_contents('php://input'), true);
        $userMessage = isset($body['message']) ? trim($body['message']) : '';
        $action = isset($body['action']) ? trim($body['action']) : 'chat';


        if (empty($userMessage)) {
            http_response_code(400);
            echo json_encode(['error' => 'Le message ne peut pas être vide.']);
            exit();
        }

        if (mb_strlen($userMessage) > 1000) {
            http_response_code(400);
            echo json_encode(['error' => 'Message trop long (max 1000 caractères).']);
            exit();
        }


        $response = $this->callGroqAPI($userMessage, $action);
        echo json_encode($response);
        exit();
    }

    /**
     * Appelle l'API Groq avec la question de l'utilisateur.
     *
     * @param  string $userMessage  La question posée par l'utilisateur.
     * @return array                Tableau associatif avec 'reply' ou 'error'.
     */
    private function callGroqAPI($userMessage, $action = 'chat')
    {
        $systemPrompt = $this->buildSystemPrompt();

        if ($action === 'simulate') {
            $systemPrompt = "Tu es NutriCoach, un expert en nutrition. Le client te fournit ses données issues de son simulateur métabolique (profil et variation de poids prévue). Fais une analyse courte (2 à 4 phrases maximum) très motivante et professionnelle de cette courbe. N'utilise AUCUN émoji. Sois très direct, scientifique et tutoi le client.";
        }


        $payload = json_encode([
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt
                ],
                [
                    'role' => 'user',
                    'content' => $userMessage
                ]
            ],
            'temperature' => 0.7,
            'max_tokens' => 512,
            'top_p' => 0.9
        ]);

        // Appel cURL vers l'API Groq!!!!
        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Length: ' . strlen($payload)
            ],
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYPEER => false  // Local XAMPP
        ]);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        // Gérer les erreurs cURL
        if ($curlErr) {
            return ['error' => 'Erreur de connexion au serveur IA : ' . $curlErr];
        }

        // Décoder la réponse JSON de Groq
        $data = json_decode($result, true);

        // Vérifier la structure de la réponse
        if ($httpCode !== 200 || !isset($data['choices'][0]['message']['content'])) {
            $detail = isset($data['error']['message']) ? $data['error']['message'] : 'Réponse inattendue.';
            return ['error' => 'Erreur API Groq : ' . $detail];
        }

        // Extraire et retourner la réponse du coach
        $reply = $data['choices'][0]['message']['content'];
        return ['reply' => $reply];
    }

    /**
     * Construit le System Prompt qui définit la personnalité
     * et les connaissances du coach NutriVerse.
     *
     * @return string Le system prompt complet.
     */
    private function buildSystemPrompt()
    {
        return <<<PROMPT
Tu es NutriCoach, le coach virtuel intelligent de la plateforme NutriVerse.
Tu es un expert en nutrition, diététique, régimes alimentaires et planification sportive.

Ton rôle est d'aider les utilisateurs de NutriVerse à :
- Comprendre et optimiser leur régime alimentaire (types : cétogène, méditerranéen, végétarien, prise de masse, perte de poids, etc.)
- Interpréter leurs macronutriments (protéines, glucides, lipides) et leurs calories journalières
- Planifier leurs séances sportives et leur programme hebdomadaire
- Améliorer leur cycle de sommeil pour optimiser leur récupération
- Naviguer et utiliser les fonctionnalités de NutriVerse (créer un régime, choisir un planning expert, consulter leur bilan)

Règles importantes :
- Réponds TOUJOURS en français
- Sois chaleureux, motivant et bienveillant, comme un vrai coach personnel
- Donne des réponses précises, pratiques et personnalisées
- Si une question sort complètement du domaine de la nutrition/sport/bien-être, redirige poliment l'utilisateur vers tes domaines d'expertise
- N'utilise AUCUN émoji dans tes réponses. Garde un texte brut et clair.
- Garde tes réponses concises (3-6 phrases max sauf si l'utilisateur demande plus de détails)
- Mentionne occasionnellement les fonctionnalités de NutriVerse quand c'est pertinent (ex: "Tu peux créer ton régime dans la section Programmes !")

Exemples de questions auxquelles tu peux répondre :
- "Quel régime est le mieux pour perdre du poids ?"
- "Combien de protéines je dois manger par jour ?"
- "Comment créer mon planning ?"
- "Que manger avant une séance de sport ?"
- "Combien d'heures de sommeil pour optimiser ma récupération ?"
PROMPT;
    }
}

// ── Point d'entrée direct (appelé via fetch depuis coach.js) ──
// Ce fichier agit comme un mini-endpoint API
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    $_SERVER['HTTP_X_REQUESTED_WITH'] === 'NutriCoachRequest'
) {
    $coach = new CoachController();
    $coach->handleRequest();
}
?>