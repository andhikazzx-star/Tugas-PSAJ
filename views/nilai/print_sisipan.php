<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rapor Sisipan</title>

    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12px;
            margin: 20px;
            color: #000;
        }

        .container {
            width: 900px;
            margin: auto;
        }

        /* HEADER */
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
            position: relative;
        }

        .header img {
            position: absolute;
            left: 0;
            top: 0;
            width: 70px;
        }

        .header h1 {
            margin: 0;
            font-size: 14px;
        }

        .header p {
            margin: 2px;
            font-size: 11px;
        }

        /* INFO SISWA */
        .info-siswa {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-siswa td {
            padding: 3px;
        }

        /* TABEL */
        table {
            border-collapse: collapse;
            width: 100%;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        .report-table th {
            font-weight: bold;
        }

        .text-left {
            text-align: left;
        }

        .category-row td {
            font-weight: bold;
            text-align: left;
        }

        /* CATATAN */
        .section {
            margin-top: 15px;
        }

        .box {
            border: 1px solid #000;
            min-height: 70px;
            padding: 8px;
        }

        /* FOOTER */
        .footer {
            margin-top: 30px;
            width: 100%;
        }

        .footer td {
            text-align: center;
            padding: 10px;
        }

        @media print {
            body {
                margin: 0;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <!-- HEADER -->
    <div class="header">
        <img src="<?= asset('img/logo.png') ?>" style="width: 70px;">
        <h1>PEMERINTAH PROVINSI JAWA TIMUR</h1>
        <h1>DINAS PENDIDIKAN</h1>
        <h1>SMK NEGERI 10 SURABAYA</h1>
        <p>Jl. Keputih Tegal - Surabaya</p>
    </div>

    <h3 style="text-align:center; text-decoration: underline;">
        LAPORAN HASIL BELAJAR TENGAH SEMESTER
    </h3>

    <!-- DATA SISWA -->
    <table class="info-siswa">
        <tr>
            <td>Nama Siswa</td><td>:</td>
            <td><b><?= $data['siswa']['nama'] ?></b></td>

            <td>Kelas</td><td>:</td>
            <td><?= $data['siswa']['kelas_nama'] ?></td>
        </tr>
        <tr>
            <td>NIS</td><td>:</td>
            <td><?= $data['siswa']['nis'] ?></td>

            <td>Konsentrasi Keahlian</td><td>:</td>
            <td><?= $data['siswa']['jurusan_nama'] ?></td>
        </tr>
    </table>

    <!-- TABEL NILAI -->
    <table class="report-table">

        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">MATA PELAJARAN</th>
                <th rowspan="2">KKTP</th>
                <th colspan="4">NILAI</th>
                <th rowspan="2">S</th>
                <th rowspan="2">I</th>
                <th rowspan="2">A</th>
            </tr>
            <tr>
                <th>SUM I</th>
                <th>SUM II</th>
                <th>SUM III</th>
                <th>PTS</th>
            </tr>
        </thead>

        <tbody>

            <!-- KELOMPOK A -->
            <tr class="category-row">
                <td colspan="10">A. KELOMPOK MATA PELAJARAN UMUM</td>
            </tr>

            <?php foreach ($data['grades_umum'] as $i => $g): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td class="text-left"><?= $g['mapel'] ?></td>
                <td><?= $g['kkm'] ?></td>
                <td><?= $g['s1'] ?></td>
                <td><?= $g['s2'] ?></td>
                <td><?= $g['s3'] ?></td>
                <td><?= $g['pts'] ?></td>
                <td><?= $g['sakit'] ?></td>
                <td><?= $g['izin'] ?></td>
                <td><?= $g['alfa'] ?></td>
            </tr>
            <?php endforeach; ?>

            <!-- KELOMPOK B -->
            <tr class="category-row">
                <td colspan="10">B. MUATAN KEWILAYAHAN</td>
            </tr>

            <?php foreach ($data['grades_wilayah'] as $i => $g): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td class="text-left"><?= $g['mapel'] ?></td>
                <td><?= $g['kkm'] ?></td>
                <td><?= $g['s1'] ?></td>
                <td><?= $g['s2'] ?></td>
                <td><?= $g['s3'] ?></td>
                <td><?= $g['pts'] ?></td>
                <td><?= $g['sakit'] ?></td>
                <td><?= $g['izin'] ?></td>
                <td><?= $g['alfa'] ?></td>
            </tr>
            <?php endforeach; ?>

        </tbody>
    </table>

    <!-- CATATAN -->
    <table class="report-table section">
        <tr>
            <td style="width:50%;">
                <b>CATATAN BP / BK :</b><br><br>
                <?= $data['catatan'] ?>
            </td>
            <td>
                <b>CATATAN WALI KELAS :</b><br><br>
                <?= $data['sikap'] ?>
            </td>
        </tr>
    </table>

    <!-- FOOTER -->
    <table class="footer">
        <tr>
            <td></td>
            <td></td>
            <td>Surabaya, <?= date('d-m-Y') ?></td>
        </tr>
        <tr>
            <td>Orang Tua / Wali</td>
            <td></td>
            <td>Wali Kelas</td>
        </tr>
        <tr style="height:80px;">
            <td></td><td></td><td></td>
        </tr>
        <tr>
            <td>....................</td>
            <td></td>
            <td><b><?= $data['wali']['nama'] ?></b></td>
        </tr>
    </table>

</div>

</body>
</html>