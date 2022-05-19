<?php

namespace Model;

use Slim\Psr7\UploadedFile;

class DataUploader
{

    public function getFileData($file): array {
        $filename = $this->moveUploadedFile(UPLOAD_DIR, $file);
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);
        $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
        $data = $sheet->toArray();
        return $data;
    }

    private function moveUploadedFile($directory, UploadedFile $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $directory . DIRECTORY_SEPARATOR . $filename;
    }
}