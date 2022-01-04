<?php

namespace App\Helpers;

class FileHelper
{

    /**
     * @param string $prefix
     * @return string|null
     */
    public static function createTmpFile(string $prefix = 'tmp_'): ?string
    {
        return tempnam(sys_get_temp_dir(), $prefix);
    }

    /**
     * @param $thumbnailPath
     * @return bool|string|null
     */
    public static function hashFile($thumbnailPath): bool|string|null
    {
        if (file_exists($thumbnailPath)) {
            return hash_file('sha1', $thumbnailPath);
        }

        return null;
    }

    public static function hash($string)
    {
        return hash('sha1', $string);
    }

}
