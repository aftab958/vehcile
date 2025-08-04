<?php
header("Content-Type: application/json");

$id = isset($_GET['id']) ? $_GET['id'] : '';
if (empty($id)) {
    echo json_encode(["status" => "error", "message" => "No ID provided"]);
    exit;
}

$url = "https://domicile.punjab.gov.pk/AjaxCall.aspx?ID=" . urlencode($id);
$html = @file_get_contents($url);

if ($html === false || strlen($html) < 50) {
    echo json_encode(["status" => "error", "message" => "Failed to fetch data or empty response"]);
    exit;
}

// Load HTML into DOM
libxml_use_internal_errors(true);
$doc = new DOMDocument();
$doc->loadHTML($html);
$xpath = new DOMXPath($doc);

// Helper function
function getSpanText($xpath, $id) {
    $nodes = $xpath->query("//span[@id='$id']");
    return $nodes->length > 0 ? trim($nodes->item(0)->textContent) : null;
}

// Extracting required fields
$data = [
    "Name" => getSpanText($xpath, "lblName"),
    "FatherName" => getSpanText($xpath, "lblGaudain2"),
    "District" => getSpanText($xpath, "lblDistrict"),
    "Tehsil" => getSpanText($xpath, "lblTehsil"),
    "Status" => getSpanText($xpath, "lblStatus"),
    "SubmissionDate" => getSpanText($xpath, "lblSubmissionDate")
];

echo json_encode([
    "status" => "success",
    "id" => $id,
    "data" => $data
], JSON_PRETTY_PRINT);
