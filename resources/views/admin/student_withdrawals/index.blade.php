@extends('admin.navigation')

@section('content')

<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Student Withdrawals (SLC)') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Students') }}</a></li>
                        <li><a href="#">{{ get_phrase('Withdrawals') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap-2">
            <form class="mb-3" action="{{ route('admin.student_withdrawals') }}">
                <div class="d-flex flex-wrap align-items-end" style="gap: 16px;">
                    <div class="d-flex flex-column" style="min-width: 360px;">
                        <label class="eForm-label mb-1">{{ get_phrase('Search') }}</label>
                        <input type="text" class="form-control eForm-control" name="search" value="{{ $search }}"
                            placeholder="Search (student, SLC no, admission, enrollment, CNIC)">
                    </div>
                    <div class="d-flex flex-column">
                        <label class="eForm-label mb-1 opacity-0">.</label>
                        <div class="d-flex align-items-center" style="gap: 12px;">
                            <button class="eBtn eBtn btn-primary" type="submit">{{ get_phrase('Apply') }}</button>
                            <a class="eBtn eBtn btn-light" href="{{ route('admin.student_withdrawals') }}">{{ get_phrase('Reset') }}</a>
                        </div>
                    </div>
                </div>
            </form>

            @if(count($withdrawals) > 0)
                <div class="table-responsive">
                    <table class="table eTable eTable-2">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ get_phrase('Student') }}</th>
                                <th>{{ get_phrase('SLC No') }}</th>
                                <th>{{ get_phrase('Class/Section') }}</th>
                                <th>{{ get_phrase('Withdrawal Date') }}</th>
                                <th>{{ get_phrase('Dues') }}</th>
                                <th>{{ get_phrase('Options') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($withdrawals as $key => $w)
                                <tr>
                                    <td>{{ $withdrawals->firstItem() + $key }}</td>
                                    <td>
                                        <div class="dAdmin_info_name min-w-250px">
                                            <p><strong>{{ $w->student_name }}</strong></p>
                                            <p class="text-muted">{{ $w->student_email }}</p>
                                        </div>
                                    </td>
                                    <td><code>{{ $w->slc_no }}</code></td>
                                    <td>
                                        {{ $w->class_name ?? '(' . get_phrase('Removed') . ')' }}
                                        /
                                        {{ $w->section_name ?? '(' . get_phrase('Removed') . ')' }}
                                    </td>
                                    <td>{{ $w->withdrawal_date }}</td>
                                    <td>
                                        @if(!empty($w->dues_cleared))
                                            <span class="eBadge ebg-soft-success">{{ get_phrase('Cleared') }}</span>
                                        @else
                                            <span class="eBadge ebg-soft-danger">{{ get_phrase('Not cleared') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="adminTable-action">
                                            <a class="eBtn eBtn btn-primary btn-sm" target="_blank"
                                                href="{{ route('admin.student.withdrawal.print', ['id' => $w->id]) }}">
                                                {{ get_phrase('Print SLC') }}
                                            </a>
                                            <a class="eBtn eBtn btn-light btn-sm ms-2" href="javascript:;"
                                                onclick="largeModal('{{ route('admin.student.student_profile', ['id' => $w->student_id]) }}','{{ get_phrase('Student Profile') }}')">
                                                {{ get_phrase('Profile') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $withdrawals->links() }}
                </div>
            @else
                <div class="alert alert-info mb-0">{{ get_phrase('No withdrawals found.') }}</div>
            @endif
        </div>
    </div>
</div>

@endsection

