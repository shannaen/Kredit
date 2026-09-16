<?php
// Kalkulator kredit mobil
// Bunga tetap 20% dari harga mobil
// Angsuran = ((harga + bunga) - DP) / jumlah bulan

$harga_mobil = "";
$dp_persen   = 20;
$tenor_tahun = 3;
$hasil       = null;
$error       = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $harga_mobil_raw = str_replace(['.', ','], '', $_POST['harga_mobil'] ?? '0');
    $harga_mobil     = (float) $harga_mobil_raw;
    $dp_persen       = (int) ($_POST['dp_persen'] ?? 0);
    $tenor_tahun     = (int) ($_POST['tenor_tahun'] ?? 0);

    if ($harga_mobil <= 0) {
        $error = "Harga mobil harus diisi dan lebih dari 0.";
    } else {
        $bunga             = $harga_mobil * 0.20;
        $nominal_dp        = $harga_mobil * ($dp_persen / 100);
        $total_harga_bunga = $harga_mobil + $bunga;
        $jumlah_pinjaman   = $total_harga_bunga - $nominal_dp;
        $jumlah_bulan      = $tenor_tahun * 12;
        $angsuran_bulan    = $jumlah_pinjaman / $jumlah_bulan;

        $hasil = [
            'harga_mobil'       => $harga_mobil,
            'dp_persen'         => $dp_persen,
            'nominal_dp'        => $nominal_dp,
            'bunga'             => $bunga,
            'total_harga_bunga' => $total_harga_bunga,
            'jumlah_pinjaman'   => $jumlah_pinjaman,
            'tenor_tahun'       => $tenor_tahun,
            'jumlah_bulan'      => $jumlah_bulan,
            'angsuran_bulan'    => $angsuran_bulan,
        ];
    }
}

function rupiah($angka) {
    return "Rp " . number_format((float) $angka, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Auto Kredit - Simulasi Kredit Mobil</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body{ background:#F5F5F5; font-family: 'Segoe UI', Arial, sans-serif; }
    .navbar-brand{ font-weight:700; }
    .hero{
        background: linear-gradient(135deg,#e9ecef,#dee2e6);
        border-radius: 10px;
        padding: 60px 40px;
        margin: 24px 0;
    }
    .hero h1{ font-weight:700; }
    .card-kalkulator{ border-radius:10px; }
    .angsuran-box{
        background:#f1f3f5; border-radius:8px; padding:20px; text-align:center; margin-bottom:20px;
    }
    .angsuran-box .angka{ font-size:32px; font-weight:700; color:#343a40; }
    table.tabel-hasil td{ padding:8px 4px; }
    footer{ background:#343a40; color:#ced4da; padding:36px 0 18px 0; margin-top:40px; }
    footer h6{ color:#fff; }
    footer a{ color:#ced4da; text-decoration:none; }
    .hero-img{
        height: 280px;
        object-fit: contain;
        background:#e9ecef;
        border-radius: 10px;
        padding: 10px;
    }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="#beranda">🚗 AUTO KREDIT</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navMenu">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="#beranda">Beranda</a></li>
        <li class="nav-item"><a class="nav-link" href="#tentang">Tentang Perusahaan</a></li>
        <li class="nav-item"><a class="nav-link" href="#kontak">Kontak Perusahaan</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">

  <section id="beranda" class="hero">
    <div class="row align-items-center g-4">
      <div class="col-md-6">
        <h1>Dapatkan Mobil Impian Anda</h1>
        <p class="text-muted">Hitung cicilan kredit mobil dengan mudah, cepat, dan transparan.</p>
        <a href="#kalkulator" class="btn btn-dark">Hitung Sekarang</a>
      </div>
      <div class="col-md-6">
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner rounded">
            <div class="carousel-item active">
              <img src="assets/mobil1.png" class="d-block w-100 hero-img" alt="Mobil 1">
            </div>
            <div class="carousel-item">
              <img src="assets/mobil2.png" class="d-block w-100 hero-img" alt="Mobil 2">
            </div>
            <div class="carousel-item">
              <img src="assets/mobil3.png" class="d-block w-100 hero-img" alt="Mobil 3">
            </div>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>
        </div>
      </div>
    </div>
  </section>

  <section id="kalkulator" class="row g-4 mb-5">

    <div class="col-md-6">
      <div class="card card-kalkulator shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title mb-4">Hitung Kredit Mobil</h5>

          <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <form method="post" action="#kalkulator">
            <div class="mb-3">
              <label class="form-label">Harga Mobil (Rp)</label>
              <input type="text" name="harga_mobil" class="form-control"
                     placeholder="Masukkan harga mobil"
                     value="<?= htmlspecialchars($harga_mobil ?: '') ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label">DP (Persen)</label>
              <select name="dp_persen" class="form-select">
                <?php foreach ([10,20,30,40,50,60] as $opsi): ?>
                  <option value="<?= $opsi ?>" <?= $dp_persen == $opsi ? 'selected' : '' ?>>
                    <?= $opsi ?>%
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Tenor (Tahun)</label>
              <select name="tenor_tahun" class="form-select">
                <?php foreach ([1,2,3,4,5] as $opsi): ?>
                  <option value="<?= $opsi ?>" <?= $tenor_tahun == $opsi ? 'selected' : '' ?>>
                    <?= $opsi ?> Tahun
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="alert alert-secondary small">Bunga tetap 20% dari harga mobil.</div>

            <button type="submit" class="btn btn-dark w-100">Hitung Angsuran</button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card card-kalkulator shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title mb-4">Hasil Perhitungan</h5>

          <?php if ($hasil): ?>
            <div class="angsuran-box">
              <div class="text-muted small">Angsuran Per Bulan</div>
              <div class="angka"><?= rupiah($hasil['angsuran_bulan']) ?></div>
            </div>

            <table class="table table-sm tabel-hasil">
              <tr><td>Harga Mobil</td><td class="text-end"><?= rupiah($hasil['harga_mobil']) ?></td></tr>
              <tr><td>DP (Persen)</td><td class="text-end"><?= $hasil['dp_persen'] ?>%</td></tr>
              <tr><td>Nominal DP</td><td class="text-end"><?= rupiah($hasil['nominal_dp']) ?></td></tr>
              <tr><td>Bunga (20% dari harga mobil)</td><td class="text-end"><?= rupiah($hasil['bunga']) ?></td></tr>
              <tr><td>Total Harga + Bunga</td><td class="text-end"><?= rupiah($hasil['total_harga_bunga']) ?></td></tr>
              <tr><td>Jumlah Pinjaman (Total - DP)</td><td class="text-end"><?= rupiah($hasil['jumlah_pinjaman']) ?></td></tr>
              <tr><td>Tenor (Tahun)</td><td class="text-end"><?= $hasil['tenor_tahun'] ?> Tahun</td></tr>
              <tr><td>Jumlah Bulan (Tenor x 12)</td><td class="text-end"><?= $hasil['jumlah_bulan'] ?> Bulan</td></tr>
              <tr class="fw-bold"><td>Angsuran Per Bulan</td><td class="text-end"><?= rupiah($hasil['angsuran_bulan']) ?></td></tr>
            </table>
          <?php else: ?>
            <p class="text-muted">Isi form di sebelah kiri lalu klik <strong>Hitung Angsuran</strong> untuk melihat hasil perhitungan.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <section id="tentang" class="mb-5">
    <h4>Tentang Perusahaan</h4>
    <p class="text-muted">
      Auto Kredit adalah penyedia layanan simulasi kredit mobil yang membantu calon pembeli
      menghitung estimasi angsuran bulanan sebelum mengajukan pembiayaan. Kami hadir untuk
      membuat proses perhitungan cicilan menjadi lebih mudah, cepat, dan transparan bagi
      siapa saja yang ingin mewujudkan mobil impiannya.
    </p>
  </section>

  <section id="kontak" class="mb-5">
    <h4>Kontak Perusahaan</h4>
    <ul class="list-unstyled text-muted">
      <li>Jl. Merdeka No. 123, Jakarta, Indonesia</li>
      <li>(021) 1234 5678</li>
      <li>info@autokredit.co.id</li>
      <li>www.autokredit.co.id</li>
    </ul>
  </section>

</div>

<footer>
  <div class="container">
    <div class="row">
      <div class="col-md-4 mb-3">
        <h6>AUTO KREDIT</h6>
        <p class="small">Kami membantu Anda mewujudkan mobil impian dengan cicilan yang ringan dan proses mudah.</p>
      </div>
      <div class="col-md-4 mb-3">
        <h6>Tentang Perusahaan</h6>
        <p class="small">Layanan simulasi kredit mobil terpercaya untuk membantu keputusan finansial Anda.</p>
      </div>
      <div class="col-md-4 mb-3">
        <h6>Kontak Perusahaan</h6>
        <p class="small mb-1">Jl. Merdeka No. 123, Jakarta, Indonesia</p>
        <p class="small mb-1">(021) 1234 5678</p>
        <p class="small mb-1">info@autokredit.co.id</p>
        <p class="small">www.autokredit.co.id</p>
      </div>
    </div>
    <hr class="border-secondary">
    <p class="small text-center mb-0">&copy; 2026 Auto Kredit. All rights reserved.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
