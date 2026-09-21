<?php
require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../models/ProductModel.php';

// Quản lý hiển thị Trang chủ (Landing Page) phía Client: Lắp ráp và điều phối các bộ sưu tập sản phẩm
class HomeController extends Controller
{
    private ProductModel $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

    // Luồng xử lý chính: Nhận tham số tìm kiếm và phân bổ dữ liệu cho các khu vực hiển thị trên trang chủ
    public function index()
    {
        // 1. Nhận và chuẩn hóa các tham số lọc từ URL (Query String)
        $activeFilter = $_GET['filter'] ?? 'all';
        $brandId = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : null;
        $categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
        $keyword = trim($_GET['keyword'] ?? '');
        $gender = trim($_GET['gender'] ?? '');

        // 2. Truy vấn danh sách sản phẩm theo bộ lọc (kích hoạt khi người dùng dùng thanh tìm kiếm hoặc menu lọc)
        $products = $this->productModel->getProductsFiltered(
            $activeFilter,
            $brandId,
            $categoryId,
            $keyword,
            $gender
        );

        // 3. Đổ dữ liệu tổng hợp ra View. 
        // Khối mảng này đóng vai trò "cấp vốn" cho tất cả các Section UI trên trang chủ (Hero banner, Best sellers...)
        $this->render('client/Home', [
            // Dữ liệu trạng thái bộ lọc (để giữ UI active tương ứng trên giao diện)
            'activeFilter' => $activeFilter,
            'keyword' => $keyword,
            'brandId' => $brandId,
            'categoryId' => $categoryId,
            'gender' => $gender,
            
            // Kết quả tìm kiếm/lọc chính
            'products' => $products,
            
            // Các bộ sưu tập (Collections) phân tách theo từng nhóm để hiển thị thành các dải sản phẩm riêng biệt
            'heroProducts' => $this->productModel->getHeroProducts(5),
            'featuredProducts' => $this->productModel->getFeaturedProducts(8),
            'newProducts' => $this->productModel->getNewProducts(8),
            'bestSellerProducts' => $this->productModel->getBestSellerProducts(8),
            'maleProducts' => $this->productModel->getProductsByGender('male', 8),
            'femaleProducts' => $this->productModel->getProductsByGender('female', 8),
            'unisexProducts' => $this->productModel->getProductsByGender('unisex', 8),
            
            // Danh sách thương hiệu phục vụ cho bộ lọc hoặc Carousel Brands
            'brands' => $this->productModel->getBrands()
        ]);
    }
}