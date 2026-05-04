<?php
ob_start();
session_start();

// Simulation d'un utilisateur connecté (id=1 doit exister dans la table user)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}
require_once __DIR__ . '/config/db.php';

require_once __DIR__ . '/controller/ProductController.php';
require_once __DIR__ . '/controller/CartController.php';
require_once __DIR__ . '/controller/OrderController.php';
require_once __DIR__ . '/controller/AdminController.php';
require_once __DIR__ . '/controller/LivraisonController.php';

$action = $_GET['action'] ?? 'products';

switch ($action) {
    case 'products':
        (new ProductController($pdo))->listProducts();
        break;
    case 'front':
        (new ProductController($pdo))->frontPage();
        break;
    case 'cart':
        (new CartController($pdo))->showCart();
        break;
    case 'add_to_cart':
        (new CartController($pdo))->add();
        break;
    case 'update_cart':
        (new CartController($pdo))->update();
        break;
    case 'remove_from_cart':
        (new CartController($pdo))->remove();
        break;
    case 'checkout':
        (new OrderController($pdo))->showForm();
        break;
    case 'place_order':
        (new OrderController($pdo))->placeOrder();
        break;
    case 'validate_promo':
        (new OrderController($pdo))->validatePromo();
        break;
    case 'order_confirmation':
        (new OrderController($pdo))->confirmation();
        break;
    case 'front_update_address':
        (new OrderController($pdo))->updateAddress();
        break;
    case 'front_cancel_order':
        (new OrderController($pdo))->cancelOrder();
        break;
    case 'admin_dashboard':
        (new AdminController($pdo))->dashboard();
        break;
    case 'admin_orders':
        (new AdminController($pdo))->listOrders();
        break;
    case 'admin_order_view':
        (new AdminController($pdo))->viewOrder();
        break;
    case 'admin_order_edit':
        (new AdminController($pdo))->editStatus();
        break;
    case 'admin_order_delete':
        (new AdminController($pdo))->deleteOrder();
        break;
    case 'admin_livraisons':
        (new LivraisonController($pdo))->listLivraisons();
        break;
    case 'admin_livraison_update':
        (new LivraisonController($pdo))->updateLivraison();
        break;
    case 'admin_livraison_delete':
        (new LivraisonController($pdo))->deleteLivraison();
        break;
    case 'my_orders':
        (new OrderController($pdo))->myOrders();
        break;
    case 'order_detail':
        (new OrderController($pdo))->orderDetail();
        break;
    case 'change_to_livraison':
        (new OrderController($pdo))->changeToLivraison();
        break;
    default:
        http_response_code(404);
        echo 'Page introuvable';
}
