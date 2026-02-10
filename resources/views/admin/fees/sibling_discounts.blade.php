@extends('admin.navigation')

@section('content')

<div class="mainSection-title">
  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
        <div class="d-flex flex-column">
          <h4>{{ get_phrase('Sibling Discounts') }}</h4>
          <ul class="d-flex align-items-center eBreadcrumb-2">
            <li><a href="#">{{ get_phrase('Home') }}</a></li>
            <li><a href="#">{{ get_phrase('Accounting') }}</a></li>
            <li><a href="#">{{ get_phrase('Sibling Discounts') }}</a></li>
          </ul>
        </div>
        <div class="d-flex flex-wrap" style="gap:10px;">
          <a class="btn btn-outline-primary" href="{{ route('admin.fees.generator') }}">{{ get_phrase('Fee Generator') }}</a>
          <a class="btn btn-outline-secondary" href="{{ route('admin.fees.class_fees') }}">{{ get_phrase('Class Fees') }}</a>
          <a class="btn btn-outline-secondary" href="{{ route('admin.fees.concessions') }}">{{ get_phrase('Scholarships / Discounts') }}</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="eSection-wrap-2">
      <form method="POST" action="{{ route('admin.fees.sibling_discounts.store') }}" class="ajaxForm">
        @csrf
        <div class="row">
          <div class="col-md-4 fpb-7">
            <label class="eForm-label">{{ get_phrase('Basis for youngest child') }}</label>
            <select class="form-select eForm-select" name="basis" required>
              <option value="hybrid">{{ get_phrase('Hybrid (DOB if available, else class)') }}</option>
              <option value="dob">{{ get_phrase('Date of birth (DOB)') }}</option>
              <option value="class">{{ get_phrase('Smallest class (sort order)') }}</option>
            </select>
          </div>

          <div class="col-md-4 fpb-7">
            <label class="eForm-label">{{ get_phrase('Minimum children') }}</label>
            <input type="number" class="form-control eForm-control" name="min_children" value="2" min="1" max="10" required>
            <small class="text-muted">{{ get_phrase('Example: 2 means apply only if family has 2+ children enrolled.') }}</small>
          </div>

          <div class="col-md-4 fpb-7">
            <label class="eForm-label">{{ get_phrase('Apply to specific Father CNIC (optional)') }}</label>
            <select class="form-select eForm-select" name="guardian_id" id="guardian_id_select" style="width: 100%;">
              <option value="">{{ get_phrase('All families (no specific father)') }}</option>
            </select>
            <small class="text-muted">{{ get_phrase('Search father by name/CNIC, or leave empty to apply to all families.') }}</small>
          </div>

          <div class="col-md-3 fpb-7">
            <label class="eForm-label">{{ get_phrase('Mode') }}</label>
            <select class="form-select eForm-select" name="mode" required>
              <option value="percent">{{ get_phrase('Percent (%)') }}</option>
              <option value="fixed">{{ get_phrase('Fixed amount') }}</option>
            </select>
          </div>

          <div class="col-md-3 fpb-7">
            <label class="eForm-label">{{ get_phrase('Value') }}</label>
            <input type="number" class="form-control eForm-control" name="value" value="50" min="0" required>
          </div>

          <div class="col-md-3 fpb-7">
            <label class="eForm-label">{{ get_phrase('Session (optional)') }}</label>
            <input type="number" class="form-control eForm-control" name="session_id" value="{{ $activeSession }}">
            <small class="text-muted">{{ get_phrase('Set session to apply only in a specific session.') }}</small>
          </div>

          <div class="col-md-3 fpb-7 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
              <label class="form-check-label" for="is_active">{{ get_phrase('Active') }}</label>
            </div>
          </div>

          <div class="col-12 fpb-7">
            <label class="eForm-label">{{ get_phrase('Note') }}</label>
            <input type="text" class="form-control eForm-control" name="note" placeholder="e.g., Youngest child 50% discount">
          </div>

          <div class="col-12 fpb-7 pt-2">
            <button class="btn-form" type="submit">{{ get_phrase('Save Rule') }}</button>
          </div>
        </div>
      </form>

      <hr>

      <h5 class="mb-3">{{ get_phrase('Latest rules') }}</h5>
      @if(count($rules) > 0)
        <div class="table-responsive">
          <table class="table eTable eTable-2">
            <thead>
              <tr>
                <th>#</th>
                <th>{{ get_phrase('Basis') }}</th>
                <th>{{ get_phrase('Min children') }}</th>
                <th>{{ get_phrase('Scope') }}</th>
                <th>{{ get_phrase('Mode') }}</th>
                <th>{{ get_phrase('Value') }}</th>
                <th>{{ get_phrase('Session') }}</th>
                <th>{{ get_phrase('Active') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($rules as $k => $r)
                <tr>
                  <td>{{ $k + 1 }}</td>
                  <td>{{ $r->basis }}</td>
                  <td>{{ $r->min_children }}</td>
                  <td>
                    @if(empty($r->guardian_id))
                      {{ get_phrase('All families') }}
                    @else
                      @php
                        $g = !empty($guardiansById) && isset($guardiansById[$r->guardian_id]) ? $guardiansById[$r->guardian_id] : null;
                        $guardianText = null;
                        if (!empty($g)) {
                          $guardianText = trim(($g->name ?? '').' '.(!empty($g->id_card_no) ? ('('.$g->id_card_no.')') : ''));
                        }
                      @endphp
                      {{ $guardianText ?? ('#'.$r->guardian_id) }}
                    @endif
                  </td>
                  <td>{{ $r->mode }}</td>
                  <td>{{ $r->value }}</td>
                  <td>{{ $r->session_id ?? get_phrase('All') }}</td>
                  <td>
                    @if(!empty($r->is_active))
                      <span class="eBadge ebg-soft-success">{{ get_phrase('Yes') }}</span>
                    @else
                      <span class="eBadge ebg-soft-danger">{{ get_phrase('No') }}</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="alert alert-info mb-0">{{ get_phrase('No sibling discount rules found yet.') }}</div>
      @endif
    </div>
  </div>
</div>

<script>
  "use strict";
  document.addEventListener('DOMContentLoaded', function () {
    if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.select2) {
      console.warn('Select2 is not loaded; guardian search dropdown disabled.');
      return;
    }

    $('#guardian_id_select').select2({
      width: '100%',
      placeholder: "{{ get_phrase('Search father by name/CNIC') }}",
      allowClear: true,
      dropdownAutoWidth: true,
      dropdownCssClass: 'cm-select2-dropdown-search',
      minimumResultsForSearch: 0,
      ajax: {
        url: "{{ route('admin.fees.search.guardians') }}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return { q: params.term || '' };
        },
        processResults: function (data) {
          return data;
        },
        cache: true
      },
      minimumInputLength: 0
    });
  });
</script>

@endsection

