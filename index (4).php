<?php
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
<link href="assets/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root{
        --glass-bg: rgba(255,255,255,0.07);
        --glass-border: rgba(255,255,255,0.16);
        --accent: #4fa3ff;
        --accent2: #7ee0ff;
        --panel: #0e1c40;
    }

    *{ font-family:'Poppins', sans-serif; }

    body{
        min-height:100vh;
        margin:0;
        background: #060c1f;
        background-image:
            radial-gradient(circle at 12% 15%, rgba(79,163,255,0.35), transparent 40%),
            radial-gradient(circle at 85% 10%, rgba(126,224,255,0.25), transparent 45%),
            radial-gradient(circle at 50% 90%, rgba(79,110,255,0.3), transparent 45%),
            linear-gradient(160deg, #050a1a 0%, #0b1638 55%, #06122c 100%);
        color: #eaf2ff;
    }

    a{ text-decoration:none; }

    .glass{
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        border-radius: 20px;
    }

    .navbar{
        background: rgba(6,12,31,0.55) !important;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-bottom: 1px solid var(--glass-border);
    }
    .navbar-brand{ font-weight:800; letter-spacing:.5px; color:#fff !important; }
    .navbar-brand span{ color: var(--accent2); }
    .nav-link{ color: rgba(234,242,255,0.75) !important; font-weight:500; }
    .nav-link:hover{ color:#fff !important; }

    .hero{ padding: 46px; margin: 28px 0; }
    .hero h1{
        font-weight:800;
        font-size:2.4rem;
        line-height:1.15;
        background: linear-gradient(90deg,#ffffff,#bfe3ff);
        -webkit-background-clip:text;
        background-clip:text;
        color:transparent;
    }
    .hero p{ color: rgba(234,242,255,0.7); }
    .btn-glow{
        background: linear-gradient(120deg, var(--accent), var(--accent2));
        border:none;
        color:#04122b;
        font-weight:700;
        padding:.65rem 1.5rem;
        border-radius:12px;
        box-shadow: 0 8px 24px rgba(79,163,255,0.35);
    }
    .btn-glow:hover{ color:#04122b; filter:brightness(1.08); }

    .hero-carousel{ position:relative; z-index:1; }
    .hero-img{
        height: 280px;
        width:100%;
        object-fit: contain;
        background: rgba(255,255,255,0.05);
        border-radius: 16px;
        padding: 10px;
    }
    .hero-carousel .carousel-control-prev,
    .hero-carousel .carousel-control-next{
        width: 46px;
        height: 46px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(6,12,31,0.55);
        border: 1px solid var(--glass-border);
        border-radius: 50%;
        opacity: 1;
        z-index: 10;
    }
    .hero-carousel .carousel-control-prev{ left: 14px; }
    .hero-carousel .carousel-control-next{ right: 14px; }
    .hero-carousel .carousel-control-prev:hover,
    .hero-carousel .carousel-control-next:hover{ background: rgba(79,163,255,0.35); }
    .hero-carousel .carousel-control-prev-icon,
    .hero-carousel .carousel-control-next-icon{ width: 18px; height: 18px; }

    .kalkulator-card{ padding: 32px; }
    .kalkulator-card h5{ font-weight:700; color:#fff; }

    .form-label{ color: rgba(234,242,255,0.75); font-weight:500; font-size:.9rem; }
    .form-control{
        background: rgba(255,255,255,0.06);
        border: 1px solid var(--glass-border);
        color: #fff;
        border-radius: 10px;
        padding: .6rem .9rem;
        width: 100%;
    }
    .form-control::placeholder{ color: rgba(234,242,255,0.35); }
    .form-control:focus{
        background: rgba(255,255,255,0.09);
        border-color: var(--accent);
        color:#fff;
        box-shadow: 0 0 0 3px rgba(79,163,255,0.2);
        outline:none;
    }

    .alert-danger{
        background: rgba(255,80,80,0.12);
        border: 1px solid rgba(255,80,80,0.35);
        color: #ffb3b3;
        border-radius: 10px;
    }

    /* ---------- custom dropdown (ganti <select> bawaan) ---------- */
    .cs{ position:relative; }
    .cs-toggle{
        background: rgba(255,255,255,0.06);
        border: 1px solid var(--glass-border);
        color: #fff;
        border-radius: 10px;
        padding: .6rem .9rem;
        width: 100%;
        text-align:left;
        display:flex;
        justify-content:space-between;
        align-items:center;
        cursor:pointer;
    }
    .cs-toggle:after{
        content:"";
        width:8px; height:8px;
        border-right:2px solid rgba(234,242,255,0.6);
        border-bottom:2px solid rgba(234,242,255,0.6);
        transform: rotate(45deg);
        transition: transform .15s;
    }
    .cs.open .cs-toggle:after{ transform: rotate(-135deg); }
    .cs-toggle:focus{
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(79,163,255,0.2);
        outline:none;
    }
    .cs-menu{
        display:none;
        position:absolute;
        top: calc(100% + 6px);
        left:0; right:0;
        background: var(--panel);
        border: 1px solid var(--glass-border);
        border-radius: 12px;
        padding: 6px;
        z-index: 50;
        box-shadow: 0 16px 40px rgba(0,0,0,0.45);
    }
    .cs.open .cs-menu{ display:block; }
    .cs-option{
        padding: .55rem .75rem;
        border-radius: 8px;
        cursor:pointer;
        color: rgba(234,242,255,0.85);
    }
    .cs-option:hover{ background: rgba(79,163,255,0.18); color:#fff; }
    .cs-option.selected{ background: rgba(79,163,255,0.28); color:#fff; }

    .angsuran-box{
        background: linear-gradient(120deg, rgba(79,163,255,0.18), rgba(126,224,255,0.08));
        border: 1px solid var(--glass-border);
        border-radius: 14px;
        padding: 22px;
        text-align:center;
        margin-bottom: 20px;
    }
    .angsuran-box .label{ color: rgba(234,242,255,0.65); font-size:.85rem; }
    .angsuran-box .angka{ font-size:2.1rem; font-weight:800; color:#fff; }

    table.tabel-hasil{ color: #eaf2ff; }
    table.tabel-hasil td{ padding: 10px 4px; border-bottom: 1px solid rgba(255,255,255,0.08); }
    table.tabel-hasil tr:last-child td{ border-bottom:none; }

    section h4{ font-weight:700; color:#fff; }
    section p, section li{ color: rgba(234,242,255,0.68); }

    footer{
        background: rgba(6,12,31,0.6);
        backdrop-filter: blur(16px);
        border-top: 1px solid var(--glass-border);
        padding: 40px 0 20px;
        margin-top: 50px;
    }
    footer h6{ color:#fff; font-weight:700; }
    footer p, footer a{ color: rgba(234,242,255,0.6); }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="#beranda">Auto<span>Kredit</span></a>
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

  <section id="beranda" class="hero glass">
    <div class="row align-items-center g-4">
      <div class="col-md-6">
        <h1>Dapatkan Mobil Impian Anda</h1>
        <p>Hitung cicilan kredit mobil dengan mudah, cepat, dan transparan.</p>
        <a href="#kalkulator" class="btn btn-glow">Hitung Sekarang</a>
      </div>
      <div class="col-md-6">
        <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img src="assets/mobil1.png" class="d-block hero-img" alt="Mobil 1">
            </div>
            <div class="carousel-item">
              <img src="assets/mobil2.png" class="d-block hero-img" alt="Mobil 2">
            </div>
            <div class="carousel-item">
              <img src="assets/mobil3.png" class="d-block hero-img" alt="Mobil 3">
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
      <div class="glass kalkulator-card h-100">
        <h5 class="mb-4">Hitung Kredit Mobil</h5>

        <?php if ($error): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="#kalkulator" id="formKredit">
          <div class="mb-3">
            <label class="form-label">Harga Mobil (Rp)</label>
            <input type="text" name="harga_mobil" class="form-control"
                   placeholder="Masukkan harga mobil"
                   value="<?= htmlspecialchars($harga_mobil ?: '') ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">DP (Persen)</label>
            <div class="cs" data-name="dp_persen">
              <button type="button" class="cs-toggle"><?= $dp_persen ?>%</button>
              <div class="cs-menu">
                <?php foreach ([10,20,30,40,50,60] as $opsi): ?>
                  <div class="cs-option <?= $dp_persen == $opsi ? 'selected' : '' ?>" data-value="<?= $opsi ?>"><?= $opsi ?>%</div>
                <?php endforeach; ?>
              </div>
              <input type="hidden" name="dp_persen" value="<?= $dp_persen ?>">
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label">Tenor (Tahun)</label>
            <div class="cs" data-name="tenor_tahun">
              <button type="button" class="cs-toggle"><?= $tenor_tahun ?> Tahun</button>
              <div class="cs-menu">
                <?php foreach ([1,2,3,4,5] as $opsi): ?>
                  <div class="cs-option <?= $tenor_tahun == $opsi ? 'selected' : '' ?>" data-value="<?= $opsi ?>"><?= $opsi ?> Tahun</div>
                <?php endforeach; ?>
              </div>
              <input type="hidden" name="tenor_tahun" value="<?= $tenor_tahun ?>">
            </div>
          </div>

          <button type="submit" class="btn btn-glow w-100">Hitung Angsuran</button>
        </form>
      </div>
    </div>

    <div class="col-md-6">
      <div class="glass kalkulator-card h-100">
        <h5 class="mb-4">Hasil Perhitungan</h5>

        <?php if ($hasil): ?>
          <div class="angsuran-box">
            <div class="label">Angsuran Per Bulan</div>
            <div class="angka"><?= rupiah($hasil['angsuran_bulan']) ?></div>
          </div>

          <table class="table table-sm tabel-hasil">
            <tr><td>Harga Mobil</td><td class="text-end"><?= rupiah($hasil['harga_mobil']) ?></td></tr>
            <tr><td>DP (Persen)</td><td class="text-end"><?= $hasil['dp_persen'] ?>%</td></tr>
            <tr><td>Nominal DP</td><td class="text-end"><?= rupiah($hasil['nominal_dp']) ?></td></tr>
            <tr><td>Bunga</td><td class="text-end"><?= rupiah($hasil['bunga']) ?></td></tr>
            <tr><td>Total Harga + Bunga</td><td class="text-end"><?= rupiah($hasil['total_harga_bunga']) ?></td></tr>
            <tr><td>Jumlah Pinjaman</td><td class="text-end"><?= rupiah($hasil['jumlah_pinjaman']) ?></td></tr>
            <tr><td>Tenor</td><td class="text-end"><?= $hasil['tenor_tahun'] ?> Tahun</td></tr>
            <tr><td>Jumlah Bulan</td><td class="text-end"><?= $hasil['jumlah_bulan'] ?> Bulan</td></tr>
          </table>
        <?php else: ?>
          <p>Isi form di sebelah kiri lalu klik <strong>Hitung Angsuran</strong> untuk melihat hasil perhitungan.</p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <section id="tentang" class="glass mb-5" style="padding:32px;">
    <h4>Tentang Perusahaan</h4>
    <p>
      Auto Kredit adalah penyedia layanan simulasi kredit mobil yang membantu calon pembeli
      menghitung estimasi angsuran bulanan sebelum mengajukan pembiayaan. Kami hadir untuk
      membuat proses perhitungan cicilan menjadi lebih mudah, cepat, dan transparan bagi
      siapa saja yang ingin mewujudkan mobil impiannya.
    </p>
  </section>

  <section id="kontak" class="glass mb-5" style="padding:32px;">
    <h4>Kontak Perusahaan</h4>
    <ul class="list-unstyled mb-0">
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
        <h6>AutoKredit</h6>
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
    <hr style="border-color: rgba(255,255,255,0.1);">
    <p class="small text-center mb-0">© 2026 Auto Kredit. All rights reserved.</p>
  </div>
</footer>

<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
  // custom dropdown untuk DP (Persen) dan Tenor (Tahun)
  document.querySelectorAll('.cs').forEach(function (cs) {
    const toggle = cs.querySelector('.cs-toggle');
    const hidden = cs.querySelector('input[type=hidden]');
    const options = cs.querySelectorAll('.cs-option');

    toggle.addEventListener('click', function (e) {
      e.stopPropagation();
      document.querySelectorAll('.cs.open').forEach(function (other) {
        if (other !== cs) other.classList.remove('open');
      });
      cs.classList.toggle('open');
    });

    options.forEach(function (opt) {
      opt.addEventListener('click', function () {
        options.forEach(function (o) { o.classList.remove('selected'); });
        opt.classList.add('selected');
        toggle.textContent = opt.textContent;
        hidden.value = opt.dataset.value;
        cs.classList.remove('open');
      });
    });
  });

  document.addEventListener('click', function () {
    document.querySelectorAll('.cs.open').forEach(function (cs) {
      cs.classList.remove('open');
    });
  });
</script>
</body>
</html>
