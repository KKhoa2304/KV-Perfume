<?php
require_once __DIR__ . '/../../../core/Controller.php';

// Điều hướng và xử lý hiển thị trang thông tin giới thiệu thương hiệu (About Us) phía Client
class AboutController extends Controller
{
    public function index()
    {
        // Render view chính của trang About. 
        // Lưu ý: Các thành phần Layout (Header/Footer) đã được base Controller xử lý bọc tự động.
       $this->render('client/about');
    }
}