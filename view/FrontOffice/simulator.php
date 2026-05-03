<?php
require_once __DIR__ . '/../../controller/RegimeController.php';

$id_regime = isset($_GET['id_regime']) ? $_GET['id_regime'] : null;

if (!$id_regime) {
    echo "ID du régime manquant.";
    exit();
}

$regimeCtrl = new RegimeController();
$regime = $regimeCtrl->getRegime($id_regime);

if (!$regime) {
    echo "Régime introuvable.";
    exit();
}

$calories_regime = $regime->getCalorieJour();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulateur d'Évolution - <?php echo htmlspecialchars($regime->getNom()); ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="assets/front.css">
    <link rel="stylesheet" href="../assets/style.css?v=1.3">

    <!-- Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="assets/technical_front.css">
</head>

<body
    style="background: linear-gradient(135deg, #59b84d 0%, #a8dba0 45%, #ffffff 100%); min-height: 100vh; font-family: 'Poppins', sans-serif;">

    <?php include 'global_header.php'; ?>

    <!-- HERO VERT -->
    <section class="recipe-header fade-up">
        <div class="icons">
            <span>🥑</span>
            <span>🥕</span>
            <span>🥦</span>
            <span>🍎</span>
            <span>🍇</span>
            <span>🥬</span>
            <span>🍅</span>
            <span>🍌</span>
            <span>🍓</span>
            <span>🥒</span>
            <span>🌽</span>
            <span>🍍</span>
            <span>🥭</span>
            <span>🍉</span>
            <span>🥔</span>
        </div>
        <div class="header-content">
            <h1 style="margin-bottom: 0;">NutriVerse</h1>
            <h2 style="font-size: 2rem; opacity: 0.9; font-weight: 700; margin: 10px 0; color: white;">Prédiction IA
            </h2>
        </div>
    </section>

    <div class="simulator-container fade-up">
        <div class="simulator-header">
            <span
                style="text-transform: uppercase; letter-spacing: 2px; font-weight: 800; font-size: 0.8rem; color: var(--primary-dark);">IA
                PRÉDICTIVE</span>
            <h1 style="margin-bottom: 10px; font-weight: 800;">Simulateur d'Évolution Métabolique</h1>
            <p>Découvrez l'impact du régime <strong>"<?php echo htmlspecialchars($regime->getNom()); ?>"</strong>
                (<?php echo $calories_regime; ?> kcal/jour) sur votre corps au cours des 4 prochaines semaines.</p>
        </div>

        <div class="simulator-grid">
            <!-- Formulaire -->
            <div>
                <div class="form-group">
                    <label>Votre Sexe</label>
                    <select id="sim-sexe">
                        <option value="homme">Homme</option>
                        <option value="femme">Femme</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Âge (années)</label>
                    <input type="number" id="sim-age" value="25" min="15" max="100">
                </div>
                <div class="form-group">
                    <label>Poids Actuel (kg)</label>
                    <input type="number" id="sim-poids" value="70" min="40" max="200" step="0.1">
                </div>
                <div class="form-group">
                    <label>Taille (cm)</label>
                    <input type="number" id="sim-taille" value="175" min="100" max="250">
                </div>
                <button class="btn-simulate" onclick="runSimulation()">LANCER LA SIMULATION</button>

                <div class="result-box" id="result-box">
                    <h4 style="margin-bottom: 10px;">Résultat Prédictif</h4>
                    <p id="result-text" style="font-size: 0.9rem;"></p>
                </div>
            </div>

            <!-- Graphique et IA -->
            <div class="chart-container" style="display: flex; flex-direction: column; gap: 20px;">
                <div id="ai-loading"
                    style="display:none; text-align:center; padding: 30px; font-weight:800; color:var(--primary-dark); background: rgba(89, 184, 77, 0.05); border-radius: 12px; border: 2px dashed var(--primary);">
                    <span
                        style="font-size: 2rem; display:block; margin-bottom:10px; animation: bounce 1s infinite;">🤖</span>
                    L'IA de NutriVerse analyse vos données en temps réel...
                </div>

                <div class="result-box" id="ai-result-box"
                    style="display:none; background: linear-gradient(135deg, rgba(255,255,255,1) 0%, rgba(245,245,245,1) 100%); border-left: 5px solid #000; margin: 0; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <h4 style="margin-bottom: 15px; font-weight: 800; font-size: 1.2rem;">🤖 Rapport du Coach IA</h4>
                    <p id="ai-result-text" style="font-size: 0.95rem; line-height: 1.8; color: #333;"></p>
                </div>

                <canvas id="evolutionChart" style="display:none;"></canvas>
            </div>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="summary.php?id_regime=<?php echo $id_regime; ?>" class="btn-primary"
                style="display: inline-block; padding: 12px 30px;">Voir le Rapport Détaillé</a>
        </div>
    </div>

    <script>
        const caloriesRegime = <?php echo $calories_regime; ?>;
        let evolutionChart = null;

        function runSimulation() {
            const sexe = document.getElementById('sim-sexe').value;
            const age = parseFloat(document.getElementById('sim-age').value);
            const poids = parseFloat(document.getElementById('sim-poids').value);
            const taille = parseFloat(document.getElementById('sim-taille').value);

            if (!age || !poids || !taille) {
                alert("Veuillez remplir tous les champs.");
                return;
            }

            // Calcul du Métabolisme de Base (Harris-Benedict)
            let bmr = 0;
            if (sexe === 'homme') {
                bmr = 88.362 + (13.397 * poids) + (4.799 * taille) - (5.677 * age);
            } else {
                bmr = 447.593 + (9.247 * poids) + (3.098 * taille) - (4.330 * age);
            }

            // Dépense Énergétique Journalière (Activité modérée : BMR * 1.55)
            const tdee = bmr * 1.55;

            // Déficit ou Surplus journalier
            const dailyDiff = caloriesRegime - tdee;

            // 1 kg de masse corporelle correspond à environ 7700 kcal
            const weeklyWeightDiff = (dailyDiff * 7) / 7700;

            // Projections sur 4 semaines
            const labels = ['Semaine 0 (Aujourd\'hui)', 'Semaine 1', 'Semaine 2', 'Semaine 3', 'Semaine 4'];
            const dataPoids = [
                poids,
                poids + (weeklyWeightDiff * 1),
                poids + (weeklyWeightDiff * 2),
                poids + (weeklyWeightDiff * 3),
                poids + (weeklyWeightDiff * 4)
            ];

            const finalWeight = dataPoids[4].toFixed(1);
            let message = "";
            if (weeklyWeightDiff < -0.1) {
                message = `Excellente nouvelle ! Avec ce régime, vous perdrez environ <strong>${Math.abs((poids - finalWeight)).toFixed(1)} kg</strong> en 4 semaines pour atteindre <strong>${finalWeight} kg</strong>.`;
            } else if (weeklyWeightDiff > 0.1) {
                message = `Avec ce régime de prise de masse, vous gagnerez environ <strong>${Math.abs((finalWeight - poids)).toFixed(1)} kg</strong> en 4 semaines pour atteindre <strong>${finalWeight} kg</strong>.`;
            } else {
                message = `Ce régime correspond parfaitement à votre métabolisme de maintien. Votre poids restera stable autour de <strong>${finalWeight} kg</strong>.`;
            }

            document.getElementById('result-box').style.display = 'block';
            document.getElementById('result-text').innerHTML = message;

            // --- Masquer le graphique et préparer le chargement IA ---
            document.getElementById('evolutionChart').style.display = 'none';
            document.getElementById('ai-result-box').style.display = 'none';
            document.getElementById('ai-loading').style.display = 'block';

            if (evolutionChart) {
                evolutionChart.destroy();
                evolutionChart = null;
            }

            const aiMessage = `Je suis un ${sexe} de ${age} ans, je mesure ${taille} cm et je pèse ${poids} kg. Avec mon régime de ${caloriesRegime} kcal, la prédiction dit que je vais atteindre ${finalWeight} kg dans 4 semaines. Donne-moi ton analyse d'expert sur ce résultat précis.`;

            fetch('../../controller/CoachController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'NutriCoachRequest'
                },
                body: JSON.stringify({ action: 'simulate', message: aiMessage })
            })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('ai-loading').style.display = 'none';
                    if (data.reply) {
                        document.getElementById('ai-result-box').style.display = 'block';
                        document.getElementById('ai-result-text').innerHTML = data.reply.replace(/\n/g, '<br>');

                        // Dessiner le graphique de façon créative APRÈS le rapport
                        document.getElementById('evolutionChart').style.display = 'block';
                        updateChart(labels, dataPoids, weeklyWeightDiff);
                    } else if (data.error) {
                        console.error("Erreur IA:", data.error);
                    }
                })
                .catch(err => {
                    document.getElementById('ai-loading').style.display = 'none';
                    console.error("Erreur de connexion:", err);
                });
        }

        function updateChart(labels, data, weeklyDiff) {
            const ctx = document.getElementById('evolutionChart').getContext('2d');

            if (evolutionChart) {
                evolutionChart.destroy();
            }

            const isLosing = weeklyDiff < 0;
            const lineColor = isLosing ? '#59b84d' : '#ff9f43';

            // Création d'un gradient dynamique et créatif
            let gradient = ctx.createLinearGradient(0, 0, 0, 400);
            if (isLosing) {
                gradient.addColorStop(0, 'rgba(89, 184, 77, 0.6)');
                gradient.addColorStop(1, 'rgba(89, 184, 77, 0.0)');
            } else {
                gradient.addColorStop(0, 'rgba(255, 159, 67, 0.6)');
                gradient.addColorStop(1, 'rgba(255, 159, 67, 0.0)');
            }

            evolutionChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Évolution du Poids (kg)',
                        data: data,
                        borderColor: lineColor,
                        backgroundColor: gradient,
                        borderWidth: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: lineColor,
                        pointBorderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 10,
                        pointHoverBackgroundColor: lineColor,
                        pointHoverBorderColor: '#fff',
                        fill: true,
                        tension: 0.5 // Courbe très fluide et esthétique
                    }]
                },
                options: {
                    responsive: true,
                    animation: {
                        duration: 2500, // Animation plus longue
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#111',
                            titleFont: { size: 14, family: 'Poppins' },
                            bodyFont: { size: 18, weight: 'bold', family: 'Poppins' },
                            padding: 15,
                            cornerRadius: 10,
                            displayColors: false,
                            callbacks: {
                                label: function (context) { return context.parsed.y.toFixed(1) + ' kg'; }
                            }
                        }
                    },
                    scales: {
                        y: {
                            min: Math.min(...data) - 2,
                            max: Math.max(...data) + 2,
                            grid: { borderDash: [5, 5], color: 'rgba(0,0,0,0.05)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // Run simulation with default values on load
        window.onload = function () {
            runSimulation();
        }
    </script>

    <?php include 'coach_widget.php'; ?>
</body>

</html>