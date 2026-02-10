@extends('admin.navigation')

@section('content')
@php
  $statusOptions = [
    'pending' => get_phrase('Pending'),
    'approved' => get_phrase('Approved'),
    'rejected' => get_phrase('Rejected'),
    'all' => get_phrase('All'),
  ];
  $typeOptions = [
    '' => get_phrase('All types'),
    'leave' => get_phrase('Leave'),
    'other' => get_phrase('Other'),
  ];
@endphp

<div class="mainSection-title">
  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
        <div class="d-flex flex-column">
          <h4>{{ get_phrase('Parent Applications') }}</h4>
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

      <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-md-3">
          <label class="eForm-label">{{ get_phrase('Status') }}</label>
          <select class="form-select eForm-select" name="status">
            @foreach($statusOptions as $k => $label)
              <option value="{{ $k }}" {{ (string) $status === (string) $k ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="eForm-label">{{ get_phrase('Type') }}</label>
          <select class="form-select eForm-select" name="type">
            @foreach($typeOptions as $k => $label)
              <option value="{{ $k }}" {{ (string) $type === (string) $k ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="eForm-label">{{ get_phrase('Search') }}</label>
          <input type="text" class="form-control eForm-control" name="q" value="{{ $q ?? '' }}" placeholder="{{ get_phrase('Parent, student, CNIC, title...') }}">
        </div>
        <div class="col-md-2">
          <button class="btn-form w-100" type="submit">{{ get_phrase('Filter') }}</button>
        </div>
      </form>

      @if($rows->count() > 0)
        <div class="table-responsive">
          <table class="table eTable eTable-2">
            <thead>
              <tr>
                <th>#</th>
                <th>{{ get_phrase('Status') }}</th>
                <th>{{ get_phrase('Type') }}</th>
                <th>{{ get_phrase('Parent') }}</th>
                <th>{{ get_phrase('Father CNIC') }}</th>
                <th>{{ get_phrase('Student') }}</th>
                <th>{{ get_phrase('Class') }}</th>
                <th>{{ get_phrase('Section') }}</th>
                <th>{{ get_phrase('Title') }}</th>
                <th>{{ get_phrase('Leave') }}</th>
                <th>{{ get_phrase('Submitted') }}</th>
                <th>{{ get_phrase('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($rows as $idx => $r)
                @php
                  $status = (string) $r->status;
                  $badge = 'ebg-soft-warning';
                  $statusLabel = get_phrase('Pending');
                  if ($status === 'approved') { $badge = 'ebg-soft-success'; $statusLabel = get_phrase('Approved'); }
                  if ($status === 'rejected') { $badge = 'ebg-soft-danger'; $statusLabel = get_phrase('Rejected'); }
                @endphp
                <tr>
                  <td>{{ ($rows->currentPage() - 1) * $rows->perPage() + ($idx + 1) }}</td>
                  <td><span class="eBadge {{ $badge }}">{{ $statusLabel }}</span></td>
                  <td>{{ ucfirst($r->type) }}</td>
                  <td>{{ $r->parent_name ?? ('#'.$r->parent_id) }}</td>
                  <td>{{ $r->guardian_cnic ?? '-' }}</td>
                  <td>{{ $r->student_name ?? '-' }}</td>
                  <td>{{ $r->class_name ?? '-' }}</td>
                  <td>{{ $r->section_name ?? '-' }}</td>
                  <td>
                    <div style="min-width:240px;">
                      <strong>{{ $r->title }}</strong>
                      @if(!empty($r->message))
                        <div class="text-muted" style="white-space: normal;">{{ \Illuminate\Support\Str::limit($r->message, 120) }}</div>
                      @endif
                      @if(!empty($r->attachment_path))
                        <div><a href="{{ asset($r->attachment_path) }}" target="_blank">{{ get_phrase('Attachment') }}</a></div>
                      @endif
                      @if(!empty($r->decision_note))
                        <div class="mt-1"><span class="text-muted">{{ get_phrase('Response') }}:</span> {{ $r->decision_note }}</div>
                      @endif
                    </div>
                  </td>
                  <td>
                    @if($r->type === 'leave' && !empty($r->leave_from) && !empty($r->leave_to))
                      {{ $r->leave_from }} → {{ $r->leave_to }}
                    @else
                      -
                    @endif
                  </td>
                  <td>{{ $r->created_at }}</td>
                  <td>
                    <div class="d-flex flex-wrap" style="gap:8px; min-width:220px;">
                      <form method="POST" action="{{ route('admin.applications.decision', ['id' => $r->id]) }}">
                        @csrf
                        <input type="hidden" name="decision" value="approved">
                        <input type="hidden" name="decision_note" value="">
                        <button type="submit" class="btn btn-sm btn-success">{{ get_phrase('Approve') }}</button>
                      </form>
                      <form method="POST" action="{{ route('admin.applications.decision', ['id' => $r->id]) }}">
                        @csrf
                        <input type="hidden" name="decision" value="rejected">
                        <input type="hidden" name="decision_note" value="">
                        <button type="submit" class="btn btn-sm btn-danger">{{ get_phrase('Reject') }}</button>
                      </form>
                    </div>
                    <div class="mt-2">
                      <form method="POST" action="{{ route('admin.applications.decision', ['id' => $r->id]) }}" class="d-flex" style="gap:8px;">
                        @csrf
                        <select class="form-select form-select-sm" name="decision" style="max-width:120px;">
                          <option value="approved">{{ get_phrase('Approve') }}</option>
                          <option value="rejected">{{ get_phrase('Reject') }}</option>
                        </select>
                        <input type="text" class="form-control form-control-sm" name="decision_note" placeholder="{{ get_phrase('Optional note') }}">
                        <button class="btn btn-sm btn-outline-primary" type="submit">{{ get_phrase('Save') }}</button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $rows->links() }}
        </div>
      @else
        <div class="alert alert-info mb-0">{{ get_phrase('No applications found.') }}</div>
      @endif
    </div>
  </div>
</div>
@endsection

