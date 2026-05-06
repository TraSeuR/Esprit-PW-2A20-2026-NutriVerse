<?php
session_start();

require_once __DIR__ . '/../../../config/config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'add') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        if (!isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] = 1;
        } else {
            $_SESSION['cart'][$id]++;
        }
        echo json_encode(['status' => 'added']);
        exit;
    }
}

if ($action === 'remove') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        unset($_SESSION['cart'][$id]);
        echo json_encode(['status' => 'removed']);
        exit;
    }
}

if ($action === 'get') {
    if (empty($_SESSION['cart'])) {
        echo json_encode([]);
        exit;
    }

    $db = config::getConnexion();
    $ids = implode(',', array_keys($_SESSION['cart']));
    
    // On sécurise un peu même si ce sont des clés internes
    $stmt = $db->query("SELECT idproduit as id, nom, prix FROM produit WHERE idproduit IN ($ids)");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($products as &$p) {
        $p['qte'] = $_SESSION['cart'][$p['id']];
    }
    
    echo json_encode($products);
    exit;
}

echo json_encode(['status' => 'invalid']);
