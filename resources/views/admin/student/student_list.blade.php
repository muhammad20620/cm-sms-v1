@extends('admin.navigation')
   
@section('content')

<?php 

use App\Http\Controllers\CommonController;
use App\Models\School;
use App\Models\Section;

$user = Auth()->user();
$menu_permission = (empty($user->menu_permission) || $user->menu_permission == 'null') ? []:json_decode($user->menu_permission, true);
?>

<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4>{{ get_phrase('Students') }}</h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#">{{ get_phrase('Home') }}</a></li>
              <li><a href="#">{{ get_phrase('Users') }}</a></li>
              <li><a href="#">{{ get_phrase('Students') }}</a></li>
            </ul>
          </div>
          @if(empty($user->menu_permission) || in_array('admin.offline_admission.single', $menu_permission)) 
          <div class="export-btn-area">
            <a href="{{ route('admin.offline_admission.single', ['type' => 'single']) }}" class="export_btn">{{ get_phrase('Create Student') }}</a>
          </div>
          @endif
        </div>
      </div>
    </div>
</div>
<!-- Start Students area -->
<div class="row">
    <div class="col-12" >
        <div class="eSection-wrap-2">
          <!-- Search and filter -->
            <div
              class="search-filter-area d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15"
            >
              <form action="{{ route('admin.student') }}" class="d-flex align-items-end flex-wrap" style="gap: 24px;">
                <div class="d-flex flex-column" style="min-width: 360px;">
                  <label for="tableSearch" class="eForm-label mb-1">{{ get_phrase('Search') }}</label>
                  <div class="search-input d-flex justify-content-start align-items-center">
                    <span>
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path id="Search_icon" data-name="Search icon" d="M2,7A4.951,4.951,0,0,1,7,2a4.951,4.951,0,0,1,5,5,4.951,4.951,0,0,1-5,5A4.951,4.951,0,0,1,2,7Zm12.3,8.7a.99.99,0,0,0,1.4-1.4l-3.1-3.1A6.847,6.847,0,0,0,14,7,6.957,6.957,0,0,0,7,0,6.957,6.957,0,0,0,0,7a6.957,6.957,0,0,0,7,7,6.847,6.847,0,0,0,4.2-1.4Z" fill="#797c8b" />
                      </svg>
                    </span>
                    <input
                      type="text"
                      id="tableSearch"
                      name="search"
                      value="{{ $search }}"
                      placeholder="Search (name, email, parent, CNIC) — realtime"
                      class="form-control"
                    />
                  </div>
                </div>

                <div class="d-flex flex-column" style="min-width: 180px;">
                  <label class="eForm-label mb-1">{{ get_phrase('Class') }}</label>
                  <select class="form-select" name="class_id" id="class_filter" onchange="classWiseSection(this.value)">
                    <option value="">{{ get_phrase('All classes') }}</option>
                    @foreach($classes as $class)
                      <option value="{{ $class->id }}" {{ $class_id == $class->id ?  'selected':'' }}>{{ $class->name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="d-flex flex-column" style="min-width: 180px;">
                  <label class="eForm-label mb-1">{{ get_phrase('Section') }}</label>
                  <select class="form-select" name="section_id" id="section_filter">
                    <?php if($class_id !=""){
                      $sections = Section::get()->where('class_id', $class_id); ?>
                      <option value="">{{ get_phrase('All sections') }}</option>
                      @foreach($sections as $section)
                        <option value="{{ $section->id }}" {{ $section_id == $section->id ?  'selected':'' }}>{{ $section->name }}</option>
                      @endforeach
                    <?php } else { ?>
                      <option value="">{{ get_phrase('All sections') }}</option>
                    <?php } ?>
                  </select>
                </div>

                <div class="d-flex flex-column">
                  <label class="eForm-label mb-1 opacity-0">.</label>
                  <div class="d-flex align-items-center" style="gap: 12px;">
                    <button class="eBtn eBtn btn-primary" type="submit">{{ get_phrase('Apply') }}</button>
                    <a class="eBtn eBtn btn-light" href="{{ route('admin.student') }}">{{ get_phrase('Reset') }}</a>
                  </div>
                </div>
              </form>
              <div class="filter-export-area d-flex align-items-center">
                <!-- Export Button -->
                @if(count($students) > 0)
                <div class="position-relative">
                  <button
                    class="eBtn-3 dropdown-toggle"
                    type="button"
                    id="defaultDropdown"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="true"
                    aria-expanded="false"
                  >
                    <span class="pr-10">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="12.31"
                        height="10.77"
                        viewBox="0 0 10.771 12.31"
                      >
                        <path
                          id="arrow-right-from-bracket-solid"
                          d="M3.847,1.539H2.308a.769.769,0,0,0-.769.769V8.463a.769.769,0,0,0,.769.769H3.847a.769.769,0,0,1,0,1.539H2.308A2.308,2.308,0,0,1,0,8.463V2.308A2.308,2.308,0,0,1,2.308,0H3.847a.769.769,0,1,1,0,1.539Zm8.237,4.39L9.007,9.007A.769.769,0,0,1,7.919,7.919L9.685,6.155H4.616a.769.769,0,0,1,0-1.539H9.685L7.92,2.852A.769.769,0,0,1,9.008,1.764l3.078,3.078A.77.77,0,0,1,12.084,5.929Z"
                          transform="translate(0 12.31) rotate(-90)"
                          fill="#00a3ff"
                        />
                      </svg>
                    </span>
                    {{ get_phrase('Export') }}
                  </button>
                  <ul
                    class="dropdown-menu dropdown-menu-end eDropdown-menu-2"
                  >
                    <li>
                        <a class="dropdown-item" id="pdf" href="javascript:;" onclick="Export()">{{ get_phrase('PDF') }}</a>
                    </li>
                    <li>
                        <a class="dropdown-item" id="print" href="javascript:;" onclick="printableDiv('student_list')">{{ get_phrase('Print') }}</a>
                    </li>
                  </ul>
                </div>
                @endif
              </div>
            </div>
            @if(count($students) > 0)
            <!-- Table -->
            <div class="table-responsive eTable-scroll">
              <table class="table eTable eTable-2" id="studentsTable">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{ get_phrase('Name') }}</th>
                    <th scope="col">{{ get_phrase('Email') }}</th>
                    <th scope="col">{{ get_phrase('User Info') }}</th>
                    <th scope="col">{{ get_phrase('Parent') }}</th>
                    <th scope="col">{{ get_phrase('Parent ID card') }}</th>
                    <th scope="col">{{ get_phrase('Student Status') }}</th>
                    <th scope="col">{{ get_phrase('Account Status') }}</th>
                    <th scope="col">{{ get_phrase('Options') }}</th>
                </thead>
                <tbody>
                    @foreach($students as $key => $user)
                    <?php 
                        $user_image = get_user_image($user->user_id);
                        $info = json_decode($user->student_user_information ?? '{}');
                        $parent_info = json_decode($user->legacy_parent_information ?? '{}');
                        $parent_name = $user->guardian_name ?? ($user->legacy_parent_name ?? '');
                        $parent_id_card = $user->guardian_id_card_no ?? ($parent_info->id_card_no ?? '');
                        $class_name = $user->class_name ?? '';
                        $section_name = $user->section_name ?? '';

                        $rowSearch = strtolower(
                          ($user->student_name ?? '').' '.
                          ($user->student_email ?? '').' '.
                          ($info->phone ?? '').' '.
                          ($info->address ?? '').' '.
                          ($class_name).' '.$section_name.' '.
                          ($parent_name).' '.($parent_id_card).' '.
                          (($user->withdrawal_slc_no ?? ''))
                        );
                    ?>
                      <tr data-search="{{ $rowSearch }}">
                        <th scope="row">
                          <p class="row-number">{{ $students->firstItem() + $key }}</p>
                        </th>
                        <td>
                          <div
                            class="dAdmin_profile d-flex align-items-center min-w-200px"
                          >
                            <div class="dAdmin_profile_img">
                              <img
                                class="img-fluid"
                                width="50"
                                height="50"
                                src="{{ $user_image }}"
                              />
                            </div>
                            <div class="dAdmin_profile_name dAdmin_info_name">
                              <h4>{{ $user->student_name }}</h4>
                              <p>
                                <span>{{ get_phrase('Class') }}:</span> {{ $class_name == '' ? get_phrase('Removed') : $class_name }}
                                <br>
                                <span>{{ get_phrase('Section') }}:</span> {{ $section_name == '' ? get_phrase('Removed') : $section_name }}
                              </p>
                            </div>
                          </div>
                        </td>
                        <td>
                          <div class="dAdmin_info_name min-w-250px">
                            <p>{{ $user->student_email }}</p>
                          </div>
                        </td>
                        <td>
                          <div class="dAdmin_info_name min-w-250px">
                            <p><span>{{ get_phrase('Phone') }}:</span> {{ $info->phone }}</p>
                            <p>
                              <span>{{ get_phrase('Address') }}:</span> {{ $info->address }}
                            </p>
                          </div>
                        </td>
                        <td>
                          <div class="dAdmin_info_name min-w-200px">
                            <p>{{ $parent_name == '' ? '(' . get_phrase('Not found') . ')' : $parent_name }}</p>
                          </div>
                        </td>
                        <td>
                          <div class="dAdmin_info_name min-w-200px">
                            <p>{{ $parent_id_card == '' ? '(' . get_phrase('Not found') . ')' : $parent_id_card }}</p>
                          </div>
                        </td>
                        <td>
                          <div class="dAdmin_info_name min-w-150px">
                            @if(!empty($user->withdrawal_id))
                              <span class="eBadge ebg-soft-danger">{{ get_phrase('Withdrawn') }}</span>
                              <div class="text-muted" style="font-size: 12px;">
                                <span>{{ get_phrase('SLC') }}:</span> <code>{{ $user->withdrawal_slc_no }}</code>
                              </div>
                            @else
                              <span class="eBadge ebg-soft-success">{{ get_phrase('Active') }}</span>
                            @endif
                          </div>
                        </td>
                        <td>
                          <div class="dAdmin_info_name min-w-100px">
                            @if(!empty($user->student_account_status == 'disable'))
                            <span class="eBadge ebg-soft-danger">{{get_phrase('Disabled')}}</span>
                            @else
                            <span class="eBadge ebg-soft-success">{{get_phrase('Enable')}}</span>
                            @endif
                          </div>
                        </td>
                        <td>
                          <div class="adminTable-action">
                            <button
                              type="button"
                              class="eBtn eBtn-black dropdown-toggle table-action-btn-2"
                              data-bs-toggle="dropdown"
                              aria-expanded="false"
                            >
                              {{ get_phrase('Actions') }}
                            </button>
                            <ul
                              class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action"
                            >
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="largeModal('{{ route('admin.student.id_card', ['id' => $user->user_id]) }}', '{{ get_phrase('Generate id card') }}')">{{ get_phrase('Generate Id card') }}</a>
                              </li>

                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('admin.student_edit_modal', ['id' => $user->user_id]) }}', 'Edit Student')">{{ get_phrase('Edit') }}</a>
                              </li>
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.student.delete', ['id' => $user->user_id]) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
                              </li>
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="largeModal('{{ route('admin.student.student_profile', ['id' => $user->user_id]) }}','{{ get_phrase('Student Profile') }}')">{{ get_phrase('Profile') }}</a>
                              </li>
                              <li>
                                <a class="dropdown-item" href="{{ route('admin.student.documents', ['id' => $user->user_id]) }}">{{ get_phrase('Documents') }}</a>
                              </li>
                              @if(!empty($user->student_account_status == 'disable'))
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.account_enable', ['id' => $user->user_id]) }}', 'undefined');">{{ get_phrase('Enable') }}</a>
                              </li>
                              @else
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.account_disable', ['id' => $user->user_id]) }}', 'undefined');">{{ get_phrase('Disable') }}</a>
                              </li>
                              @endif
                              <li>
                                @if(empty($user->withdrawal_id))
                                  <a class="dropdown-item" href="javascript:;"
                                    onclick="largeModal('{{ route('admin.student.withdrawal.modal', ['id' => $user->user_id]) }}','{{ get_phrase('Withdraw Student / Issue SLC') }}')">
                                    {{ get_phrase('Withdraw / Issue SLC') }}
                                  </a>
                                @else
                                  <a class="dropdown-item" target="_blank"
                                    href="{{ route('admin.student.withdrawal.print', ['id' => $user->withdrawal_id]) }}">
                                    {{ get_phrase('Print SLC') }}
                                  </a>
                                @endif
                              </li>
                            </ul>
                          </div>
                        </td>
                      </tr>
                    @endforeach
                </tbody>
              </table>
              
              <div
                  class="admin-tInfo-pagi d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15"
                >
                  <p class="admin-tInfo">{{ get_phrase('Showing').' 1 - '.count($students).' '.get_phrase('from').' '.$students->total().' '.get_phrase('data') }}</p>
                  <div class="admin-pagi">
                    {!! $students->appends(request()->all())->links() !!}
                  </div>
                </div>
              </div>
              
            </div>
            @else
            <div class="empty_box center">
              <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
              <br>
              <span class="">{{ get_phrase('No data found') }}</span>
            </div>
            @endif
        </div>
    </div>
</div>

@if(count($students) > 0)
<!-- Table -->
<div class="table-responsive student_list display-none-view" id="student_list">
  <h4 class="" style="font-size: 16px; font-weight: 600; line-height: 26px; color: #181c32; margin-left:45%; margin-bottom:15px; margin-top:17px;">{{ get_phrase(' Students List') }}</h4>
  <table class="table eTable eTable-2">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">{{ get_phrase('Name') }}</th>
        <th scope="col">{{ get_phrase('Email') }}</th>
        <th scope="col">{{ get_phrase('User Info') }}</th>
    </thead>
    <tbody>
      @foreach($students as $user)
      <?php 

          $student = DB::table('users')->where('id', $user->user_id)->first();

          $user_image = get_user_image($user->user_id);
          $info = json_decode($student->user_information);

          $student_details = (new CommonController)->get_student_academic_info($student->id);
      ?>
        <tr>
          <th scope="row">
            <p class="row-number">{{ $loop->index + 1 }}</p>
          </th>
          <td>
            <div
              class="dAdmin_profile d-flex align-items-center min-w-200px"
            >
              <div class="dAdmin_profile_img">
                <img
                  class="img-fluid"
                  width="50"
                  height="50"
                  src="{{ asset('assets') }}/{{ $user_image }}"
                />
              </div>
              <div class="dAdmin_profile_name dAdmin_info_name">
                <h4>{{ $student->name }}</h4>
                <p>
                  @if(empty($student_details->class_name))
                  <span>{{ get_phrase('Class') }}:</span> removed
                  @else
                  <span>{{ get_phrase('Class') }}:</span> {{ $student_details->class_name }}
                  @endif
                </p>
              </div>
            </div>
          </td>
          <td>
            <div class="dAdmin_info_name min-w-250px">
              <p>{{ $student->email }}</p>
            </div>
          </td>
          <td>
            <div class="dAdmin_info_name min-w-250px">
              <p><span>{{ get_phrase('Phone') }}:</span> {{ $info->phone }}</p>
              <p>
                <span>{{ get_phrase('Address') }}:</span> {{ $info->address }}
              </p>
            </div>
          </td>
          
        </tr>
      @endforeach
  </tbody>
  </table>
  {!! $students->appends(request()->all())->links() !!}
</div>
@endif


<script type="text/javascript">

  "use strict";

  function classWiseSection(classId) {
    let url = "{{ route('class_wise_sections', ['id' => ":classId"]) }}";
    url = url.replace(":classId", classId);
    $.ajax({
        url: url,
        success: function(response){
            $('#section_filter').html(response);
        }
    });
  }

  function filterStudentsTable() {
    var q = ($('#tableSearch').val() || '').toString().toLowerCase().trim();
    $('#studentsTable tbody tr').each(function () {
      var hay = ($(this).attr('data-search') || '').toString().toLowerCase();
      if (q === '' || hay.indexOf(q) !== -1) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  }

  $(document).on('input', '#tableSearch', function () {
    filterStudentsTable();
  });

  function Export() {

      // Choose the element that our invoice is rendered in.
      const element = document.getElementById("student_list");

      // clone the element
      var clonedElement = element.cloneNode(true);

      // change display of cloned element
      $(clonedElement).css("display", "block");

      // Choose the clonedElement and save the PDF for our user.
    var opt = {
      margin:       1,
      filename:     'student_list_{{ date("y-m-d") }}.pdf',
      image:        { type: 'jpeg', quality: 0.98 },
      html2canvas:  { scale: 2 }
    };

    // New Promise-based usage:
    html2pdf().set(opt).from(clonedElement).save();

      // remove cloned element
      clonedElement.remove();
  }

  function printableDiv(printableAreaDivId) {
    var printContents = document.getElementById(printableAreaDivId).innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;
  }

</script>


<!-- End Students area -->
@endsection