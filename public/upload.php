<?php
// Ruta donde se guardarán los archivos subidos
$uploadDirectory = __DIR__ . '/uploads/';

// Verificar si el directorio existe, si no, crear el directorio
if (!is_dir($uploadDirectory)) {
    mkdir($uploadDirectory, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $filePath = $uploadDirectory . basename($file['name']);

    // Mover el archivo subido al directorio de destino
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Retornar la ruta del archivo almacenado
        echo json_encode(['file_path' => $filePath]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al subir el archivo.']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Solicitud inválida.']);
}
?>
