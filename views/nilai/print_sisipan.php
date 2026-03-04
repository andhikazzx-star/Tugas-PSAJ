<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rapor Sisipan -
        <?= e($data['siswa']['nama']) ?>
    </title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 800px;
            margin: 20px auto;
            padding: 30px;
            border: 1px solid #ccc;
            background: #fff;
        }

        /* Header */
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
            width: 80px;
        }

        .header h1 {
            font-size: 16pt;
            margin: 0;
            text-transform: uppercase;
        }

        .header h2 {
            font-size: 14pt;
            margin: 5px 0;
            text-transform: uppercase;
        }

        .header p {
            font-size: 10pt;
            margin: 2px 0;
        }

        /* Student Info Table */
        .info-siswa {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-siswa td {
            padding: 2px 5px;
            vertical-align: top;
        }

        .info-siswa .label {
            width: 180px;
        }

        .info-siswa .separator {
            width: 10px;
        }

        /* Report Table */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        .report-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .report-table .text-left {
            text-align: left;
        }

        .category-row {
            background-color: #eee;
            font-weight: bold;
            text-align: left !important;
        }

        /* Sections */
        .section-title {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            text-decoration: underline;
        }

        .footer-section {
            width: 100%;
            margin-top: 30px;
        }

        .footer-section td {
            text-align: center;
            width: 33%;
            vertical-align: top;
        }

        @media print {
            .container {
                border: none;
                margin: 0;
                padding: 0;
                width: 100%;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="no-print" style="background:#fff; padding:15px; text-align:center; border-bottom:1px solid #ddd; position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <button onclick="window.history.back()"
            style="padding:8px 20px; cursor:pointer; background:#95a5a6; color:#fff; border:none; border-radius:4px; margin-right: 10px;">
            <i class="fas fa-arrow-left"></i> KEMBALI
        </button>
        <button onclick="window.print()"
            style="padding:8px 20px; cursor:pointer; background:#27ae60; color:#fff; border:none; border-radius:4px; font-weight: bold;">
            <i class="fas fa-print"></i> CETAK RAPOR (Ctrl+P)
        </button>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="<?= ROOT_PATH ?>/public/img/logo provinsi.png" alt="Logo">
            <h1>PEMERINTAH PROVINSI JAWA TIMUR</h1>
            <h1>DINAS PENDIDIKAN</h1>
            <h1>SEKOLAH MENENGAH KEJURUAN NEGERI 10</h1>
            <h1>SURABAYA </h1>
            <p>JL. KEPUTIH TEGAL FAX, TELP. 5939581 | Telp: (031) 5678901</p>
            <p>Website: www.smkn10sby.sch.id | Email: info@smkn10sby.sch.id</p>
        </div>

        <div style="text-align: center; margin-bottom: 30px;">
            <h3 style="margin: 0; text-decoration: underline;">LAPORAN HASIL BELAJAR TENGAH SEMESTER</h3>
            <h3 style="margin: 5px 0;">
                SEMESTER <?= $data['semester'] == 1 ? 'GANJIL' : 'GENAP' ?>
            </h3>
            <h3 style="margin: 5px 0;">TAHUN PELAJARAN : <?= e($data['siswa']['tahun_ajaran_nama'] ?? '-') ?></h3>
        </div>

        <!-- Student Data -->
        <table class="info-siswa">
            <tr>
                <td class="label">Nama Peserta Didik</td>
                <td class="separator">:</td>
                <td style="font-weight: bold;">
                    <?= e($data['siswa']['nama']) ?>
                </td>
                <td class="label">Kelas</td>
                <td class="separator">:</td>
                <td>
                    <?= e($data['siswa']['kelas_nama']) ?>
                </td>
            </tr>
            <tr>
                <td class="label">NIS / NISN</td>
                <td class="separator">:</td>
                <td>
                    <?= e($data['siswa']['nis']) ?> /
                    <?= e($data['siswa']['nisn']) ?>
                </td>
                <td class="label">Semester</td>
                <td class="separator">:</td>
                <td>
                    <?= $data['semester'] == 1 ? '1 (Ganjil)' : '2 (Genap)' ?>
                </td>
            </tr>
            <tr>
                <td class="label">Kompetensi Keahlian</td>
                <td class="separator">:</td>
                <td>
                    <?= e($data['siswa']['jurusan_nama']) ?>
                </td>
                <td colspan="3"></td>
            </tr>
        </table>

        <!-- Mapel Table -->
        <table class="report-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 40px;">No</th>
                    <th rowspan="2">Mata Pelajaran</th>
                    <th rowspan="2" style="width: 60px;">KKM</th>
                    <th colspan="2">Nilai</th>
                    <th colspan="3">Kehadiran</th>
                </tr>
                <tr>
                    <th style="width: 80px;">Penget.</th>
                    <th style="width: 80px;">Keter.</th>
                    <th style="width: 40px;">S</th>
                    <th style="width: 40px;">I</th>
                    <th style="width: 40px;">A</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $currentGroup = '';
                $no = 1;
                $groups = [
                    'Muatan Nasional' => 'A. MUATAN NASIONAL',
                    'Muatan Kewilayahan' => 'B. MUATAN KEWILAYAHAN',
                    'Muatan Kejuruan' => 'C. MUATAN KEJURUAN'
                ];

                foreach ($groups as $key => $title):
                    $hasItems = false;
                    foreach ($data['grades'] as $g) {
                        if ($g['kategori'] === $key) {
                            $hasItems = true;
                            break;
                        }
                    }

                    if ($hasItems):
                        ?>
                        <tr class="category-row">
                            <td colspan="8">
                                <?= $title ?>
                            </td>
                        </tr>
                        <?php
                        $subNo = 1;
                        foreach ($data['grades'] as $g):
                            if ($g['kategori'] !== $key)
                                continue;
                            ?>
                            <tr>
                                <td>
                                    <?= $subNo++ ?>
                                </td>
                                <td class="text-left">
                                    <?= e($g['mapel_nama']) ?>
                                </td>
                                <td>
                                    <?= e($g['kkm'] ?: 75) ?>
                                </td>
                                <td>
                                    <?= $g['pengetahuan'] !== null ? number_format($g['pengetahuan'], 0) : '-' ?>
                                </td>
                                <td>
                                    <?= $g['keterampilan'] !== null ? number_format($g['keterampilan'], 0) : '-' ?>
                                </td>
                                <td>
                                    <?= e($g['sakit'] ?: 0) ?>
                                </td>
                                <td>
                                    <?= e($g['izin'] ?: 0) ?>
                                </td>
                                <td>
                                    <?= e($g['alfa'] ?: 0) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php
                    endif;
                endforeach;
                ?>
            </tbody>
        </table>

        <!-- Ekstrakurikuler -->
        <div class="section-title">D. EKSTRAKURIKULER</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th style="width: 250px;">Kegiatan Ekstrakurikuler</th>
                    <th>Keterangan / Prestasi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['ekskul'])): ?>
                    <tr>
                        <td>1</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data['ekskul'] as $idx => $e): ?>
                        <tr>
                            <td>
                                <?= $idx + 1 ?>
                            </td>
                            <td class="text-left">
                                <?= e($e['nama_kegiatan']) ?>
                            </td>
                            <td class="text-left">
                                <?= e($e['keterangan']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Catatan Wali Kelas -->
        <div class="section-title">E. CATATAN WALI KELAS</div>
        <div style="border: 1px solid #000; padding: 10px; min-height: 60px;">
            <?= nl2br(e($data['catatan'] ?: '-')) ?>
        </div>

        <!-- Catatan Nilai Sikap -->
        <div class="section-title">F. CATATAN NILAI SIKAP</div>
        <div style="border: 1px solid #000; padding: 10px; min-height: 60px; margin-bottom: 30px;">
            <?= nl2br(e($data['sikap'] ?: '-')) ?>
        </div>

        <!-- Tanda Tangan -->
        <?php
        date_default_timezone_set('Asia/Jakarta');
        $months = [
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];
        $formattedDate = date('d') . ' ' . $months[(int) date('m')] . ' ' . date('Y');
        ?>
        <table class="footer-section">
            <tr>
                <td colspan="2"></td>
                <td>Surabaya,
                    <?= $formattedDate ?>
                </td>
            </tr>
            <tr>
                <td>Orang Tua / Wali Murid</td>
                <td></td>
                <td>Wali Kelas,</td>
            </tr>
            <tr style="height: 80px;">
                <td colspan="3"></td>
            </tr>
            <tr>
                <td>.........................................</td>
                <td></td>
                <td style="font-weight: bold; text-decoration: underline;">
                    <?= e($data['wali']['name'] ?? '-') ?>
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>
                    NIP :
                    <?= !empty($data['wali']['nip']) ? e($data['wali']['nip']) : '-' ?>
                </td>
            </tr>
        </table>
    </div>

    <script>
        // Auto-print if query param present
        if (window.location.search.indexOf('autoprint=1') > -1) {
            window.print();
        }
    </script>
</body>

</html>