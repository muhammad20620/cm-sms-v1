<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ get_phrase('Family Fee Receipt') }}</title>
  <style>
    body { font-family: Arial, Helvetica, sans-serif; color:#111; }
    .wrap { max-width: 900px; margin: 0 auto; padding: 24px; }
    .header { text-align: center; border-bottom: 2px solid #111; padding-bottom: 12px; margin-bottom: 18px; }
    .header h1 { margin: 0; font-size: 20px; }
    .meta { display:flex; justify-content: space-between; gap: 12px; margin: 12px 0; font-size: 13px; }
    table { width:100%; border-collapse: collapse; margin-top: 10px; }
    th, td { padding: 8px 10px; border: 1px solid #333; vertical-align: top; font-size: 13px; text-align:left; }
    th { background:#f5f5f5; }
    .totals { margin-top: 12px; display:flex; justify-content: flex-end; }
    .totals table { width: 320px; }
    .no-print { text-align:right; margin-bottom:10px; }
    @media print { .no-print { display:none; } }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="no-print">
      <button onclick="window.print()">{{ get_phrase('Print') }}</button>
    </div>

    <div class="header">
      <h1>{{ get_phrase('Family Fee Receipt') }}</h1>
      <div style="margin-top:6px;font-size:13px;">
        {{ get_phrase('Group') }}: <code>{{ $feeGroupId }}</code>
      </div>
    </div>

    <div class="meta">
      <div>
        <strong>{{ get_phrase('Father') }}:</strong>
        {{ $guardian->name ?? '(' . get_phrase('Not found') . ')' }}<br>
        <strong>{{ get_phrase('CNIC') }}:</strong>
        {{ $guardian->id_card_no ?? '(' . get_phrase('Not found') . ')' }}
      </div>
      <div style="text-align:right;">
        <strong>{{ get_phrase('Date') }}:</strong> {{ date('Y-m-d') }}
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>{{ get_phrase('Student') }}</th>
          <th>{{ get_phrase('Class') }}</th>
          <th>{{ get_phrase('Invoice Title') }}</th>
          <th>{{ get_phrase('Base') }}</th>
          <th>{{ get_phrase('Discount') }}</th>
          <th>{{ get_phrase('Payable') }}</th>
        </tr>
      </thead>
      <tbody>
        @php
          $sumBase = 0; $sumDisc = 0; $sumPayable = 0;
        @endphp
        @foreach($invoices as $k => $inv)
          @php
            $sd = $students[$k] ?? null;
            $sumBase += (int) ($inv->amount ?? 0);
            $sumDisc += (int) ($inv->discounted_price ?? 0);
            $sumPayable += (int) ($inv->total_amount ?? 0);
          @endphp
          <tr>
            <td>{{ $k + 1 }}</td>
            <td>{{ $sd['name'] ?? $inv->student_id }}</td>
            <td>{{ $sd['class_name'] ?? '' }} / {{ $sd['section_name'] ?? '' }}</td>
            <td>{{ $inv->title }}</td>
            <td>{{ $inv->amount ?? 0 }}</td>
            <td>{{ $inv->discounted_price ?? 0 }}</td>
            <td><strong>{{ $inv->total_amount ?? 0 }}</strong></td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="totals">
      <table>
        <tr>
          <th>{{ get_phrase('Total Base') }}</th>
          <td>{{ $sumBase }}</td>
        </tr>
        <tr>
          <th>{{ get_phrase('Total Discount') }}</th>
          <td>{{ $sumDisc }}</td>
        </tr>
        <tr>
          <th>{{ get_phrase('Grand Total') }}</th>
          <td><strong>{{ $sumPayable }}</strong></td>
        </tr>
      </table>
    </div>
  </div>
</body>
</html>

