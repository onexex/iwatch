<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Information Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.4; }

        /* WATERMARK */
        .watermark {
            position: fixed;
            left: 20;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 100px;
            color: rgba(200,0,0,0.15);
            z-index: 0;
            pointer-events: none;
            text-transform: uppercase;
            font-weight: bold;
        }

        h2 { text-align: center; margin: 5px 0; }
        h2 { text-decoration: underline; font-weight: bold; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; z-index: 1; position: relative; }
        th, td { border: 1px solid #000; padding: 5px; vertical-align: top; word-wrap: break-word; }

        /* Number column */
        .num { width: 25px; text-align: center; font-weight: bold; }

        /* Label and value equal width */
        .label, .value { width: calc(50% - 25px); }

        th { background-color: #f0f0f0; text-align: left; }

        .section-title {
            font-weight: bold;
            margin-top: 15px;
            border: 1px solid #000;
            padding: 5px;
        }

        td p { margin: 0; }
    </style>
</head>
<body>
    <div class="watermark">COPY FOR {{ $copyFor ?? '' }}</div>

    <h3>A. Information Report/s Submitted</h3>
    <h2>INFORMATION REPORT</h2>

    <table>
        <tbody>
            <tr>
                <td class="num">1</td>
                <td class="label">FILE NUMBER</td>
                <td class="value">{{ $incident->file_number }}</td>
            </tr>
            <tr>
                <td class="num">2</td>
                <td class="label">REFERENCE</td>
                <td class="value">{{ $incident->reference }}</td>
            </tr>
            <tr>
                <td class="num">3</td>
                <td class="label">SUBJECT</td>
                <td class="value">{{ $incident->subject }}</td>
            </tr>
            <tr>
                <td class="num">4</td>
                <td class="label">DATE OF REPORT</td>
                <td class="value">{{ $incident->date_of_report }}</td>
            </tr>
            <tr>
                <td class="num">5</td>
                <td class="label">REPORTER</td>
                <td class="value">{{ $incident->reporter }}</td>
            </tr>
            <tr>
                <td class="num">6</td>
                <td class="label">DESIGNATION/UNIT ASSIGNMENT</td>
                <td class="value">{{ $incident->designation }}</td>
            </tr>
            <tr>
                <td class="num">7</td>
                <td class="label">EVALUATION</td>
                <td class="value">{{ $incident->evaluation }}</td>
            </tr>
            <tr>
                <td class="num">8</td>
                <td class="label">SOURCE</td>
                <td class="value">{{ $incident->source }}</td>
            </tr>
            <tr>
                <td class="num">9</td>
                <td class="label">DATE ACQUIRED</td>
                <td class="value">{{ $incident->date_acquired }}</td>
            </tr>
            <tr>
                <td class="num">10</td>
                <td class="label">MANNER ACQUIRED</td>
                <td class="value">{{ $incident->manner_acquired }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">11. INFORMATION PROPER</div>
    <p>
        {!! nl2br(e($incident->information_proper)) !!}
    </p>

    <table>
        <tbody>
            <tr>
                <td class="num">12</td>
                <td class="label">COMMENTS/ANALYSIS</td>
                <td class="value">{!! nl2br(e($incident->comments)) !!}</td>
            </tr>
            <tr>
                <td class="num">13</td>
                <td class="label">ACTION/s TAKEN/TO BE UNDERTAKEN/REQUESTED</td>
                <td class="value">{!! nl2br(e($incident->action_taken)) !!}</td>
            </tr>
            <tr>
                <td class="num">14</td>
                <td class="label">ATTACHMENT</td>
                <td class="value">{{ $incident->attachment ?? 'N/A' }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
