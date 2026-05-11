<!-- LINK GLOBAL NUTRIVERSE STYLE -->
<link rel="stylesheet" href="view/assets/style.css">

<!-- LUXURY DECORATIVE ELEMENTS -->
<div class="luxury-bg-blob blob-1"></div>
<div class="luxury-bg-blob blob-2" style="background: var(--primary);"></div>

<!-- HERO SECTION: SYNCED WITH RECETTES/PROGRAMMES -->
<section class="recipe-header fade-up">
    <div class="icons">
        <span>🥗</span><span>🍎</span><span>🥑</span><span>🍉</span><span>🥦</span><span>🍓</span>
        <span>🥕</span><span>🍋</span><span>🍇</span><span>🥝</span><span>🍍</span><span>🥬</span>
    </div>
    <div class="header-content">
        <h1 style="margin-bottom: 0;">NutriVerse</h1>
        <h2 style="font-size: 2rem; opacity: 0.9; font-weight: 700; margin: 10px 0; color: white;">Marché de Produits Frais</h2>
    </div>
</section>

<div class="container fade-in" style="padding: 0 20px 100px; position: relative; z-index: 2; margin-top: -40px;">
    
    <!-- Filtres & Tris -->
    <div class="glass-card fade-up" style="padding: 20px 40px; margin-bottom: 50px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div style="background: white; padding: 10px 20px; border-radius: 15px; display: flex; align-items: center; gap: 10px; border: 1.5px solid #f1f5f9; flex: 1; max-width: 400px;">
            <span style="font-size: 1.2rem;">🔍</span>
            <input type="text" placeholder="Rechercher un produit..." style="border: none; outline: none; width: 100%; font-weight: 600; color: var(--text-dark);">
        </div>
        <div style="display: flex; gap: 10px;">
            <button class="btn-premium active" style="width: auto; padding: 10px 20px; font-size: 0.8rem;">Tous</button>
            <button class="btn-premium" style="width: auto; padding: 10px 20px; font-size: 0.8rem; background: transparent; color: var(--text-muted); border: 1.5px solid #eef2f1;">Légumes</button>
            <button class="btn-premium" style="width: auto; padding: 10px 20px; font-size: 0.8rem; background: transparent; color: var(--text-muted); border: 1.5px solid #eef2f1;">Fruits</button>
            <button class="btn-premium" style="width: auto; padding: 10px 20px; font-size: 0.8rem; background: transparent; color: var(--text-muted); border: 1.5px solid #eef2f1;">Protéines</button>
        </div>
    </div>

    <div class="product-luxury-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px;">
        <?php foreach ($products as $product): ?>
            <div class="glass-card fade-up" style="padding: 0; overflow: hidden; transition: 0.4s; border: 1.5px solid #eef2f1; height: 100%; display: flex; flex-direction: column;">
                <div style="position: relative; height: 220px; overflow: hidden;">
                    <img src="view/FrontOffice/images/<?= htmlspecialchars($product['categorie']) ?>.jpg" alt="<?= htmlspecialchars($product['nom']) ?>" style="width: 100%; height: 100%; object-fit: cover; transition: 0.5s;">
                    <span style="position: absolute; top: 20px; left: 20px; background: white; padding: 6px 15px; border-radius: 20px; font-size: 0.7rem; font-weight: 800; color: var(--primary-dark); text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);"><?= htmlspecialchars($product['categorie']) ?></span>
                </div>
                <div style="padding: 25px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                    <h3 style="font-size: 1.2rem; color: var(--text-dark); margin-bottom: 15px; font-weight: 700;"><?= htmlspecialchars($product['nom']) ?></h3>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 1.5rem; font-weight: 900; color: var(--text-dark);"><?= number_format($product['prix'], 2) ?> <small style="font-size: 0.9rem; color: var(--primary); font-weight: 700;">DT</small></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .glass-card:hover { transform: translateY(-10px); border-color: var(--primary-light) !important; box-shadow: 0 20px 40px rgba(89, 184, 77, 0.1) !important; }
    .glass-card:hover img { transform: scale(1.1); }
    
    .luxury-bg-blob { position: fixed; width: 500px; height: 500px; filter: blur(120px); z-index: -1; opacity: 0.15; border-radius: 50%; }
    .blob-1 { top: -100px; right: -100px; background: var(--primary); }
    .blob-2 { bottom: -100px; left: -100px; }
</style>
