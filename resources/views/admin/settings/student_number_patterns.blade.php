@extends('admin.navigation')

@section('content')
<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
          <div class="d-flex flex-column">
            <h4>{{ get_phrase('Student Number Patterns') }}</h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#">{{ get_phrase('Home') }}</a></li>
              <li><a href="#">{{ get_phrase('Settings') }}</a></li>
              <li><a href="#">{{ get_phrase('Student Number Patterns') }}</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="eSection-wrap-2">
      <form method="POST" action="{{ route('admin.student_number_patterns.update') }}" class="ajaxForm">
        @csrf

        <div class="row mb-3">
          <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
              <div class="text-muted">
                <small>
                  {{ get_phrase('Admission No') }} → <code>users.code</code> |
                  {{ get_phrase('Enrollment No') }} → <code>enrollments.enrollment_no</code>
                </small>
              </div>
              <div class="d-flex flex-wrap" style="gap:10px;">
                <button type="button" class="btn btn-light btn-sm" onclick="applyPreset('SC-{YYYY}-{SEQ:5}','SC-{MM}-{YY}-{SEQ:5}')">{{ get_phrase('Preset') }}: SC</button>
                <button type="button" class="btn btn-light btn-sm" onclick="applyPreset('ADM-{YY}-{SEQ:4}','ENR-{YYYY}-{SEQ:5}')">{{ get_phrase('Preset') }}: ADM/ENR</button>
                <button type="button" class="btn btn-light btn-sm" onclick="applyPreset('{YYYY}-{SEQ:5}','{YYYY}-{SEQ:5}')">{{ get_phrase('Preset') }}: Default</button>
              </div>
            </div>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-12">
            <div class="p-3" style="background:#f7f9fc;border:1px solid #e3e4ea;border-radius:8px;">
              <div class="d-flex flex-wrap align-items-center" style="gap:10px;">
                <strong class="me-2">{{ get_phrase('Tokens') }}:</strong>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="insertToken(activePatternInput,'{YYYY}')">{YYYY}</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="insertToken(activePatternInput,'{YY}')">{YY}</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="insertToken(activePatternInput,'{MM}')">{MM}</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="insertToken(activePatternInput,'{SEQ:4}')">{SEQ:4}</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="insertToken(activePatternInput,'{SEQ:5}')">{SEQ:5}</button>
                <span class="text-muted ms-2"><small>{{ get_phrase('Click a pattern field, then click tokens to insert') }}</small></span>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="fpb-7">
              <label class="eForm-label">{{ get_phrase('Admission number pattern') }}</label>
              <input type="text" class="form-control eForm-control" id="admission_number_pattern" name="admission_number_pattern" value="{{ $school_details->admission_number_pattern }}" placeholder="{YYYY}-{SEQ:5}" onclick="activePatternInput='admission_number_pattern'" oninput="updatePreviews()">
              <small class="text-muted">
                {{ get_phrase('Allowed tokens') }}: <code>{YYYY}</code>, <code>{YY}</code>, <code>{MM}</code>, <code>{SEQ:n}</code> (required)
              </small>
            </div>
            <div class="fpb-7">
              <small class="text-muted">
                {{ get_phrase('Last used seq') }}: <code>{{ $preview['admission_last'] }}</code> |
                {{ get_phrase('Next') }}: <code id="admission_preview">{{ $preview['admission'] }}</code>
              </small>
            </div>
          </div>

          <div class="col-md-6">
            <div class="fpb-7">
              <label class="eForm-label">{{ get_phrase('Enrollment number pattern') }}</label>
              <input type="text" class="form-control eForm-control" id="enrollment_number_pattern" name="enrollment_number_pattern" value="{{ $school_details->enrollment_number_pattern }}" placeholder="{YYYY}-{SEQ:5}" onclick="activePatternInput='enrollment_number_pattern'" oninput="updatePreviews()">
              <small class="text-muted">
                {{ get_phrase('Allowed tokens') }}: <code>{YYYY}</code>, <code>{YY}</code>, <code>{MM}</code>, <code>{SEQ:n}</code> (required)
              </small>
            </div>
            <div class="fpb-7">
              <small class="text-muted">
                {{ get_phrase('Last used seq') }}: <code>{{ $preview['enrollment_last'] }}</code> |
                {{ get_phrase('Next') }}: <code id="enrollment_preview">{{ $preview['enrollment'] }}</code>
              </small>
            </div>
          </div>
        </div>

        <div class="fpb-7 pt-2">
          <button class="btn-form" type="submit">{{ get_phrase('Save') }}</button>
          <a class="btn btn-light ms-2" href="{{ route('admin.settings.school') }}">{{ get_phrase('Back') }}</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

<script>
  "use strict";

  let activePatternInput = 'admission_number_pattern';

  function insertToken(inputId, token) {
    const el = document.getElementById(inputId);
    if (!el) return;
    const start = el.selectionStart ?? el.value.length;
    const end = el.selectionEnd ?? el.value.length;
    const before = el.value.substring(0, start);
    const after = el.value.substring(end);
    el.value = before + token + after;
    el.focus();
    const pos = start + token.length;
    el.setSelectionRange(pos, pos);
    updatePreviews();
  }

  function applyPreset(admission, enrollment) {
    const a = document.getElementById('admission_number_pattern');
    const e = document.getElementById('enrollment_number_pattern');
    if (a) a.value = admission;
    if (e) e.value = enrollment;
    updatePreviews();
  }

  function buildPreview(pattern, year, yy, mm, nextSeq) {
    let pad = 5;
    const m = pattern.match(/\{SEQ:(\d{1,2})\}/);
    if (m && m[1]) {
      const n = parseInt(m[1], 10);
      if (!isNaN(n) && n >= 1 && n <= 12) pad = n;
    }
    const seq = String(nextSeq).padStart(pad, '0');
    let out = pattern.replaceAll('{YYYY}', year).replaceAll('{YY}', yy).replaceAll('{MM}', mm);
    out = out.replace(/\{SEQ:\d{1,2}\}/, seq);
    return out;
  }

  function updatePreviews() {
    const year = String(new Date().getFullYear());
    const yy = year.slice(-2);
    const mm = String(new Date().getMonth() + 1).padStart(2, '0');

    const nextAdmissionSeq = {{ (int) $preview['admission_next_seq'] }};
    const nextEnrollmentSeq = {{ (int) $preview['enrollment_next_seq'] }};

    const admissionPattern = (document.getElementById('admission_number_pattern')?.value || '{YYYY}-{SEQ:5}').trim();
    const enrollmentPattern = (document.getElementById('enrollment_number_pattern')?.value || '{YYYY}-{SEQ:5}').trim();

    const aPrev = buildPreview(admissionPattern, year, yy, mm, nextAdmissionSeq);
    const ePrev = buildPreview(enrollmentPattern, year, yy, mm, nextEnrollmentSeq);

    const aEl = document.getElementById('admission_preview');
    const eEl = document.getElementById('enrollment_preview');
    if (aEl) aEl.textContent = aPrev;
    if (eEl) eEl.textContent = ePrev;
  }

  document.addEventListener('DOMContentLoaded', function () {
    updatePreviews();
  });
</script>

