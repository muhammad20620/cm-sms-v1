<!DOCTYPE html>
<html lang="en">

<head>
    <!-- New -->
    <title>{{ get_phrase('Admin').' | '.get_settings('system_title') }}</title>
    <!-- all the meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- all the css files -->
    <link rel="shortcut icon" href="{{ asset('public/assets/uploads/logo/'.get_settings('favicon')) }}" />
    <!-- Bootstrap CSS -->
    <link
      rel="stylesheet"
      type="text/css"
      href="{{ asset('public/assets/vendors/bootstrap-5.1.3/css/bootstrap.min.css') }}"
    />

    <!--Custom css-->
    <link
      rel="stylesheet"
      type="text/css"
      href="{{ asset('public/assets/css/swiper-bundle.min.css') }}"
    />

    
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/css/style.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/css/main.css') }}" />
    <!-- Datepicker css -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/daterangepicker.css') }}" />
    <!-- Select2 css -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/select2.min.css') }}" />

    <link
      rel="stylesheet"
      type="text/css"
      href="{{ asset('public/assets/vendors/bootstrap-icons-1.8.1/bootstrap-icons.css') }}"
    />

    <!--Toaster css-->
    <link
      rel="stylesheet"
      type="text/css"
      href="{{ asset('public/assets/css/toastr.min.css') }}"
    />

    <!-- Calender css -->
    <link
      rel="stylesheet"
      type="text/css"
      href="{{ asset('public/assets/calender/main.css') }}"
    />


</head>

<body>

    <div class="sidebar">
        <div class="logo-details mt-4 mb-3">
            <div class="img_wrapper">
                <img height="40px" class="" src="{{ asset('public/assets/uploads/logo/'.get_settings('light_logo')) }}" alt="" />
            </div>
        </div>
      <div class="closeIcon">
        <span>{{ get_phrase('Close') }}</span>
      </div>
      <ul class="nav-links">
        <!-- sidebar title -->
        <li class="nav-links-li {{ request()->is('admin/dashboard') ? 'showMenu':'' }}">
            <div class="iocn-link">
              <a href="{{ route('admin.dashboard') }}">
                <div class="sidebar_icon">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" width="48" height="48">
                  <g>
                    <path d="M117.333,234.667C52.532,234.667,0,182.135,0,117.333S52.532,0,117.333,0s117.333,52.532,117.333,117.333   C234.596,182.106,182.106,234.596,117.333,234.667z M117.333,64C87.878,64,64,87.878,64,117.333s23.878,53.333,53.333,53.333   s53.333-23.878,53.333-53.333S146.789,64,117.333,64z"/>
                    <path d="M394.667,234.667c-64.801,0-117.333-52.532-117.333-117.333S329.865,0,394.667,0S512,52.532,512,117.333   C511.929,182.106,459.439,234.596,394.667,234.667z M394.667,64c-29.455,0-53.333,23.878-53.333,53.333   s23.878,53.333,53.333,53.333S448,146.789,448,117.333S424.122,64,394.667,64z"/>
                    <path d="M117.333,512C52.532,512,0,459.468,0,394.667s52.532-117.333,117.333-117.333s117.333,52.532,117.333,117.333   C234.596,459.439,182.106,511.929,117.333,512z M117.333,341.333C87.878,341.333,64,365.211,64,394.667S87.878,448,117.333,448   s53.333-23.878,53.333-53.333S146.789,341.333,117.333,341.333z"/>
                    <path d="M394.667,512c-64.801,0-117.333-52.532-117.333-117.333s52.532-117.333,117.333-117.333S512,329.865,512,394.667   C511.929,459.439,459.439,511.929,394.667,512z M394.667,341.333c-29.455,0-53.333,23.878-53.333,53.333S365.211,448,394.667,448   S448,424.122,448,394.667S424.122,341.333,394.667,341.333z"/>
                  </g>
                  </svg>

                </div>
                <span class="link_name">{{ get_phrase('Dashboard') }}</span>
              </a>
            </div>
        </li>
        <!-- Sidebar menu -->
        <li class="nav-links-li {{ request()->is('admin/admin*') || request()->is('admin/teacher*') || request()->is('admin/accountant*') || request()->is('admin/librarian*') || request()->is('admin/parent*') || request()->is('admin/student') || request()->is('admin/permission*') ? 'showMenu':'' }}">
            <div class="iocn-link">
                <a href="#">
                    <div class="sidebar_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="48" height="48"><path d="M16.5,24a1.5,1.5,0,0,1-1.489-1.335,3.031,3.031,0,0,0-6.018,0,1.5,1.5,0,0,1-2.982-.33,6.031,6.031,0,0,1,11.982,0,1.5,1.5,0,0,1-1.326,1.656A1.557,1.557,0,0,1,16.5,24Zm6.167-9.009a1.5,1.5,0,0,0,1.326-1.656A5.815,5.815,0,0,0,18.5,8a1.5,1.5,0,0,0,0,3,2.835,2.835,0,0,1,2.509,2.665A1.5,1.5,0,0,0,22.5,15,1.557,1.557,0,0,0,22.665,14.991ZM2.991,13.665A2.835,2.835,0,0,1,5.5,11a1.5,1.5,0,0,0,0-3A5.815,5.815,0,0,0,.009,13.335a1.5,1.5,0,0,0,1.326,1.656A1.557,1.557,0,0,0,1.5,15,1.5,1.5,0,0,0,2.991,13.665ZM12.077,16a3.5,3.5,0,1,0-3.5-3.5A3.5,3.5,0,0,0,12.077,16Zm6-9a3.5,3.5,0,1,0-3.5-3.5A3.5,3.5,0,0,0,18.077,7Zm-12,0a3.5,3.5,0,1,0-3.5-3.5A3.5,3.5,0,0,0,6.077,7Z"/></svg>
                    </div>
                    <span class="link_name">{{ get_phrase('Users') }}</span>
                </a>
                <span class="arrow">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="4.743"
                    height="7.773"
                    viewBox="0 0 4.743 7.773"
                  >
                    <path
                      id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                      d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                      fill="#fff"
                      opacity="1"
                    />
                  </svg>
                </span>
            </div>
            <ul class="sub-menu">
                <li><a class="{{ (request()->is('admin/admin*')) ? 'active' : '' }}" href="{{ route('admin.admin') }}"><span>
                            {{ get_phrase('Admin') }}
                        </span></a></li>
                <li><a class="{{ (request()->is('admin/teacher*')) ? 'active' : '' }}" href="{{ route('admin.teacher') }}"><span>
                            {{ get_phrase('Teacher') }}
                        </span></a></li>
                <li><a class="{{ (request()->is('admin/accountant*')) ? 'active' : '' }}" href="{{ route('admin.accountant') }}"><span>
                            {{ get_phrase('Accountant') }}
                        </span></a></li>
                <li><a class="{{ (request()->is('admin/librarian*')) ? 'active' : '' }}" href="{{ route('admin.librarian') }}"><span>
                            {{ get_phrase('Librarian') }}
                        </span></a></li>
                <li><a class="{{ (request()->is('admin/parent*')) ? 'active' : '' }}" href="{{ route('admin.parent') }}"><span>
                            {{ get_phrase('Parent') }}
                        </span></a></li>
                <li><a class="{{ (request()->is('admin/student')) ? 'active' : '' }}" href="{{ route('admin.student') }}"><span>
                            {{ get_phrase('Student') }}
                        </span></a></li>
                <li><a class="{{ (request()->is('admin/permission*')) ? 'active' : '' }}" href="{{ route('admin.teacher.permission') }}"><span>
                            {{ get_phrase('Teacher Permission') }}
                        </span></a></li>
            </ul>
        </li>

        <li class="nav-links-li {{ request()->is('admin/offline_admission*') ? 'showMenu':'' }}">
            <div class="iocn-link">
                <a class="{{ (request()->is('admin/offline_admission*')) ? 'active' : '' }}" href="{{ route('admin.offline_admission.single', ['type' => 'single']) }}">
                    <div class="sidebar_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" id="Isolation_Mode" data-name="Isolation Mode" viewBox="0 0 24 24" width="48" height="48"><path d="M11,14H5a5.006,5.006,0,0,0-5,5v5H3V19a2,2,0,0,1,2-2h6a2,2,0,0,1,2,2v5h3V19A5.006,5.006,0,0,0,11,14Z"/><path d="M8,12A6,6,0,1,0,2,6,6.006,6.006,0,0,0,8,12ZM8,3A3,3,0,1,1,5,6,3,3,0,0,1,8,3Z"/><polygon points="21 10 21 7 18 7 18 10 15 10 15 13 18 13 18 16 21 16 21 13 24 13 24 10 21 10"/></svg>
                    </div>
                    <span class="link_name">
                        {{ get_phrase('Admissions') }}
                    </span>
                </a>
            </div>
        </li>

        <li class="nav-links-li {{ request()->is('admin/exam_category*') || request()->is('admin/offline_exam*') || request()->is('admin/marks') || request()->is('admin/grade') || request()->is('admin/promotion*') ? 'showMenu':'' }}">
            <div class="iocn-link">
                <a href="#">
                    <div class="sidebar_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="48" height="48"><path d="M18,17.5A1.5,1.5,0,0,1,16.5,19h-1a1.5,1.5,0,0,1,0-3h1A1.5,1.5,0,0,1,18,17.5ZM13.092,14H10.908A1.5,1.5,0,0,1,8,13.5V10a4,4,0,0,1,8,0v3.5a1.5,1.5,0,0,1-2.908.5ZM11,10v1h2V10a1,1,0,0,0-2,0Zm-.569,5.947-.925.941a1.5,1.5,0,0,0-2.139,2.095s.163.187.189.211a2.757,2.757,0,0,0,3.9-.007l1.116-1.134a1.5,1.5,0,1,0-2.138-2.106ZM22,7.157V18.5A5.507,5.507,0,0,1,16.5,24h-9A5.507,5.507,0,0,1,2,18.5V5.5A5.507,5.507,0,0,1,7.5,0h7.343a5.464,5.464,0,0,1,3.889,1.611l1.657,1.657A5.464,5.464,0,0,1,22,7.157ZM18.985,7H17a2,2,0,0,1-2-2V3.015C14.947,3.012,7.5,3,7.5,3A2.5,2.5,0,0,0,5,5.5v13A2.5,2.5,0,0,0,7.5,21h9A2.5,2.5,0,0,0,19,18.5S18.988,7.053,18.985,7Z"/></svg>
                    </div>
                    <span class="link_name">
                        {{ get_phrase('Examination') }}
                    </span>
                </a>
                <span class="arrow">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="4.743"
                    height="7.773"
                    viewBox="0 0 4.743 7.773"
                  >
                    <path
                      id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                      d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                      fill="#fff"
                      opacity="1"
                    />
                  </svg>
                </span>
            </div>
            <ul class="sub-menu">
                <li>
                    <a class="{{ (request()->is('admin/exam_category*')) ? 'active' : '' }}" href="{{ route('admin.exam_category') }}"><span>{{ get_phrase('Exam Category') }}</span></a>
                </li>
                <li>
                    <a class="{{ (request()->is('admin/offline_exam*')) ? 'active' : '' }}" href="{{ route('admin.offline_exam') }}"><span>{{ get_phrase('Offline Exam') }}</span></a>
                </li>
                <li>
                    <a class="{{ (request()->is('admin/marks')) ? 'active' : '' }}" href="{{ route('admin.marks') }}"><span>{{ get_phrase('Marks') }}</span></a>
                </li>
                <li>
                    <a class="{{ (request()->is('admin/grade')) ? 'active' : '' }}" href="{{ route('admin.grade_list') }}"><span>{{ get_phrase('Grades') }}</span></a>
                </li>
                <li>
                    <a class="{{ (request()->is('admin/promotion*')) ? 'active' : '' }}" href="{{ route('admin.promotion') }}"><span>{{ get_phrase('Promotion') }}</span></a>
                </li>
            </ul>
        </li>


        <li class="nav-links-li {{ request()->is('admin/attendance*') || request()->is('admin/class_list*') || request()->is('admin/routine*') || request()->is('admin/subject*') || request()->is('admin/syllabus*') || request()->is('admin/gradebook*') || request()->is('admin/class_room*') || request()->is('admin/department*') ? 'showMenu':'' }}">
            <div class="iocn-link">
                <a href="#">
                    <div class="sidebar_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="48" height="48"><path d="M7.5,4.5c.151-5.935,8.85-5.934,9,0-.151,5.935-8.85,5.934-9,0ZM24,15.5v1.793c0,2.659-1.899,4.935-4.516,5.411l-5.763,1.139c-1.142,.207-2.285,.21-3.421,.004l-5.807-1.147c-2.595-.472-4.494-2.748-4.494-5.407v-1.793c-.083-3.331,3.222-6.087,6.483-5.411l3.36,.702c.824,.15,1.564,.527,2.16,1.062,.601-.537,1.351-.916,2.191-1.069l3.282-.688c1.653-.301,3.293,.134,4.548,1.181,1.256,1.048,1.976,2.587,1.976,4.223Zm-13.5-.289c0-.726-.518-1.346-1.231-1.476l-3.36-.702c-.707-.126-1.439,.075-2.01,.548-.571,.477-.898,1.176-.898,1.919v1.793c0,1.209,.863,2.243,2.053,2.46l5.447,1.076v-5.618Zm10.5,.289c0-.744-.327-1.443-.897-1.919-.57-.476-1.318-.674-2.05-.54l-3.282,.687c-.753,.137-1.271,.758-1.271,1.483v5.618l5.425-1.072c1.212-.221,2.075-1.255,2.075-2.464v-1.793Z"/></svg>
                    </div>
                    <span class="link_name">
                        {{ get_phrase('Academic') }}
                    </span>
                </a>
                <span class="arrow">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="4.743"
                    height="7.773"
                    viewBox="0 0 4.743 7.773"
                  >
                    <path
                      id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                      d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                      fill="#fff"
                      opacity="1"
                    />
                  </svg>
                </span>
            </div>
            <ul class="sub-menu">
                <li><a class="{{ (request()->is('admin/attendance*')) ? 'active' : '' }}" href="{{ route('admin.daily_attendance') }}"><span>
                            {{ get_phrase('Daily Attendance') }}
                        </span></a></li>
                <li><a class="{{ (request()->is('admin/class_list*')) ? 'active' : '' }}" href="{{ route('admin.class_list') }}"><span>
                            {{ get_phrase('Class List') }}
                        </span></a></li>
                <li><a class="{{ (request()->is('admin/routine*')) ? 'active' : '' }}" href="{{ route('admin.routine') }}"><span>
                            {{ get_phrase('Class Routine') }}
                        </span></a></li>
                <li><a class="{{ (request()->is('admin/subject*')) ? 'active' : '' }}" href="{{ route('admin.subject_list') }}"><span>
                            {{ get_phrase('Subjects') }}
                        </span></a></li>
                <li><a class="{{ (request()->is('admin/gradebook*')) ? 'active' : '' }}" href="{{ route('admin.gradebook') }}"><span>
                            {{ get_phrase('Gradebooks') }}
                        </span></a></li>
                <li><a class="{{ (request()->is('admin/syllabus*')) ? 'active' : '' }}" href="{{ route('admin.syllabus') }}"><span>
                            {{ get_phrase('Syllabus') }}
                        </span></a></li>
                <li><a class="{{ (request()->is('admin/class_room*')) ? 'active' : '' }}" href="{{ route('admin.class_room_list') }}"><span>
                            {{ get_phrase('Class Room') }}
                        </span></a></li>
                <li><a class="{{ (request()->is('admin/department*')) ? 'active' : '' }}" href="{{ route('admin.department_list') }}"><span>
                            {{ get_phrase('Department') }}
                        </span></a></li>
            </ul>
        </li>


        <li class="nav-links-li {{ request()->is('admin/fee_manager*') || request()->is('admin/offline_payment/pending*')|| request()->is('admin/expense_category*') || request()->is('admin/expenses*') ? 'showMenu':'' }}">
            <div class="iocn-link">
                <a href="#">
                    <div class="sidebar_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="48" height="48"><path d="M16.5,10c-1.972-.034-1.971-2.967,0-3h1c1.972,.034,1.971,2.967,0,3h-1Zm-3.5,4.413c0-1.476-.885-2.783-2.255-3.331l-2.376-.95c-.591-.216-.411-1.15,.218-1.132h1.181c.181,0,.343,.094,.434,.251,.415,.717,1.334,.962,2.05,.547,.717-.415,.962-1.333,.548-2.049-.511-.883-1.381-1.492-2.363-1.684-.399-1.442-2.588-1.375-2.896,.091-3.161,.875-3.414,5.6-.285,6.762l2.376,.95c.591,.216,.411,1.15-.218,1.132h-1.181c-.181,0-.343-.094-.434-.25-.415-.717-1.334-.961-2.05-.547-.717,.415-.962,1.333-.548,2.049,.511,.883,1.381,1.491,2.363,1.683,.399,1.442,2.588,1.375,2.896-.091,1.469-.449,2.54-1.817,2.54-3.431ZM18.5,1H5.5C2.468,1,0,3.467,0,6.5v11c0,3.033,2.468,5.5,5.5,5.5h3c1.972-.034,1.971-2.967,0-3h-3c-1.379,0-2.5-1.122-2.5-2.5V6.5c0-1.378,1.121-2.5,2.5-2.5h13c1.379,0,2.5,1.122,2.5,2.5v2c.034,1.972,2.967,1.971,3,0v-2c0-3.033-2.468-5.5-5.5-5.5Zm-5.205,18.481c-.813,.813-1.269,1.915-1.269,3.064,.044,.422-.21,1.464,.5,1.455,1.446,.094,2.986-.171,4.019-1.269l6.715-6.715c2.194-2.202-.9-5.469-3.157-3.343l-6.808,6.808Z"/></svg>
                    </div>
                    <span class="link_name">
                        {{ get_phrase('Accounting') }}
                    </span>
                </a>
                <span class="arrow">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="4.743"
                    height="7.773"
                    viewBox="0 0 4.743 7.773"
                  >
                    <path
                      id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                      d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                      fill="#fff"
                      opacity="1"
                    />
                  </svg>
                </span>
            </div>
            <ul class="sub-menu">
                <li><a class="{{ (request()->is('admin/fee_manager')) ? 'active' : '' }}" href="{{ route('admin.fee_manager.list') }}"><span>
                            {{ get_phrase('Student Fee Manager') }}
                        </span></a></li>
                <li><a class="{{ (request()->is('admin/offline_payment/pending*')) ? 'active' : '' }}" href="{{ route('admin.offline_payment_pending') }}"><span>
                            {{ get_phrase('Offline Payment Request') }}
                        </span></a></li>
                <li><a class="{{ (request()->is('admin/expenses*')) ? 'active' : '' }}" href="{{ route('admin.expense.list') }}"><span>
                            {{ get_phrase('Expense Manager') }}
                        </span></a></li>
                <li><a class="{{ (request()->is('admin/expense_category*')) ? 'active' : '' }}" href="{{ route('admin.expense.category_list') }}"><span>
                            {{ get_phrase('Expense Category') }}
                        </span></a></li>
            </ul>
        </li>

        @if(addon_status('hr_management')==1)

        <?php

        $to=strtotime(date("m/d/Y"))+8600;

        $f= date('m/d/Y', strtotime("-31 days"));
        $form=strtotime(date('m/d/Y'),strtotime($f));
        ?>

        <li class="nav-links-li {{ request()->is('hr/leave_list*') || request()->is('payroll/create/payslip*') || request()->is('attendence/list*') || request()->is('payroll/list*') || request()->is('user/roles*') || request()->is('user/list*')  ? 'showMenu':'' }}">
            <div class="iocn-link">
                <a href="#">
                    <div class="sidebar_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 14.991 17.164">
                            <path id="Exam_Icon" data-name="Exam Icon" d="M6.331,13.015h5.83v1.716H6.331Zm0-3.433H14.66V11.3H6.331Zm0-3.433H14.66V7.866H6.331Zm9.994-3.433H12.844a2.465,2.465,0,0,0-4.7,0H4.666a1.417,1.417,0,0,0-.333.034,1.659,1.659,0,0,0-.841.472,1.723,1.723,0,0,0-.358.549A1.7,1.7,0,0,0,3,4.433V16.448a1.766,1.766,0,0,0,.491,1.219,1.659,1.659,0,0,0,.841.472,2.1,2.1,0,0,0,.333.026h11.66a1.7,1.7,0,0,0,1.666-1.716V4.433A1.7,1.7,0,0,0,16.325,2.716ZM10.5,2.5a.644.644,0,1,1-.625.644A.639.639,0,0,1,10.5,2.5Zm5.83,13.946H4.666V4.433h11.66Z" transform="translate(-3 -1)" />
                        </svg>
                    </div>
                    <span class="link_name">
                        {{ get_phrase('Human Resource') }}
                    </span>
                </a>
                <span class="arrow">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="4.743"
                    height="7.773"
                    viewBox="0 0 4.743 7.773"
                  >
                    <path
                      id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                      d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                      fill="#fff"
                      opacity="1"
                    />
                  </svg>
                </span>
            </div>
            <ul class="sub-menu">

                <li>
                    <a class="{{ (request()->is('user/roles*')) ? 'active' : '' }}" href="{{ route('hr.user_role_index') }}">
                        <span>
                            {{ get_phrase('User Roles') }}
                        </span>
                        <!-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> -->
                    </a>
                </li>

                <li>
                    <a class="{{ (request()->is('user/list*')) ? 'active' : '' }}" href="{{ route('hr.userlist_index') }}">
                        <span>
                            {{ get_phrase('User List') }}
                        </span>
                        <!-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> -->
                    </a>
                </li>


                <li>
                    <a class="{{ (request()->is('attendence/list*')) ? 'active' : '' }}" href="{{ route('hr.list_of_attendence') }}">
                        <span>
                            {{ get_phrase('Take Attendence'); }}
                        </span>
                        <!-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> -->
                    </a>
                </li>

                <li>
                    <a class="{{ (request()->is('hr/leave_list*')) ? 'active' : '' }}" href="{{ route('hr.list_of_leaves') }}">
                        <span>
                            {{ get_phrase('Leave'); }}
                        </span>
                        <!-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> -->
                    </a>
                </li>
                <li>
                    <a class="{{ (request()->is('payroll/list*')) ? 'active' : '' }}" href="{{ route('hr.list_of_payrolls') }}">
                        <span>
                            {{ get_phrase('Payroll'); }}
                        </span>
                        <!-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> -->
                    </a>
                </li>

            </ul>
        </li>

        @endif


        <li class="nav-links-li {{ request()->is('admin/book*') || request()->is('admin/book_issue*') || request()->is('admin/noticeboard*') || request()->is('admin/subscription') || request()->is('admin/events/list*') ? 'showMenu':'' }}">
            <div class="iocn-link">
                <a href="#">
                    <div class="sidebar_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" id="Bold" viewBox="0 0 24 24" width="48" height="48"><path d="M18.5,3h-.642A4,4,0,0,0,14,0H10A4,4,0,0,0,6.142,3H5.5A5.506,5.506,0,0,0,0,8.5v10A5.506,5.506,0,0,0,5.5,24h13A5.507,5.507,0,0,0,24,18.5V8.5A5.507,5.507,0,0,0,18.5,3ZM5.5,6h13A2.5,2.5,0,0,1,21,8.5V11H3V8.5A2.5,2.5,0,0,1,5.5,6Zm13,15H5.5A2.5,2.5,0,0,1,3,18.5V14h7a2,2,0,0,0,2,2h0a2,2,0,0,0,2-2h7v4.5A2.5,2.5,0,0,1,18.5,21Z"/></svg>
                    </div>
                    <span class="link_name">
                        {{ get_phrase('Back Office') }}
                    </span>
                </a>
                <span class="arrow">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="4.743"
                    height="7.773"
                    viewBox="0 0 4.743 7.773"
                  >
                    <path
                      id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                      d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                      fill="#fff"
                      opacity="1"
                    />
                  </svg>
                </span>
            </div>
            <ul class="sub-menu">
                <li><a class="{{ (request()->is('admin/book/list')) ? 'active' : '' }}" href="{{ route('admin.book.book_list') }}"><span>
                            {{ get_phrase('Book List Manager') }}
                        </span></a></li>
                <li><a class="{{ (request()->is('admin/book_issue')) ? 'active' : '' }}" href="{{ route('admin.book_issue.list') }}"><span>
                            {{ get_phrase('Book Issue Report') }}
                        </span></a></li>
                <li><a class="{{ (request()->is('admin/noticeboard*')) ? 'active' : '' }}" href="{{ route('admin.noticeboard.list') }}"><span>
                            {{ get_phrase('Noticeboard') }}
                        </span></a></li>
                <li><a class="{{ (request()->is('admin/subscription')) ? 'active' : '' }}" href="{{ route('admin.subscription') }}"><span>
                            {{ get_phrase('Subscription') }}
                        </span></a></li>
                <li>
                    <a class="{{ (request()->is('admin/events/list*')) ? 'active' : '' }}" href="{{ route('admin.events.list') }}"><span>{{ get_phrase('Events') }}
                    </span></a>
                </li>
            </ul>
        </li>

        <li class="nav-links-li {{ request()->is('admin/settings/school*') || request()->is('admin/session_manager*') || request()->is('admin/settings/payment*') || request()->is('admin/live_class_settings*') || request()->is('admin/profile*') ? 'showMenu':'' }}">
            <div class="iocn-link">
                <a href="#">
                    <div class="sidebar_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" width="48" height="48">
                        <g>
                          <path d="M256,162.923c-51.405,0-93.077,41.672-93.077,93.077s41.672,93.077,93.077,93.077s93.077-41.672,93.077-93.077   C349.019,204.619,307.381,162.981,256,162.923z M256,285.077c-16.059,0-29.077-13.018-29.077-29.077s13.018-29.077,29.077-29.077   c16.059,0,29.077,13.018,29.077,29.077l0,0C285.066,272.054,272.054,285.066,256,285.077z"/>
                          <path d="M469.333,256c-0.032-32.689-7.633-64.927-22.208-94.187l10.965-7.616c14.496-10.104,18.058-30.046,7.957-44.544l0,0   c-10.104-14.496-30.046-18.058-44.544-7.957l-10.987,7.637c-32.574-34.38-75.691-56.904-122.517-64V32c0-17.673-14.327-32-32-32   l0,0c-17.673,0-32,14.327-32,32v13.333c-46.826,7.096-89.944,29.62-122.517,64l-10.987-7.637   c-14.498-10.101-34.44-6.538-44.544,7.957l0,0c-10.101,14.498-6.538,34.44,7.957,44.544l10.965,7.616   c-29.61,59.3-29.61,129.073,0,188.373l-10.965,7.616c-14.496,10.104-18.058,30.046-7.957,44.544l0,0   c10.104,14.496,30.046,18.058,44.544,7.957l10.987-7.637c32.574,34.38,75.691,56.904,122.517,64V480c0,17.673,14.327,32,32,32l0,0   c17.673,0,32-14.327,32-32v-13.333c46.826-7.096,89.944-29.62,122.517-64l10.987,7.637c14.498,10.101,34.44,6.538,44.544-7.957l0,0   c10.101-14.498,6.538-34.44-7.957-44.544l-10.965-7.616C461.7,320.927,469.301,288.689,469.333,256z M256,405.333   c-82.475,0-149.333-66.859-149.333-149.333S173.525,106.667,256,106.667S405.333,173.525,405.333,256   C405.228,338.431,338.431,405.228,256,405.333z"/>
                        </g>
                        </svg>
                    </div>
                    <span class="link_name">
                        {{ get_phrase('Settings') }}
                    </span>
                </a>
                <span class="arrow">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="4.743"
                    height="7.773"
                    viewBox="0 0 4.743 7.773"
                  >
                    <path
                      id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                      d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                      fill="#fff"
                      opacity="1"
                    />
                  </svg>
                </span>
            </div>
            <ul class="sub-menu">
                <li>
                    <a class="{{ (request()->is('admin/settings/school*')) ? 'active' : '' }}" href="{{ route('admin.settings.school') }}">
                        <span>
                            {{ get_phrase('School Settings') }}
                        </span>
                    </a>
                </li>

                <li>
                    <a class="{{ (request()->is('admin/session_manager*')) ? 'active' : '' }}" href="{{ route('admin.settings.session_manager') }}">
                        <span>
                            {{ get_phrase('Session Manager') }}
                        </span>
                    </a>
                </li>

                <li>
                    <a class="{{ (request()->is('admin/settings/payment*')) ? 'active' : '' }}" href="{{ route('admin.settings.payment') }}">
                        <span>
                            {{ get_phrase('Payment Settings') }}
                        </span>
                    </a>
                </li>

                @if(addon_status('online_bundles')==1 && addon_status('online_live_class')==1)
                <li>
                    <a class="{{ (request()->is('admin/live_class_settings*')) ? 'active' : '' }}" href="{{ route('admin.live_class_key') }}">
                        <span>
                            {{ get_phrase('Live class'); }}
                        </span>
                        <!-- <span class="badge bg-success m-1" style="">{{ get_phrase('Addon') }}</span> -->
                    </a>
                </li>
                @endif

                <li><a class="{{ (request()->is('admin/profile*')) ? 'active' : '' }}" href="{{ route('admin.profile') }}"><span>
                        {{ get_phrase('My Account') }}
                </span></a></li>

            </ul>
        </li>

      </ul>
    </div>

    <section class="home-section">
      <div class="home-content">
        <div class="home-header">
          <div class="row w-100 justify-content-between align-items-center">
            <div class="col-auto">
              <div class="sidebar_menu_icon">
                <div class="menuList">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="15"
                    height="12"
                    viewBox="0 0 15 12"
                  >
                    <path
                      id="Union_5"
                      data-name="Union 5"
                      d="M-2188.5,52.5v-2h15v2Zm0-5v-2h15v2Zm0-5v-2h15v2Z"
                      transform="translate(2188.5 -40.5)"
                      fill="#6e6f78"
                    />
                  </svg>
                </div>
              </div>
            </div>

            <div class="col-auto d-xl-block d-none">
              <div class="header_notification d-flex align-items-center">
                <div class="notification_icon">
                  <svg style="width: 14px; fill: #00a3ff;" xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="48px" height="48px"><path d="M19,6H14.321A3.95,3.95,0,0,0,13,5.388V5l3.53-1.652a1,1,0,0,0,0-1.7l-3.2-1.5A1.637,1.637,0,0,0,11,1.636V5.388A3.937,3.937,0,0,0,9.68,6H5a5.006,5.006,0,0,0-5,5V21a3,3,0,0,0,3,3H21a3,3,0,0,0,3-3V11A5.006,5.006,0,0,0,19,6Zm2.816,4H19.657a3.017,3.017,0,0,1-2.121-.879L16.414,8H19A3,3,0,0,1,21.816,10ZM5,8H7.586L6.464,9.121A3.017,3.017,0,0,1,4.343,10H2.184A3,3,0,0,1,5,8Zm8,14H11V19a1,1,0,0,1,2,0Zm8,0H15V19a3,3,0,0,0-6,0v3H3a1,1,0,0,1-1-1V12H4.343a4.968,4.968,0,0,0,3.535-1.465l2.708-2.707a2,2,0,0,1,2.828,0l2.708,2.707A4.968,4.968,0,0,0,19.657,12H22v9A1,1,0,0,1,21,22ZM7,15a1,1,0,0,1-1,1H5a1,1,0,0,1,0-2H6A1,1,0,0,1,7,15Zm0,4a1,1,0,0,1-1,1H5a1,1,0,0,1,0-2H6A1,1,0,0,1,7,19Zm13-4a1,1,0,0,1-1,1H18a1,1,0,0,1,0-2h1A1,1,0,0,1,20,15Zm0,4a1,1,0,0,1-1,1H18a1,1,0,0,1,0-2h1A1,1,0,0,1,20,19Zm-6-7a2,2,0,1,1-2-2A2,2,0,0,1,14,12Z"></path></svg>
                </div>
                <p>
                  {{DB::table('schools')->where('id', auth()->user()->school_id)->value('title') }}
                </p>
              </div>
            </div>
            
            <div class="col-auto">
              <div class="header-menu">
                <ul>
                  <li class="user-profile">
                    <div class="btn-group">
                      <button
                        class="btn btn-secondary dropdown-toggle"
                        type="button"
                        id="defaultDropdown"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="true"
                        aria-expanded="false"
                      >
                        <div class="">
                          <img src="{{ get_user_image(auth()->user()->id) }}" height="42px" />
                        </div>
                        <div class="px-2 text-start">
                          <span class="user-name">{{ auth()->user()->name }}</span>
                          <span class="user-title">{{ get_phrase('Admin') }}</span>
                        </div>
                      </button>
                      <ul
                        class="dropdown-menu dropdown-menu-end eDropdown-menu"
                        aria-labelledby="defaultDropdown"
                      >
                        <li class="user-profile user-profile-inner">
                          <button
                            class="btn w-100 d-flex align-items-center"
                            type="button"
                          >
                            <div class="">
                              <img
                                class="radious-5px"
                                src="{{ get_user_image(auth()->user()->id) }}"
                                height="42px"
                              />
                            </div>
                            <div class="px-2 text-start">
                              <span class="user-name">{{ auth()->user()->name }}</span>
                              <span class="user-title">{{ get_phrase('Admin') }}</span>
                            </div>
                          </button>
                        </li>

                        <li>
                          <a class="dropdown-item" href="{{route('admin.profile')}}">
                            <span>
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="13.275"
                                height="14.944"
                                viewBox="0 0 13.275 14.944"
                              >
                                <g
                                  id="user_icon"
                                  data-name="user icon"
                                  transform="translate(-1368.531 -147.15)"
                                >
                                  <g
                                    id="Ellipse_1"
                                    data-name="Ellipse 1"
                                    transform="translate(1370.609 147.15)"
                                    fill="none"
                                    stroke="#181c32"
                                    stroke-width="2"
                                  >
                                    <ellipse
                                      cx="4.576"
                                      cy="4.435"
                                      rx="4.576"
                                      ry="4.435"
                                      stroke="none"
                                    />
                                    <ellipse
                                      cx="4.576"
                                      cy="4.435"
                                      rx="3.576"
                                      ry="3.435"
                                      fill="none"
                                    />
                                  </g>
                                  <path
                                    id="Path_41"
                                    data-name="Path 41"
                                    d="M1485.186,311.087a5.818,5.818,0,0,1,5.856-4.283,5.534,5.534,0,0,1,5.466,4.283"
                                    transform="translate(-115.686 -149.241)"
                                    fill="none"
                                    stroke="#181c32"
                                    stroke-width="2"
                                  />
                                </g>
                              </svg>
                            </span>
                            {{ get_phrase('My Account') }}
                          </a>
                        </li>
                        <li>
                          <a class="dropdown-item" href="{{route('admin.password', ['edit'])}}">
                            <span>
                              <svg id="Layer_1" width="13.275" height="14.944" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1"><path d="m6.5 16a1.5 1.5 0 1 1 -1.5 1.5 1.5 1.5 0 0 1 1.5-1.5zm3 7.861a7.939 7.939 0 0 0 6.065-5.261 7.8 7.8 0 0 0 .32-3.85l.681-.689a1.5 1.5 0 0 0 .434-1.061v-2h.5a2.5 2.5 0 0 0 2.5-2.5v-.5h1.251a2.512 2.512 0 0 0 2.307-1.52 5.323 5.323 0 0 0 .416-2.635 4.317 4.317 0 0 0 -4.345-3.845 5.467 5.467 0 0 0 -3.891 1.612l-6.5 6.5a7.776 7.776 0 0 0 -3.84.326 8 8 0 0 0 2.627 15.562 8.131 8.131 0 0 0 1.475-.139zm-.185-12.661a1.5 1.5 0 0 0 1.463-.385l7.081-7.08a2.487 2.487 0 0 1 1.77-.735 1.342 1.342 0 0 1 1.36 1.149 2.2 2.2 0 0 1 -.08.851h-1.409a2.5 2.5 0 0 0 -2.5 2.5v.5h-.5a2.5 2.5 0 0 0 -2.5 2.5v1.884l-.822.831a1.5 1.5 0 0 0 -.378 1.459 4.923 4.923 0 0 1 -.074 2.955 5 5 0 1 1 -6.36-6.352 4.9 4.9 0 0 1 1.592-.268 5.053 5.053 0 0 1 1.357.191z"/></svg>
                            </span>
                            {{ get_phrase('Change Password') }}
                          </a>
                        </li>
                        
                        <li>
                          <hr class="my-0">
                        </li>
                
                        <!-- Logout Button -->
                        <li>
                            <a class="btn eLogut_btn" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                <span>
                                  <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="14.046"
                                    height="12.29"
                                    viewBox="0 0 14.046 12.29"
                                  >
                                    <path
                                      id="Logout"
                                      d="M4.389,42.535H2.634a.878.878,0,0,1-.878-.878V34.634a.878.878,0,0,1,.878-.878H4.389a.878.878,0,0,0,0-1.756H2.634A2.634,2.634,0,0,0,0,34.634v7.023A2.634,2.634,0,0,0,2.634,44.29H4.389a.878.878,0,1,0,0-1.756Zm9.4-5.009-3.512-3.512a.878.878,0,0,0-1.241,1.241l2.015,2.012H5.267a.878.878,0,0,0,0,1.756H11.05L9.037,41.036a.878.878,0,1,0,1.241,1.241l3.512-3.512A.879.879,0,0,0,13.788,37.525Z"
                                      transform="translate(0 -32)"
                                      fill="#fff"
                                    />
                                  </svg>
                                </span>
                                {{ get_phrase('Log out') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                      </ul>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="main_content">
            @yield('content')
            <!-- Start Footer -->
            <div class="copyright-text">
              <?php $active_session = DB::table('sessions')->where('id',  get_settings('running_session'))->value('session_title'); ?>
                <p>{{ $active_session }} &copy; <span><a class="text-info footer-color" target="_blank" href="{{ get_settings('footer_link') }}">{{ get_settings('footer_text') }}</a></span></p>
            </div>
            <!-- End Footer -->
        </div>
      </div>
      @include('modal')
    </section>


    <!--Main Jquery-->
    <script src="{{ asset('public/assets/vendors/jquery/jquery-3.6.0.min.js') }}"></script>
    <!--Bootstrap bundle with popper-->
    <script src="{{ asset('public/assets/vendors/bootstrap-5.1.3/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/swiper-bundle.min.js') }}"></script>
    <!-- Datepicker js -->
    <script src="{{ asset('public/assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/daterangepicker.min.js') }}"></script>
    <!-- Select2 js -->
    <script src="{{ asset('public/assets/js/select2.min.js') }}"></script>

    <!--Custom Script-->
    <script src="{{ asset('public/assets/js/script.js') }}"></script>

    <!-- Calender js -->
    <script src="{{ asset('public/assets/calender/main.js') }}"></script>
    <script src="{{ asset('public/assets/calender/locales-all.js') }}"></script>

    <!--Toaster Script-->
    <script src="{{ asset('public/assets/js/toastr.min.js') }}"></script>

    <!--pdf Script-->
    <script src="{{ asset('public/assets/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/html2pdf.bundle.min.js') }}"></script>

    <!--html2canvas Script-->
    <script src="{{ asset('public/assets/js/html2canvas.min.js') }}"></script>

    <script>

        "use strict";
        
        @if(Session::has('message'))
		toastr.options =
		{
			"closeButton" : true,
			"progressBar" : true
		}
				toastr.success("{{ session('message') }}");
		@endif

		@if(Session::has('error'))
		toastr.options =
		{
			"closeButton" : true,
			"progressBar" : true
		}
				toastr.error("{{ session('error') }}");
		@endif

		@if(Session::has('info'))
		toastr.options =
		{
			"closeButton" : true,
			"progressBar" : true
		}
				toastr.info("{{ session('info') }}");
		@endif

		@if(Session::has('warning'))
		toastr.options =
		{
			"closeButton" : true,
			"progressBar" : true
		}
				toastr.warning("{{ session('warning') }}");
		@endif
    </script>

    <script>

        "use strict";
        
        jQuery(document).ready(function(){
        $('input[name="datetimes"]').daterangepicker({
            timePicker: true,
            startDate: moment().startOf('day').subtract(30, 'day'),
            endDate: moment().startOf('day'),
            locale: {
           format: 'M/DD/YYYY '
          }

        });
    });

    </script>

</body>

</html>
