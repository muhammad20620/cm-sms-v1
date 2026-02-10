@extends('admin.navigation')

@section('content')
<?php
use App\Models\Classes;
use App\Models\Section;
?>

<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Student Full Profile') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Students') }}</a></li>
                        <li><a href="#">{{ $student->name }}</a></li>
                    </ul>
                </div>

                <div class="d-flex flex-wrap" style="gap: 10px;">
                    <a class="btn btn-light" href="{{ route('admin.student') }}">{{ get_phrase('Back') }}</a>
                    <a class="btn btn-primary" href="javascript:;" onclick="largeModal('{{ route('admin.student.id_card', ['id' => $student->id]) }}', '{{ get_phrase('Generate id card') }}')">
                        {{ get_phrase('ID Card') }}
                    </a>
                    <a class="btn btn-dark" href="javascript:;" onclick="largeModal('{{ route('admin.student.student_profile', ['id' => $student->id]) }}','{{ get_phrase('Student Profile') }}')">
                        {{ get_phrase('Open Modal Profile') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="student-full-profile">
<div class="row">
    <div class="col-lg-4">
        <div class="eSection-wrap-2">
            <div class="text-center mb-3">
                <img src="{{ $student_details['photo'] ?? '' }}" class="rounded-circle" style="width:90px;height:90px;object-fit:cover;" />
                <h5 class="mt-2 mb-0">{{ $student->name }}</h5>
                <small class="text-muted">{{ $student->email }}</small>
            </div>

            <div class="mb-2">
                <strong>{{ get_phrase('Admission No') }}:</strong> <code>{{ $student_details['admission_no'] ?? '' }}</code>
            </div>
            <div class="mb-2">
                <strong>{{ get_phrase('Enrollment No') }}:</strong> <code>{{ $student_details['enrollment_no'] ?? '' }}</code>
            </div>
            <div class="mb-2">
                <strong>{{ get_phrase('Class') }}:</strong> {{ $student_details->class_name ?? '' }}
            </div>
            <div class="mb-2">
                <strong>{{ get_phrase('Section') }}:</strong> {{ $student_details->section_name ?? '' }}
            </div>
            <div class="mb-2">
                <strong>{{ get_phrase('Father Name') }}:</strong> {{ $student_details['father_name'] ?? '' }}
            </div>
            <div class="mb-2">
                <strong>{{ get_phrase('Father CNIC') }}:</strong> {{ $student_details['parent_id_card'] ?? '' }}
            </div>
            <div class="mb-2">
                <strong>{{ get_phrase('Phone') }}:</strong> {{ $student_details['phone'] ?? '' }}
            </div>
            <div class="mb-2">
                <strong>{{ get_phrase('Address') }}:</strong> {{ $student_details['address'] ?? '' }}
            </div>
            <div class="mb-2">
                <strong>{{ get_phrase('Account Status') }}:</strong>
                @if(($student->account_status ?? '') === 'disable')
                    <span class="eBadge ebg-soft-danger">{{ get_phrase('Disabled') }}</span>
                @else
                    <span class="eBadge ebg-soft-success">{{ get_phrase('Enable') }}</span>
                @endif
            </div>

            <hr>

            <div class="mb-2">
                <strong>{{ get_phrase('Withdrawal') }}:</strong>
                @if(!empty($withdrawal))
                    <span class="eBadge ebg-soft-danger">{{ get_phrase('Withdrawn') }}</span>
                    <div class="text-muted" style="font-size: 12px;">
                        {{ get_phrase('SLC') }}: <code>{{ $withdrawal->slc_no }}</code>
                    </div>
                    <div class="mt-2">
                        <a class="btn btn-sm btn-primary" target="_blank" href="{{ route('admin.student.withdrawal.print', ['id' => $withdrawal->id]) }}">
                            {{ get_phrase('Print SLC') }}
                        </a>
                    </div>
                @else
                    <span class="eBadge ebg-soft-success">{{ get_phrase('Active') }}</span>
                    <div class="mt-2">
                        <a class="btn btn-sm btn-danger" href="javascript:;"
                            onclick="largeModal('{{ route('admin.student.withdrawal.modal', ['id' => $student->id]) }}','{{ get_phrase('Withdraw Student / Issue SLC') }}')">
                            {{ get_phrase('Withdraw / Issue SLC') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="row">
            <div class="col-md-6">
                <div class="eSection-wrap-2">
                    <h5 class="mb-3">{{ get_phrase('Attendance Summary') }}</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ get_phrase('Running session') }} (P/A)</span>
                        <strong>{{ $attendance['session_present'] ?? 0 }} / {{ $attendance['session_absent'] ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>{{ get_phrase('Overall') }} (P/A)</span>
                        <strong>{{ $attendance['overall_present'] ?? 0 }} / {{ $attendance['overall_absent'] ?? 0 }}</strong>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="eSection-wrap-2">
                    <h5 class="mb-3">{{ get_phrase('Fee Summary') }}</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ get_phrase('Invoices') }}</span>
                        <strong>{{ $fees['invoice_count'] ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ get_phrase('Total') }}</span>
                        <strong>{{ $fees['total_amount'] ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ get_phrase('Paid') }}</span>
                        <strong>{{ $fees['paid_amount'] ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>{{ get_phrase('Due') }}</span>
                        <strong>{{ $fees['due_amount'] ?? 0 }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="eSection-wrap-2 mt-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                <h5 class="mb-0">{{ get_phrase('Academic Results (Gradebook)') }}</h5>
                <a class="btn btn-light btn-sm" href="{{ route('admin.gradebook') }}">{{ get_phrase('Open Gradebook Module') }}</a>
            </div>

            @if(count($gradebookRows) > 0)
                <div class="table-responsive mt-3 ">
                    <table class="table eTable eTable-2 ">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ get_phrase('Exam Category') }}</th>
                                <th>{{ get_phrase('Session') }}</th>
                                <th>{{ get_phrase('Date') }}</th>
                                <th>{{ get_phrase('Subject') }}</th>
                                <th>{{ get_phrase('Marks') }}</th>
                                <th>{{ get_phrase('Grade') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 0; @endphp
                            @foreach($gradebookRows as $row)
                                @php
                                    $marks = json_decode((string) $row->marks, true);
                                    $marks = is_array($marks) ? $marks : [];
                                    $examName = $examCategoryNames[$row->exam_category_id] ?? ('#'.$row->exam_category_id);
                                    $sessionName = $sessionNames[$row->session_id] ?? ('#'.$row->session_id);
                                    $date = !empty($row->timestamp) ? date('Y-m-d', (int) $row->timestamp) : '';
                                @endphp

                                @if(empty($marks))
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $examName }}</td>
                                        <td>{{ $sessionName }}</td>
                                        <td>{{ $date }}</td>
                                        <td colspan="3" class="text-muted">{{ get_phrase('No marks found') }}</td>
                                    </tr>
                                @else
                                    @foreach($marks as $subjectId => $mark)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $examName }}</td>
                                            <td>{{ $sessionName }}</td>
                                            <td>{{ $date }}</td>
                                            <td>{{ $subjectNames[(int) $subjectId] ?? ('#'.$subjectId) }}</td>
                                            <td>{{ $mark }}</td>
                                            <td>{{ is_numeric($mark) ? get_grade($mark) : 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info mt-3 mb-0">{{ get_phrase('No gradebook records found for this student.') }}</div>
            @endif
        </div>

        <div class="eSection-wrap-2 mt-3">
            <h5 class="mb-3">{{ get_phrase('Recent Fee Invoices') }}</h5>
            @if(count($recent_invoices) > 0)
                <div class="table-responsive">
                    <table class="table eTable eTable-2">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ get_phrase('Title') }}</th>
                                <th>{{ get_phrase('Total') }}</th>
                                <th>{{ get_phrase('Paid') }}</th>
                                <th>{{ get_phrase('Status') }}</th>
                                <th>{{ get_phrase('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_invoices as $k => $inv)
                                <tr>
                                    <td>{{ $k + 1 }}</td>
                                    <td>{{ $inv->title }}</td>
                                    <td>{{ $inv->total_amount }}</td>
                                    <td>{{ $inv->paid_amount }}</td>
                                    <td>{{ $inv->status }}</td>
                                    <td>{{ !empty($inv->timestamp) ? date('Y-m-d', (int) $inv->timestamp) : '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info mb-0">{{ get_phrase('No invoices found.') }}</div>
            @endif
        </div>
    </div>
</div>
</div>

@endsection

