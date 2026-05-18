<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte - {{ $finca->nombre }}</title>
    <style>
        @page { margin: 24px 32px; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #2c3e50;
            font-size: 11px;
        }
        .header {
            border-bottom: 3px solid #00B894;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .brand {
            color: #00B894;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1.5px;
        }
        h1 {
            font-size: 22px;
            margin: 6px 0 4px;
            color: #1a1a1a;
        }
        .meta {
            color: #888;
            font-size: 10px;
        }
        .info-box {
            background: #F4F8F7;
            border-left: 4px solid #00B894;
            padding: 12px 16px;
            margin-bottom: 16px;
        }
        .info-row { margin: 3px 0; }
        .info-label { color: #666; font-weight: bold; display: inline-block; min-width: 100px; }
        .summary {
            background: #00B894;
            color: white;
            padding: 14px;
            border-radius: 6px;
            margin-bottom: 16px;
            text-align: center;
        }
        .summary .num {
            font-size: 28px;
            font-weight: bold;
            display: block;
        }
        .summary .label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        thead th {
            background: #0F2E2E;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        tbody td {
            padding: 6px;
            border-bottom: 1px solid #E5E5E5;
        }
        tbody tr:nth-child(even) { background: #F9FAFB; }
        .estado {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .estado-bien { background: #D4F4EA; color: #00B894; }
        .estado-enfermo { background: #FADBD8; color: #E74C3C; }
        .estado-medicado, .estado-en-tratamiento { background: #FCEEC8; color: #F39C12; }
        .estado-muerto { background: #E5E5E5; color: #7F8C8D; }
        .estado-vendido { background: #E8DAEF; color: #9B59B6; }
        .footer {
            position: fixed;
            bottom: 0; left: 32px; right: 32px;
            border-top: 1px solid #ddd;
            padding-top: 8px;
            font-size: 9px;
            color: #999;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">BOVWEIGHT CR</div>
        <h1>Reporte de Finca: {{ $finca->nombre }}</h1>
        <div class="meta">Generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="info-box">
        @if($finca->ubicacion)
            <div class="info-row"><span class="info-label">Ubicación:</span> {{ $finca->ubicacion }}</div>
        @endif
        @if($finca->hectareas)
            <div class="info-row"><span class="info-label">Hectáreas:</span> {{ $finca->hectareas }} ha</div>
        @endif
        @if($finca->notas)
            <div class="info-row"><span class="info-label">Notas:</span> {{ $finca->notas }}</div>
        @endif
    </div>

    <div class="summary">
        <span class="num">{{ $animales->count() }}</span>
        <span class="label">Animales registrados</span>
    </div>

    @if($animales->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>N° Arete</th>
                    <th>Nombre</th>
                    <th>Sexo</th>
                    <th>Raza</th>
                    <th>Estado</th>
                    <th>Último peso</th>
                    <th>Fecha pesaje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($animales as $a)
                    <tr>
                        <td><strong>{{ $a->numero_arete }}</strong></td>
                        <td>{{ $a->nombre ?? '—' }}</td>
                        <td>{{ ucfirst($a->sexo) }}</td>
                        <td>{{ $a->raza }}</td>
                        <td>
                            @if($a->estado)
                                <span class="estado estado-{{ \Illuminate\Support\Str::slug($a->estado->nombre_estado) }}">
                                    {{ $a->estado->nombre_estado }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($a->ultimoHistorial)
                                {{ $a->ultimoHistorial->peso_real ?? $a->ultimoHistorial->peso }} kg
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $a->ultimoHistorial?->created_at?->format('d/m/Y') ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align:center; color:#888; padding:40px;">
            No hay animales registrados en esta finca.
        </p>
    @endif

    <div class="footer">
        BovWeight CR · Universidad de Costa Rica · Sede Guanacaste
    </div>
</body>
</html>