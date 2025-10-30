<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ItemExportController
{
    public function exportCsv(Request $request)
    {
        $fileName = 'items_export_' . date('Ymd_His') . '.csv';

        $response = new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');
            // header
            fputcsv($handle, ['id', 'kode', 'nama', 'satuan', 'stok', 'created_at']);

            // stream rows
            foreach (Item::orderBy('id')->cursor() as $item) {
                fputcsv($handle, [
                    $item->id,
                    $item->kode,
                    $item->nama,
                    $item->satuan,
                    $item->stok,
                    optional($item->created_at)->toDateTimeString(),
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        return $response;
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');
        $header = null;
        $created = 0;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if (!$header) {
                $header = $row;
                continue;
            }

            $data = array_combine($header, $row);
            if (!$data) continue;

            $kode = $data['kode'] ?? null;
            $nama = $data['nama'] ?? null;
            $satuan = $data['satuan'] ?? null;
            $stok = isset($data['stok']) ? (int)$data['stok'] : 0;

            if (!$nama) continue;

            if ($kode && Item::where('kode', $kode)->exists()) {
                // skip duplicates by kode
                continue;
            }

            // ensure kode unique; if missing, generate
            if (!$kode) {
                $last = Item::orderBy('id', 'desc')->first();
                $nextId = $last ? $last->id + 1 : 1;
                $kode = 'kd-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            }

            Item::create([
                'kode' => $kode,
                'nama' => $nama,
                'satuan' => $satuan,
                'stok' => max(0, $stok),
            ]);
            $created++;
        }

        fclose($handle);

        return redirect()->back()->with('status', "Imported {$created} items");
    }
}
