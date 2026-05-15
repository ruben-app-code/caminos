<?php

$directorio = __DIR__ . '/hojas-informativas';
$logo = 'logo.png';

$ordenarPor = $_GET['ordenar'] ?? 'fecha';
$direccion = $_GET['dir'] ?? 'desc';

$imagenes = [];

if (is_dir($directorio)) {

    $archivos = scandir($directorio);

    foreach ($archivos as $archivo) {

        $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

        if (in_array($extension, ['png', 'jpg', 'jpeg'])) {

            $rutaCompleta = $directorio . '/' . $archivo;

            $imagenes[] = [
                'nombre' => $archivo,
                'ruta' => 'hojas-informativas/' . $archivo,
                'fecha' => filemtime($rutaCompleta),
            ];
        }
    }
}

/*
|--------------------------------------------------------------------------
| Ordenamiento
|--------------------------------------------------------------------------
*/

usort($imagenes, function ($a, $b) use ($ordenarPor, $direccion) {

    if ($ordenarPor === 'nombre') {
        $resultado = strcasecmp($a['nombre'], $b['nombre']);
    } else {
        $resultado = $a['fecha'] <=> $b['fecha'];
    }

    return $direccion === 'asc'
        ? $resultado
        : -$resultado;
});

function buildUrl($ordenar, $dir)
{
    return "?ordenar={$ordenar}&dir={$dir}";
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Hojas Informativas</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            background: #111827;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-thumb {
            background: #374151;
            border-radius: 10px;
        }
    </style>
</head>
<body class="text-white">

<div class="flex h-screen">

    <!-- SIDEBAR -->
    <aside class="w-80 bg-gray-900 border-r border-gray-700 flex flex-col">

        <!-- LOGO -->
        <div class="p-6 border-b border-gray-700 flex justify-center">
            <img
                src="<?= $logo ?>"
                class="w-40 h-40 object-contain rounded-xl shadow-lg"
                alt="Logo"
            >
        </div>

        <!-- CONTROLES -->
        <div class="p-4 border-b border-gray-700 space-y-2">

            <div class="text-sm font-bold text-gray-300">
                Ordenar por:
            </div>

            <div class="flex gap-2 flex-wrap">

                <a
                    href="<?= buildUrl('nombre', 'asc') ?>"
                    class="px-3 py-2 bg-gray-800 hover:bg-gray-700 rounded-lg text-sm"
                >
                    Nombre ↑
                </a>

                <a
                    href="<?= buildUrl('nombre', 'desc') ?>"
                    class="px-3 py-2 bg-gray-800 hover:bg-gray-700 rounded-lg text-sm"
                >
                    Nombre ↓
                </a>

                <a
                    href="<?= buildUrl('fecha', 'asc') ?>"
                    class="px-3 py-2 bg-gray-800 hover:bg-gray-700 rounded-lg text-sm"
                >
                    Fecha ↑
                </a>

                <a
                    href="<?= buildUrl('fecha', 'desc') ?>"
                    class="px-3 py-2 bg-gray-800 hover:bg-gray-700 rounded-lg text-sm"
                >
                    Fecha ↓
                </a>

            </div>
        </div>

        <!-- LISTA -->
        <div class="flex-1 overflow-y-auto">

            <?php foreach ($imagenes as $img): ?>

                <a
                    href="?img=<?= urlencode($img['ruta']) ?>&ordenar=<?= $ordenarPor ?>&dir=<?= $direccion ?>"
                    class="block px-4 py-3 border-b border-gray-800 hover:bg-gray-800 transition"
                >
                    <div class="font-medium break-all">
                        <?= htmlspecialchars($img['nombre']) ?>
                    </div>

                    <div class="text-xs text-gray-400 mt-1">
                        <?= date('Y-m-d H:i', $img['fecha']) ?>
                    </div>
                </a>

            <?php endforeach; ?>

        </div>

    </aside>

    <!-- CONTENIDO -->
    <main class="flex-1 overflow-auto bg-gray-950 flex items-center justify-center p-6">

        <?php if (!empty($_GET['img'])): ?>

            <img
                src="<?= htmlspecialchars($_GET['img']) ?>"
                class="max-w-full max-h-full rounded-2xl shadow-2xl"
            >

        <?php else: ?>

            <div class="text-center text-gray-400">
                <div class="text-3xl mb-4">
                    Selecciona una imagen
                </div>

                <div>
                    Las imágenes aparecerán aquí
                </div>
            </div>

        <?php endif; ?>

    </main>

</div>

</body>
</html>