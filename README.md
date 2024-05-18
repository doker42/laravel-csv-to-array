# laravel-csv-to-array
laravel helper convert csv/xls/xlsx to array

base usage

$emptyUnit = [
    'key_a' => '',
    'key_b' => '',
    'key_c' => '',
];

$heading = [
    'key_a',
    'key_b',
    'key_c',
];

$array = BaseImport::getArray(
    $file,
    $fileExtension,
    $emptyUnit,
    $heading
);