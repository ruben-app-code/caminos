<?php

$directorio = __DIR__ . '/hojas-informativas';
$logo = 'logo.png';

$ordenarPor = $_GET['ordenar'] ?? 'fecha';
$direccion = $_GET['dir'] ?? 'desc';

$imagenes = [];

/*
|--------------------------------------------------------------------------
| Leer imágenes
|--------------------------------------------------------------------------
*/

if (is_dir($directorio)) {

    $archivos = scandir($directorio);

    foreach ($archivos as $archivo) {

        $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

        if (in_array($extension, ['png', 'jpg', 'jpeg'])) {

            $rutaCompleta = $directorio . '/' . $archivo;

            $imagenes[] = [
                'nombre' => $archivo,
                'ruta'   => 'hojas-informativas/' . $archivo,
                'fecha'  => filemtime($rutaCompleta),
            ];
        }
    }
}

/*
|--------------------------------------------------------------------------
| Ordenamiento seguro
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

/*
|--------------------------------------------------------------------------
| Imagen seleccionada segura
|--------------------------------------------------------------------------
|
| Ya NO se recibe una ruta.
| Ahora solo se recibe un índice numérico:
|
| ?img=0
| ?img=1
|
*/

$idImagen = isset($_GET['img'])
    ? intval($_GET['img'])
    : null;

$imagenActual = null;

if (
    $idImagen !== null &&
    isset($imagenes[$idImagen])
) {
    $imagenActual = $imagenes[$idImagen];
}

/*
|--------------------------------------------------------------------------
| Helper URL
|--------------------------------------------------------------------------
*/

function buildUrl($params = [])
{
    $query = array_merge($_GET, $params);

    return '?' . http_build_query($query);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="UTF-8">

    <title>Shomer Hadavar</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>

        body{
            background:
                linear-gradient(
                    135deg,
                    #f7f1e7 0%,
                    #efe3d1 50%,
                    #e6d3bc 100%
                );
        }

        .sidebar{
            background: rgba(255,255,255,0.45);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(120,80,30,.15);
        }

        .menu-item{
            transition: .2s ease;
        }

        .menu-item:hover{
            background: rgba(120,80,30,.08);
        }

        .gold-text{
            color:#8a5a18;
        }

        .soft-card{
            background: rgba(255,255,255,0.35);
            backdrop-filter: blur(8px);
            border:1px solid rgba(120,80,30,.12);
        }

        .viewer{
            background:
                radial-gradient(
                    circle at center,
                    rgba(255,255,255,.65),
                    rgba(235,220,200,.45)
                );
        }

        ::-webkit-scrollbar{
            width:8px;
        }

        ::-webkit-scrollbar-thumb{
            background:#c7a16a;
            border-radius:10px;
        }

    </style>

</head>

<body class="text-[#4d3416]">

<div class="flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside class="w-80 sidebar flex flex-col">

        <!-- LOGO -->
        <div class="p-6 border-b border-[#c9aa7a55]">

            <div class="soft-card rounded-3xl p-5 shadow-xl">

                <img
                    src="<?= htmlspecialchars($logo) ?>"
                    class="w-full rounded-2xl object-contain"
                    alt="Logo"
                >

            </div>

        </div>

        <!-- CONTROLES -->
        <div class="p-5 border-b border-[#c9aa7a55]">

            <div class="uppercase tracking-widest text-xs mb-4 gold-text font-bold">
                Ordenar imágenes
            </div>

            <div class="grid grid-cols-2 gap-2">

                <a
                    href="<?= buildUrl([
                        'ordenar' => 'nombre',
                        'dir' => 'asc'
                    ]) ?>"
                    class="soft-card px-3 py-2 rounded-xl text-sm text-center"
                >
                    Nombre ↑
                </a>

                <a
                    href="<?= buildUrl([
                        'ordenar' => 'nombre',
                        'dir' => 'desc'
                    ]) ?>"
                    class="soft-card px-3 py-2 rounded-xl text-sm text-center"
                >
                    Nombre ↓
                </a>

                <a
                    href="<?= buildUrl([
                        'ordenar' => 'fecha',
                        'dir' => 'asc'
                    ]) ?>"
                    class="soft-card px-3 py-2 rounded-xl text-sm text-center"
                >
                    Fecha ↑
                </a>

                <a
                    href="<?= buildUrl([
                        'ordenar' => 'fecha',
                        'dir' => 'desc'
                    ]) ?>"
                    class="soft-card px-3 py-2 rounded-xl text-sm text-center"
                >
                    Fecha ↓
                </a>

            </div>

        </div>

        <!-- LISTA -->
        <div class="flex-1 overflow-y-auto">

            <?php foreach ($imagenes as $index => $img): ?>

                <a
                    href="<?= buildUrl([
                        'img' => $index
                    ]) ?>"
                    class="menu-item block px-5 py-4 border-b border-[#c9aa7a22]"
                >

                    <div class="font-semibold text-sm break-all">
                        <?= htmlspecialchars($img['nombre']) ?>
                    </div>

                    <div class="text-xs mt-2 text-[#8f6b42]">
                        <?= date('Y-m-d H:i', $img['fecha']) ?>
                    </div>

                </a>

            <?php endforeach; ?>

        </div>

    </aside>

    <!-- VISOR -->
    <main class="flex-1 viewer overflow-auto p-10 flex items-center justify-center">

        <?php if ($imagenActual): ?>

            <div class="soft-card p-5 rounded-[35px] shadow-2xl max-w-full">

                <img
                    src="<?= htmlspecialchars($imagenActual['ruta']) ?>"
                    class="max-w-full max-h-[90vh] rounded-2xl"
                    alt=""
                >

            </div>

        <?php else: ?>

            <div class="text-center">

                <div class="text-5xl mb-5 gold-text font-serif">
                    Shomer Hadavar
                </div>

                <div class="text-lg text-[#8f6b42]">
                    Selecciona una hoja informativa
                </div>

            </div>

        <?php endif; ?>

    </main>

</div>

</body>
</html>