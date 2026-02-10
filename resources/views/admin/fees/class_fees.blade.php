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
          <h4>{{ get_phrase('Class Fees') }}</h4>
          <ul class="d-flex align-items-center eBreadcrumb-2">
            <li><a href="#">{{ get_phrase('Home') }}</a></li>
            <li><a href="#">{{ get_phrase('Accounting') }}</a></li>
            <li><a href="#">{{ get_phrase('Class Fees') }}</a></li>
          </ul>
        </div>
        <div class="d-flex flex-wrap" style="gap:10px;">
          <a class="btn btn-outline-primary" href="{{ route('admin.fees.generator') }}">{{ get_phrase('Fee Generator') }}</a>
          <a class="btn btn-outline-secondary" href="{{ route('admin.fees.concessions') }}">{{ get_phrase('Scholarships / Discounts') }}</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="eSection-wrap-2">

      <form method="POST" action="{{ route('admin.fees.class_fees.store') }}" class="ajaxForm">
        @csrf
        <div class="row">
          <div class="col-md-4 fpb-7">
            <label class="eForm-label">{{ get_phrase('Class') }}</label>
            <select class="form-select eForm-select eChoice-multiple-with-remove" name="class_id" required onchange="classWiseSection(this.value)">
              <option value="">{{ get_phrase('Select a class') }}</option>
              @foreach($classes as $class)
                <option value="{{ $class->id }}">{{ $class->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-4 fpb-7">
            <label class="eForm-label">{{ get_phrase('Section (optional)') }}</label>
            <select class="form-select eForm-select eChoice-multiple-with-remove" name="section_id" id="section_id">
              <option value="">{{ get_phrase('All sections') }}</option>
            </select>
            <small class="text-muted">{{ get_phrase('If section is empty, fee applies to all sections of the class.') }}</small>
          </div>

          <div class="col-md-4 fpb-7">
            <label class="eForm-label">{{ get_phrase('Fee title') }}</label>
            <input type="text" class="form-control eForm-control" name="title" placeholder="Monthly Fee">
          </div>

          <div class="col-md-4 fpb-7">
            <label class="eForm-label">{{ get_phrase('Amount').' ('.school_currency().')' }}</label>
            <input type="number" class="form-control eForm-control" name="amount" min="0" required>
          </div>

          <div class="col-12 fpb-7 pt-2">
            <button class="btn-form" type="submit">{{ get_phrase('Save') }}</button>
          </div>
        </div>
      </form>

      <hr>

      <h5 class="mb-3">{{ get_phrase('Saved class fees (running session)') }}</h5>
      @if(count($fees) > 0)
        <div class="table-responsive">
          <table class="table eTable eTable-2">
            <thead>
              <tr>
                <th>#</th>
                <th>{{ get_phrase('Class') }}</th>
                <th>{{ get_phrase('Section') }}</th>
                <th>{{ get_phrase('Title') }}</th>
                <th>{{ get_phrase('Amount') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($fees as $k => $f)
                @php
                  $className = $classes->firstWhere('id', $f->class_id)->name ?? ('#'.$f->class_id);
                  $sectionName = $f->section_id ? (Section::find($f->section_id)->name ?? ('#'.$f->section_id)) : get_phrase('All');
                @endphp
                <tr>
                  <td>{{ $k + 1 }}</td>
                  <td>{{ $className }}</td>
                  <td>{{ $sectionName }}</td>
                  <td>{{ $f->title }}</td>
                  <td>{{ $f->amount }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="alert alert-info mb-0">{{ get_phrase('No class fees saved yet.') }}</div>
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
        $('#section_id').html('<option value="">{{ get_phrase('All sections') }}</option>' + response);
      }
    });
  }
  $(document).ready(function () {
    $(".eChoice-multiple-with-remove").select2();
  });
</script>

@endsection

