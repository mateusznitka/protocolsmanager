<?php

include('../../../inc/includes.php');

if (!Session::haveRight("config", READ)) {
    http_response_code(403);
    exit;
}

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/inc/generate.class.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Collect template settings from POST
$font         = !empty($_POST['font'])         ? $_POST['font']         : 'DejaVu Sans';
$fontsize     = !empty($_POST['fontsize'])      ? (int)$_POST['fontsize'] : 9;
$header_color = !empty($_POST['header_color'])  ? $_POST['header_color'] : '#dee2e6';
$breakword    = isset($_POST['breakword'])       ? (int)$_POST['breakword'] : 1;
$city         = !empty($_POST['city'])           ? $_POST['city']         : '';
$orientation  = !empty($_POST['orientation'])    ? $_POST['orientation']  : 'Portrait';
$serial_mode  = !empty($_POST['serial_mode'])    ? (int)$_POST['serial_mode'] : 1;
$man_mode     = !empty($_POST['man_mode'])       ? (int)$_POST['man_mode']    : 1;
$title        = !empty($_POST['template_name'])  ? nl2br(htmlspecialchars($_POST['template_name'])) : 'Preview';
$upper_content = !empty($_POST['template_uppercontent']) ? nl2br($_POST['template_uppercontent']) : '';
$content      = !empty($_POST['template_content'])       ? nl2br($_POST['template_content'])      : '';
$footer       = !empty($_POST['footer_text'])             ? nl2br($_POST['footer_text'])            : '';
$logo_existing = $_POST['logo_existing'] ?? '';

// Replace placeholders with sample values
$sample_owner  = 'Jan Kowalski';
$sample_author = 'Admin';
foreach (['upper_content', 'content', 'footer'] as $var) {
    $$var = str_replace('{cur_date}', date('d.m.Y'), $$var);
    $$var = str_replace('{owner}',    $sample_owner,  $$var);
    $$var = str_replace('{admin}',    $sample_author, $$var);
}

// Logo
if (!empty($logo_existing)) {
    $logo   = GLPI_ROOT . '/files/_pictures/' . $logo_existing;
    $backtop = '40mm';
    $islogo  = 1;
} else {
    $logo    = '';
    $backtop = '20mm';
    $islogo  = 0;
}

// Sample data
$prot_num   = 'PREVIEW';
$owner      = $sample_owner;
$author     = $sample_author;
$comments   = [null, null, null];

$number = [1, 2, 3];
$type_name   = [1 => 'Computer',    2 => 'Monitor',          3 => 'Printer'];
$man_name    = [1 => 'Dell',        2 => 'LG',               3 => 'HP'];
$mod_name    = [1 => 'OptiPlex 7090', 2 => 'UltraSharp 27"', 3 => 'LaserJet Pro'];
$item_name   = [1 => 'PC-001',      2 => 'MON-001',          3 => 'PRN-001'];
$serial      = [1 => 'SN123456',    2 => 'SN789012',         3 => 'SN345678'];
$otherserial = [1 => 'INV-001',     2 => 'INV-002',          3 => 'INV-003'];

ob_start();
include dirname(__DIR__) . '/inc/template.php';
$html = ob_get_clean();

$fd = PluginProtocolsmanagerGenerate::prepareFontDir();
$html = str_replace('</head>', "<style>
    @font-face{font-family:'Roboto';src:url('file://{$fd}Roboto-Regular.ttf');font-weight:normal;}
    @font-face{font-family:'Roboto';src:url('file://{$fd}Roboto-Bold.ttf');font-weight:bold;}
    @font-face{font-family:'Noto Serif';src:url('file://{$fd}NotoSerif-Regular.ttf');font-weight:normal;}
    @font-face{font-family:'Noto Serif';src:url('file://{$fd}NotoSerif-Bold.ttf');font-weight:bold;}
</style></head>", $html);

$options = new Options();
$options->set('defaultFont', $font);
$options->setChroot('/');
$options->setFontDir($fd);
$options->setFontCache($fd);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', $orientation);
$dompdf->render();
$pdf = $dompdf->output();

// Flush any GLPI/PHP output buffers so only PDF bytes are sent
while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="preview.pdf"');
header('Content-Length: ' . strlen($pdf));
echo $pdf;
exit;
