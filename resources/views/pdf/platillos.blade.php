<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Platillos y Existencias</title>
    <style>
        @font-face {
            font-family: 'Poppins';
            src: url("{{ resource_path('fonts/Poppins-Regular.ttf') }}") format("truetype");
            font-weight: normal;
        }

        @font-face {
            font-family: 'Quicksand';
            src: url("{{ resource_path('fonts/Quicksand-Bold.ttf') }}") format("truetype");
            font-weight: bold;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 40px;
        }

        h1 {
            font-family: 'Quicksand', sans-serif;
            font-size: 26px;
            text-align: center;
            color: #fe7f2d;
            margin-bottom: 10px;
        }

        .logo {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo img {
            width: 100px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #51bfcc;
            color: white;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h1
        style="
        font-family: 'quicksand', sans-serif;
        text-align: center;
        font-size: 48px;
        color: #fe7f2d;
        margin-bottom: 5px;
    ">
        Cheesito
    </h1>

    <h2
        style="
        font-family: 'poppins', sans-serif;
        text-align: center;
        font-size: 18px;
        color: #000;
        margin-top: 0;
        margin-bottom: 20px;
    ">
        Platillos y Existencias
    </h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Precio</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($platillos as $index => $platillo)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $platillo->nombre }}</td>
                    <td>{{ ucfirst($platillo->tipo) }}</td>
                    <td>${{ number_format($platillo->precio, 2) }}</td>
                    <td>{{ $platillo->inventario->cantidad_disponible }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
