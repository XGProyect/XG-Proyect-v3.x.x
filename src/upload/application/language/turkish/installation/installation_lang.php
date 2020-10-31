<?php
$lang = [
    // messages
    'ins_no_server_requirements' => 'Sunucunuz / barındırmanız, XG Project\'i çalıştırmak için gereken minimum gereksinimleri karşılamıyor.<br /><br />Gereksinimler: <br />- PHP 7.3+<br />- MySQL 5.5+',
    'ins_not_writable' => 'Kuruluma devam etmek için application/config dizinine yazma izni (chmod 777) sağlamalısınız.',
    'ins_already_installed' => 'XG Proyect zaten kurulu. Bir seçenek seçin: <br /><br /> - <a href="../admin.php?page=update">Güncelleme</a> <br /> - <a href="../admin.php?page=migrate">Göç</a> <br /> - <a href="../">Oyuna dön</a> <br /><br />Herhangi bir işlem yapmak istemiyorsanız, güvenlik için kurulum dizinini <span style="color:red;text-decoration:underline;">SİLMENİZİ</span> öneririz.',

    // error headers
    'ins_error_title' => 'Uyarı!',
    'ins_warning_title' => 'Dikkat!',
    'ins_ok_title' => 'Tamam!',

    // navigation bar
    'ins_overview' => 'Genel Bakış',
    'ins_license' => 'Lisans',
    'ins_install' => 'Yükle',
    'ins_language_select' => 'Dil Seçin',

    // overview page
    'ins_install_title' => 'Kurulum',
    'ins_title' => 'Giriş',
    'ins_welcome' => 'XG Projesi\'ne hoş geldiniz!',
    'ins_welcome_first_line' => 'XG Proyect, etraftaki en iyi OGame klonlarıdır. XG Proyect 3, daha önce hiç geliştirilmemiş en yeni ve en sabit pakettir. Diğer tüm sürümlerde olduğu gibi, XG Proyect, Xtreme-gameZ olarak bilinen ekipten destek alır ve her zaman en iyi kalitede bakımı ve sürümün istikrarını sağlar. XG Proyect 3, gün geçtikçe geleceğe bakıyor ve büyüme, istikrar, esneklik, dinamizm, kalite ve kullanıcı güveni arıyor. Her zaman XG Proyect\'in beklentilerinizden daha iyi olmasını bekliyoruz.',
    'ins_welcome_second_line' => 'Kurulum sistemi, kurulum veya önceki bir sürümden en son sürüme yükseltme sırasında size rehberlik edecektir. Şüpheler, sorunlar veya sorular için, <a href="https://www.xgproyect.org/"><em>destek ve geliştirme topluluğumuzu</em></a> görmekten çekinmeyin..',
    'ins_welcome_third_line' => 'XG Proyect bir Açık Kaynak projesidir, lisans özelliklerini görmek için ana menüdeki lisansın üzerine tıklayınız. Yüklemeyi başlatmak için yükle butonuna tıklayınız, güncellemek veya geçmek için ADMIN CP’ye giriş yapınız.',
    'ins_install_license' => 'Lisans',

    // installation - general
    'ins_steps' => 'Adımlar',
    'ins_step1' => 'Bağlantı verileri',
    'ins_step2' => 'Bağlantıyı kontrol et',
    'ins_step3' => 'Yapılandırma dosyası',
    'ins_step4' => 'Veri ekle',
    'ins_step5' => 'Yönetici oluştur',
    'ins_continue' => 'Devam Et',

    // installation - step 1
    'ins_connection_data_title' => 'Veritabanına bağlanacak veriler',
    'ins_server_title' => 'SQL Server:',
    'ins_db_title' => 'Veritabanı:',
    'ins_user_title' => 'Kullanıcı:',
    'ins_password_title' => 'Şifre:',
    'ins_prefix_title' => 'Tablolar öneki:',
    'ins_ex_tag' => 'Ör:',
    'ins_install_go' => 'Yükle',

    // installation - errors
    'ins_not_connected_error' => 'Girilen verilerle veritabanına bağlanılamıyor.',
    'ins_db_not_exists' => 'Verilen adla veritabanına erişilemiyor.',
    'ins_empty_fields_error' => 'Tüm alanlar zorunludur',
    'ins_write_config_error' => 'Config.php dosyası yazılırken hata oluştu, dosyanın 777 CHMOD (yazma izinleri) olduğundan veya dosyanın mevcut olduğundan emin olun',
    'ins_insert_tables_error' => 'Veritabanına veri eklenemedi, veritabanını kontrol edin veya sunucunun aktif olup olmadığını kontrol edin.',

    // installation -  step 2
    'ins_done_config' => 'config.php dosyası başarıyla yapılandırıldı.',
    'ins_done_connected' => 'Bağlantı başarıyla kuruldu.',
    'ins_done_insert' => 'Temel veriler başarıyla eklendi.',

    // installation - step 3
    'ins_admin_create_title' => 'Yeni yönetici hesabı',
    'ins_admin_create_user' => 'Kullanıcı:',
    'ins_admin_create_pass' => 'Şifre:',
    'ins_admin_create_email' => 'E-Posta Adresi:',
    'ins_admin_create_create' => 'Oluştur',

    // installation - errors
    'ins_adm_empty_fields_error' => 'Tüm alanlar zorunludur',
    'ins_adm_invalid_email_address' => 'Lütfen geçerli bir e-posta adresi belirtin',

    // installation - step 4
    'ins_completed' => 'KURULUM TAMAMLANDI!',
    'ins_admin_account_created' => 'Yönetici başarıyla oluşturdu!',
    'ins_delete_install' => 'Güvenlik risklerini önlemek için <i>install</i> dizinini silmeniz gerekir!',
    'ins_end' => 'Tamamlandı',
];

/* end of installation_lang.php */
