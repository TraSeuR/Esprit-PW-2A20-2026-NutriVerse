<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'add') {
    $id = $_POST['id'] ?? null;
    $nom = $_POST['nom'] ?? '';
    $prix = $_POST['prix'] ?? 0;
    
    if ($id) {
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $id) {
                $item['qte'] += 1;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $id,
                'nom' => $nom,
                'prix' => $prix,
                'qte' => 1
            ];
        }
        echo json_encode(['status' => 'added']);
        exit;
    }
}

if ($action === 'remove') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        foreach ($_SESSION['cart'] as $k => $item) {
            if ($item['id'] == $id) {
                unset($_SESSION['cart'][$k]);
                break;
            }
        }
        // Reindex array
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        echo json_encode(['status' => 'removed']);
        exit;
    }
}

if ($action === 'get') {
    echo json_encode($_SESSION['cart']);
    exit;
}

echo json_encode(['status' => 'invalid']);
