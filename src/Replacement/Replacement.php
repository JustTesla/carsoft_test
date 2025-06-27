<?php

declare(strict_types=1);

namespace App\Replacement;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Replacement
{
    public function parseFile(UploadedFile $uploadedFile): array
    {
        $data = [];
        $line = '';
        for ($i = 0; $i < strlen($uploadedFile->getContent()); $i++) {
            $char = $uploadedFile->getContent()[$i];
            $line .= $char;
            if ("\n" === $char) {
                if (1 === preg_match('/\s*(?P<address>[A-F\d]+)\:\s*([A-F\d]{2})\s*(?P<value>[A-F\d]{2})/u', $line, $matches)) {
                    $data[$matches['address']] = $matches['value'];
                }

                $line = '';
            }
        }

        return $data;
    }
}
