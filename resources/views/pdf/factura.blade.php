<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura #{{ $factura->id }}</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            color: #000;
        }

        h1 {
            font-family: 'Quicksand', sans-serif;
            color: #fe7f2d;
            text-align: center;
            font-size: 36px;
            margin-bottom: 5px;
        }

        h2 {
            font-family: 'Poppins', sans-serif;
            color: #000;
            font-size: 18px;
            text-align: center;
            margin-top: 0;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px 12px;
            text-align: left;
        }

        th {
            background-color: #51bfcc;
            color: #fff;
        }

        .totales {
            margin-top: 20px;
            text-align: right;
        }

        .totales p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h1>Cheesito</h1>
    <h2>Factura y Detalle de Consumo</h2>

    <p><strong>Fecha:</strong> {{ optional($factura->fecha)->format('d/m/Y H:i') ?? 'N/D' }}</p>
    <p><strong>Mesa:</strong> {{ optional($factura->orden->mesa)->num_mesa ?? 'Sin mesa asignada' }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Platillo</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($factura->orden->detalles as $index => $detalle)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detalle->platillo->nombre }}</td>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>${{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totales">
        <p><strong>Subtotal:</strong> ${{ number_format($factura->subtotal, 2) }}</p>
        <p><strong>Propina (10%):</strong> ${{ number_format($factura->propina, 2) }}</p>
        <p><strong>Total:</strong> ${{ number_format($factura->total, 2) }}</p>
    </div>
</body>
</html>
