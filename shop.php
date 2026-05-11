<?php
ob_start();
session_start();



// Requirements
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/Controller/ProductController.php';
require_once __DIR__ . '/Controller/CartC.php';
require_once __DIR__ . '/Controller/OrderC.php';
require_once __DIR__ . '/Controller/AdminC.php';
require_once __DIR__ . '/Controller/LivraisonC.php';

$action = $_GET['action'] ?? 'products';

switch ($action) {
    case 'products':
        (new ProductController())->listProducts();
        break;
    case 'front':
        (new ProductController())->frontPage();
        break;
    case 'cart':
        (new CartController())->showCart();
        break;
    case 'add_to_cart':
        (new CartController())->add();
        break;
    case 'update_cart':
        (new CartController())->update();
        break;
    case 'remove_from_cart':
        (new CartController())->remove();
        break;
    case 'checkout':
        (new OrderController())->showForm();
        break;
    case 'place_order':
        (new OrderController())->placeOrder();
        break;
    case 'validate_promo':
        (new OrderController())->validatePromo();
        break;
    case 'order_confirmation':
        (new OrderController())->confirmation();
        break;
    case 'front_update_address':
        (new OrderController())->updateAddress();
        break;
    case 'front_cancel_order':
        (new OrderController())->cancelOrder();
        break;
    case 'admin_dashboard':
        (new AdminController())->dashboard();
        break;
    case 'admin_orders':
        (new AdminController())->listOrders();
        break;
    case 'admin_order_view':
        (new AdminController())->viewOrder();
        break;
    case 'admin_order_edit':
        (new AdminController())->editStatus();
        break;
    case 'admin_order_delete':
        (new AdminController())->deleteOrder();
        break;
    case 'admin_livraisons':
        (new LivraisonController())->listLivraisons();
        break;
    case 'admin_livraison_update':
        (new LivraisonController())->updateLivraison();
        break;
    case 'admin_livraison_delete':
        (new LivraisonController())->deleteLivraison();
        break;
    case 'my_orders':
        (new OrderController())->myOrders();
        break;
    case 'order_detail':
        (new OrderController())->orderDetail();
        break;
    default:
        http_response_code(404);
        echo 'Page introuvable';
}
