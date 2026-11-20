<?php
declare(strict_types=1);
$config = [
    'site_name' => 'VTS',
    'tagline' => 'Đào tạo từ xa – Vì tương lai bạn',
    'hotline' => '0964 380 333',
    'hotline_href' => 'tel:0964380333',
    'consulting_hours' => '08:00 – 20:00',
    'email' => 'Đang cập nhật – vui lòng liên hệ',
    'lead_email' => '',
    'address' => '469 Lê Hồng Phong, Phường Vườn Lài, TP HCM',
    'legal_name' => 'VTS – Trường Trung cấp Vạn Tường',
    'show_placeholder_labels' => true,
    'canonical' => '',
    'social' => ['facebook' => '', 'youtube' => '', 'tiktok' => '', 'zalo' => ''],
    'analytics' => ['ga4' => '', 'meta_pixel' => ''],
];
$programs = require __DIR__ . '/data/programs.php';
$schools = require __DIR__ . '/data/schools.php';
$faqs = require __DIR__ . '/data/faq.php';
function smart_image(
    string $src,
    string $alt,
    string $class = '',
    string $ratio = '4 / 3',
    string $suggested = '',
): void {
    global $config;
    $absolute = __DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $src);
    if (is_file($absolute)) {
        echo '<img class="' .
            htmlspecialchars($class) .
            '" src="' .
            htmlspecialchars($src) .
            '" alt="' .
            htmlspecialchars($alt) .
            '" loading="lazy">';
        return;
    }
    $label = !empty($config['show_placeholder_labels'])
        ? '<span>' .
            htmlspecialchars($alt) .
            ($suggested ? '<small>' . htmlspecialchars($suggested) . '</small>' : '') .
            '</span>'
        : '';
    echo '<div class="image-placeholder ' .
        htmlspecialchars($class) .
        '" style="aspect-ratio:' .
        htmlspecialchars($ratio) .
        '" role="img" aria-label="' .
        htmlspecialchars($alt) .
        '">
        <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M4 5h16v14H4zM4 15l4-4 4 4 3-3 5 5M15 9h.01"/></svg>' .
        $label .
        '</div>';
}
function program_card(array $program, array $schools): void
{
    $school = $schools[$program['school']];
    $icons = [
        'Ngôn ngữ' =>
            '<path d="M3 4c4-1 6 0 9 2 3-2 5-3 9-2v15c-4-1-6 0-9 2-3-2-5-3-9-2V4zm9 2v15M6 8l3 1M6 12l3 1M15 9l3-1M15 13l3-1"/>',
        'CNTT' =>
            '<rect x="4" y="4" width="16" height="13" rx="1"/><path d="M2 21h20l-2-4H4l-2 4z"/>',
        'Luật' => '<path d="M12 3v17M6 21h12M4 7h16M5 7l-3 7h6L5 7zm14 0-3 7h6l-3-7z"/>',
        'Kinh tế–Quản trị' => '<path d="M3 21V14h4v7m3 0V9h4v12m3 0V4h4v17M3 9l6-4 5 1 7-5"/>',
        'Du lịch–Khách sạn' => '<path d="M3 9l9-6 9 6H3zm2 3v8m5-8v8m4-8v8m5-8v8M2 22h20"/>',
    ];
    $icon =
        $icons[$program['group']] ??
        '<circle cx="12" cy="6" r="3"/><path d="M5 21v-5a7 7 0 0114 0v5M3 21h18"/>';
    ?>
<article
    class="program-card reveal"
    data-school="<?= htmlspecialchars( $program['school']) ?>"
    data-group="<?= htmlspecialchars($program['group']) ?>"
    data-slug="<?= htmlspecialchars($program['slug']) ?>"
>
    <div class="program-card-top">
        <span class="program-group"><?= htmlspecialchars( $program['group']) ?></span>
        <span class="school-badge"><?= htmlspecialchars($school['short']) ?></span>
    </div>
    <div class="program-identity">
        <span class="line-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><?= $icon ?></svg>
        </span>
        <div>
            <p class="program-school"><?= htmlspecialchars($school['name']) ?></p>
            <h3><?= htmlspecialchars($program['name']) ?></h3>
        </div>
    </div>
    <div class="card-actions">
        <button class="btn btn-outline js-open-program" type="button">Xem chi tiết</button>
        <a class="btn btn-gold js-consult" href="#dang-ky" data-program="<?= htmlspecialchars( $program['slug']) ?>">
            Tư vấn ngành này
        </a>
    </div>
</article>
<?php
}
function school_card(string $key, array $school): void
{
    ?>
<article class="school-card reveal">
    <div class="school-logo-wrap"><?php smart_image(
      $school['logo'],
      'Logo ' . $school['name'],
      'school-logo',
      '16 / 7',
      'PNG/WebP nền trong, 640×280px',
  ); ?></div>
    <h3><?= htmlspecialchars($school['name']) ?></h3>
    <ul>
        <?php foreach ( $school['facts'] as $fact ): ?>
        <li>
            <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12l4 4L19 6" /></svg>
            <?= htmlspecialchars( $fact) ?>
        </li>
        <?php endforeach; ?>
    </ul>
    <button class="btn btn-gold js-school-filter" type="button" data-school="<?= htmlspecialchars( $key) ?>">
        Xem ngành của trường
    </button>
</article>
<?php
}

$title = 'Tuyển sinh đại học từ xa 2026 | VTS';
$description =
    'Khám phá 15 chương trình đại học từ xa từ 3 trường đại học. VTS tư vấn lộ trình phù hợp cho người bận rộn.';
$canonical = $config['canonical'] ?: '';
$programJson = [];
$formPrograms = [];
foreach ($programs as $p) {
    $p['school_name'] = $schools[$p['school']]['name'];
    $p['school_short'] = $schools[$p['school']]['short'];
    $p['major_name'] = preg_replace('/^Văn bằng 2\s+/u', '', $p['name']);
    $programJson[$p['slug']] = $p;
    if (!isset($formPrograms[$p['name']])) {
        $formPrograms[$p['name']] = $p;
    }
}
$majorCount = count(
    array_unique(
        array_map(
            static fn(array $p): string => preg_replace('/^Văn bằng 2\s+/u', '', $p['name']),
            $programs,
        ),
    ),
);
?>
<!doctype html>
<html lang="vi">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width,initial-scale=1" />
        <title><?= htmlspecialchars( $title) ?></title>
        <meta name="description" content="<?= htmlspecialchars($description) ?>" />
        <?php if ($canonical): ?>
        <link rel="canonical" href="<?= htmlspecialchars( $canonical) ?>" />
        <?php endif; ?>
        <meta property="og:type" content="website" />
        <meta property="og:title" content="<?= htmlspecialchars( $title) ?>" />
        <meta property="og:description" content="<?= htmlspecialchars( $description) ?>" />
        <meta property="og:image" content="assets/images/og-image.jpg" />
        <link rel="icon" type="image/png" href="assets/images/favicon-vts.png?v=1" sizes="256x256" />
        <link rel="apple-touch-icon" href="assets/images/favicon-vts.png?v=1" />
        <link rel="stylesheet" href="assets/css/style.css?v=14" />
        <script type="application/ld+json">
            <?= json_encode( [ '@context' => 'https://schema.org', '@type' => 'EducationalOrganization', 'name' => 'VTS', 'telephone' => $config['hotline'], 'address' => $config['address'], ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
        </script>
        <script type="application/ld+json">
            <?= json_encode( [ '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array_map( fn($f) => [ '@type' => 'Question', 'name' => $f[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]], ], $faqs), ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
        </script>
    </head>
    <body id="top">
        <?php require __DIR__ . '/components/header.php'; ?>
        <main>
            <section class="hero section-dark">
                <div class="hero-orbit" aria-hidden="true"></div>
                <div class="container hero-grid">
                    <div class="hero-copy reveal">
                        <p class="eyebrow">
                            <span class="status-dot" aria-hidden="true"></span>
                            TUYỂN SINH ĐẠI HỌC TỪ XA · 2026
                        </p>
                        <h1>
                            <span>Đại học từ xa</span>
                            <br />
                            Chủ động học tập, vững bước sự nghiệp
                        </h1>
                        <p class="hero-lead">
                            Học đại học theo cách linh hoạt hơn, chủ động thời gian và địa điểm học tập, dễ
                            dàng tiếp cận kiến thức và từng bước hoàn thành mục tiêu học vấn.
                        </p>
                        <div class="hero-actions">
                            <a class="btn btn-gold" href="#chuong-trinh">Khám phá chương trình</a>
                            <a class="btn btn-ghost" href="#dang-ky">Đăng ký tư vấn</a>
                        </div>
                        <div class="hero-stats" aria-label="Thống kê chương trình">
                            <div>
                                <svg class="stat-icon" aria-hidden="true" viewBox="0 0 24 24">
                                    <path d="M3 9l9-6 9 6H3zm2 3v7m5-7v7m4-7v7m5-7v7M3 21h18" />
                                </svg>
                                <strong data-count="<?= count( $schools) ?>"><?= count($schools) ?></strong>
                                <span>Trường đào tạo</span>
                            </div>
                            <div>
                                <svg class="stat-icon" aria-hidden="true" viewBox="0 0 24 24">
                                    <path
                                        d="M3 4c4-1 6 0 9 2 3-2 5-3 9-2v15c-4-1-6 0-9 2-3-2-5-3-9-2V4zm9 2v15M6 9l3 1M15 10l3-1"
                                    />
                                </svg>
                                <strong data-count="<?= $majorCount ?>"><?= $majorCount ?></strong>
                                <span>Ngành đào tạo</span>
                            </div>
                            <div>
                                <svg class="stat-icon" aria-hidden="true" viewBox="0 0 24 24">
                                    <path d="M5 3h14v18H5zM8 7h8M8 11h8M8 15h5" />
                                </svg>
                                <strong data-count="<?= count( $programs) ?>"><?= count($programs) ?></strong>
                                <span>Chương trình tuyển sinh</span>
                            </div>
                        </div>
                    </div>
                    <div class="hero-visual reveal">
                        <?php smart_image(
            'assets/images/hero-education.png',
            'Sách mở và mũ cử nhân tượng trưng cho hành trình học tập',
            'hero-image',
            '16 / 10',
            '1920×1000px',
        ); ?>
                        <div class="hero-script">
                            <span>HÀNH TRÌNH TRI THỨC</span>
                            <strong>
                                Chủ động hôm nay.
                                <br />
                                Vững bước ngày mai.
                            </strong>
                            <small>Học trực tuyến · Đồng hành cùng VTS</small>
                        </div>
                    </div>
                </div>
            </section>

            <section class="benefits" aria-labelledby="benefits-title">
                <div class="container benefits-layout">
                    <div class="benefits-intro">
                        <p class="eyebrow dark">HỌC TẬP THEO CÁCH CỦA BẠN</p>
                        <h2 id="benefits-title">
                            Linh hoạt để học
                            <br />
                            <span>Vững bước để tiến xa</span>
                        </h2>
                        <p>Một lộ trình phù hợp với nhịp sống, công việc và mục tiêu của bạn.</p>
                        <a href="#chuong-trinh" class="benefits-link">
                            Khám phá chương trình
                            <span aria-hidden="true">↗</span>
                        </a>
                    </div>
                    <div class="benefit-grid">
                        <?php
    $benefitIcons = [
        'clock' =>
            '<rect x="3" y="5" width="18" height="12" rx="1"/><path d="M2 20h20M9 17l-1 3m7-3 1 3"/>',
        'file' => '<path d="M6 2h8l4 4v16H6zM14 2v5h5M9 12h6M9 16h6"/>',
        'map' =>
            '<circle cx="8" cy="8" r="3"/><circle cx="16" cy="8" r="3"/><path d="M2 20v-3c0-3 3-5 6-5s6 2 6 5v3M13 13c1-.7 2-1 3-1 3 0 6 2 6 5v3"/>',
        'award' => '<path d="M4 7l8-4 8 4-8 4-8-4zm2 3v6l6 3 6-3v-6M4 7v7"/>',
        'trend' => '<circle cx="12" cy="9" r="7"/><path d="M8 15l-1 7 5-3 5 3-1-7M9 9l2 2 4-4"/>',
    ];
    $benefits = [
        ['clock', 'Học trực tuyến linh hoạt', 'Chủ động thời gian, học mọi nơi'],
        ['file', 'Xét tuyển hồ sơ', 'Thủ tục đơn giản, hỗ trợ tận tâm'],
        ['map', 'Tuyển sinh toàn quốc', 'Tư vấn học viên trên toàn quốc'],
        ['award', 'Văn bằng do trường cấp', 'Theo quy định của Bộ GD&ĐT'],
        ['trend', 'Cơ hội thăng tiến', 'Phù hợp người đi làm'],
    ];
    foreach ($benefits as [$icon, $name, $text]): ?>
                        <article>
                            <span class="benefit-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><?= $benefitIcons[ $icon ] ?></svg>
                            </span>
                            <div>
                                <h3><?= $name ?></h3>
                                <p><?= $text ?></p>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <section class="learner-fit" id="phu-hop-voi-ai" aria-labelledby="learner-fit-title">
                <div class="container">
                    <div class="learner-fit-heading">
                        <div>
                            <p class="eyebrow dark">DÀNH CHO HÀNH TRÌNH CỦA BẠN</p>
                            <h2 id="learner-fit-title">
                                Đại học từ xa
                                <br />
                                <span>phù hợp với ai?</span>
                            </h2>
                        </div>
                    </div>
                    <div class="learner-fit-grid">
                        <article>
                            <div class="fit-card-top">
                                <span class="fit-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <rect x="3" y="7" width="18" height="14" rx="2" />
                                        <path d="M8 7V3h8v4M3 13h18M10 13v3h4v-3" />
                                    </svg>
                                </span>
                            </div>
                            <h3>Đang đi làm, muốn thăng tiến</h3>
                            <p>
                                Bạn muốn bổ sung bằng cấp và nâng cao chuyên môn? Tìm lộ trình vừa làm vừa học
                                để tiếp tục phát triển mà vẫn duy trì công việc.
                            </p>
                            <span class="fit-tag">Cân bằng học tập & công việc</span>
                        </article>
                        <article>
                            <div class="fit-card-top">
                                <span class="fit-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path
                                            d="M3 4c4-1 6 0 9 2 3-2 5-3 9-2v15c-4-1-6 0-9 2-3-2-5-3-9-2V4zm9 2v15"
                                        />
                                    </svg>
                                </span>
                            </div>
                            <h3>Muốn học văn bằng thứ hai</h3>
                            <p>
                                Bạn muốn chuyển hướng hoặc bổ sung kiến thức lĩnh vực khác? Tìm hiểu chương
                                trình văn bằng thứ hai theo điều kiện của từng trường.
                            </p>
                            <span class="fit-tag">Mở rộng kiến thức & định hướng</span>
                        </article>
                        <article>
                            <div class="fit-card-top">
                                <span class="fit-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1116 0z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                </span>
                            </div>
                            <h3>Ở xa, khó sắp xếp đi lại</h3>
                            <p>
                                Bạn sinh sống xa trường hoặc khó di chuyển thường xuyên? Học trực tuyến giúp
                                tiếp cận bài giảng từ xa; lịch học và thi theo quy định của trường.
                            </p>
                            <span class="fit-tag">Tiếp cận kiến thức từ xa</span>
                        </article>
                        <article>
                            <div class="fit-card-top">
                                <span class="fit-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" />
                                        <path d="M12 7v5l3 2" />
                                    </svg>
                                </span>
                            </div>
                            <h3>Cần chủ động thời gian</h3>
                            <p>
                                Bạn làm việc tự do, chăm sóc con nhỏ hoặc có lịch trình thay đổi? Tìm hình
                                thức học linh hoạt để cân bằng việc học với cuộc sống.
                            </p>
                            <span class="fit-tag">Phù hợp nhịp sống riêng</span>
                        </article>
                        <article>
                            <div class="fit-card-top">
                                <span class="fit-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M3 6h17v15H3zM3 6V3h15v3M15 11h7v6h-7zM18 14h.01" />
                                    </svg>
                                </span>
                            </div>
                            <h3>Muốn tối ưu chi phí sinh hoạt</h3>
                            <p>
                                Bạn muốn hạn chế việc chuyển nơi ở và đi lại thường xuyên? Cân nhắc học từ xa
                                để chủ động kế hoạch sinh hoạt theo điều kiện của mình.
                            </p>
                            <span class="fit-tag">Chủ động kế hoạch cá nhân</span>
                        </article>
                    </div>
                    <div class="learner-fit-bottom">
                        <p>
                            <strong>Bạn thấy mình trong những nhóm trên?</strong>
                            <span>
                                Điều kiện đầu vào, công nhận tín chỉ và hình thức học, thi tùy từng trường.
                            </span>
                        </p>
                        <a class="btn btn-navy" href="#dang-ky">
                            Tư vấn lộ trình phù hợp
                            <span aria-hidden="true">↗</span>
                        </a>
                    </div>
                </div>
            </section>

            <section id="chuong-trinh" class="section programs-section">
                <div class="container">
                    <div class="section-heading reveal">
                        <p class="eyebrow dark">01 / KHÁM PHÁ CHƯƠNG TRÌNH</p>
                        <h2>
                            Ngành học phù hợp
                            <br />
                            <span>Tương lai rộng mở</span>
                        </h2>
                    </div>
                    <div class="filters reveal" role="group" aria-label="Bộ lọc chương trình">
                        <div class="school-tabs" role="tablist">
                            <button class="is-active" role="tab" aria-selected="true" data-school="all">
                                Tất cả
                                <span><?= count( $programs) ?></span>
                            </button>
                            <?php foreach ( $schools as $key => $school ): ?>
                            <button role="tab" aria-selected="false" data-school="<?= $key ?>">
                                <?= htmlspecialchars( $school['name']) ?>
                                <span><?= $school['program_count'] ?></span>
                            </button>
                            <?php endforeach; ?>
                        </div>
                        <label class="group-filter">
                            <span>Nhóm ngành</span>
                            <select id="group-filter">
                                <option value="all">Tất cả nhóm ngành</option>
                                <?php foreach ( array_unique(array_column($programs, 'group')) as $group ): ?>
                                <option value="<?= htmlspecialchars($group) ?>"><?= htmlspecialchars( $group) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>
                    <p class="filter-summary" id="filter-summary" role="status" aria-live="polite">
                        Hiển thị 6 /
                        <?= count( $programs) ?>
                        chương trình
                    </p>
                    <div class="program-grid" id="program-grid">
                        <?php foreach ( $programs as $i => $program ): ?>
                        <div class="program-item<?= $i >= 6 ? ' is-collapsed' : '' ?>"><?php program_card(
    $program,
    $schools,
); ?></div>
                        <?php endforeach; ?>
                    </div>
                    <div id="no-results" class="no-results" hidden>
                        Không có chương trình phù hợp với bộ lọc hiện tại.
                    </div>
                    <div class="center">
                        <button class="btn btn-navy" id="show-all-programs" type="button">
                            Xem tất cả
                            <?= count( $programs) ?>
                            chương trình
                        </button>
                    </div>
                </div>
            </section>

            <section id="cac-truong" class="section section-dark schools-section">
                <div class="container">
                    <div class="section-heading light reveal">
                        <p class="eyebrow">HỆ SINH THÁI ĐÀO TẠO</p>
                        <h2>
                            Kết nối tri thức
                            <br />
                            <span>Đồng hành cùng các trường</span>
                        </h2>
                        <p>
                            Văn bằng do trường đại học cấp. VTS tư vấn, phối hợp tuyển sinh và hỗ trợ bạn
                            lựa chọn chương trình phù hợp.
                        </p>
                        <div class="school-support">
                            <h3>Cùng bạn tìm lộ trình phù hợp</h3>
                            <ul>
                                <li><strong>Chọn ngành theo mục tiêu</strong><span>Tìm hiểu chương trình phù hợp với công việc và định hướng phát triển của bạn.</span></li>
                                <li><strong>Hiểu rõ điều kiện đầu vào</strong><span>Đối chiếu bằng cấp hiện có với yêu cầu xét tuyển của từng trường.</span></li>
                                <li><strong>Chuẩn bị hồ sơ từng bước</strong><span>Được hướng dẫn giấy tờ cần thiết và nơi tiếp nhận theo thông báo của từng trường.</span></li>
                            </ul>
                            <p>Lịch học, hình thức thi và việc công nhận tín chỉ thực hiện theo quy định của từng trường.</p>
                        </div>
                        <a class="btn btn-ghost" href="#chuong-trinh">
                            Khám phá các chương trình
                            <span aria-hidden="true">↗</span>
                        </a>
                    </div>
                    <div class="school-grid"><?php foreach ($schools as $key => $school) { school_card($key, $school); } ?></div>
                </div>
            </section>

            <section id="doi-tuong" class="section cream-section">
                <div class="container audience-grid">
                    <div class="reveal">
                        <div class="section-heading left">
                            <p class="eyebrow dark">SẴN SÀNG CHO BƯỚC TIẾP THEO</p>
                            <h2>Chuẩn bị gì<br />trước khi đăng ký?</h2>
                            <p>Một vài định hướng rõ ràng sẽ giúp bạn lựa chọn chương trình dễ dàng hơn.</p>
                        </div>
                        <div class="study-readiness">
                            <article>
                                <span class="readiness-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" /><circle cx="12" cy="12" r="4" /><path d="M12 12l8-8" /></svg></span>
                                <div><h3>Xác định mục tiêu học tập</h3><p>Bạn muốn nâng cao chuyên môn, phát triển công việc hay học thêm một lĩnh vực mới? Bắt đầu từ điều bạn muốn đạt được.</p></div>
                            </article>
                            <article>
                                <span class="readiness-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l4 2" /></svg></span>
                                <div><h3>Sắp xếp quỹ thời gian</h3><p>Dự kiến thời gian dành cho việc học mỗi tuần và tìm hiểu lịch học, lịch thi để cân bằng với công việc, cuộc sống.</p></div>
                            </article>
                            <article>
                                <span class="readiness-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 4c4-1 6 0 9 2 3-2 5-3 9-2v15c-4-1-6 0-9 2-3-2-5-3-9-2V4zm9 2v15" /></svg></span>
                                <div><h3>Tìm hiểu kỹ chương trình</h3><p>Đối chiếu điều kiện đầu vào, nội dung đào tạo và yêu cầu học tập từ thông báo chính thức của trường trước khi lựa chọn.</p></div>
                            </article>
                        </div>
                    </div>
                    <div class="documents-card reveal">
                        <p class="eyebrow dark">HỒ SƠ CẦN CHUẨN BỊ</p>
                        <h2>Checklist hồ sơ xét tuyển</h2>
                        <ul>
                                <?php foreach ( [ 'Phiếu đăng ký xét tuyển có xác nhận', 'Bản sao công chứng văn bằng cao nhất', 'Căn cước và giấy khai sinh bản sao công chứng', '02 ảnh 4×6 có ghi thông tin ở mặt sau', 'Giấy công nhận nếu sử dụng văn bằng nước ngoài', ] as $doc ): ?>
                            <li>
                                <svg aria-hidden="true" viewBox="0 0 24 24">
                                    <path d="M5 12l4 4L19 6" />
                                </svg>
                                <?= $doc ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="documents-note">
                            <strong>Lưu ý:</strong>
                            Danh sách hồ sơ trên chỉ mang tính tham khảo. Yêu cầu hồ sơ có thể thay đổi tùy theo từng ngành học và quy định của trường. Vui lòng liên hệ tư vấn để được hướng dẫn chi tiết.
                        </p>
                    </div>
                </div>
            </section>

            <section id="quy-trinh" class="section process-tech" aria-labelledby="process-title">
                <div class="container">
                    <div class="process-heading">
                        <div class="section-heading reveal">
                            <p class="eyebrow dark">ĐỒNG HÀNH CÙNG VTS</p>
                            <h2 id="process-title">Quy trình đăng ký <span>3 bước</span></h2>
                        </div>
                        <p>Từ lựa chọn chương trình đến bắt đầu học tập, từng bước đều có hướng dẫn rõ ràng.</p>
                    </div>
                    <ol class="process-diagram">
                        <li class="process-stage reveal">
                            <div class="stage-node">
                                <span class="stage-number">01</span>
                                <span class="stage-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 4h16v12H8l-4 4V4zM8 8h8M8 12h5" /></svg></span>
                            </div>
                            <div class="stage-content"><span>Bước khởi đầu</span><h3>Đăng ký tư vấn</h3><p>Điền thông tin để được chuyên viên liên hệ.</p></div>
                        </li>
                        <li class="process-stage reveal">
                            <div class="stage-node">
                                <span class="stage-number">02</span>
                                <span class="stage-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M6 3h8l4 4v14H6zM14 3v5h4M9 12h6M9 16h6" /></svg></span>
                            </div>
                            <div class="stage-content"><span>Chuẩn bị</span><h3>Nộp hồ sơ xét tuyển</h3><p>Được hướng dẫn chuẩn bị và nộp hồ sơ.</p></div>
                        </li>
                        <li class="process-stage reveal">
                            <div class="stage-node">
                                <span class="stage-number">03</span>
                                <span class="stage-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="4" /><path d="M7 12l3 3 7-7" /></svg></span>
                            </div>
                            <div class="stage-content"><span>Hoàn tất</span><h3>Nhận kết quả & nhập học</h3><p>Hoàn tất thủ tục theo quy định của trường.</p></div>
                        </li>
                    </ol>
                </div>
            </section>

            <section id="faq" class="section">
                <div class="container faq-grid">
                    <div class="faq-intro">
                        <div class="section-heading left reveal">
                            <p class="eyebrow dark">GÓC GIẢI ĐÁP</p>
                            <h2>
                                Hiểu rõ hơn
                                <br />
                                An tâm lựa chọn
                            </h2>
                            <p>Những thông tin quan trọng bạn cần biết trước khi đăng ký.</p>
                        </div>
                        <div class="faq-contact">
                            <span>Bạn cần tư vấn thêm?</span>
                            <a href="<?= htmlspecialchars( $config['hotline_href']) ?>">
                                <?= htmlspecialchars( $config['hotline']) ?>
                                <span aria-hidden="true">↗</span>
                            </a>
                            <small>
                                Miễn phí ·
                                <?= htmlspecialchars( $config['consulting_hours']) ?>
                            </small>
                        </div>
                    </div>
                    <div class="faq-list">
                        <?php foreach ($faqs as $i => [$question, $answer]): ?>
                        <article class="faq-item">
                            <h3>
                                <button
                                    type="button"
                                    aria-expanded="false"
                                    aria-controls="faq-panel-<?= $i ?>"
                                >
                                    <span><?= htmlspecialchars( $question) ?></span>
                                    <svg aria-hidden="true" viewBox="0 0 24 24">
                                        <path d="M6 9l6 6 6-6" />
                                    </svg>
                                </button>
                            </h3>
                            <div id="faq-panel-<?= $i ?>" class="faq-panel" hidden>
                                <p><?= htmlspecialchars( $answer) ?></p>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <section id="dang-ky" class="section final-cta section-dark">
                <div class="container cta-grid">
                    <div class="cta-copy reveal">
                        <p class="eyebrow">03 / BƯỚC TIẾP THEO CỦA BẠN</p>
                        <h2>
                            Một cuộc trò chuyện
                            <br />
                            <span>Mở thêm nhiều cơ hội</span>
                        </h2>
                        <p>
                            Để VTS giúp bạn tìm chương trình phù hợp với mục tiêu và quỹ thời gian của mình.
                        </p>
                        <div class="cta-assurance">
                            <span>Tư vấn miễn phí</span>
                            <span>Thông tin rõ ràng</span>
                            <span>Hỗ trợ tận tâm</span>
                        </div>
                    </div>
                    <form
                        class="lead-form"
                        id="lead-form"
                        action="#dang-ky"
                        method="post"
                        novalidate
                    >
                        <div class="form-heading">
                            <span>Tư vấn miễn phí</span>
                            <h2>Chọn chương trình phù hợp với bạn</h2>
                        </div>
                        <div class="form-status" role="status" aria-live="polite" tabindex="-1"></div>
                        <label>
                            Họ và tên
                            <span aria-hidden="true">*</span>
                            <input
                                type="text"
                                name="name"
                                autocomplete="name"
                                required
                                placeholder="Nhập họ và tên"
                            />
                        </label>
                        <label>
                            Số điện thoại
                            <span aria-hidden="true">*</span>
                            <input
                                type="tel"
                                name="phone"
                                autocomplete="tel"
                                inputmode="numeric"
                                required
                                placeholder="Ví dụ: 0964380333"
                            />
                            <small class="field-error"></small>
                        </label>
                        <label class="program-field">
                            Ngành quan tâm
                            <select id="program-select" name="program">
                                <option value="">Chọn ngành học</option>
                                <?php foreach ( $formPrograms as $formProgram ): ?>
                                <option value="<?= htmlspecialchars($formProgram['name']) ?>"><?= htmlspecialchars($formProgram['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label class="consent">
                            <input type="checkbox" name="consent" value="1" required />
                            <span>Tôi đồng ý để VTS xử lý thông tin nhằm tư vấn chương trình phù hợp.</span>
                        </label>
                        <div class="honeypot" aria-hidden="true">
                            <label>
                                Website
                                <input type="text" name="website" tabindex="-1" autocomplete="off" />
                            </label>
                        </div>
                        <button class="btn btn-gold btn-full" type="submit">Đăng ký ngay</button>
                        <p class="privacy-note">
                            <svg aria-hidden="true" viewBox="0 0 24 24">
                                <path d="M7 10V7a5 5 0 0110 0v3M5 10h14v10H5z" />
                            </svg>
                            Cam kết bảo mật thông tin cá nhân
                        </p>
                    </form>
                </div>
            </section>
        </main>

        <aside class="program-drawer" id="program-drawer" aria-hidden="true">
            <div class="drawer-scrim" data-close-drawer></div>
            <div
                class="drawer-panel"
                role="dialog"
                aria-modal="true"
                aria-labelledby="drawer-title"
                tabindex="-1"
            >
                <button class="drawer-close" type="button" aria-label="Đóng chi tiết" data-close-drawer>
                    <svg aria-hidden="true" viewBox="0 0 24 24">
                        <path d="M6 6l12 12M18 6L6 18" />
                    </svg>
                </button>
                <div id="drawer-content"></div>
            </div>
        </aside>
        <div class="floating-actions">
            <button id="back-to-top" type="button" aria-label="Lên đầu trang">
                <svg aria-hidden="true" viewBox="0 0 24 24">
                    <path d="M6 15l6-6 6 6" />
                </svg>
            </button>
        </div>
        <?php require __DIR__ . '/components/footer.php'; ?>
        <script id="program-data" type="application/json">
            <?= json_encode( $programJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?>
        </script>
        <script src="assets/js/registration-config.js?v=20261028" defer></script>
        <?php if (is_file(__DIR__ . '/assets/js/registration-config.local.js')): ?>
        <script src="assets/js/registration-config.local.js?v=20261028" defer></script>
        <?php endif; ?>
        <script src="assets/js/main.js?v=20261120-7" defer></script>
    </body>
</html>
