<?php

require_once __DIR__ . '/XLData.php';

class ProductModel extends XLData
{
    public function getProductsFiltered($scentFilter = 'all', $brandId = null, $categoryId = null, $keyword = '', $gender = '')
    {
        $sql = "
            SELECT p.*, b.name AS brand_name, c.name AS category_name
            FROM products p
            LEFT JOIN brands b ON p.brand_id = b.id
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.status = 1
        ";

        $params = [];

        if ($scentFilter !== 'all') {
            $scentMap = [
                'floral' => 'Floral',
                'woody' => 'Wood',
                'fresh' => 'Fresh'
            ];

            if (isset($scentMap[$scentFilter])) {
                $sql .= " AND p.scent_group LIKE :scent_group";
                $params['scent_group'] = '%' . $scentMap[$scentFilter] . '%';
            }
        }

        if (!empty($brandId)) {
            $sql .= " AND p.brand_id = :brand_id";
            $params['brand_id'] = (int)$brandId;
        }

        if (!empty($categoryId)) {
            $sql .= " AND p.category_id = :category_id";
            $params['category_id'] = (int)$categoryId;
        }

        if (!empty($gender)) {
            $sql .= " AND p.gender = :gender";
            $params['gender'] = $gender;
        }

        if (!empty($keyword)) {
            $keywordValue = '%' . trim($keyword) . '%';

            $sql .= "
                AND (
                    p.name LIKE :kw_name
                    OR p.slug LIKE :kw_slug
                    OR p.description LIKE :kw_description
                    OR p.scent_group LIKE :kw_scent_group
                    OR b.name LIKE :kw_brand
                    OR c.name LIKE :kw_category
                )
            ";

            $params['kw_name'] = $keywordValue;
            $params['kw_slug'] = $keywordValue;
            $params['kw_description'] = $keywordValue;
            $params['kw_scent_group'] = $keywordValue;
            $params['kw_brand'] = $keywordValue;
            $params['kw_category'] = $keywordValue;
        }

        $sql .= " ORDER BY p.id DESC";

        return $this->readItem($sql, $params);
    }

    public function getProductById($id)
    {
        return $this->readOne(
            "
            SELECT p.*, b.name AS brand_name, c.name AS category_name
            FROM products p
            LEFT JOIN brands b ON p.brand_id = b.id
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = :id AND p.status = 1
            ",
            ['id' => (int)$id]
        );
    }

    public function searchProducts($keyword)
    {
        return $this->getProductsFiltered('all', null, null, $keyword, '');
    }

    public function getHeroProducts($limit = 5)
    {
        $limit = max(1, min((int)$limit, 20));

        return $this->readItem(
            "
            SELECT *
            FROM products
            WHERE status = 1
              AND main_image IS NOT NULL
              AND main_image <> ''
            ORDER BY sold_quantity DESC, id DESC
            LIMIT $limit
            "
        );
    }

    public function getFeaturedProducts($limit = 8)
    {
        $limit = max(1, min((int)$limit, 30));

        return $this->readItem(
            "
            SELECT *
            FROM products
            WHERE status = 1
            ORDER BY sold_quantity DESC, id DESC
            LIMIT $limit
            "
        );
    }

    public function getNewProducts($limit = 8)
    {
        $limit = max(1, min((int)$limit, 30));

        return $this->readItem(
            "
            SELECT *
            FROM products
            WHERE status = 1
            ORDER BY id DESC
            LIMIT $limit
            "
        );
    }

    public function getBestSellerProducts($limit = 8)
    {
        $limit = max(1, min((int)$limit, 30));

        return $this->readItem(
            "
            SELECT *
            FROM products
            WHERE status = 1
            ORDER BY sold_quantity DESC, id DESC
            LIMIT $limit
            "
        );
    }

    public function getProductsByGender($gender, $limit = 8)
    {
        $limit = max(1, min((int)$limit, 30));

        return $this->readItem(
            "
            SELECT *
            FROM products
            WHERE status = 1 AND gender = :gender
            ORDER BY id DESC
            LIMIT $limit
            ",
            ['gender' => $gender]
        );
    }

    public function getProductsByBrand($brandId, $limit = 12)
    {
        $limit = max(1, min((int)$limit, 50));

        return $this->readItem(
            "
            SELECT *
            FROM products
            WHERE status = 1 AND brand_id = :brand_id
            ORDER BY id DESC
            LIMIT $limit
            ",
            ['brand_id' => (int)$brandId]
        );
    }

    public function getBrands()
    {
        return $this->readItem("SELECT * FROM brands WHERE status = 1 ORDER BY name ASC");
    }

    public function getCategories()
    {
        return $this->readItem("SELECT * FROM categories WHERE status = 1 ORDER BY id ASC");
    }

    public function getAllBrands()
    {
        return $this->readItem("SELECT * FROM brands ORDER BY name ASC");
    }

    public function getAllCategories()
    {
        return $this->readItem("SELECT * FROM categories ORDER BY id ASC");
    }

    public function getProducts($type = 'all')
    {
        $sql = "
            SELECT p.*, b.name AS brand_name, c.name AS category_name
            FROM products p
            LEFT JOIN brands b ON p.brand_id = b.id
            LEFT JOIN categories c ON p.category_id = c.id
        ";

        if ($type === 'active') {
            $sql .= " WHERE p.status = 1";
        } elseif ($type === 'hidden') {
            $sql .= " WHERE p.status = 0";
        } elseif ($type === 'out_of_stock') {
            $sql .= " WHERE p.stock <= 0";
        }

        $sql .= " ORDER BY p.id DESC";

        return $this->readItem($sql);
    }

    public function getAdminProductById($id)
    {
        return $this->readOne(
            "
            SELECT p.*, b.name AS brand_name, c.name AS category_name
            FROM products p
            LEFT JOIN brands b ON p.brand_id = b.id
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = :id
            ",
            ['id' => (int)$id]
        );
    }

    public function createProduct($data)
    {
        $data = $this->normalizeProductData($data);

        $sql = "
            INSERT INTO products (
                category_id, brand_id, name, slug, description,
                gender, concentration, volume, scent_group,
                price, sale_price, stock, main_image, status,
                top_note, middle_note, base_note, fragrance_story,
                longevity, occasion
            ) VALUES (
                :category_id, :brand_id, :name, :slug, :description,
                :gender, :concentration, :volume, :scent_group,
                :price, :sale_price, :stock, :main_image, :status,
                :top_note, :middle_note, :base_note, :fragrance_story,
                :longevity, :occasion
            )
        ";

        return $this->executeItem($sql, $data);
    }

    public function updateProduct($id, $data)
    {
        $data = $this->normalizeProductData($data);
        $data['id'] = (int)$id;

        $sql = "
            UPDATE products SET
                category_id = :category_id,
                brand_id = :brand_id,
                name = :name,
                slug = :slug,
                description = :description,
                gender = :gender,
                concentration = :concentration,
                volume = :volume,
                scent_group = :scent_group,
                price = :price,
                sale_price = :sale_price,
                stock = :stock,
                main_image = :main_image,
                status = :status,
                top_note = :top_note,
                middle_note = :middle_note,
                base_note = :base_note,
                fragrance_story = :fragrance_story,
                longevity = :longevity,
                occasion = :occasion,
                updated_at = NOW()
            WHERE id = :id
        ";

        return $this->executeItem($sql, $data);
    }

    public function hasProductRelations($id)
    {
        $id = (int)$id;

        $order = $this->readOne(
            "SELECT COUNT(*) AS total FROM order_items WHERE product_id = :id",
            ['id' => $id]
        );

        $cart = $this->readOne(
            "SELECT COUNT(*) AS total FROM cart_items WHERE product_id = :id",
            ['id' => $id]
        );

        $review = $this->readOne(
            "SELECT COUNT(*) AS total FROM reviews WHERE product_id = :id",
            ['id' => $id]
        );

        return (
            (int)$order['total'] > 0 ||
            (int)$cart['total'] > 0 ||
            (int)$review['total'] > 0
        );
    }

    public function canDeleteProduct($id)
    {
        return !$this->hasProductRelations($id);
    }

    public function deleteProduct($id)
    {
        $id = (int)$id;

        if ($this->hasProductRelations($id)) {
            return [
                'success' => false,
                'message' => 'Không thể xóa sản phẩm này vì đã phát sinh đơn hàng, giỏ hàng hoặc đánh giá. Bạn chỉ có thể ngưng kinh doanh sản phẩm.'
            ];
        }

        $deleted = $this->executeItem(
            "DELETE FROM products WHERE id = :id",
            ['id' => $id]
        );

        return [
            'success' => (bool)$deleted,
            'message' => $deleted ? 'Xóa sản phẩm thành công.' : 'Xóa sản phẩm thất bại.'
        ];
    }

    public function lockProduct($id)
    {
        return $this->executeItem(
            "UPDATE products SET status = 0, updated_at = NOW() WHERE id = :id",
            ['id' => (int)$id]
        );
    }

    public function unlockProduct($id)
    {
        return $this->executeItem(
            "UPDATE products SET status = 1, updated_at = NOW() WHERE id = :id",
            ['id' => (int)$id]
        );
    }

    public function restoreProduct($id)
    {
        return $this->unlockProduct($id);
    }

    public function decreaseStock($productId, $quantity)
    {
        return $this->executeItem(
            "
            UPDATE products
            SET stock = stock - :quantity,
                sold_quantity = sold_quantity + :quantity,
                updated_at = NOW()
            WHERE id = :id
              AND stock >= :quantity
            ",
            [
                'id' => (int)$productId,
                'quantity' => (int)$quantity
            ]
        );
    }

    private function normalizeProductData($data)
    {
        $name = trim($data['name'] ?? '');
        $slug = trim($data['slug'] ?? '');

        if ($name === '') {
            throw new Exception('Tên sản phẩm không được để trống.');
        }

        if ($slug === '') {
            $slug = $this->makeSlug($name);
        }

        $categoryId = !empty($data['category_id']) ? (int)$data['category_id'] : 0;
        $brandId = !empty($data['brand_id']) ? (int)$data['brand_id'] : 0;
        $price = isset($data['price']) ? (float)$data['price'] : 0;
        $stock = isset($data['stock']) ? (int)$data['stock'] : 0;

        if ($categoryId <= 0) {
            throw new Exception('Vui lòng chọn danh mục sản phẩm.');
        }

        if ($brandId <= 0) {
            throw new Exception('Vui lòng chọn thương hiệu sản phẩm.');
        }

        if ($price <= 0) {
            throw new Exception('Giá sản phẩm phải lớn hơn 0.');
        }

        if ($stock < 0) {
            throw new Exception('Số lượng tồn kho không được nhỏ hơn 0.');
        }

        $salePrice = $data['sale_price'] ?? null;

        if ($salePrice === '') {
            $salePrice = null;
        }

        if ($salePrice !== null) {
            $salePrice = (float)$salePrice;

            if ($salePrice < 0) {
                throw new Exception('Giá khuyến mãi không được nhỏ hơn 0.');
            }

            if ($salePrice >= $price) {
                throw new Exception('Giá khuyến mãi phải nhỏ hơn giá gốc.');
            }
        }

        $gender = $data['gender'] ?? 'unisex';
        if (!in_array($gender, ['male', 'female', 'unisex'])) {
            $gender = 'unisex';
        }

        $status = isset($data['status']) ? (int)$data['status'] : 1;
        $status = $status === 1 ? 1 : 0;

        $mainImage = trim($data['main_image'] ?? '');

        if ($mainImage !== '' && strpos($mainImage, 'http') !== 0 && strpos($mainImage, '/CHANEL.VN-MAIN/') !== 0) {
            $mainImage = '/CHANEL.VN-MAIN/public/upload/' . ltrim($mainImage, '/');
        }

        return [
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'name' => $name,
            'slug' => $slug,
            'description' => trim($data['description'] ?? ''),
            'gender' => $gender,
            'concentration' => trim($data['concentration'] ?? ''),
            'volume' => trim($data['volume'] ?? ''),
            'scent_group' => trim($data['scent_group'] ?? ''),
            'price' => $price,
            'sale_price' => $salePrice,
            'stock' => $stock,
            'main_image' => $mainImage,
            'status' => $status,
            'top_note' => trim($data['top_note'] ?? ''),
            'middle_note' => trim($data['middle_note'] ?? ''),
            'base_note' => trim($data['base_note'] ?? ''),
            'fragrance_story' => trim($data['fragrance_story'] ?? ''),
            'longevity' => trim($data['longevity'] ?? ''),
            'occasion' => trim($data['occasion'] ?? '')
        ];
    }
    public function getReviewsByProduct($productId)
{
    return $this->readItem(
        "
        SELECT 
            r.*,
            u.full_name AS customer_name,
            u.email AS customer_email
        FROM reviews r
        INNER JOIN users u ON r.user_id = u.id
        WHERE r.product_id = :product_id
          AND r.status = 1
        ORDER BY r.created_at DESC
        ",
        ['product_id' => (int)$productId]
    );
}

public function getReviewSummaryByProduct($productId)
{
    return $this->readOne(
        "
        SELECT 
            COUNT(*) AS total_reviews,
            COALESCE(AVG(rating), 0) AS average_rating
        FROM reviews
        WHERE product_id = :product_id
          AND status = 1
        ",
        ['product_id' => (int)$productId]
    );
}

    private function makeSlug($text)
    {
        $text = mb_strtolower(trim($text), 'UTF-8');

        $unicode = [
            'à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ',
            'è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ',
            'ì','í','ị','ỉ','ĩ',
            'ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ',
            'ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ',
            'ỳ','ý','ỵ','ỷ','ỹ',
            'đ'
        ];

        $ascii = [
            'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
            'e','e','e','e','e','e','e','e','e','e','e',
            'i','i','i','i','i',
            'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
            'u','u','u','u','u','u','u','u','u','u','u',
            'y','y','y','y','y',
            'd'
        ];

        $text = str_replace($unicode, $ascii, $text);
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        $text = trim($text, '-');

        return $text !== '' ? $text : 'san-pham';
    }
}