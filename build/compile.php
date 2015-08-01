<?php
/**
 * Compiler for the Browser.
 * Creates a single PHP file for the browser, so it can be uploaded as a single
 * file.
 */
chdir(__DIR__);
echo "Starting compilation process..\n";

// Configuration
$configFile = __DIR__ . '/config.json';
echo "Loading configuration file {$configFile}..\n";
if (!file_exists($configFile)) {
    echo 'Config file not found.';
    exit(1);
}

$config = json_decode(file_get_contents($configFile), true);
echo "Configuration loaded.\n";

// Check for existing output directory
$outputDir = $config['output']['path'];
if (!is_dir($outputDir)) {
    echo "{$outputDir} does not exist. Creating... ";

    if (@mkdir($outputDir) === false) {
        echo "Fail!\n";
        exit(1);
    }

    echo "Success!\n";
} else {
    echo "Output directory {$outputDir} exists.\n";
}

// Check for a writable output directory
if (!is_writable($outputDir)) {
    echo "{$outputDir} is not writable!\n";
    exit(1);
}
echo "Output directory {$outputDir} is writable.\n";

// Check for existing source directory
$sourceDir = $config['input']['path'];
if (!is_dir($sourceDir)) {
    echo "Source directory {$sourceDir} does not exist!\n";
    exit(1);
}
echo "Source directory {$sourceDir} exists.\n";

// Check for existing templates directory
$templatesDir = $config['input']['templates'];
if (!is_dir($templatesDir)) {
    echo "Template directory {$templatesDir} does not exist!\n";
    exit(1);
}
echo "Template directory {$templatesDir} exists.\n";

// Fetch the templates
$templates = array();
foreach (glob($templatesDir . '*.php') as $template) {
    $templates[substr(basename($template), 0, -4)] = file_get_contents($template);
}

// Create the template class
$output = "<?php\nclass Template {\n";

foreach ($templates as $templateName => $templateContents) {
    $parsed = strtr(
        $templateContents,
        array(
            '$' => '\\$',
            '"' => '\\"',
            "\n" => '\\n',
            "\r" => '\\r',
            "\t" => '\\t'
        )
    );
    $output .= "  private \${$templateName} = \"{$parsed}\";\n";
}

$output .= "  public function getTemplateFilename(\$template) {";
$output .= "    \$templateValue = \$this->\$template;";
$output .= "    if (!in_array(\"template\", stream_get_wrappers())) {stream_wrapper_register(\"template\", \"VariableStream\");}";
$output .= "    \$fp = fopen(\"template://{\$template}\", \"w\");fwrite(\$fp, \$templateValue);fclose(\$fp);return \"template://{\$template}\";";
$output .= "  }";
$output .= "}\n";
$output .= "class VariableStream {private \$position;private \$varname;function stream_open(\$path, \$mode, \$options, &\$opened_path){\$url = parse_url(\$path);\$this->varname = \$url[\"host\"];\$this->position = 0;return true;}function stream_read(\$count){\$ret = substr(\$GLOBALS[\$this->varname], \$this->position, \$count);\$this->position += strlen(\$ret);return \$ret;}function stream_write(\$data){\$left = substr(\$GLOBALS[\$this->varname], 0, \$this->position);\$right = substr(\$GLOBALS[\$this->varname], \$this->position + strlen(\$data));\$GLOBALS[\$this->varname] = \$left . \$data . \$right;\$this->position += strlen(\$data);return strlen(\$data);}function stream_tell(){return \$this->position;}function stream_eof(){return \$this->position >= strlen(\$GLOBALS[\$this->varname]);}function stream_seek(\$offset, \$whence){switch (\$whence) {case SEEK_SET:if (\$offset < strlen(\$GLOBALS[\$this->varname]) && \$offset >= 0) { \$this->position = \$offset; return true;} else { return false;}break;case SEEK_CUR:if (\$offset >= 0) { \$this->position += \$offset; return true;} else { return false;}break;case SEEK_END:if (strlen(\$GLOBALS[\$this->varname]) + \$offset >= 0) { \$this->position = strlen(\$GLOBALS[\$this->varname]) + \$offset; return true;} else { return false;}break;default:return false;}}function stream_metadata(\$path, \$option, \$var) {if(\$option == STREAM_META_TOUCH) {\$url = parse_url(\$path);\$varname = \$url[\"host\"];if(!isset(\$GLOBALS[\$varname])) {\$GLOBALS[\$varname] = '';}return true;}return false;}}\n";

// Search for the source files
function recursiveSearch($path, array $excludeList = array())
{
    echo "indexing {$path}\n";
    $result = array();
    $all = glob($path . '*');
    if (!empty($all)) {
        foreach ($all as $one) {
            if (!in_array($one, $excludeList)) {
                echo "Processing {$one}... ";
                if (is_dir($one)) {
                    echo "Directory\n";
                    $result = array_merge($result, recursiveSearch($one . '/', $excludeList));
                } elseif (strtolower(substr($one, -4)) === '.php') {
                    echo "PHP file\n";
                    $result[] = $one;
                } else {
                    echo "Not a valid PHP file\n";
                    exit(1);
                }
            }
        }
    }

    return $result;
}

// Find all the PHP files
$files = recursiveSearch(
    $sourceDir,
    array(
        $sourceDir . 'index.php',
        substr($templatesDir, 0, -1),
        '.',
        '..'
    )
);

// Reverse sort the file list, so the executing script moves to the end
rsort($files);

// Add file contents
foreach ($files as $file) {
    $output .= substr(file_get_contents($file), 5);
}

// Save to the output file
$outputFile = $config['output']['path'] . $config['output']['filename'];
echo "Writing output file {$outputFile}... ";
$result = @file_put_contents($outputFile, $output);
echo $result ? "OK!\n" : "FAIL!\n";

// If not succeeded, exit with code 1
if ($result === false) {
    exit(1);
}
