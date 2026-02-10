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
          <h4>{{ get_phrase('Scholarships / Discounts') }}</h4>
          <ul class="d-flex align-items-center eBreadcrumb-2">
            <li><a href="#">{{ get_phrase('Home') }}</a></li>
            <li><a href="#">{{ get_phrase('Accounting') }}</a></li>
            <li><a href="#">{{ get_phrase('Scholarships / Discounts') }}</a></li>
          </ul>
        </div>
        <div class="d-flex flex-wrap" style="gap:10px;">
          <a class="btn btn-outline-primary" href="{{ route('admin.fees.generator') }}">{{ get_phrase('Fee Generator') }}</a>
          <a class="btn btn-outline-secondary" href="{{ route('admin.fees.class_fees') }}">{{ get_phrase('Class Fees') }}</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="eSection-wrap-2">

      <form method="POST" action="{{ route('admin.fees.concessions.store') }}" class="ajaxForm">
        @csrf
        <div class="row">
          <div class="col-md-4 fpb-7">
            <label class="eForm-label">{{ get_phrase('Apply to') }}</label>
            <select class="form-select eForm-select" name="scope_type" id="scope_type" onchange="toggleScopeFields()" required>
              <option value="student">{{ get_phrase('Specific student') }}</option>
              <option value="guardian">{{ get_phrase('Whole family (Father CNIC)') }}</option>
            </select>
          </div>

          <div class="col-md-4 fpb-7" id="student_scope_block">
            <label class="eForm-label">{{ get_phrase('Student') }}</label>
            <select class="form-select eForm-select" name="student_id" id="student_id_select" style="width: 100%;">
              <option value="">{{ get_phrase('Search student (name, class, father, CNIC)') }}</option>
            </select>
            <small class="text-muted">{{ get_phrase('Click the field and type student name, class/section, father name or CNIC.') }}</small>
          </div>

          <div class="col-md-4 fpb-7 d-none" id="guardian_scope_block">
            <label class="eForm-label">{{ get_phrase('Father (CNIC)') }}</label>
            <select class="form-select eForm-select" name="guardian_id" id="guardian_id_select" style="width: 100%;">
              <option value="">{{ get_phrase('Search father by name/CNIC') }}</option>
            </select>
            <small class="text-muted">{{ get_phrase('Click the field and type father name or CNIC.') }}</small>
          </div>

          <div class="col-md-4 fpb-7">
            <label class="eForm-label">{{ get_phrase('Mode') }}</label>
            <select class="form-select eForm-select" name="mode" required>
              <option value="fixed">{{ get_phrase('Fixed amount') }}</option>
              <option value="percent">{{ get_phrase('Percent (%)') }}</option>
            </select>
          </div>

          <div class="col-md-4 fpb-7">
            <label class="eForm-label">{{ get_phrase('Value') }}</label>
            <input type="number" class="form-control eForm-control" name="value" min="0" required>
          </div>

          <div class="col-md-4 fpb-7">
            <label class="eForm-label">{{ get_phrase('Session') }}</label>
            <input type="number" class="form-control eForm-control" name="session_id" value="{{ $activeSession }}" placeholder="{{ get_phrase('Leave empty for all sessions') }}">
            <small class="text-muted">{{ get_phrase('Set session to apply only for a specific session.') }}</small>
          </div>

          <div class="col-md-8 fpb-7">
            <label class="eForm-label">{{ get_phrase('Note') }}</label>
            <input type="text" class="form-control eForm-control" name="note" placeholder="e.g., Sibling discount">
          </div>

          <div class="col-md-4 fpb-7 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
              <label class="form-check-label" for="is_active">{{ get_phrase('Active') }}</label>
            </div>
          </div>

          <div class="col-12 fpb-7 pt-2">
            <button class="btn-form" type="submit">{{ get_phrase('Save') }}</button>
          </div>
        </div>
      </form>

      <hr>

      <h5 class="mb-3">{{ get_phrase('Latest concessions') }}</h5>
      @if(count($concessions) > 0)
        <div class="table-responsive">
          <table class="table eTable eTable-2">
            <thead>
              <tr>
                <th>#</th>
                <th>{{ get_phrase('Scope') }}</th>
                <th>{{ get_phrase('Student') }}</th>
                <th>{{ get_phrase('Class') }}</th>
                <th>{{ get_phrase('Section') }}</th>
                <th>{{ get_phrase('Guardian') }}</th>
                <th>{{ get_phrase('Mode') }}</th>
                <th>{{ get_phrase('Value') }}</th>
                <th>{{ get_phrase('Session') }}</th>
                <th>{{ get_phrase('Active') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($concessions as $k => $c)
                <tr>
                  <td>{{ $k + 1 }}</td>
                  <td>{{ $c->scope_type }}</td>
                  <td>
                    @php
                      $studentName = !empty($c->student_id) && !empty($studentsById) && isset($studentsById[$c->student_id])
                        ? $studentsById[$c->student_id]->name
                        : null;
                    @endphp
                    {{ $studentName ?? (!empty($c->student_id) ? ('#'.$c->student_id) : '-') }}
                  </td>
                  <td>
                    @php
                      $en = !empty($c->student_id) && !empty($enrollmentByStudentId) && isset($enrollmentByStudentId[$c->student_id])
                        ? $enrollmentByStudentId[$c->student_id]
                        : null;
                    @endphp
                    {{ !empty($en) && !empty($en['class_name']) ? $en['class_name'] : '-' }}
                  </td>
                  <td>
                    {{ !empty($en) && !empty($en['section_name']) ? $en['section_name'] : '-' }}
                  </td>
                  <td>
                    @php
                      $g = (!empty($c->guardian_id) && !empty($guardiansById) && isset($guardiansById[$c->guardian_id]))
                        ? $guardiansById[$c->guardian_id]
                        : null;
                      $guardianText = null;
                      if (!empty($g)) {
                        $guardianText = trim(($g->name ?? '').' '.(!empty($g->id_card_no) ? ('('.$g->id_card_no.')') : ''));
                      }
                    @endphp
                    {{ $guardianText ?? (!empty($c->guardian_id) ? ('#'.$c->guardian_id) : '-') }}
                  </td>
                  <td>{{ $c->mode }}</td>
                  <td>{{ $c->value }}</td>
                  <td>{{ $c->session_id ?? get_phrase('All') }}</td>
                  <td>
                    @if(!empty($c->is_active))
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
        <div class="alert alert-info mb-0">{{ get_phrase('No discounts found yet.') }}</div>
      @endif
    </div>
  </div>
</div>

<script>
  "use strict";
  let _studentSelect2Inited = false;
  let _guardianSelect2Inited = false;

  function initStudentSelect2() {
    if (_studentSelect2Inited) return;
    _studentSelect2Inited = true;

    if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.select2) {
      console.warn('Select2 is not loaded; student search dropdown disabled.');
      _studentSelect2Inited = false;
      return;
    }

    $('#student_id_select').select2({
      width: '100%',
      placeholder: "{{ get_phrase('Search student (name, class, father, CNIC)') }}",
      allowClear: true,
      dropdownAutoWidth: true,
      dropdownCssClass: 'cm-select2-dropdown-search',
      minimumResultsForSearch: 0,
      ajax: {
        url: "{{ route('admin.fees.search.students') }}",
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
  }

  function initGuardianSelect2() {
    if (_guardianSelect2Inited) return;
    _guardianSelect2Inited = true;

    if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.select2) {
      console.warn('Select2 is not loaded; guardian search dropdown disabled.');
      _guardianSelect2Inited = false;
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
  }

  function toggleScopeFields(){
    const scope = document.getElementById('scope_type').value;
    const s = document.getElementById('student_scope_block');
    const g = document.getElementById('guardian_scope_block');
    if(scope === 'guardian'){
      s.classList.add('d-none');
      g.classList.remove('d-none');
      initGuardianSelect2();
    }else{
      g.classList.add('d-none');
      s.classList.remove('d-none');
      initStudentSelect2();
    }
  }
  document.addEventListener('DOMContentLoaded', function () {
    initStudentSelect2();
    toggleScopeFields();
  });
</script>

@endsection

