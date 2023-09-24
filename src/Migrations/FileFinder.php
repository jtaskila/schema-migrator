<?php

declare(strict_types=1);

namespace Jtaskila\SchemaMigrator\Migrations;

class FileFinder 
{
    /**
     * Find .sql files recursively from a directory 
     */
    public function findFiles($directory): array 
    {
        $files = scandir($directory);
        unset($files[array_search('.', $files, true)]);
        unset($files[array_search('..', $files, true)]);

        $migrationFiles = [];
        foreach($files as $file) {
            if (\is_dir($directory . '/' . $file)) {
                $dirFiles = $this->findFiles($directory . '/' . $file);
                if (!empty($dirFiles)) {
                    $migrationFiles[$file] = $dirFiles;
                }
            }
            if (\str_ends_with($file, '.sql')) {
                $migrationFiles[] = $file;
            }
        }

        return $this->flattenArray($migrationFiles);
    }

    /**
     * Flatten an array and make values slash separated based on parent array keys 
     */
    private function flattenArray(array $arr, string $keyComponent = ''): array 
    {
        $flattenedArray = [];
        foreach ($arr as $key => $value) {
            if (\is_array($value)) {            
                $flattenedArray = [...$flattenedArray, ...$this->flattenArray($value, $key . '/')];

                continue;
            }

            $flattenedArray[] = $keyComponent . $value;
        }

        return $flattenedArray;
    } 
}