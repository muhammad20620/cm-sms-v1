<div class="modal-body">
    @if(!empty($existing))
        <div class="alert alert-warning">
            {{ get_phrase('This student is already withdrawn.') }}
            <div class="mt-2">
                <a class="eBtn eBtn btn-primary btn-sm" target="_blank"
                    href="{{ route('admin.student.withdrawal.print', ['id' => $existing->id]) }}">
                    {{ get_phrase('Print SLC') }}
                </a>
            </div>
        </div>
    @endif

    <div class="mb-3">
        <h5 class="mb-1">{{ $student->name }}</h5>
        <small class="text-muted">
            {{ get_phrase('Admission') }}: <code>{{ $studentDetails['admission_no'] ?? '' }}</code>
            &nbsp;|&nbsp;
            {{ get_phrase('Enrollment') }}: <code>{{ $studentDetails['enrollment_no'] ?? '' }}</code>
        </small>
    </div>

    <form method="POST" action="{{ route('admin.student.withdrawal.store', ['id' => $student->id]) }}" class="ajaxForm">
        @csrf

        <div class="row">
            <div class="col-md-6 fpb-7">
                <label class="eForm-label">{{ get_phrase('SLC No') }}</label>
                <input type="text" class="form-control eForm-control" name="slc_no"
                    value="{{ old('slc_no') }}"
                    placeholder="SLC-{YYYY}-{SEQ:4}">
                <small class="text-muted">
                    {{ get_phrase('Leave empty to auto-generate') }}
                    @if(!empty($defaultSlcNo))
                        — {{ get_phrase('Next') }}: <code>{{ $defaultSlcNo }}</code>
                    @endif
                </small>
            </div>

            <div class="col-md-6 fpb-7">
                <label class="eForm-label">{{ get_phrase('Withdrawal date') }} <span class="text-danger">*</span></label>
                <input type="date" class="form-control eForm-control" name="withdrawal_date"
                    value="{{ old('withdrawal_date', date('Y-m-d')) }}" required>
            </div>

            <div class="col-md-6 fpb-7">
                <label class="eForm-label">{{ get_phrase('SLC issue date') }}</label>
                <input type="date" class="form-control eForm-control" name="slc_issue_date"
                    value="{{ old('slc_issue_date', date('Y-m-d')) }}">
            </div>

            <div class="col-md-6 fpb-7 d-flex align-items-end">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="dues_cleared" name="dues_cleared"
                        {{ old('dues_cleared') ? 'checked' : '' }}>
                    <label class="form-check-label" for="dues_cleared">
                        {{ get_phrase('Dues cleared') }}
                    </label>
                </div>
            </div>

            <div class="col-12 fpb-7">
                <label class="eForm-label">{{ get_phrase('Reason') }}</label>
                <textarea class="form-control eForm-control" name="reason" rows="3"
                    placeholder="e.g., Transfer to another school">{{ old('reason') }}</textarea>
            </div>

            <div class="col-12 fpb-7">
                <label class="eForm-label">{{ get_phrase('Remarks') }}</label>
                <textarea class="form-control eForm-control" name="remarks" rows="3"
                    placeholder="Any additional notes">{{ old('remarks') }}</textarea>
            </div>
        </div>

        <div class="pt-2 d-flex justify-content-end" style="gap:12px;">
            <button type="submit" class="btn-form" {{ !empty($existing) ? 'disabled' : '' }}>
                {{ get_phrase('Withdraw Student') }}
            </button>
        </div>
    </form>
</div>

