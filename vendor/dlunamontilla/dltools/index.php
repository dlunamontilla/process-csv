<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

/**
 * Esta variable cualquier tipo de resultados.
 * @var string $results
 */
$results = "";

// Si estás en el directorio «public» o «public_html»,
// lo puedes utilizar así:
include __DIR__ . '/vendor/autoload.php';


use DLTools\Models\Database;
use DLTools\Models\Authenticate;

$db = new Database;
$authenticate = new Authenticate;


$hash = $db->getToken('tu-contraseña');

$is_valid = $db->validateToken('tu-contraseña', $hash);

$credentials = $db->getCredentials();
$tableName = $credentials->DL_TABLE_NAME;
$fieldName = $credentials->DL_FIELD_NAME;

/**
 * Get the data from the database
 */

$data = $db->select($fieldName)
    ->from($tableName)
    ->where($fieldName, '=', 'userA')
    ->first();

print_r($data);
echo "\n";

exit;
?>

<!DOCTYPE html>
<html lang="es-ES">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DLTools - Test</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="favicon.png" type="image/png">

    <style>
        :root {
            background-color: #092c3a;
            color: #fff;

            font-family: 'Open Sans', sans-serif;
            --logo-size: 100px;
        }

        body {
            margin: 0;
        }

        main {
            margin: 0 auto;
            padding: 40px;
            max-width: 1200px;
        }

        * {
            box-sizing: border-box;
        }

        *::before,
        *::after {
            box-sizing: inherit;
        }

        hr {
            border: 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            margin: 20px auto;
        }

        pre {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 5px;
            white-space: break-spaces;
            overflow: auto;
        }

        svg {
            width: var(--logo-size);
            height: var(--logo-size);
        }

        path {
            fill: var(--color);
            transition: 300ms ease;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;

            --color: rgba(255, 255, 255, 0.3);
        }

        .header__item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header__item:hover {
            --color: rgba(255, 220, 0, 0.5);
        }
    </style>
</head>
<body>
    <main>
        <header class="header">
            <div class="header__item">
                <span data-svg-src="assets/images/logo-DLTools.svg"></span>
                <h1>DLTools - Test</h1>
            </div>
            <div class="header__item">
                <h2>Visualizar resultados</h2>
            </div>
        </header>

        <hr>

        <section class="container">
            <pre><?= $hash; ?></pre>
            <pre><?= json_encode($is_valid); ?></pre>

            <h2>Data</h2>
            <pre><?php print_r($data); ?></pre>

            <hr>

            <pre><?= $query; ?></pre>
            <pre><?= $results; ?></pre>
        </section>
    </main>

    <script src="assets/js/getSVG.js"></script>
</body>
</html>