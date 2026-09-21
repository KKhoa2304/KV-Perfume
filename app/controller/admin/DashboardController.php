<?php

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../../core/AuthMiddleware.php';
require_once __DIR__ . '/../../models/OrderModel.php';
require_once __DIR__ . '/../../models/ProductModel.php';

class DashboardController extends Controller
{
    private OrderModel $orderModel;

    public function __construct()
    {
        AuthMiddleware::requireRole('admin');
        $this->orderModel = new OrderModel();
    }

    public function index()
    {
        $orders = $this->orderModel->getAllOrders();

        $totalOrders = 0;
        $totalRevenue = 0;
        $pendingOrders = 0;
        $shippingOrders = 0;

        foreach ($orders as $order) {
            $totalOrders++;

            $status = trim($order['status'] ?? '');

            // Đơn chờ xử lý
            if ($status === 'pending') {
                $pendingOrders++;
            }

            // Đơn đang giao
            if ($status === 'shipping') {
                $shippingOrders++;
            }

            // Tính doanh thu khi đơn đã xác nhận trở lên
            if (in_array($status, ['confirmed', 'shipping', 'delivered', 'completed'])) {
                $totalRevenue += (float)$order['final_amount'];
            }
        }

        $this->renderAdmin('admin/dashboard/index', [
            'title' => 'Tổng quan hệ thống',
            'breadcrumbs' => [
                ['label' => 'Tổng Quan']
            ],
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'pendingOrders' => $pendingOrders,
            'shippingOrders' => $shippingOrders
        ]);
    }
}