@extends('admin.navigation')

@section('content')
<?php
use App\Models\Section;
?>

<div class="mainSection-title">
  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
        <div class="d-flex flex-column">
          <h4>{{ get_phrase('Fee Generator') }}</h4>
          <ul class="d-flex align-items-center eBreadcrumb-2">
            <li><a href="#">{{ get_phrase('Home') }}</a></li>
            <li><a href="#">{{ get_phrase('Accounting') }}</a></li>
            <li><a href="#">{{ get_phrase('Fee Generator') }}</a></li>
          </ul>
        </div>
        <div class="d-flex flex-wrap" style="gap:10px;">
          <a class="btn btn-outline-secondary" href="{{ route('admin.fees.class_fees') }}">{{ get_phrase('Class Fees') }}</a>
          <a class="btn btn-outline-secondary" href="{{ route('admin.fees.concessions') }}">{{ get_phrase('Scholarships / Discounts') }}</a>
        </div>
      </div>
    </div>
  </div>
</div>

@php
  $form = $data ?? [
    'class_id' => '',
    'section_id' => '',
    'title' => '',
    'billing_month' => (int) date('m'),
    'billing_year' => (int) date('Y'),
    'due_date' => date('Y-m-d'),
    'pool_by_guardian' => 1,
  ];
@endphp

<div class="row">
  <div class="col-12">
    <div class="eSection-wrap-2">
      <form method="POST" action="{{ route('admin.fees.generator.preview') }}">
        @csrf
        <div class="row">
          <div class="col-md-3 fpb-7">
            <label class="eForm-label">{{ get_phrase('Class') }}</label>
            <select class="form-select eForm-select eChoice-multiple-with-remove" name="class_id" required onchange="classWiseSection(this.value)">
              <option value="">{{ get_phrase('Select a class') }}</option>
              @foreach($classes as $class)
                <option value="{{ $class->id }}" {{ (string) $form['class_id'] === (string) $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3 fpb-7">
            <label class="eForm-label">{{ get_phrase('Section') }}</label>
            <select class="form-select eForm-select eChoice-multiple-with-remove" name="section_id" id="section_id" required>
              @if(!empty($form['class_id']))
                @php $sections = Section::where('class_id', (int) $form['class_id'])->get(); @endphp
                <option value="">{{ get_phrase('Select section') }}</option>
                @foreach($sections as $sec)
                  <option value="{{ $sec->id }}" {{ (string) $form['section_id'] === (string) $sec->id ? 'selected' : '' }}>{{ $sec->name }}</option>
                @endforeach
              @else
                <option value="">{{ get_phrase('Select section') }}</option>
              @endif
            </select>
          </div>

          <div class="col-md-3 fpb-7">
            <label class="eForm-label">{{ get_phrase('Billing month') }}</label>
            <input type="number" class="form-control eForm-control" name="billing_month" min="1" max="12" value="{{ $form['billing_month'] }}" required>
          </div>

          <div class="col-md-3 fpb-7">
            <label class="eForm-label">{{ get_phrase('Billing year') }}</label>
            <input type="number" class="form-control eForm-control" name="billing_year" min="2000" max="2100" value="{{ $form['billing_year'] }}" required>
          </div>

          <div class="col-md-6 fpb-7">
            <label class="eForm-label">{{ get_phrase('Invoice title') }}</label>
            <input type="text" class="form-control eForm-control" name="title" value="{{ $form['title'] }}" placeholder="Fee {{ date('M Y') }}" required>
            <small class="text-muted">{{ get_phrase('Tip: Keep title same for mass generation (e.g. Fee Feb 2026).') }}</small>
          </div>

          <div class="col-md-3 fpb-7">
            <label class="eForm-label">{{ get_phrase('Due date') }}</label>
            <input type="date" class="form-control eForm-control" name="due_date" value="{{ $form['due_date'] }}">
          </div>

          <div class="col-md-3 fpb-7 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="pool_by_guardian" id="pool_by_guardian" value="1" {{ !empty($form['pool_by_guardian']) ? 'checked' : '' }}>
              <label class="form-check-label" for="pool_by_guardian">
                {{ get_phrase('Pool by Father CNIC (family)') }}
              </label>
            </div>
          </div>

          <div class="col-12 fpb-7 pt-2">
            <button class="btn-form" type="submit">{{ get_phrase('Preview') }}</button>
          </div>
        </div>
      </form>

      @if(request()->get('generated') && request()->get('fee_group_id'))
        <hr>
        <div class="alert alert-success mb-0">
          {{ get_phrase('Invoices generated.') }}
          @if(!empty($generatedFeeGroupId) && !empty($generatedGuardians))
            <div class="mt-2">
              <strong>{{ get_phrase('Family receipts') }}:</strong>
              <div class="mt-1" style="display:flex;gap:8px;flex-wrap:wrap;">
                @foreach($generatedGuardians as $gid)
                  <a class="btn btn-sm btn-primary" target="_blank"
                    href="{{ route('admin.fees.family_receipt', ['fee_group_id' => $generatedFeeGroupId, 'guardian_id' => $gid]) }}">
                    {{ get_phrase('Print') }} #{{ $gid }}
                  </a>
                @endforeach
              </div>
              <small class="text-muted d-block mt-2">{{ get_phrase('Tip: these are grouped by guardian (Father CNIC).') }}</small>
            </div>
          @else
            <div class="mt-2">
              <small class="text-muted">{{ get_phrase('Tip: set guardians (Father CNIC) to enable pooling/receipts.') }}</small>
            </div>
          @endif
        </div>
      @endif

      @if(!empty($preview))
        <hr>
        <h5 class="mb-2">{{ get_phrase('Preview') }}</h5>
        <div class="text-muted mb-3">
          <small>
            {{ get_phrase('Base fee title') }}: <strong>{{ $preview['fee_title'] }}</strong>,
            {{ get_phrase('Base amount') }}: <strong>{{ $preview['base_amount'] }}</strong>
          </small>
        </div>

        <form method="POST" action="{{ route('admin.fees.generator.generate') }}">
          @csrf
          <input type="hidden" name="class_id" value="{{ $data['class_id'] }}">
          <input type="hidden" name="section_id" value="{{ $data['section_id'] }}">
          <input type="hidden" name="title" value="{{ $data['title'] }}">
          <input type="hidden" name="billing_month" value="{{ $data['billing_month'] }}">
          <input type="hidden" name="billing_year" value="{{ $data['billing_year'] }}">
          <input type="hidden" name="due_date" value="{{ $data['due_date'] ?? '' }}">
          @if(!empty($preview['pool']))
            <input type="hidden" name="pool_by_guardian" value="1">
          @endif

          <div class="table-responsive">
            <table class="table eTable eTable-2">
              <thead>
                <tr>
                  <th>#</th>
                  <th>{{ get_phrase('Student') }}</th>
                  <th>{{ get_phrase('Father') }}</th>
                  <th>{{ get_phrase('CNIC') }}</th>
                  <th>{{ get_phrase('Base') }}</th>
                  <th>{{ get_phrase('Discount') }}</th>
                  <th>{{ get_phrase('Payable') }}</th>
                  <th>{{ get_phrase('Discount reason') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($preview['rows'] as $k => $r)
                  <tr>
                    <td>{{ $k + 1 }}</td>
                    <td>{{ $r['student_name'] }}</td>
                    <td>{{ $r['guardian_name'] ?: '(' . get_phrase('Not found') . ')' }}</td>
                    <td>{{ $r['guardian_cnic'] ?: '(' . get_phrase('Not found') . ')' }}</td>
                    <td>{{ $r['base_amount'] }}</td>
                    <td>{{ $r['discount_amount'] }}</td>
                    <td><strong>{{ $r['total_amount'] }}</strong></td>
                    <td class="text-muted" style="font-size:12px;">{{ $r['discount_reason'] ?? '' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="pt-2 d-flex justify-content-end">
            <button class="btn-form" type="submit">{{ get_phrase('Generate Invoices') }}</button>
          </div>
        </form>
      @endif

    </div>
  </div>
</div>

<script>
  "use strict";
  function classWiseSection(classId) {
    let url = "{{ route('admin.class_wise_sections', ['id' => ":classId"]) }}";
    url = url.replace(":classId", classId);
    $.ajax({
      url: url,
      success: function (response) {
        $('#section_id').html(response);
      }
    });
  }
  $(document).ready(function () {
    $(".eChoice-multiple-with-remove").select2();
  });
</script>

@endsection

