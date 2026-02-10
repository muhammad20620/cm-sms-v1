@php
  use App\Models\School;
  use App\Models\User;
  use App\Models\Classes;
  use App\Models\Section;
  use App\Models\Guardian;

  $a = $application;
  $school = School::find($a->school_id);
  $student = !empty($a->student_id) ? User::find($a->student_id) : null;
  $class = !empty($a->class_id) ? Classes::find($a->class_id) : null;
  $section = !empty($a->section_id) ? Section::find($a->section_id) : null;
  $guardian = !empty($a->guardian_id) ? Guardian::find($a->guardian_id) : null;

  $reviewUrl = url('/admin/applications?status=pending');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Application</title>
</head>
<body style="margin:0; padding:0; font-family: Arial, sans-serif; background:#f6f6f9;">
  <div style="max-width: 640px; margin: 0 auto; padding: 24px;">
    <div style="background:#fff; border:1px solid #e3e4ea; border-radius:10px; padding:24px;">
      <h2 style="margin:0 0 8px 0; color:#0C141D;">
        {{ get_phrase('New Parent Application') }}
      </h2>
      <p style="margin:0 0 16px 0; color:#6b7280;">
        {{ get_phrase('School') }}: <strong>{{ $school->title ?? get_settings('system_title') }}</strong>
      </p>

      <p style="margin:0 0 18px 0; color:#374151;">
        <strong>{{ $parentName }}</strong> {{ get_phrase('submitted a new application.') }}
      </p>

      <div style="border:1px solid #e3e4ea; border-radius:10px; padding:16px; background:#fafafa;">
        <p style="margin:0 0 8px 0;"><strong>{{ get_phrase('Title') }}:</strong> {{ $a->title }}</p>
        <p style="margin:0 0 8px 0;"><strong>{{ get_phrase('Type') }}:</strong> {{ ucfirst((string) $a->type) }}</p>
        <p style="margin:0 0 8px 0;"><strong>{{ get_phrase('Student') }}:</strong> {{ $student->name ?? '-' }}</p>
        <p style="margin:0 0 8px 0;"><strong>{{ get_phrase('Class') }}:</strong> {{ $class->name ?? '-' }}</p>
        <p style="margin:0 0 8px 0;"><strong>{{ get_phrase('Section') }}:</strong> {{ $section->name ?? '-' }}</p>
        <p style="margin:0 0 8px 0;"><strong>{{ get_phrase('Father CNIC') }}:</strong> {{ $guardian->id_card_no ?? '-' }}</p>
        @if($a->type === 'leave' && !empty($a->leave_from) && !empty($a->leave_to))
          <p style="margin:0 0 8px 0;"><strong>{{ get_phrase('Leave') }}:</strong> {{ $a->leave_from }} → {{ $a->leave_to }}</p>
        @endif
        @if(!empty($a->message))
          <p style="margin:0;"><strong>{{ get_phrase('Message') }}:</strong> {{ $a->message }}</p>
        @endif
        @if(!empty($a->attachment_path))
          <p style="margin:8px 0 0 0;">
            <strong>{{ get_phrase('Attachment') }}:</strong>
            <a href="{{ asset($a->attachment_path) }}" target="_blank">{{ get_phrase('View') }}</a>
          </p>
        @endif
      </div>

      <div style="margin-top: 18px;">
        <a href="{{ $reviewUrl }}" target="_blank" style="display:inline-block; padding:10px 14px; background:#2563eb; color:#fff; text-decoration:none; border-radius:8px;">
          {{ get_phrase('Review in Admin Panel') }}
        </a>
      </div>

      <p style="margin:18px 0 0 0; color:#6b7280; font-size: 12px;">
        {{ get_phrase('This is an automated email. Please do not reply.') }}
      </p>
    </div>
  </div>
</body>
</html>

