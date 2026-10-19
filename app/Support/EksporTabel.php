<?php

namespace App\Support;

use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Ekspor tabel sederhana ke Excel / PDF.
 * $tabel: judul, info (array baris keterangan), kolom (array [judul, lebar Excel, rata tengah?]), baris (array of array).
 */
class EksporTabel
{
    public static function excel(array $tabel, string $namaFile)
    {
        $jumlahKolom = count($tabel['kolom']);
        $huruf = fn (int $i) => Coordinate::stringFromColumnIndex($i);
        $akhir = $huruf($jumlahKolom);

        $ss = new Spreadsheet();
        $ss->getProperties()->setCreator('SIM Akreditasi BPM UNAIR')->setTitle($tabel['judul']);
        $sh = $ss->getActiveSheet();
        $sh->setTitle(mb_substr(preg_replace('/[\\\\\/\?\*\[\]:]/', '', $tabel['judul']), 0, 31));

        $sh->setCellValue('A1', $tabel['judul'])->mergeCells("A1:{$akhir}1");
        $sh->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $baris = 2;
        foreach ($tabel['info'] as $info) {
            $sh->setCellValue("A{$baris}", $info)->mergeCells("A{$baris}:{$akhir}{$baris}");
            $sh->getStyle("A{$baris}")->getFont()->setSize(10)->getColor()->setRGB('4B5563');
            $baris++;
        }

        $kepala = $baris + 1;
        foreach ($tabel['kolom'] as $i => [$judul, $lebar]) {
            $sh->setCellValue($huruf($i + 1) . $kepala, $judul);
            $sh->getColumnDimension($huruf($i + 1))->setWidth($lebar);
        }
        $sh->getStyle("A{$kepala}:{$akhir}{$kepala}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F2937']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);

        $r = $kepala;
        foreach ($tabel['baris'] as $isi) {
            $r++;
            foreach (array_values($isi) as $i => $nilai) {
                $sh->setCellValueExplicit($huruf($i + 1) . $r, (string) $nilai, is_int($nilai) ? DataType::TYPE_NUMERIC : DataType::TYPE_STRING);
            }
        }
        if (empty($tabel['baris'])) {
            $r++;
            $sh->setCellValue("A{$r}", 'Tidak ada data.')->mergeCells("A{$r}:{$akhir}{$r}");
        }
        $sh->getStyle("A{$kepala}:{$akhir}{$r}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('D1D5DB');
        $sh->getStyle('A' . ($kepala + 1) . ":{$akhir}{$r}")->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
        foreach ($tabel['kolom'] as $i => $k) {
            if (!empty($k[2])) {
                $sh->getStyle($huruf($i + 1) . ($kepala + 1) . ':' . $huruf($i + 1) . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }

        $sh->freezePane('A' . ($kepala + 1));
        $sh->setAutoFilter("A{$kepala}:{$akhir}{$r}");
        $sh->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)->setFitToHeight(0)->setRowsToRepeatAtTopByStartAndEnd($kepala, $kepala);

        return response()->streamDownload(fn () => (new Xlsx($ss))->save('php://output'), $namaFile, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public static function pdf(array $tabel, string $namaFile)
    {
        $logo = public_path('img/logo-bpm-bg-putih-removebg-preview.png');
        $tabel['logo'] = is_file($logo) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logo)) : null;

        $folderFont = storage_path('app/dompdf');
        if (!is_dir($folderFont)) {
            mkdir($folderFont, 0755, true);
        }
        $opsi = new Options();
        $opsi->set('isRemoteEnabled', false);
        $opsi->set('defaultFont', 'DejaVu Sans');
        $opsi->set('fontDir', $folderFont);
        $opsi->set('fontCache', $folderFont);

        $pdf = new Dompdf($opsi);
        $pdf->loadHtml(view('backend/tabel_pdf', $tabel)->render());
        $pdf->setPaper('A4', 'landscape');
        $pdf->render();
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
