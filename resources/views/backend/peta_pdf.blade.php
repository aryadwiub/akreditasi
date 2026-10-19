<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Peta Sebaran Akreditasi Nasional</title>
    <style>
        @page { margin: 18mm 12mm 16mm 12mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 8px; color: #111827; }
        .kop { width: 100%; border-bottom: 1.5px solid #1f2937; padding-bottom: 6px; margin-bottom: 8px; }
        .kop td { vertical-align: middle; }
        .kop img { height: 34px; }
        h1 { font-size: 13px; margin: 0; }
        .sub { color: #4b5563; font-size: 8.5px; margin-top: 2px; }
        table.peta { width: 100%; border-collapse: collapse; }
        table.peta th, table.peta td { border: 0.6px solid #d1d5db; padding: 3px 2px; }
        table.peta thead th { background: #1f2937; color: #fff; font-weight: bold; text-align: center; }
        table.peta thead th.ini { background: #4f46e5; }
        table.peta thead { display: table-header-group; }
        table.peta tr { page-break-inside: avoid; }
        td.no, td.sel { text-align: center; }
        td.prodi { padding-left: 4px; }
        td.sel span { font-weight: bold; }
        tr.nonaktif td.no, tr.nonaktif td.prodi { color: #6b7280; font-style: italic; }
        .label-nonaktif { color: #991b1b; background: #fee2e2; font-style: normal; font-weight: bold; font-size: 6.5px; padding: 0 3px; }
        .kosong { color: #d1d5db; }
        .fak { color: #6b7280; font-size: 7px; }
        .judul-legenda { margin-top: 10px; margin-bottom: 3px; font-size: 7.5px; font-weight: bold; }
        table.legenda { border-collapse: separate; border-spacing: 0 3px; font-size: 7.5px; }
        table.legenda td.warna { padding: 1px 6px; font-weight: bold; }
        table.legenda td.kode { width: 18px; padding: 1px 3px; text-align: center; font-weight: bold; border: 0.6px solid #d1d5db; }
        table.legenda td.nama { padding: 1px 12px 1px 4px; }
        .catatan { margin-top: 6px; font-size: 7.5px; color: #4b5563; font-style: italic; }
    </style>
</head>
<body>
    <table class="kop">
        <tr>
            @if ($logo)
                <td style="width: 90px;"><img src="{{ $logo }}" alt="BPM UNAIR"></td>
            @endif
            <td>
                <h1>Peta Sebaran Akreditasi Nasional</h1>
                <div class="sub">Fakultas: <b>{{ $namaFakultas }}</b> &nbsp;|&nbsp; Status: <b>{{ $namaStatus }}</b> &nbsp;|&nbsp; {{ $peta->count() }} program studi &nbsp;|&nbsp; Tahun {{ $tahun[0] }}&ndash;{{ end($tahun) }}</div>
                <div class="sub">Badan Penjaminan Mutu Universitas Airlangga &nbsp;|&nbsp; Dicetak {{ $dicetak }}</div>
            </td>
        </tr>
    </table>

    <table class="peta">
        <thead>
            <tr>
                <th rowspan="2" style="width: 18px;">No</th>
                <th rowspan="2">Nama Program Studi</th>
                <th colspan="{{ count($tahun) }}">Tahun</th>
            </tr>
            <tr>
                @foreach ($tahun as $t)
                    <th class="{{ $t === $tahunIni ? 'ini' : '' }}" style="width: 30px;">{{ $t }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($peta as $p)
                <tr class="{{ $p->aktif ? '' : 'nonaktif' }}">
                    <td class="no">{{ $loop->iteration }}</td>
                    <td class="prodi">
                        {{ $p->nama_jenjang }} {{ $p->nama_prodi }}
                        @unless ($p->aktif) <span class="label-nonaktif">TIDAK AKTIF</span> @endunless
                        <br><span class="fak">{{ $p->nm_fakultas }}</span>
                    </td>
                    @foreach ($tahun as $t)
                        @php $sk = $p->sel[$t]; @endphp
                        @if ($sk)
                            @php [$bg, $fg] = \App\Support\EksporPeta::warnaSel($sk, $lembaga); @endphp
                            <td class="sel" style="background: #{{ $bg }}; color: #{{ $fg }};"><span>{{ \App\Support\EksporPeta::teksSel($sk) }}</span></td>
                        @else
                            <td class="sel kosong">&ndash;</td>
                        @endif
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($tahun) + 2 }}" style="text-align:center; padding: 12px;">Tidak ada program studi.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- legenda dalam tabel (inline-block kurang rapi di dompdf) --}}
    <div class="judul-legenda">Warna sel = Lembaga Akreditasi</div>
    <table class="legenda">
        <tr>
            @foreach ($lembaga as $nama => [$bg, $fg])
                <td class="warna" style="background: #{{ $bg }}; color: #{{ $fg }};">{{ $nama }}</td><td class="nama"></td>
            @endforeach
            @php [$bg, $fg] = \App\Support\EksporPeta::WARNA_LAIN; @endphp
            <td class="warna" style="background: #{{ $bg }}; color: #{{ $fg }};">Tidak tercatat</td>
        </tr>
    </table>

    <div class="judul-legenda">Kode = Peringkat Akreditasi</div>
    <table class="legenda">
        @foreach ($peringkat->chunk(7) as $baris)
            <tr>
                @foreach ($baris as $kode => $nama)
                    <td class="kode">{{ $kode }}</td>
                    <td class="nama">{{ ucwords(strtolower($nama)) }}</td>
                @endforeach
            </tr>
        @endforeach
    </table>

    <div class="catatan">
        &ndash; Tidak ada SK berlaku pada tahun tersebut. Tanggal berlaku yang kosong dihitung 5 tahun sebelum tanggal berakhir.
    </div>
</body>
</html>
