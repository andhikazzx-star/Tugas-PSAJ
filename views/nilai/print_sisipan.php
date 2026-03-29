<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor Sisipan - <?= e($data['siswa']['nama']) ?></title>
    <!-- Modern font from Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #1e293b;
            --secondary: #64748b;
            --border: #e2e8f0;
            --bg-light: #f8fafc;
            --text: #0f172a;
        }

        /* RESET & BASE */
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            font-size: 13px;
            line-height: 1.5;
            color: var(--text);
            margin: 0;
            background-color: #cbd5e1; /* For screen preview */
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 40px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        /* HEADER / KOP SURAT */
        .kop-table {
            border-bottom: 2px solid var(--primary);
            margin-bottom: 25px;
        }

        /* TITLE */
        .report-title {
            text-align: center;
            margin-bottom: 25px;
        }

        .report-title h3 {
            margin: 0;
            font-size: 16px;
            text-decoration: underline;
            font-weight: 700;
            color: var(--primary);
        }

        /* INFO SECTION */
        .info-label {
            width: 140px;
            color: var(--secondary);
            font-weight: 500;
        }

        .info-value {
            font-weight: 600;
            color: var(--primary);
        }

        /* TABLE STYLING */
        .table-responsive {
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .report-table th,
        .report-table td {
            border: 1px solid var(--border);
            padding: 10px 8px;
            text-align: center;
        }

        .report-table th {
            background-color: rgba(255, 193, 7, 0.1) !important; /* Yellow transparent */
            color: var(--primary);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .report-table tr:nth-child(even) {
            background-color: var(--bg-light);
        }

        .category-header {
            background-color: #f1f5f9 !important;
            font-weight: 700;
            text-align: left !important;
            padding-left: 15px !important;
            color: var(--primary);
        }

        .text-left {
            text-align: left !important;
            padding-left: 15px !important;
        }

        /* NOTES SECTION */
        .note-content {
            font-style: italic;
            color: #475569;
            min-height: 60px;
            font-size: 12px;
        }

        /* SIGNATURES */
        .sig-space {
            height: 60px;
        }

        .sig-name {
            font-weight: 700;
            text-decoration: underline;
        }

        .sig-nip {
            margin-top: 2px;
            font-size: 12px;
        }

        /* PRINT SETTINGS */
        @media print {
            body {
                background-color: white;
                padding: 0;
            }

            .container {
                box-shadow: none;
                border-radius: 0;
                width: 100%;
                max-width: none;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: A4;
                margin: 1.5cm;
            }
        }

        /* FLOATING PRINT BUTTON */
        .no-print {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }

        .btn-print {
            background-color: var(--primary);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border: none;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn-print:hover {
            transform: scale(1.05);
            background-color: #334155;
        }
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- KOP SURAT / HEADER - Table for PDF Compatibility -->
        <table style="width:100%; border-bottom: 2px solid var(--primary); padding-bottom: 15px; margin-bottom: 25px;">
            <tr>
                <td style="width: 80px;">
                    <img src="<?= asset('img/logo.png') ?>" alt="Logo" style="width: 80px; height: auto;">
                </td>
                <td style="text-align: center;">
                    <h1 style="margin: 0; font-size: 18px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--primary); font-weight: 700;">Pemerintah Provinsi Jawa Timur</h1>
                    <h2 style="margin: 2px 0; font-size: 16px; text-transform: uppercase; font-weight: 600;">Dinas Pendidikan</h2>
                    <h2 style="margin: 2px 0; font-size: 20px; text-transform: uppercase; font-weight: 600;">SMK Negeri 10 Surabaya</h2>
                    <p style="margin: 0; font-size: 12px; color: var(--secondary);">Jl. Keputih Tegal - Surabaya | Telp: (031) xxxxxxx</p>
                    <p style="margin: 0; font-size: 12px; color: var(--secondary);">Email: admin@smkn10sby.sch.id | Website: smkn10sby.sch.id</p>
                </td>
            </tr>
        </table>

        <section class="report-title">
            <h3>LAPORAN HASIL BELAJAR TENGAH SEMESTER</h3>
        </section>

        <!-- STUDENT INFORMATION - Table for PDF Compatibility -->
        <table style="width:100%; margin-bottom: 25px; background: var(--bg-light); padding: 15px; border-radius: 6px; border: 1px solid var(--border);">
            <tr>
                <td style="width: 50%; vertical-align: top; padding-right: 10px;">
                    <table style="width: 100%; border: none;">
                        <tr>
                            <td class="info-label" style="padding-bottom: 5px;">Nama Siswa</td>
                            <td class="info-value" style="padding-bottom: 5px;">: <?= e($data['siswa']['nama']) ?></td>
                        </tr>
                        <tr>
                            <td class="info-label" style="padding-bottom: 5px;">Kelas</td>
                            <td class="info-value" style="padding-bottom: 5px;">: <?= e($data['siswa']['kelas_nama']) ?></td>
                        </tr>
                        <tr>
                            <td class="info-label">Konsentrasi</td>
                            <td class="info-value">: <?= e($data['siswa']['jurusan_nama']) ?></td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%; vertical-align: top; padding-left: 10px;">
                    <table style="width: 100%; border: none;">
                        <tr>
                            <td class="info-label" style="padding-bottom: 5px;">NIS / NISN</td>
                            <td class="info-value" style="padding-bottom: 5px;">: <?= e($data['siswa']['nis']) ?> / <?= e($data['siswa']['nisn'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="info-label" style="padding-bottom: 5px;">Tahun Pelajaran</td>
                            <td class="info-value" style="padding-bottom: 5px;">: <?= e($data['activeYear']['nama'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="info-label">Semester</td>
                            <td class="info-value">: <?= $data['semester'] == 1 ? '1 (Ganjil)' : '2 (Genap)' ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- GRADES TABLE -->
        <div class="table-responsive">
            <table class="report-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 40px;">No</th>
                        <th rowspan="2">Mata Pelajaran</th>
                        <th rowspan="2" style="width: 60px;">KKTP</th>
                        <th colspan="4">Ketetapan Nilai</th>
                        <th colspan="3">Absensi</th>
                    </tr>
                    <tr>
                        <th style="width: 60px;">S-1</th>
                        <th style="width: 60px;">S-2</th>
                        <th style="width: 60px;">S-3</th>
                        <th style="width: 60px;">PTS</th>
                        <th style="width: 40px;">S</th>
                        <th style="width: 40px;">I</th>
                        <th style="width: 40px;">A</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- KELOMPOK A -->
                    <tr>
                        <td colspan="10" class="category-header">A. KELOMPOK MATA PELAJARAN UMUM</td>
                    </tr>
                    <?php if (empty($data['grades_umum'])): ?>
                        <tr><td colspan="10" class="text-muted py-3">Tidak ada data untuk kategori ini</td></tr>
                    <?php else: ?>
                        <?php foreach ($data['grades_umum'] as $i => $g): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="text-left"><?= e($g['mapel']) ?></td>
                            <td><?= e($g['kkm']) ?></td>
                            <td class="highlight-grade"><?= e($g['s1']) ?></td>
                            <td class="highlight-grade"><?= e($g['s2']) ?></td>
                            <td class="highlight-grade"><?= e($g['s3']) ?></td>
                            <td class="highlight-grade" style="background:#f1f5f9"><?= e($g['pts']) ?></td>
                            <td><?= e($g['sakit']) ?></td>
                            <td><?= e($g['izin']) ?></td>
                            <td><?= e($g['alfa']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- KELOMPOK B -->
                    <tr>
                        <td colspan="10" class="category-header">B. MUATAN KEWILAYAHAN</td>
                    </tr>
                    <?php if (empty($data['grades_wilayah'])): ?>
                        <tr><td colspan="10" class="text-muted py-3">Tidak ada data untuk kategori ini</td></tr>
                    <?php else: ?>
                        <?php foreach ($data['grades_wilayah'] as $i => $g): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="text-left"><?= e($g['mapel']) ?></td>
                            <td><?= e($g['kkm']) ?></td>
                            <td class="highlight-grade"><?= e($g['s1']) ?></td>
                            <td class="highlight-grade"><?= e($g['s2']) ?></td>
                            <td class="highlight-grade"><?= e($g['s3']) ?></td>
                            <td class="highlight-grade" style="background:#f1f5f9"><?= e($g['pts']) ?></td>
                            <td><?= e($g['sakit']) ?></td>
                            <td><?= e($g['izin']) ?></td>
                            <td><?= e($g['alfa']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- EXTRA-CURRICULAR SECTION - Table for PDF Compatibility -->
        <table style="width: 100%; margin-bottom: 25px; border-collapse: collapse;">
            <thead>
                <tr>
                    <th colspan="3" class="category-header">C. EKSTRAKURIKULER</th>
                </tr>
                <tr class="report-table">
                    <th style="width: 40px; border: 1px solid var(--border); padding: 8px;">No</th>
                    <th style="border: 1px solid var(--border); padding: 8px;">Kegiatan Ekstrakurikuler</th>
                    <th style="border: 1px solid var(--border); padding: 8px;">Predikat / Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['ekskul'])): ?>
                    <tr>
                        <td colspan="3" style="border: 1px solid var(--border); padding: 10px; text-align: center; font-style: italic; color: var(--secondary);">Belum ada data kegiatan ekstrakurikuler</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data['ekskul'] as $i => $e): ?>
                    <tr>
                        <td style="border: 1px solid var(--border); padding: 8px; text-align: center;"><?= $i + 1 ?></td>
                        <td style="border: 1px solid var(--border); padding: 8px;"><?= e($e['nama_kegiatan']) ?></td>
                        <td style="border: 1px solid var(--border); padding: 8px; text-align: center;"><?= e($e['keterangan'] ?: '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- NOTES SECTION - Table for PDF Compatibility -->
        <table style="width: 100%; border-collapse: separate; border-spacing: 20px 0; margin-bottom: 30px;">
            <tr>
                <td style="width: 50%; vertical-align: top; border: 1px solid var(--border); border-radius: 6px; padding: 15px;">
                    <h4 style="margin: 0 0 10px 0; font-size: 13px; text-transform: uppercase; color: var(--secondary); border-bottom: 1px solid var(--border); padding-bottom: 5px;">Catatan BP / BK</h4>
                    <div class="note-content">
                        <?= !empty($data['catatan']) ? e($data['catatan']) : '<em>Tidak ada catatan khusus.</em>' ?>
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top; border: 1px solid var(--border); border-radius: 6px; padding: 15px;">
                    <h4 style="margin: 0 0 10px 0; font-size: 13px; text-transform: uppercase; color: var(--secondary); border-bottom: 1px solid var(--border); padding-bottom: 5px;">Catatan Wali Kelas</h4>
                    <div class="note-content">
                        <?= !empty($data['sikap']) ? e($data['sikap']) : '<em>Semangat dalam belajar dan jaga kedisiplinan.</em>' ?>
                    </div>
                </td>
            </tr>
        </table>

        <!-- FOOTER / SIGNATURES - Table for PDF Compatibility -->
        <table style="width: 100%; margin-top: 40px; text-align: center;">
            <tr>
                <td style="width: 40%; vertical-align: bottom;">
                    <span>Orang Tua / Wali</span>
                    <div class="sig-space"></div>
                    <span class="sig-name">................................</span>
                </td>
                <td style="width: 20%;"></td> <!-- Spacer -->
                <td style="width: 40%; vertical-align: bottom;">
                    <div class="sig-date" style="margin-bottom: 0;">Surabaya, <?= !empty($data['report_date']) ? formatDate($data['report_date']) : date('d F Y') ?></div>
                    <span style="margin-bottom: 0;">Wali Kelas,</span>
                    <div class="sig-space"></div>
                    <span class="sig-name"><?= e($data['wali']['nama']) ?></span><br>
                    <span class="sig-nip">NIP. <?= e($data['wali']['nip']) ?></span>
                </td>
            </tr>
        </table>

    </div>

    <!-- Floating Print Button (Screen Only) -->
    <div class="no-print" style="position: fixed; bottom: 30px; right: 30px; z-index: 9999;">
        <button onclick="window.print()" style="background: #2563eb; color: white; border: none; padding: 12px 24px; border-radius: 50px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: 600; box-shadow: 0 4px 12px rgba(0,0,0,0.3); font-family: 'Inter', sans-serif;">
            <i class="fas fa-print"></i> Cetak Rapor
        </button>
    </div>

</body>

</html>