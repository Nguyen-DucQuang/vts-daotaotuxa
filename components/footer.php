<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <img
                class="footer-official-logo"
                src="assets/images/logo-vts.webp"
                alt="Logo VTS"
                width="160"
                height="145"
            />
            <h2>VTS</h2>
            <p>Đơn vị tư vấn và phối hợp tuyển sinh chương trình đại học từ xa cùng các trường đại học.</p>
            <p>469 Lê Hồng Phong, Phường Vườn Lài, TP HCM</p>
        </div>
        <div class="footer-contact">
            <h2>LIÊN HỆ</h2>
            <p class="footer-phone">
                Hotline:
                <a href="<?= htmlspecialchars( $config['hotline_href']) ?>"><?= htmlspecialchars( $config['hotline']) ?></a>
                <button type="button" class="copy-hotline" data-copy-phone="0964380333">Sao chép</button>
            </p>
            <p>
                Tư vấn
                <?= htmlspecialchars($config['consulting_hours']) ?>, tất cả các ngày
            </p>
            <a href="https://vt.edu.vn/" target="_blank" rel="noopener noreferrer">www.vt.edu.vn</a>
            <span class="copy-status" role="status" aria-live="polite"></span>
        </div>
        <div class="footer-connect">
            <h2>THEO DÕI</h2>
            <nav class="footer-social-links" aria-label="Kênh thông tin của Trường Trung Cấp Vạn Tường">
                <a href="https://www.facebook.com/truongtrungcapvantuong.vt" target="_blank" rel="noopener noreferrer">Facebook</a>
                <span aria-hidden="true">·</span>
                <a href="https://zalo.me/2979259251784093657" target="_blank" rel="noopener noreferrer">Zalo</a>
                <span aria-hidden="true">·</span>
                <a href="https://vt.edu.vn/" target="_blank" rel="noopener noreferrer">Website</a>
                <span aria-hidden="true">·</span>
                <a href="https://www.tiktok.com/@vt.edu.vn" target="_blank" rel="noopener noreferrer">TikTok</a>
            </nav>
        </div>
    </div>
    <div class="container footer-bottom" id="chinh-sach-du-lieu">
        <span>© 2026 VTS · Trường Trung cấp Vạn Tường.</span>
        <a href="#chinh-sach-du-lieu">Chính sách bảo vệ dữ liệu cá nhân</a>
    </div>
    <div class="container footer-data-note">
        <h3>Thông tin đăng ký được sử dụng thế nào?</h3>
        <p>
            VTS sử dụng họ tên, số điện thoại, trường và ngành học bạn lựa chọn để liên hệ xác nhận nhu cầu và tư vấn chương trình phù hợp.
            Liên hệ hotline trên để hỏi về thông tin đã cung cấp. Nội dung này là thông tin tóm tắt; chính sách đầy đủ cần được VTS xác nhận trước khi triển khai đăng ký.
        </p>
    </div>
</footer>
