<?php


namespace Doker42\ToArrayConverter;
class BaseImport
{
    /**
     * @param object $file
     * @param array $emptyUnit
     * @param array $headers
     * @param string|null $nestedArrayName
     * @return array
     */
    public static function xlsToArray(object $file, array $emptyUnit, array $headers, string $nestedArrayName = null): array
    {
        $array = (new \App\Imports\ImportRow())->toArray($file);

        $data_array = $array[0];
        $headerKeys = (array)array_filter(array_shift($data_array));
        $row_length = count($headerKeys);

        $formattedData = [];
        foreach ($data_array as $row) {

            if ($row) {

                $row = array_slice($row, 0, $row_length);

                if (sizeof($row) == sizeof($headerKeys)) {
                    $associatedRowData = array_combine($headers, $row);
                    if ($nestedArrayName) {
                        $nested = $associatedRowData[$nestedArrayName];
                        if (!empty($nested)) {
                            $associatedRowData[$nestedArrayName] = explode(',', $associatedRowData[$nestedArrayName]);
                        } else {
                            $associatedRowData[$nestedArrayName] = [];
                        }
                    }
                    $formattedData[] = $associatedRowData;
                } else {
                    $formattedData[] = $emptyUnit;
                }
            }
        }

        return $formattedData;
    }


    /**
     * @param $file
     * @param array $headers
     * @param string|null $nestedArrayName
     * @return array
     */
    public static function xlsxToArray($file, array $headers, string $nestedArrayName = null): array
    {
        $array = (new ImportRow())->toArray($file);

        $data_array = $array[0];
        $headerKeys = (array)array_filter(array_shift($data_array));
        $row_length = count($headerKeys);

        $formattedData = [];
        foreach ($data_array as $row) {

            if ($row) {

                $row = array_slice($row, 0, $row_length);

                if (!empty(array_filter($row)) && sizeof($row) == sizeof($headerKeys)) {
                    $associatedRowData = array_combine($headers, $row);
                    if ($nestedArrayName) {
                        $nested = $associatedRowData[$nestedArrayName];
                        if (!empty($nested)) {
                            $associatedRowData[$nestedArrayName] = explode(',', $nested);
                        } else {
                            $associatedRowData[$nestedArrayName] = [];
                        }
                    }

                    $formattedData[] = $associatedRowData;
                }
            }
        }

        return $formattedData;
    }


    /**
     * @param $filePath
     * @param $empty_unit
     * @param array $headers
     * @param int $length
     * @param $nestedArrayName
     * @return array
     */
    public static function csvToArray($filePath, $empty_unit, array $headers, int $length = 1000, $nestedArrayName = null): array
    {
        $csvToRead = fopen($filePath, 'r');

        while (!feof($csvToRead)) {
            $row = fgetcsv($csvToRead, $length, ';');
//            if(!empty($row) && count($row) == 1 || empty($row)){continue;}
            $rows[] = $row;
        }
        fclose($csvToRead);

        $headerKeys = array_shift($rows);

        $formattedData = [];
        foreach ($rows as $row) {
            if ($row) {
                if (sizeof($row) == sizeof($headerKeys)) {
                    $associatedRowData = array_combine($headers, $row);
                    if ($nestedArrayName) {
                        $nested = $associatedRowData[$nestedArrayName];
                        if (strlen($nested) > 1) {
                            $associatedRowData[$nestedArrayName] = explode(',', $nested);
                        } else {
                            $associatedRowData[$nestedArrayName] = [];
                        }
                    }
                    $formattedData[] = $associatedRowData;
                } else {
                    $formattedData[] = $empty_unit;
                }
            }
        }

        /* delete empty rows from array end */
        $numRows = count($formattedData) - 1;
        for ($i = $numRows; $i >= 1; $i--) {
            if (!empty(array_filter($formattedData[$i]))) {
                break;
            }
            unset($formattedData[$i]);
        }

        return $formattedData;
    }


    /**
     * @param $data
     * @param string $fileExtension
     * @param array $emptyUnit
     * @param array $heading
     * @param string|null $nestedArrayName
     * @return array
     */
    public static function getArray($data, string $fileExtension, array $emptyUnit, array $heading, string $nestedArrayName = null): array
    {
        $array = [];

        if (in_array($fileExtension, ['csv'])) {

            $array = self::csvToArray($data, $emptyUnit, $heading, $nestedArrayName);
        } elseif (in_array($fileExtension, ['xls'])) {

            $array = self::xlsToArray($data, $emptyUnit, $heading, $nestedArrayName);
        } elseif (in_array($fileExtension, ['xlsx'])) {

            $array = self::xlsxToArray($data, $heading, $nestedArrayName);
        }

        return $array;
    }
}
