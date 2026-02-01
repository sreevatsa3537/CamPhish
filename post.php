<?php
header("Content-Type: application/json");

/* BASIC RATE LIMIT (1 frame / second / IP) */
session_start();
$now = time();
if (isset($_SESSION['last']) && ($now - $_SESSION['last']) < 1) {
  echo json_encode(["status" => "rate-limited"]);
  exit;
}
$_SESSION['last'] = $now;

/* VALIDATE INPUT */
if (!isset($_POST['frame'])) {
  echo json_encode(["status" => "no-data"]);
  exit;
}

$data = $_POST['frame'];

/* STRIP BASE64 HEADER */
$data = preg_replace('#^data:image/\w+;base64,#i', '', $data);
$image = base64_decode($data);

if ($image === false) {
  echo json_encode(["status" => "decode-failed"]);
  exit;
}

/* SAVE IMAGE */
$dir = "captures";
if (!is_dir($dir)) mkdir($dir, 0755);

$filename = $dir . "/" . time() . "_" . uniqid() . ".png";
file_put_contents($filename, $image);

echo json_encode(["status" => "ok"]);
