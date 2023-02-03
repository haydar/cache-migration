<?php

$questions = [
    ':package_name' => 'Package Name',
    ':package_vendor' => 'Package Vendor',
    ':package_title' => 'Package Title',
    ':package_description' => 'Package Description',
    ':author_name' => 'Author Name',
    ':author_email' => 'Author Email',
    ':author_homepage' => 'Author Homepage',
];

$files =  [
    'README.md',
    'composer.json',
    'src/PackageServiceProvider.php'
];

echo <<<_LOGO

    __                     __              ____
   / /   ____ __________ _/ /_____  ____  / / /_  ____  _  __
  / /   / __ `/ ___/ __ `/ __/ __ \/ __ \/ / __ \/ __ \| |/_/
 / /___/ /_/ / /  / /_/ / /_/ /_/ / /_/ / / /_/ / /_/ />  <
/_____/\__,_/_/   \__,_/\__/\____/\____/_/_.___/\____/_/|_|


_LOGO;

foreach ($questions as $key => $question) {
    $answers[$key] = readline(sprintf('%s : ', $question));
}

foreach ($files as $file) {
    $content = file_get_contents($file);
    $content = str_replace(array_keys($answers), array_values($answers), $content);
    file_put_contents($file, $content);
}

echo 'Completed.'.PHP_EOL;

if (readline('Delete file ? [y/n] ') === 'y') {
    unlink('configure.php');
}
