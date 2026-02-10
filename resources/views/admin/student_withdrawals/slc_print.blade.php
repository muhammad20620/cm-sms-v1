<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ get_phrase('School Leaving Certificate') }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color:#111; }
        .wrap { max-width: 900px; margin: 0 auto; padding: 24px; }
        .header { text-align: center; border-bottom: 2px solid #111; padding-bottom: 12px; margin-bottom: 18px; }
        .header h1 { margin: 0; font-size: 22px; }
        .header h2 { margin: 6px 0 0; font-size: 14px; font-weight: normal; }
        .title { text-align:center; margin: 18px 0; font-size: 18px; font-weight: bold; text-decoration: underline; }
        .meta { display:flex; justify-content: space-between; margin-bottom: 12px; font-size: 13px; }
        table { width:100%; border-collapse: collapse; margin-top: 10px; }
        td { padding: 8px 10px; border: 1px solid #333; vertical-align: top; font-size: 13px; }
        .label { width: 35%; font-weight: bold; background: #f5f5f5; }
        .sign { display:flex; justify-content: space-between; margin-top: 60px; }
        .sign .box { width: 30%; text-align:center; }
        .sign .line { border-top: 1px solid #111; margin-top: 40px; padding-top: 6px; font-size: 13px; }
        @media print { .no-print { display:none; } }
    </style>
</head>
<body>
<div class="wrap">
    <div class="no-print" style="text-align:right;margin-bottom:10px;">
        <button onclick="window.print()">{{ get_phrase('Print') }}</button>
    </div>

    <div class="header">
        <h1>{{ $school->title ?? get_phrase('School') }}</h1>
        <h2>{{ $school->address ?? '' }} {{ !empty($school->phone) ? (' | '.$school->phone) : '' }}</h2>
    </div>

    <div class="title">{{ get_phrase('School Leaving Certificate') }}</div>

    <div class="meta">
        <div>{{ get_phrase('SLC No') }}: <strong>{{ $withdrawal->slc_no }}</strong></div>
        <div>{{ get_phrase('Issue Date') }}: <strong>{{ $withdrawal->slc_issue_date ?? $withdrawal->created_at->format('Y-m-d') }}</strong></div>
    </div>

    <table>
        <tr>
            <td class="label">{{ get_phrase('Student Name') }}</td>
            <td>{{ $student->name ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">{{ get_phrase('Admission No') }}</td>
            <td><code>{{ $withdrawal->admission_no }}</code></td>
        </tr>
        <tr>
            <td class="label">{{ get_phrase('Enrollment No') }}</td>
            <td><code>{{ $withdrawal->enrollment_no }}</code></td>
        </tr>
        <tr>
            <td class="label">{{ get_phrase('Father Name') }}</td>
            <td>{{ $withdrawal->father_name }}</td>
        </tr>
        <tr>
            <td class="label">{{ get_phrase('Father CNIC') }}</td>
            <td>{{ $withdrawal->father_cnic }}</td>
        </tr>
        <tr>
            <td class="label">{{ get_phrase('Withdrawal Date') }}</td>
            <td>{{ $withdrawal->withdrawal_date }}</td>
        </tr>
        <tr>
            <td class="label">{{ get_phrase('Reason') }}</td>
            <td>{{ $withdrawal->reason }}</td>
        </tr>
        <tr>
            <td class="label">{{ get_phrase('Dues') }}</td>
            <td>
                @if(!empty($withdrawal->dues_cleared))
                    {{ get_phrase('Cleared') }}
                @else
                    {{ get_phrase('Not cleared') }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">{{ get_phrase('Remarks') }}</td>
            <td>{{ $withdrawal->remarks }}</td>
        </tr>
    </table>

    <div class="sign">
        <div class="box">
            <div class="line">{{ get_phrase('Class Teacher') }}</div>
        </div>
        <div class="box">
            <div class="line">{{ get_phrase('Principal') }}</div>
        </div>
        <div class="box">
            <div class="line">{{ get_phrase('School Stamp') }}</div>
        </div>
    </div>
</div>
</body>
</html>

