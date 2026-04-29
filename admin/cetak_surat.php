    <?php
    // File: admin/cetak_surat.php - Cetak Surat Pengajuan (Tanpa Garis Bawah Berlebih)
    session_start();
    if(!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
        die("Silakan login terlebih dahulu");
    }
    include '../koneksi.php';

    $id = $_GET['id'];
    $jenis = $_GET['jenis'];

    $query = "SELECT * FROM pengajuan WHERE id = '$id'";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);

    if(!$data) die("Data tidak ditemukan");

    // Ambil data penduduk untuk alamat
    $no_kk = '';
    $query_kk = "SELECT no_kk, alamat, rt, rw, desa_kelurahan, kecamatan FROM penduduk WHERE nik = '{$data['nik_warga']}'";
    $result_kk = mysqli_query($conn, $query_kk);
    if(mysqli_num_rows($result_kk) > 0) {
        $data_kk = mysqli_fetch_assoc($result_kk);
        $no_kk = $data_kk['no_kk'];
    }
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Cetak Surat - SILAMPID</title>
        <style>
            body {
                font-family: 'Times New Roman', Times, serif;
                padding: 20mm;
                margin: 0;
                background: white;
            }
            .kop {
                text-align: center;
                border-bottom: 2px solid #000;
                padding-bottom: 10px;
                margin-bottom: 20px;
            }
            .kop h1 {
                margin: 0;
                font-size: 24px;
            }
            .kop h2 {
                margin: 5px 0;
                font-size: 18px;
            }
            .kop p {
                margin: 0;
                font-size: 12px;
            }
            .title {
                text-align: center;
                font-size: 18px;
                font-weight: bold;
                margin: 20px 0;
            }
            .info-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            .info-table tr:last-child td {
                border-bottom: none;
            }
            .info-table td {
                padding: 8px;
            }
            .info-table td:first-child {
                font-weight: bold;
                width: 120px;
            }
            .footer {
                margin-top: 40px;
                text-align: right;
            }
            button {
                margin: 10px;
                padding: 10px 20px;
                background: #27ae60;
                color: white;
                border: none;
                cursor: pointer;
                font-family: Arial, sans-serif;
                border-radius: 5px;
            }
            button:hover {
                background: #219a52;
            }
            @media print {
                button {
                    display: none;
                }
                body {
                    padding: 0;
                }
                .info-table tr:last-child td {
                    border-bottom: none;
                }
            }
        </style>
    </head>
    <body>
        <button onclick="window.print()"><i class="fas fa-print"></i> Cetak / Simpan PDF</button>
        <button onclick="window.close()">Tutup</button>
        
        <div class="kop">
            <h1>PEMERINTAH KABUPATEN PAKPAK BHARAT</h1>
            <h2>DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL</h2>
            <p>Alamat: Jl. Kantor Dukcapil, Kab. Pakpak Bharat</p>
            <p>Email: dukcapil@pakpakbharatkab.go.id | Telp: (0627) 123456</p>
        </div>
        
        <div class="title">
            SURAT KETERANGAN <?php echo strtoupper($jenis); ?>
        </div>
        
        <p style="margin-bottom: 15px;">Yang bertanda tangan di bawah ini, Kepala Dinas Kependudukan dan Pencatatan Sipil Kabupaten Pakpak Bharat, menerangkan bahwa:</p>
        
        <table class="info-table">
            <tr><td>NIK</td><td>: <?php echo $data['nik_warga']; ?></td></tr>
            <tr><td>No Kartu Keluarga</td><td>: <?php echo $no_kk ?: '-'; ?></td></tr>
            <tr><td>Nama Lengkap</td><td>: <?php echo htmlspecialchars($data['nama_warga']); ?></td></tr>
            <tr><td>Jenis Surat</td><td>: <?php echo $data['jenis_surat']; ?></td></tr>
            <tr><td>Tanggal Pengajuan</td><td>: <?php echo date('d/m/Y', strtotime($data['tgl_pengajuan'])); ?></td></tr>
            <tr><td>Status</td><td>: <?php echo $data['status']; ?></td></tr>
            <tr><td>Kode Unik</td><td>: <?php echo $data['kode_unik']; ?></td></tr>
        </table>
        
        <p style="margin: 20px 0;">Telah mengajukan permohonan surat keterangan dan dinyatakan <strong>SAH/TELAH DIPROSES</strong> sesuai dengan ketentuan yang berlaku.</p>
        
        <p>Demikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
        
        <div class="footer">
            <p>Pakpak Bharat, <?php echo date('d F Y'); ?></p>
            <p>a.n. KEPALA DINAS DUKCAPIL</p>
            <p>Kepala Bidang Pelayanan</p>
            <br><br><br>
            <p><u>DRA. HJ. SITI NURJANNAH, M.Si</u></p>
            <p>NIP. 19750812 200312 2 001</p>
        </div>
        
        <div style="margin-top: 20px; font-size: 10px; text-align: center;">
            <p>Dicetak pada: <?php echo date('d/m/Y H:i:s'); ?> oleh: <?php echo $_SESSION['nama_lengkap']; ?> (<?php echo $_SESSION['role']; ?>)</p>
        </div>
    </body>
    </html>