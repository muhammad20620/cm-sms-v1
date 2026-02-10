@extends('parent.navigation')
@section('content')

@php
  $typeOptions = [
    'leave' => get_phrase('Leave application'),
    'other' => get_phrase('Other application'),
  ];
@endphp

<div class="mainSection-title">
  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
        <div class="d-flex flex-column">
          <h4>{{ get_phrase('Applications') }}</h4>
          <ul class="d-flex align-items-center eBreadcrumb-2">
            <li><a href="#">{{ get_phrase('Home') }}</a></li>
            <li><a href="#">{{ get_phrase('Applications') }}</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="eSection-wrap-2">
      <form method="POST" action="{{ route('parent.applications.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
          <div class="col-md-4 fpb-7">
            <label class="eForm-label">{{ get_phrase('Type') }}</label>
            <select class="form-select eForm-select" name="type" id="application_type" required>
              @foreach($typeOptions as $k => $label)
                <option value="{{ $k }}" {{ old('type', 'leave') === $k ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-4 fpb-7">
            <label class="eForm-label">{{ get_phrase('Student (optional)') }}</label>
            <select class="form-select eForm-select" name="student_id" id="student_id_select" style="width: 100%;">
              <option value="">{{ get_phrase('Select a child') }}</option>
              @foreach($children as $child)
                <option value="{{ $child->id }}" {{ (string) old('student_id') === (string) $child->id ? 'selected' : '' }}>
                  {{ $child->name }}
                </option>
              @endforeach
            </select>
            <small class="text-muted">{{ get_phrase('Choose a child if this application is for a specific student.') }}</small>
          </div>

          <div class="col-md-4 fpb-7">
            <label class="eForm-label">{{ get_phrase('Title') }}</label>
            <input type="text" class="form-control eForm-control" name="title" value="{{ old('title') }}" required>
          </div>

          <div class="col-md-4 fpb-7" id="leave_from_block">
            <label class="eForm-label">{{ get_phrase('Leave from') }}</label>
            <input type="date" class="form-control eForm-control" name="leave_from" value="{{ old('leave_from') }}">
          </div>

          <div class="col-md-4 fpb-7" id="leave_to_block">
            <label class="eForm-label">{{ get_phrase('Leave to') }}</label>
            <input type="date" class="form-control eForm-control" name="leave_to" value="{{ old('leave_to') }}">
          </div>

          <div class="col-md-4 fpb-7">
            <label class="eForm-label">{{ get_phrase('Attachment (optional)') }}</label>
            <input type="file" class="form-control eForm-control" name="attachment">
          </div>

          <div class="col-12 fpb-7">
            <label class="eForm-label">{{ get_phrase('Message') }}</label>
            <textarea class="form-control eForm-control" name="message" rows="4" placeholder="{{ get_phrase('Write details...') }}">{{ old('message') }}</textarea>
          </div>

          <div class="col-12 fpb-7 pt-2">
            <button class="btn-form" type="submit">{{ get_phrase('Submit Application') }}</button>
          </div>
        </div>
      </form>

      <hr>

      <h5 class="mb-3">{{ get_phrase('My applications') }}</h5>
      @if($applications->count() > 0)
        <div class="table-responsive">
          <table class="table eTable eTable-2">
            <thead>
              <tr>
                <th>#</th>
                <th>{{ get_phrase('Type') }}</th>
                <th>{{ get_phrase('Student') }}</th>
                <th>{{ get_phrase('Class') }}</th>
                <th>{{ get_phrase('Section') }}</th>
                <th>{{ get_phrase('Title') }}</th>
                <th>{{ get_phrase('Leave') }}</th>
                <th>{{ get_phrase('Status') }}</th>
                <th>{{ get_phrase('School response') }}</th>
                <th>{{ get_phrase('Attachment') }}</th>
                <th>{{ get_phrase('Submitted') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($applications as $k => $a)
                @php
                  $student = !empty($a->student_id) && isset($studentsById[$a->student_id]) ? $studentsById[$a->student_id] : null;
                  $class = !empty($a->class_id) && isset($classesById[$a->class_id]) ? $classesById[$a->class_id] : null;
                  $section = !empty($a->section_id) && isset($sectionsById[$a->section_id]) ? $sectionsById[$a->section_id] : null;
                  $status = (string) $a->status;
                @endphp
                <tr>
                  <td>{{ ($applications->currentPage() - 1) * $applications->perPage() + ($k + 1) }}</td>
                  <td>{{ ucfirst($a->type) }}</td>
                  <td>{{ !empty($student) ? $student->name : '-' }}</td>
                  <td>{{ !empty($class) ? $class->name : '-' }}</td>
                  <td>{{ !empty($section) ? $section->name : '-' }}</td>
                  <td>{{ $a->title }}</td>
                  <td>
                    @if($a->type === 'leave' && !empty($a->leave_from) && !empty($a->leave_to))
                      {{ $a->leave_from }} → {{ $a->leave_to }}
                    @else
                      -
                    @endif
                  </td>
                  <td>
                    @if($status === 'approved')
                      <span class="eBadge ebg-soft-success">{{ get_phrase('Approved') }}</span>
                    @elseif($status === 'rejected')
                      <span class="eBadge ebg-soft-danger">{{ get_phrase('Rejected') }}</span>
                    @else
                      <span class="eBadge ebg-soft-warning">{{ get_phrase('Pending') }}</span>
                    @endif
                  </td>
                  <td>{{ $a->decision_note ?? '-' }}</td>
                  <td>
                    @if(!empty($a->attachment_path))
                      <a href="{{ asset($a->attachment_path) }}" target="_blank">{{ get_phrase('View') }}</a>
                    @else
                      -
                    @endif
                  </td>
                  <td>{{ $a->created_at }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="mt-3">
          {{ $applications->links() }}
        </div>
      @else
        <div class="alert alert-info mb-0">{{ get_phrase('No applications submitted yet.') }}</div>
      @endif
    </div>
  </div>
</div>

<script>
  "use strict";

  function toggleLeaveFields() {
    const type = document.getElementById('application_type')?.value || 'leave';
    const from = document.getElementById('leave_from_block');
    const to = document.getElementById('leave_to_block');
    if (!from || !to) return;
    if (type === 'leave') {
      from.classList.remove('d-none');
      to.classList.remove('d-none');
    } else {
      from.classList.add('d-none');
      to.classList.add('d-none');
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    toggleLeaveFields();
    document.getElementById('application_type')?.addEventListener('change', toggleLeaveFields);

    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.select2) {
      $('#student_id_select').select2({
        width: '100%',
        allowClear: true,
        dropdownAutoWidth: true,
        dropdownCssClass: 'cm-select2-dropdown-search',
        minimumResultsForSearch: 0,
      });
    }
  });
</script>

@endsection

