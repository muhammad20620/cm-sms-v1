<!DOCTYPE html>
<html lang="en">
<head>
    <!-- New -->
    <title>{{ get_phrase('Superadmin').' | '.get_settings('system_title') }}</title>
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
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/css/main.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/css/style.css') }}" />
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
	<div class="sidebar print-hidden">
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
      <li class="nav-links-li {{ request()->is('superadmin/dashboard') ? 'showMenu':'' }}">
        <div class="iocn-link">
          <a href="{{ route('superadmin.dashboard') }}">
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

			<li class="nav-links-li {{ request()->is('superadmin/school/list*') || request()->is('superadmin/school/add*') || request()->is('superadmin/school/pending*') ? 'showMenu':'' }}">
				<div class="iocn-link">
					<a href="#">
						<div class="sidebar_icon">
							<svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="48px" height="48px"><path d="M19,6H14.321A3.95,3.95,0,0,0,13,5.388V5l3.53-1.652a1,1,0,0,0,0-1.7l-3.2-1.5A1.637,1.637,0,0,0,11,1.636V5.388A3.937,3.937,0,0,0,9.68,6H5a5.006,5.006,0,0,0-5,5V21a3,3,0,0,0,3,3H21a3,3,0,0,0,3-3V11A5.006,5.006,0,0,0,19,6Zm2.816,4H19.657a3.017,3.017,0,0,1-2.121-.879L16.414,8H19A3,3,0,0,1,21.816,10ZM5,8H7.586L6.464,9.121A3.017,3.017,0,0,1,4.343,10H2.184A3,3,0,0,1,5,8Zm8,14H11V19a1,1,0,0,1,2,0Zm8,0H15V19a3,3,0,0,0-6,0v3H3a1,1,0,0,1-1-1V12H4.343a4.968,4.968,0,0,0,3.535-1.465l2.708-2.707a2,2,0,0,1,2.828,0l2.708,2.707A4.968,4.968,0,0,0,19.657,12H22v9A1,1,0,0,1,21,22ZM7,15a1,1,0,0,1-1,1H5a1,1,0,0,1,0-2H6A1,1,0,0,1,7,15Zm0,4a1,1,0,0,1-1,1H5a1,1,0,0,1,0-2H6A1,1,0,0,1,7,19Zm13-4a1,1,0,0,1-1,1H18a1,1,0,0,1,0-2h1A1,1,0,0,1,20,15Zm0,4a1,1,0,0,1-1,1H18a1,1,0,0,1,0-2h1A1,1,0,0,1,20,19Zm-6-7a2,2,0,1,1-2-2A2,2,0,0,1,14,12Z"/></svg>
						</div>
						<span class="link_name">{{ get_phrase('Schools') }}</span>
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
					<li><a class="{{ (request()->is('superadmin/school/list*')) ? 'active' : '' }}" href="{{ route('superadmin.school.list') }}"><span>{{ get_phrase('School List') }}</span></a></li>
					<li><a class="{{ (request()->is('superadmin/school/add*')) ? 'active' : '' }}" href="{{ route('superadmin.school.add') }}"><span>{{ get_phrase('Create school') }}</span></a></li>
					<li><a class="{{ (request()->is('superadmin/school/pending*')) ? 'active' : '' }}" href="{{ route('superadmin.school.pending') }}"><span>{{ get_phrase('Pending List') }}</span></a></li>
				</ul>
			</li>
			<li class="nav-links-li {{ request()->is('superadmin/package*') ? 'showMenu':'' }}">
				<div class="iocn-link">
					<a class="{{ (request()->is('superadmin/package*')) ? 'active' : '' }}" href="{{ route('superadmin.package') }}">
						<div class="sidebar_icon">
							<svg id="Layer_1" height="48px" viewBox="0 0 24 24" width="48px" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1"><path d="m5 11.5a1.5 1.5 0 0 1 1.5-1.5h11a1.5 1.5 0 0 1 0 3h-11a1.5 1.5 0 0 1 -1.5-1.5zm1.5 6.5h7a1.5 1.5 0 0 0 0-3h-7a1.5 1.5 0 0 0 0 3zm17.5-11.5v11a5.506 5.506 0 0 1 -5.5 5.5h-13a5.506 5.506 0 0 1 -5.5-5.5v-11a5.506 5.506 0 0 1 5.5-5.5h13a5.506 5.506 0 0 1 5.5 5.5zm-16-2a1.5 1.5 0 1 0 1.5-1.5 1.5 1.5 0 0 0 -1.5 1.5zm-5 0a1.5 1.5 0 1 0 1.5-1.5 1.5 1.5 0 0 0 -1.5 1.5zm18 3.5h-18v9.5a2.5 2.5 0 0 0 2.5 2.5h13a2.5 2.5 0 0 0 2.5-2.5z"/></svg>
						</div>
						<span class="link_name">{{ get_phrase('Package') }}</span>
					</a>
				</div>
			</li>

			<li class="nav-links-li {{ request()->is('superadmin/subscription/report*') || request()->is('superadmin/subscription/pending*') || request()->is('superadmin/subscription/approve*') ? 'showMenu':'' }}">
				<div class="iocn-link">
					<a href="#">
						<div class="sidebar_icon">
							<svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="48" height="48"><path d="M16.5,10c-1.972-.034-1.971-2.967,0-3h1c1.972,.034,1.971,2.967,0,3h-1Zm-3.5,4.413c0-1.476-.885-2.783-2.255-3.331l-2.376-.95c-.591-.216-.411-1.15,.218-1.132h1.181c.181,0,.343,.094,.434,.251,.415,.717,1.334,.962,2.05,.547,.717-.415,.962-1.333,.548-2.049-.511-.883-1.381-1.492-2.363-1.684-.399-1.442-2.588-1.375-2.896,.091-3.161,.875-3.414,5.6-.285,6.762l2.376,.95c.591,.216,.411,1.15-.218,1.132h-1.181c-.181,0-.343-.094-.434-.25-.415-.717-1.334-.961-2.05-.547-.717,.415-.962,1.333-.548,2.049,.511,.883,1.381,1.491,2.363,1.683,.399,1.442,2.588,1.375,2.896-.091,1.469-.449,2.54-1.817,2.54-3.431ZM18.5,1H5.5C2.468,1,0,3.467,0,6.5v11c0,3.033,2.468,5.5,5.5,5.5h3c1.972-.034,1.971-2.967,0-3h-3c-1.379,0-2.5-1.122-2.5-2.5V6.5c0-1.378,1.121-2.5,2.5-2.5h13c1.379,0,2.5,1.122,2.5,2.5v2c.034,1.972,2.967,1.971,3,0v-2c0-3.033-2.468-5.5-5.5-5.5Zm-5.205,18.481c-.813,.813-1.269,1.915-1.269,3.064,.044,.422-.21,1.464,.5,1.455,1.446,.094,2.986-.171,4.019-1.269l6.715-6.715c2.194-2.202-.9-5.469-3.157-3.343l-6.808,6.808Z"/></svg>
						</div>
						<span class="link_name">{{ get_phrase('Subscriptions') }}</span>
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
					<li><a class="{{ (request()->is('superadmin/subscription/report*')) ? 'active' : '' }}" href="{{ route('superadmin.subscription.report') }}"><span>{{ get_phrase('Subscription Report') }}</span></a></li>
					<li><a class="{{ (request()->is('superadmin/subscription/pending*')) ? 'active' : '' }}" href="{{ route('superadmin.subscription.pending') }}"><span>{{ get_phrase('Pending Request') }}</span></a></li>
					<li><a class="{{ (request()->is('superadmin/subscription/approve*')) ? 'active' : '' }}" href="{{ route('superadmin.subscription.approve') }}"><span>{{ get_phrase('Confirmed Payment') }}</span></a></li>
				</ul>
			</li>


			<li class="nav-links-li {{ request()->is('superadmin/addon/list*') ? 'showMenu':'' }}">
				<div class="iocn-link">
					<a class="{{ (request()->is('superadmin/addon/list*')) ? 'active' : '' }}" href="{{ route('superadmin.addon.list') }}">
						<div class="sidebar_icon">
							<svg xmlns="http://www.w3.org/2000/svg" id="Isolation_Mode" data-name="Isolation Mode" viewBox="0 0 24 24" width="48" height="48"><polygon points="20 4 20 0 17 0 17 4 13 4 13 7 17 7 17 11 20 11 20 7 24 7 24 4 20 4"/><path d="M0,3v8H11V0H3A3,3,0,0,0,0,3ZM3,3H8V8H3Z"/><path d="M0,21a3,3,0,0,0,3,3h8V13H0Zm3-5H8v5H3Z"/><path d="M13,24h8a3,3,0,0,0,3-3V13H13Zm3-8h5v5H16Z"/></svg>
						</div>
						<span class="link_name">{{ get_phrase('Addons') }}</span>
					</a>
				</div>
			</li>

			<li class="nav-links-li {{ request()->is('superadmin/settings/system*') || request()->is('superadmin/payment/settings*') || request()->is('superadmin/settings/smtp*') || request()->is('superadmin/settings/about*') ? 'showMenu':'' }}">
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
						<span class="link_name">{{ get_phrase('Settings') }}</span>
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
					<li><a class="{{ (request()->is('superadmin/settings/system*')) ? 'active' : '' }}" href="{{ route('superadmin.system_settings') }}"><span>{{ get_phrase('System Settings') }}</span></a></li>
					<li><a class="{{ (request()->is('superadmin/payment/settings*')) ? 'active' : '' }}" href="{{ route('superadmin.payment_settings') }}"><span>{{ get_phrase('Payment Settings') }}</span></a></li>
					<li><a class="{{ (request()->is('superadmin/settings/smtp*')) ? 'active' : '' }}" href="{{ route('superadmin.smtp_settings') }}"><span>{{ get_phrase('Smtp settings') }}</span></a></li>
					<li><a class="{{ (request()->is('superadmin/settings/about*')) ? 'active' : '' }}" href="{{ route('superadmin.about') }}"><span>{{ get_phrase('About') }}</span></a></li>
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
                          <span class="user-title">{{ get_phrase('Superadmin') }}</span>
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
                              <span class="user-title">{{ get_phrase('Superadmin') }}</span>
                            </div>
                          </button>
                        </li>
                        <li>
                          <a class="dropdown-item" href="{{ route('superadmin.profile') }}">
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
                          <a class="dropdown-item" href="{{route('superadmin.password', ['edit'])}}">
                            <span>
                              <svg id="Layer_1" width="13.275" height="14.944" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1"><path d="m6.5 16a1.5 1.5 0 1 1 -1.5 1.5 1.5 1.5 0 0 1 1.5-1.5zm3 7.861a7.939 7.939 0 0 0 6.065-5.261 7.8 7.8 0 0 0 .32-3.85l.681-.689a1.5 1.5 0 0 0 .434-1.061v-2h.5a2.5 2.5 0 0 0 2.5-2.5v-.5h1.251a2.512 2.512 0 0 0 2.307-1.52 5.323 5.323 0 0 0 .416-2.635 4.317 4.317 0 0 0 -4.345-3.845 5.467 5.467 0 0 0 -3.891 1.612l-6.5 6.5a7.776 7.776 0 0 0 -3.84.326 8 8 0 0 0 2.627 15.562 8.131 8.131 0 0 0 1.475-.139zm-.185-12.661a1.5 1.5 0 0 0 1.463-.385l7.081-7.08a2.487 2.487 0 0 1 1.77-.735 1.342 1.342 0 0 1 1.36 1.149 2.2 2.2 0 0 1 -.08.851h-1.409a2.5 2.5 0 0 0 -2.5 2.5v.5h-.5a2.5 2.5 0 0 0 -2.5 2.5v1.884l-.822.831a1.5 1.5 0 0 0 -.378 1.459 4.923 4.923 0 0 1 -.074 2.955 5 5 0 1 1 -6.36-6.352 4.9 4.9 0 0 1 1.592-.268 5.053 5.053 0 0 1 1.357.191z"/></svg>
                            </span>
                            {{ get_phrase('Change Password') }}
                          </a>
                        </li>

                        <li>
                          <a class="dropdown-item" href="{{route('superadmin.system_settings')}}">
                            <span>
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" width="13.275" height="14.944">
                                <g>
                                  <path d="M256,162.923c-51.405,0-93.077,41.672-93.077,93.077s41.672,93.077,93.077,93.077s93.077-41.672,93.077-93.077   C349.019,204.619,307.381,162.981,256,162.923z M256,285.077c-16.059,0-29.077-13.018-29.077-29.077s13.018-29.077,29.077-29.077   c16.059,0,29.077,13.018,29.077,29.077l0,0C285.066,272.054,272.054,285.066,256,285.077z"/>
                                  <path d="M469.333,256c-0.032-32.689-7.633-64.927-22.208-94.187l10.965-7.616c14.496-10.104,18.058-30.046,7.957-44.544l0,0   c-10.104-14.496-30.046-18.058-44.544-7.957l-10.987,7.637c-32.574-34.38-75.691-56.904-122.517-64V32c0-17.673-14.327-32-32-32   l0,0c-17.673,0-32,14.327-32,32v13.333c-46.826,7.096-89.944,29.62-122.517,64l-10.987-7.637   c-14.498-10.101-34.44-6.538-44.544,7.957l0,0c-10.101,14.498-6.538,34.44,7.957,44.544l10.965,7.616   c-29.61,59.3-29.61,129.073,0,188.373l-10.965,7.616c-14.496,10.104-18.058,30.046-7.957,44.544l0,0   c10.104,14.496,30.046,18.058,44.544,7.957l10.987-7.637c32.574,34.38,75.691,56.904,122.517,64V480c0,17.673,14.327,32,32,32l0,0   c17.673,0,32-14.327,32-32v-13.333c46.826-7.096,89.944-29.62,122.517-64l10.987,7.637c14.498,10.101,34.44,6.538,44.544-7.957l0,0   c10.101-14.498,6.538-34.44-7.957-44.544l-10.965-7.616C461.7,320.927,469.301,288.689,469.333,256z M256,405.333   c-82.475,0-149.333-66.859-149.333-149.333S173.525,106.667,256,106.667S405.333,173.525,405.333,256   C405.228,338.431,338.431,405.228,256,405.333z"/>
                                </g>
                                </svg>
                            </span>
                            {{ get_phrase('Settings') }}
                          </a>
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
    <script src="{{ asset('public/assets/js/custom.js') }}"></script>

    <!--Toaster Script-->
    <script src="{{ asset('public/assets/js/toastr.min.js') }}"></script>

    <!--pdf Script-->
    <script src="{{ asset('public/assets/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/html2pdf.bundle.min.js') }}"></script>

    <!--html2canvas Script-->
    <script src="{{ asset('public/assets/js/html2canvas.min.js') }}"></script>


    <!--Toaster Script-->
    <script src="{{ asset('public/assets/js/toastr.min.js') }}"></script>

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
