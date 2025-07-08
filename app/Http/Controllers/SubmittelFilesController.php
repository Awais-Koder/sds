<?php

namespace App\Http\Controllers;

use App\Models\Submittel;
use Illuminate\Http\Request;
use ZipArchive;
use Illuminate\Support\Facades\Storage;

class SubmittelFilesController extends Controller
{
    public function downloadSubmittelFiles($id)
    {
        $submittel = Submittel::with('outgoings')->findOrFail($id);

        $zipFileName = 'submittel-files-' . $submittel->ref_no . '.zip';
        $zipFilePath = storage_path('app/public/' . $zipFileName);

        $zip = new ZipArchive;

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            // Add outgoing files
            foreach ($submittel->outgoings as $outgoing) {
                $fileName = $outgoing->file;
                $filePath = Storage::disk('public')->path($fileName);

                if (file_exists($filePath)) {
                    $zip->addFile($filePath, 'outgoings/' . basename($fileName));
                }
            }

            // 🔥 Add soft_copy_file if exists
            if (!empty($submittel->soft_copy_file)) {
                $softCopyPath = Storage::disk('public')->path($submittel->soft_copy_file);

                if (file_exists($softCopyPath)) {
                    // Optional: rename inside ZIP to make it clear
                    $zip->addFile($softCopyPath, 'soft-copy/' . basename($submittel->soft_copy_file));
                }
            }

            $zip->close();

            return response()->download($zipFilePath);
        } else {
            return response()->json(['error' => 'Could not create ZIP file.'], 500);
        }
    }
}
