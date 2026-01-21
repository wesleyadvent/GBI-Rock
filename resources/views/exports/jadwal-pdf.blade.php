<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Jadwal Pelayanan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #1a1a1a;
            background: white;
        }
        
        .container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
        }
        
        /* Header */
        .document-header {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px 0;
            border-bottom: 3px solid #1a1a1a;
        }
        
        .document-header h1 {
            color: #1a1a1a;
            font-size: 20pt;
            font-weight: bold;
            margin-bottom: 5px;
            letter-spacing: 3px;
        }
        
        .document-header .period {
            color: #333;
            font-size: 12pt;
            font-weight: 600;
        }
        
        /* Jadwal Card */
        .jadwal-card {
            margin-bottom: 20px;
            page-break-inside: avoid;
            border: 2px solid #1a1a1a;
            background: white;
        }
        
        .jadwal-header {
            background-color: #1a1a1a;
            color: white;
            padding: 12px 15px;
        }
        
        .jadwal-title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .jadwal-info-table {
            width: 100%;
            font-size: 9pt;
        }
        
        .jadwal-info-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        
        .info-label {
            width: 90px;
            font-weight: 600;
            color: #ddd;
        }
        
        .info-separator {
            width: 10px;
        }
        
        .info-value {
            color: #fff;
        }
        
        /* Body */
        .jadwal-content {
            padding: 15px;
        }
        
        .tema-box {
            background: #f5f5f5;
            border-left: 4px solid #1a1a1a;
            padding: 12px 15px;
            margin-bottom: 15px;
        }
        
        .tema-label {
            font-weight: bold;
            font-size: 9pt;
            color: #1a1a1a;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .tema-text {
            font-size: 9pt;
            color: #333;
            line-height: 1.5;
        }
        
        .pekerja-title {
            font-size: 11pt;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid #1a1a1a;
            text-transform: uppercase;
        }
        
        /* Grid 3 kolom */
        .bidang-grid {
            width: 100%;
            margin-bottom: 10px;
        }
        
        .bidang-row {
            display: flex;
            margin-bottom: 10px;
        }
        
        .bidang-item {
            flex: 1;
            margin-right: 10px;
            border: 1.5px solid #ddd;
            background: white;
        }
        
        .bidang-item:last-child {
            margin-right: 0;
        }
        
        .bidang-head {
            padding: 8px 10px;
            color: white;
            font-weight: bold;
            font-size: 9pt;
            text-transform: uppercase;
        }
        
        .bidang-head.usher { background-color: #17a2b8; }
        .bidang-head.pembicara { background-color: #dc3545; }
        .bidang-head.pendoa { background-color: #007bff; }
        .bidang-head.pw { background-color: #ffc107; color: #333; }
        .bidang-head.multimedia { background-color: #28a745; }
        
        .bidang-content {
            padding: 10px;
            min-height: 60px;
        }
        
        .worker-item {
            padding: 5px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 9pt;
        }
        
        .worker-item:last-child {
            border-bottom: none;
        }
        
        .worker-name {
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 2px;
        }
        
        .worker-name:before {
            content: "• ";
            color: #666;
            margin-right: 3px;
        }
        
        .worker-role {
            color: #666;
            font-size: 8pt;
            font-style: italic;
            margin-left: 12px;
        }
        
        .no-data {
            text-align: center;
            color: #999;
            font-style: italic;
            padding: 20px;
            background: #f9f9f9;
            border: 1px dashed #ddd;
        }
        
        /* Footer */
        .document-footer {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 2px solid #1a1a1a;
            text-align: center;
            font-size: 8pt;
            color: #666;
        }
        
        .footer-line {
            margin: 3px 0;
        }
        
        .footer-bold {
            font-weight: 600;
            color: #1a1a1a;
        }
        
        /* Page breaks */
        .page-break {
            page-break-after: always;
        }
        
        @page {
            size: A4;
            margin: 15mm 12mm;
        }
        
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            
            .jadwal-card {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- <div class="document-header">
            <h1>JADWAL PELAYANAN</h1>
            <div class="period">
                @php
                    $bulan = \Carbon\Carbon::create(null, $month, 1)->translatedFormat('F');
                @endphp
                {{ $bulan }} {{ $year }}
            </div>
        </div> -->

        @forelse($jadwals as $index => $jadwal)
            <div class="jadwal-card">
                <div class="jadwal-header">
                    <div class="jadwal-title">{{ $jadwal->jenis_kebaktian }}</div>
                    
                    <table class="jadwal-info-table">
                        <tr>
                            <td class="info-label">TANGGAL</td>
                            <td class="info-separator">:</td>
                            <td class="info-value">{{ $jadwal->tanggal_pelayanan->translatedFormat('l, d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">WAKTU</td>
                            <td class="info-separator">:</td>
                            <td class="info-value">{{ $jadwal->waktu_mulai }} - {{ $jadwal->waktu_selesai }} WIB</td>
                        </tr>
                        @if($jadwal->lokasi)
                        <tr>
                            <td class="info-label">LOKASI</td>
                            <td class="info-separator">:</td>
                            <td class="info-value">{{ $jadwal->lokasi }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                
                <div class="jadwal-content">
                    @if($jadwal->tema)
                        <div class="tema-box">
                            <div class="tema-label">Tema Kebaktian</div>
                            <div class="tema-text">{{ $jadwal->tema }}</div>
                        </div>
                    @endif

                    <div class="pekerja-title">Pekerja yang Bertugas</div>

                    @php
                        $bidangList = [
                            1 => ['nama' => 'Usher', 'class' => 'usher'],
                            2 => ['nama' => 'Pembicara', 'class' => 'pembicara'],
                            3 => ['nama' => 'Pendoa', 'class' => 'pendoa'],
                            4 => ['nama' => 'Praise & Worship', 'class' => 'pw'],
                            5 => ['nama' => 'Multimedia', 'class' => 'multimedia'],
                        ];

                        $tugasPerBidang = $jadwal->tugas
                            ->where('status_tugas', 'approved')
                            ->filter(fn($t) => $t->user && $t->user->id_bidang)
                            ->groupBy(fn($t) => $t->user->id_bidang);
                        
                        $activeBidang = [];
                        foreach($bidangList as $idBidang => $info) {
                            $pekerja = $tugasPerBidang[$idBidang] ?? collect();
                            if($pekerja->isNotEmpty()) {
                                $activeBidang[] = [
                                    'info' => $info,
                                    'pekerja' => $pekerja
                                ];
                            }
                        }
                        
                        $rows = array_chunk($activeBidang, 3);
                    @endphp

                    @if(!empty($activeBidang))
                        <div class="bidang-grid">
                            @foreach($rows as $row)
                                <div class="bidang-row">
                                    @foreach($row as $bidang)
                                        <div class="bidang-item">
                                            <div class="bidang-head {{ $bidang['info']['class'] }}">
                                                {{ $bidang['info']['nama'] }}
                                            </div>
                                            <div class="bidang-content">
                                                @foreach($bidang['pekerja'] as $tugas)
                                                    <div class="worker-item">
                                                        <div class="worker-name">{{ $tugas->user->nama }}</div>
                                                        @if($tugas->peran_tugas)
                                                            <div class="worker-role">{{ $tugas->peran_tugas }}</div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="no-data">
                            Belum ada pekerja yang ditugaskan
                        </div>
                    @endif
                </div>
            </div>

            @if(($index + 1) % 2 == 0 && ($index + 1) < $jadwals->count())
                <div class="page-break"></div>
            @endif
        @empty
            <div class="no-data" style="margin: 40px 0;">
                Tidak ada jadwal pelayanan yang terpublish untuk periode ini
            </div>
        @endforelse

        <div class="document-footer">
            <div class="footer-line">Dicetak pada {{ now()->translatedFormat('d F Y') }} pukul {{ now()->format('H:i') }} WIB</div>
            <div class="footer-line footer-bold">Sistem Informasi Jadwal Pelayanan</div>
        </div>
    </div>
</body>
</html>