# AdPrint Blog Theme

Modern WordPress theme cho website Blog lĩnh vực quảng cáo và in ấn. Theme được xây dựng với Tailwind CSS, tối ưu SEO, hiệu suất cao và đảm bảo khả năng tiếp cận (WCAG 2.1 AA).

## ✨ Tính Năng Chính

### 🎨 Modern Frontend Stack
- **Tailwind CSS 3.4+**: Framework CSS hiện đại, tùy biến cao
- **Vanilla JavaScript**: Không sử dụng jQuery, hiệu suất tối ưu
- **WCAG 2.1 AA**: Đảm bảo khả năng tiếp cận cho người khuyết tật
- **Mobile-first**: Thiết kế responsive hoàn hảo trên mọi thiết bị

### 🚀 Enterprise-Level SEO
- **Schema.org Markup (7 types)**:
  - Organization
  - WebSite
  - Article/BlogPosting
  - BreadcrumbList
  - Review/Rating
  - FAQPage
  - Person
- **Open Graph & Twitter Cards**: Tối ưu chia sẻ social media
- **XML Sitemaps với images**: Sitemap tự động với hình ảnh
- **Canonical URLs & Meta Robots**: SEO on-page hoàn chỉnh

### ⚡ Performance Optimizations
- **WebP Images**: Giảm 28% dung lượng hình ảnh
- **Lazy Loading**: Tải hình ảnh và iframe theo nhu cầu
- **Critical CSS Inline**: CSS quan trọng được inline trong head
- **Resource Hints**: Preconnect, prefetch cho fonts và resources
- **Minification**: CSS và JS được minify

### 🌟 Modern UX Features
- **🌙 Dark Mode**: Chế độ tối với localStorage
- **⭐ 5-Star Rating System**: Hệ thống đánh giá bài viết
- **📱 Mobile-first responsive**: Tối ưu cho mobile
- **♿ Full accessibility**: Tuân thủ WCAG 2.1 AA
- **🔍 Enhanced Search**: Tìm kiếm với gợi ý tự động
- **📊 Dashboard Stats**: Thống kê tổng quan

### 📄 Trang Chủ (Homepage)
1. **Hero Section**: Banner lớn với thanh tìm kiếm nổi bật
2. **Dashboard**: Thống kê tổng số bài viết, danh mục, tác giả, lượt xem
3. **Latest Posts**: Hiển thị bài viết mới nhất với grid layout
4. **FAQ Section**: Câu hỏi thường gặp với Schema markup
5. **Call to Action**: Kêu gọi hành động với button nổi bật

## 📦 Cài Đặt

### Yêu Cầu
- WordPress 6.0 trở lên
- PHP 7.4 trở lên
- Node.js 14+ (để build Tailwind CSS)

### Các Bước Cài Đặt

1. **Upload theme vào WordPress**:
   ```bash
   cd wp-content/themes/
   git clone https://github.com/tootranmmo/wpsign.git adprint-blog
   cd adprint-blog
   ```

2. **Cài đặt dependencies**:
   ```bash
   npm install
   ```

3. **Build Tailwind CSS**:
   ```bash
   # Development (watch mode)
   npm run dev

   # Production (minified)
   npm run build
   ```

4. **Kích hoạt theme**:
   - Đăng nhập WordPress Admin
   - Vào Appearance → Themes
   - Kích hoạt "AdPrint Blog Theme"

## 🎨 Customization

### Theme Customizer
Vào **Appearance → Customize** để tùy chỉnh:

- **Site Identity**: Logo, site title, tagline
- **Colors**: Màu sắc chủ đạo
- **Social Media**: URL các mạng xã hội
- **Homepage Settings**: Tiêu đề hero, subtitle, số bài viết hiển thị
- **Footer Settings**: Copyright text
- **Widgets**: Sidebar và footer widgets

### Menu Locations
Theme hỗ trợ 2 vị trí menu:
- **Primary Menu**: Menu chính trong header
- **Footer Menu**: Menu trong footer

### Widget Areas
- **Sidebar**: Sidebar chính cho blog posts
- **Footer 1, 2, 3**: 3 khu vực widget trong footer

## 🔧 Development

### File Structure
```
adprint-blog/
├── assets/
│   ├── css/
│   │   ├── input.css          # Tailwind input
│   │   └── output.css         # Compiled CSS
│   ├── js/
│   │   ├── dark-mode.js       # Dark mode toggle
│   │   ├── rating.js          # Rating system
│   │   ├── search.js          # Enhanced search
│   │   ├── main.js            # Main JS
│   │   └── customizer.js      # Customizer preview
│   └── images/
├── inc/
│   ├── schema/
│   │   └── schema-org.php     # Schema.org markup
│   ├── seo/
│   │   ├── meta-tags.php      # Open Graph & Twitter Cards
│   │   └── sitemap.php        # XML Sitemap
│   ├── performance/
│   │   ├── webp-support.php   # WebP conversion
│   │   ├── lazy-load.php      # Lazy loading
│   │   └── critical-css.php   # Critical CSS
│   ├── template-tags.php      # Custom template tags
│   └── customizer.php         # Customizer settings
├── template-parts/
│   ├── components/
│   └── sections/
├── functions.php              # Theme functions
├── style.css                  # Theme header
├── header.php                 # Header template
├── footer.php                 # Footer template
├── index.php                  # Main template
├── single.php                 # Single post template
├── archive.php                # Archive template
├── sidebar.php                # Sidebar template
├── package.json               # NPM dependencies
├── tailwind.config.js         # Tailwind config
└── README.md                  # Documentation
```

### NPM Scripts
```bash
# Development mode (watch)
npm run dev

# Production build (minified)
npm run build

# Generate critical CSS
npm run build:critical
```

### Tailwind Configuration
File `tailwind.config.js` chứa cấu hình:
- Custom colors (primary, accent)
- Custom fonts (Inter, Poppins)
- Typography plugin
- Forms plugin
- Aspect ratio plugin
- Dark mode với class strategy

## 🎯 Features Details

### Dark Mode
- Toggle button trong header
- Lưu preference trong localStorage
- Smooth transition giữa light và dark
- Keyboard shortcut: `Ctrl/Cmd + Shift + D`

### 5-Star Rating System
- Rating interactive cho bài viết
- Lưu rating vào post meta
- AJAX submission
- Prevent duplicate ratings (localStorage)

### Enhanced Search
- Live search suggestions
- AJAX powered
- Show thumbnails trong suggestions
- Keyboard navigation support

### Schema Markup
- Tự động generate 7 loại schema:
  - Organization (website info)
  - WebSite (search action)
  - Article (blog posts)
  - BreadcrumbList (navigation)
  - Review/Rating (if has rating)
  - FAQPage (homepage)
  - Person (author)

### Performance Features
- WebP automatic conversion (28% smaller)
- Lazy loading images & iframes
- Critical CSS inline
- Resource hints (preconnect, prefetch)
- Defer non-critical scripts
- Remove query strings
- Disable emojis

### Accessibility Features
- WCAG 2.1 AA compliant
- Keyboard navigation
- Focus management
- ARIA labels
- Skip to main content link
- Screen reader support
- High contrast support

## 📊 SEO Features

### On-Page SEO
- Canonical URLs
- Meta robots tags
- Open Graph tags
- Twitter Card tags
- Schema.org JSON-LD
- XML Sitemaps (posts, pages, categories)
- Breadcrumbs
- Optimized permalinks

### Social Media Integration
- Facebook Open Graph
- Twitter Cards
- LinkedIn sharing
- Custom social share buttons
- Social profile links

## 🛠️ Customization Guide

### Add Custom Colors
Edit `tailwind.config.js`:
```javascript
theme: {
  extend: {
    colors: {
      primary: {
        // Your colors
      }
    }
  }
}
```

### Add Custom Fonts
1. Import trong `assets/css/input.css`
2. Update `tailwind.config.js`:
```javascript
fontFamily: {
  sans: ['YourFont', 'system-ui', 'sans-serif'],
}
```

### Modify Critical CSS
Edit các function trong `inc/performance/critical-css.php`

### Add New Widget Areas
Edit `functions.php`:
```php
register_sidebar(array(
  'name' => __('Your Widget Area', 'adprint-blog'),
  'id' => 'your-widget-area',
  // ...
));
```

## 🐛 Troubleshooting

### CSS không được apply
```bash
npm run build
```

### Dark mode không hoạt động
- Clear browser cache
- Check localStorage trong DevTools

### WebP images không hiển thị
- Đảm bảo server hỗ trợ WebP
- Kiểm tra GD library: `php -i | grep -i gd`

### Rating system không hoạt động
- Kiểm tra AJAX URL trong console
- Verify nonce đang được generate

## 📝 Changelog

### Version 1.0.0
- Initial release
- Tailwind CSS 3.4+ integration
- Dark mode support
- 5-star rating system
- Schema.org markup (7 types)
- Open Graph & Twitter Cards
- XML Sitemaps with images
- WebP support
- Lazy loading
- Critical CSS
- WCAG 2.1 AA compliance
- Enhanced search
- Dashboard stats
- FAQ section
- CTA sections

## 📄 License

GNU General Public License v2 or later
http://www.gnu.org/licenses/gpl-2.0.html

## 👨‍💻 Author

**TooTranMMO**
- GitHub: [@tootranmmo](https://github.com/tootranmmo)
- Repository: [wpsign](https://github.com/tootranmmo/wpsign)

## 🤝 Contributing

Contributions, issues and feature requests are welcome!

## ⭐ Support

Give a ⭐️ if this project helped you!

## 📮 Contact

For support or questions, please open an issue on GitHub.
