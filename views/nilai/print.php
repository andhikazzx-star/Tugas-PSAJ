<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rapor Sisipan –
        <?= e($siswa['nama']) ?>
    </title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            margin: 0;
            padding: 1cm;
            background: #fff;
        }

        .print-container {
            width: 100%;
            max-width: 21cm;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
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
            margin: 0;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 20px;
            text-decoration: underline;
        }

        .student-info {
            width: 100%;
            margin-bottom: 20px;
        }

        .student-info td {
            padding: 3px 0;
            vertical-align: top;
        }

        .student-info .label {
            width: 150px;
        }

        .student-info .colon {
            width: 20px;
            text-align: center;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .data-table th {
            background: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        .center {
            text-align: center !important;
        }

        .footer {
            width: 100%;
            margin-top: 50px;
        }

        .footer table {
            width: 100%;
        }

        .footer td {
            width: 50%;
            text-align: center;
        }

        .signature-box {
            height: 100px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }

        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #2E7D32;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <button class="btn-print no-print" onclick="window.print()">
        <i class="fas fa-print"></i> CETAK RAPOR
    </button>

    <div class="print-container">
        <!-- Kop Surat -->
        <table style="width: 100%; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px;">
            <tr>
                <td style="width: 100px; text-align: center;">
                    <img src="<?= asset('img/logo.png') ?>" alt="Logo" style="width: 80px; height: auto;">
                </td>
                <td style="text-align: center;">
                    <h2 style="font-size: 14pt; margin: 0; text-transform: uppercase;">PEMERINTAH PROVINSI JAWA TIMUR
                    </h2>
                    <h2 style="font-size: 14pt; margin: 5px 0; text-transform: uppercase;">DINAS PENDIDIKAN</h2>
                    <h1 style="font-size: 16pt; margin: 0; text-transform: uppercase;">SMK NEGERI 10 SURABAYA</h1>
                    <p style="font-size: 10pt; margin: 3px 0;">Jl. Keputih Tegal No. 54, Surabaya, Telp: (031) 5945127
                    </p>
                    <p style="font-size: 10pt; margin: 0;">Website: www.smkn10surabaya.sch.id | Email:
                        smkn10surabaya@yahoo.com</p>
                </td>
            </tr>
        </table>

        <div class="title">LAPORAN HASIL BELAJAR (SISIPAN)</div>

        <!-- Info Siswa -->
        <table class="student-info">
            <tr>
                <td class="label">Nama Siswa</td>
                <td class="colon">:</td>
                <td><strong>
                        <?= e($siswa['nama']) ?>
                    </strong></td>
                <td class="label">Kelas</td>
                <td class="colon">:</td>
                <td>
                    <?= e($siswa['kelas_nama']) ?>
                </td>
            </tr>
            <tr>
                <td class="label">NIS / NISN</td>
                <td class="colon">:</td>
                <td>-</td>
                <td class="label">Semester</td>
                <td class="colon">:</td>
                <td>
                    <?= $semester == 1 ? '1 (Ganjil)' : '2 (Genap)' ?>
                </td>
            </tr>
            <tr>
                <td class="label">Kompetensi Keahlian</td>
                <td class="colon">:</td>
                <td>
                    <?= e($siswa['jurusan_nama']) ?>
                </td>
                <td class="label">Tahun Pelajaran</td>
                <td class="colon">:</td>
                <td>2024/2025</td>
            </tr>
        </table>

        <!-- Tabel Nilai -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Mata Pelajaran</th>
                    <th style="width: 100px;">Nilai</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($grades)): ?>
                    <tr>
                        <td colspan="3" class="center">Data nilai belum diinput.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($grades as $index => $g): ?>
                        <tr>
                            <td class="center">
                                <?= $index + 1 ?>
                            </td>
                            <td>
                                <?= e($g['mapel_nama']) ?>
                            </td>
                            <td class="center"><strong>
                                    <?= $g['nilai'] !== null ? e($g['nilai']) : '-' ?>
                                </strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Catatan Wali Kelas -->
        <div style="margin-top: 20px;">
            <p style="font-weight: bold; margin-bottom: 5px;">CATATAN WALI KELAS:</p>
            <div style="border: 1px solid #000; padding: 10px; min-height: 60px; font-style: italic;">
                <?= !empty($catatan) ? nl2br(e($catatan)) : '-' ?>
            </div>
        </div>

        <!-- Footer Tanda Tangan -->
        <div class="footer">
            <table>
                <tr>
                    <td>
                        <p>Mengetahui,</p>
                        <p>Orang Tua/Wali Murid</p>
                        <div class="signature-box"></div>
                        <p>( ............................................ )</p>
                    </td>
                    <td>
                        <p>Surabaya,
                            <?= date('d F Y') ?>
                        </p>
                        <p>Wali Kelas,</p>
                        <div class="signature-box"></div>
                        <p><strong>
                                <?= e(Session::get('user_name')) ?>
                            </strong></p>
                        <p>NIP. -</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        // Auto-print option
        // window.onload = function() { window.print(); }
    </script>
</body>

</html>