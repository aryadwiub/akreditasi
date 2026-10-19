<?php

namespace App\Support;

use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Ekspor peta sebaran akreditasi nasional (dashboard admin) ke Excel dan PDF.
 * $data berisi: peta, tahun, tahunIni, peringkat, namaFakultas, dicetak.
 */
class EksporPeta
{
    // palet [latar, teks] untuk lembaga akreditasi, sesuai urutan tabel jenis_akreditasi
    private const PALET = [
        ['DBEAFE', '1E40AF'], // biru
        ['DCFCE7', '166534'], // hijau
        ['FEF3C7', '92400E'], // kuning
        ['F3E8FF', '6B21A8'], // ungu
        ['FFE4E6', '9F1239'], // merah muda
        ['CCFBF1', '115E59'], // toska
        ['FFEDD5', '9A3412'], // oranye
        ['CFFAFE', '155E75'], // biru muda
        ['ECFCCB', '3F6212'], // hijau muda
        ['FCE7F3', '9D174D'], // pink
    ];
    public const WARNA_LAIN = ['E5E7EB', '374151']; // lembaga kosong / tidak terdaftar

    /** @return \Illuminate\Support\Collection<string, array{0: string, 1: string}> nama lembaga => [latar, teks] */
    public static function warnaLembaga(iterable $namaLembaga): \Illuminate\Support\Collection
    {
        return collect($namaLembaga)->values()
            ->mapWithKeys(fn ($nama, $i) => [$nama => self::PALET[$i % count(self::PALET)]]);
    }

    // warna sel mengikuti lembaga SK (nama dicocokkan tanpa membedakan huruf besar/kecil)
    public static function warnaSel(object $sk, \Illuminate\Support\Collection $lembaga): array
    {
        foreach ($lembaga as $nama => $warna) {
            if (strcasecmp((string) $nama, (string) $sk->lembaga) === 0) {
                return $warna;
            }
        }
        return self::WARNA_LAIN;
    }

    public static function teksSel(?object $sk): string
    {
        return $sk ? (string) $sk->akreditasi : '-';
    }

    public static function excel(array $data, string $namaFile)
    {
        $tahun = $data['tahun'];
        $kolomTahun = 5; // A No, B Prodi, C Fakultas, D Status, E.. tahun
        $kolomAkhir = $kolomTahun - 1 + count($tahun);
        $huruf = fn (int $i) => Coordinate::stringFromColumnIndex($i);
        $akhir = $huruf($kolomAkhir);
        $isi = fn ($sel, $bg, $fg, $tebal = false) => $sel->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
            'font' => ['bold' => $tebal, 'color' => ['rgb' => $fg]],
        ]);

        $ss = new Spreadsheet();
        $ss->getProperties()->setCreator('SIM Akreditasi BPM UNAIR')->setTitle('Peta Sebaran Akreditasi Nasional');
        $sh = $ss->getActiveSheet();
        $sh->setTitle('Peta Sebaran');

        // judul
        $sh->setCellValue('A1', 'Peta Sebaran Akreditasi Nasional');
        $sh->setCellValue('A2', 'Fakultas: ' . $data['namaFakultas'] . '  |  Status: ' . $data['namaStatus'] . '  |  ' . $data['peta']->count() . ' program studi');
        $sh->setCellValue('A3', 'Dicetak: ' . $data['dicetak'] . '  |  Badan Penjaminan Mutu Universitas Airlangga');
        foreach ([1, 2, 3] as $r) {
            $sh->mergeCells("A{$r}:{$akhir}{$r}");
        }
        $sh->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sh->getStyle('A2:A3')->getFont()->setSize(10)->getColor()->setRGB('4B5563');

        // header 2 baris
        foreach (['A' => 'No', 'B' => 'Nama Program Studi', 'C' => 'Fakultas', 'D' => 'Status'] as $kol => $judul) {
            $sh->setCellValue("{$kol}5", $judul)->mergeCells("{$kol}5:{$kol}6");
        }
        $sh->setCellValue($huruf($kolomTahun) . '5', 'Tahun')->mergeCells($huruf($kolomTahun) . "5:{$akhir}5");
        foreach ($tahun as $i => $t) {
            $sh->setCellValue($huruf($kolomTahun + $i) . '6', $t);
        }
        $sh->getStyle("A5:{$akhir}6")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F2937']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sh->getStyle($huruf($kolomTahun + array_search($data['tahunIni'], $tahun)) . '6')
            ->getFill()->getStartColor()->setRGB('4F46E5');

        // isi
        $baris = 7;
        foreach ($data['peta'] as $no => $p) {
            $sh->setCellValue("A{$baris}", $no + 1);
            $sh->setCellValue("B{$baris}", trim($p->nama_jenjang . ' ' . $p->nama_prodi));
            $sh->setCellValue("C{$baris}", $p->nm_fakultas);
            $sh->setCellValue("D{$baris}", $p->aktif ? 'Aktif' : 'Tidak Aktif');
            if (!$p->aktif) {
                $sh->getStyle("A{$baris}:C{$baris}")->getFont()->setItalic(true)->getColor()->setRGB('6B7280');
                $isi($sh->getStyle("D{$baris}"), 'FEE2E2', '991B1B', true);
            }
            foreach ($tahun as $i => $t) {
                $sel = $huruf($kolomTahun + $i) . $baris;
                $sk = $p->sel[$t];
                $sh->setCellValueExplicit($sel, self::teksSel($sk), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                if ($sk) {
                    [$bg, $fg] = self::warnaSel($sk, $data['lembaga']);
                    $isi($sh->getStyle($sel), $bg, $fg, true);
                } else {
                    $sh->getStyle($sel)->getFont()->getColor()->setRGB('D1D5DB');
                }
            }
            $baris++;
        }
        $barisAkhir = max(7, $baris - 1);
        if ($data['peta']->isEmpty()) {
            $sh->setCellValue('A7', 'Tidak ada program studi.')->mergeCells("A7:{$akhir}7");
        }
        $sh->getStyle("A5:{$akhir}{$barisAkhir}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('D1D5DB');
        $sh->getStyle("A7:A{$barisAkhir}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sh->getStyle("D7:{$akhir}{$barisAkhir}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // legenda: warna = lembaga, teks = kode peringkat
        $baris = $barisAkhir + 2;
        $sh->setCellValue("B{$baris}", 'Warna sel = Lembaga Akreditasi')->getStyle("B{$baris}")->getFont()->setBold(true);
        $sh->setCellValue("C{$baris}", 'Kode = Peringkat Akreditasi')->getStyle("C{$baris}")->getFont()->setBold(true);
        $lembaga = $data['lembaga']->map(fn ($w, $nama) => [$nama, $w])->values()->push(['Tidak tercatat', self::WARNA_LAIN]);
        $kode = collect($data['peringkat'])->map(fn ($nama, $k) => $k . ' = ' . ucwords(strtolower($nama)))->values();
        for ($i = 0; $i < max($lembaga->count(), $kode->count()); $i++) {
            $baris++;
            if ($l = $lembaga->get($i)) {
                $sh->setCellValue("B{$baris}", $l[0]);
                $isi($sh->getStyle("B{$baris}"), $l[1][0], $l[1][1], true);
            }
            if ($k = $kode->get($i)) {
                $sh->setCellValue("C{$baris}", $k);
            }
        }
        $baris += 2;
        $sh->setCellValue("A{$baris}", '- Tidak ada SK berlaku pada tahun tersebut. Tanggal berlaku yang kosong dihitung 5 tahun sebelum tanggal berakhir.');
        $sh->getStyle("A{$baris}")->getFont()->setItalic(true)->setSize(9);

        // lebar kolom, bekukan header, cetak landscape
        foreach (['A' => 6, 'B' => 48, 'C' => 32, 'D' => 12] as $kol => $lebar) {
            $sh->getColumnDimension($kol)->setWidth($lebar);
        }
        for ($i = $kolomTahun; $i <= $kolomAkhir; $i++) {
            $sh->getColumnDimension($huruf($i))->setWidth(8);
        }
        $sh->freezePane($huruf($kolomTahun) . '7');
        $sh->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)->setFitToHeight(0)->setRowsToRepeatAtTopByStartAndEnd(5, 6);

        return response()->streamDownload(function () use ($ss) {
            (new Xlsx($ss))->save('php://output');
        }, $namaFile, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public static function pdf(array $data, string $namaFile)
    {

        $logo = public_path('img/logo-bpm-bg-putih-removebg-preview.png');
        $data['logo'] = is_file($logo) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logo)) : null;

        // dompdf menyimpan cache font; folder harus bisa ditulis oleh web server
        $folderFont = storage_path('app/dompdf');
        if (!is_dir($folderFont)) {
            mkdir($folderFont, 0755, true);
        }

        $opsi = new Options();
        $opsi->set('isRemoteEnabled', false); // tidak mengambil file dari internet
        $opsi->set('defaultFont', 'DejaVu Sans');
        $opsi->set('fontDir', $folderFont);
        $opsi->set('fontCache', $folderFont);

        $pdf = new Dompdf($opsi);
        $pdf->loadHtml(view('backend/peta_pdf', $data)->render());
        $pdf->setPaper('A4', 'landscape');
        $pdf->render();

        // nomor halaman (tanpa mengaktifkan PHP di dalam HTML)
        $kanvas = $pdf->getCanvas();
        $kanvas->page_text($kanvas->get_width() - 95, $kanvas->get_height() - 30, 'Halaman {PAGE_NUM} / {PAGE_COUNT}',
            $pdf->getFontMetrics()->getFont('DejaVu Sans'), 7, [0.42, 0.45, 0.5]);

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $namaFile . '"',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
