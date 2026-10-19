<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $judul }}</title>
    <style>
        @page { margin: 18mm 12mm 16mm 12mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 8px; color: #111827; }
        .kop { width: 100%; border-bottom: 1.5px solid #1f2937; padding-bottom: 6px; margin-bottom: 8px; }
        .kop td { vertical-align: middle; }
        .kop img { height: 34px; }
        h1 { font-size: 13px; margin: 0; }
        .sub { color: #4b5563; font-size: 8.5px; margin-top: 2px; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 0.6px solid #d1d5db; padding: 3px 4px; vertical-align: top; }
        table.data thead th { background: #1f2937; color: #fff; font-weight: bold; text-align: center; }
        table.data thead { display: table-header-group; }
        table.data tr { page-break-inside: avoid; }
        table.data tbody tr:nth-child(even) td { background: #f9fafb; }
        .tengah { text-align: center; }
    </style>
</head>
<body>
    <table class="kop">
        <tr>
            @if ($logo)
                <td style="width: 90px;"><img src="{{ $logo }}" alt="BPM UNAIR"></td>
            @endif
            <td>
                <h1>{{ $judul }}</h1>
                @foreach ($info as $keterangan)
                    <div class="sub">{{ $keterangan }}</div>
                @endforeach
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                @foreach ($kolom as $k)
                    <th>{{ $k[0] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($baris as $isi)
                <tr>
                    @foreach (array_values($isi) as $i => $nilai)
                        <td class="{{ !empty($kolom[$i][2]) ? 'tengah' : '' }}">{{ $nilai }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($kolom) }}" class="tengah" style="padding: 12px;">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
