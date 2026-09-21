KV PERFUME - InfinityFree 

Cach dung:
1. Giai nen/upload TOAN BO FILE BEN TRONG zip nay vao htdocs/ cua InfinityFree.
   Sau upload dung cau truc:
   htdocs/index.php
   htdocs/app/
   htdocs/core/
   htdocs/public/

2. Mo app/models/Database.php va doi:
   private string $password = 'DOI_MAT_KHAU_VPANEL_O_DAY';
   thanh mat khau vPanel/MySQL cua ban.

3. Vao phpMyAdmin cua database if0_42257221_kv_perfume.
   Drop het cac bang cu.
   Import file database_infinityfree.sql.
   Khi import chon Character set of the file: utf-8.

Da fix:
- Duong dan anh ve /public/upload/...
- Base URL de load CSS/JS tren hosting root.
- Loi Linux case-sensitive: XLData.php, Sidebar.php, Breadcrumb.php.
- Admin co Quan ly lien he.
- Database.php ep UTF-8/utf8mb4 cho tieng Viet.
