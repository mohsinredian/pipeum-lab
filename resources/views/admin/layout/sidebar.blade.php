<!-- Main Sidebar Container -->
<!-- <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <
    <a href="/admin/dashboard" class="brand-link">
        {{-- <img src="{{asset('theme/dist/img/logo.png')}}" alt="BSES" class="brand-image img-circle elevation-3"
            style="opacity: .8; width:100%;"> --}}
        <img src="{{ asset('theme/dist/img/logo.png') }}" alt="BSES" class="brand-image img-circle2"
            style="opacity: .8">
        &nbsp;&nbsp;

    </a>

  
    <div class="sidebar">

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @can('dashboard')
                <li class="nav-item">
                    <a href="/admin/dashboard"
                        class="nav-link @if (Session::get('active') == 'dashboard') active @endif">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                @endcan

               
                <li class="nav-item @if (Session::get('active') == 'divisions' ||
                            Session::get('active') == 'locations' ||
                            Session::get('active') == 'circles' ||
                            Session::get('active') == 'department' ||
                            Session::get('active') == 'vendors' ||
                            Session::get('active') == 'assets' ||
                            Session::get('active') == 'brand')  @endif">
                    @can('create_division')
                     <a href="#" class="nav-link @if (Session::get('active') == 'divisions' ||
                                    Session::get('active') == 'locations' ||
                                    Session::get('active') == 'circles' ||
                                    Session::get('active') == 'department' ||
                                    Session::get('active') == 'vendors' ||
                                    Session::get('active') == 'assets' ||
                                    Session::get('active') == 'brand') @endif">
                        <i class="nav-icon fas fa-list"></i>
                       
                        <p>
                            Master
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    @endcan
                    <ul class="nav nav-treeview">
                </li>
             
                @can('create_division')
                <li class="nav-item">
                    <a href="/admin/company" class="nav-link @if (Session::get('active') == 'company') active @endif">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Company</p>
                    </a>
                </li>
                @endcan
                @can('create_location')
                <li class="nav-item">
                    <a href="/admin/locations"
                        class="nav-link @if (Session::get('active') == 'locations') active @endif">
                        <i class="far fa-circle nav-icon"></i>
                        <p>
                            Location
                        </p>
                    </a>
                </li>
                @endcan
                @can('create_department')
                <li class="nav-item">
                    <a href="/admin/service" class="nav-link @if (Session::get('active') == 'service') active @endif">
                        <i class="far fa-circle nav-icon"></i>
                        <p>
                            Services
                        </p>
                    </a>
                </li>
                @endcan
                @can('create_nv_department')
                <li class="nav-item">
                    <a href="/admin/department"
                        class="nav-link @if (Session::get('active') == 'department')  @endif">
                        <i class="far fa-circle nav-icon"></i>
                        <p>
                            Department
                        </p>
                    </a>
                </li>
                @endcan

                @can('supdepartment_list')
                <li class="nav-item">
                    <a href="/admin/supdepartment"
                        class="nav-link @if (Session::get('active') == 'supdepartment')  @endif">
                        <i class="far fa-circle nav-icon"></i>
                        <p>
                            Super Department
                        </p>
                    </a>
                </li>
                @endcan

                @can('create_capex_master')
                <li class="nav-item">
                    <a href="/admin/capexmaster"
                        class="nav-link @if (Session::get('active') == 'capexmaster')  @endif">
                        <i class="far fa-circle nav-icon"></i>
                        <p>
                            CAPEX
                        </p>
                    </a>
                </li>
                @endcan
                
                @can('create_capex_master')
                <li class="nav-item">
                    <a href="/admin/opex"
                        class="nav-link @if (Session::get('active') == 'opex')  @endif">
                        <i class="far fa-circle nav-icon"></i>
                        <p>
                            OPEX
                        </p>
                    </a>
                </li>
                @endcan

                @can('create_serviceboq')
                <li class="nav-item">
                    <a href="/admin/serviceboq"
                        class="nav-link @if (Session::get('active') == 'serviceboq_list')  @endif">
                        <i class="far fa-circle nav-icon"></i>
                        <p>
                            Service BOQ
                        </p>
                    </a>
                </li>
                @endcan

                @can('create_materialboq')
                <li class="nav-item">
                    <a href="/admin/materialboq"
                        class="nav-link @if (Session::get('active') == 'materialboq_list')  @endif">
                        <i class="far fa-circle nav-icon"></i>
                        <p>
                            Material BOQ
                        </p>
                    </a>
                </li>
                @endcan

                @can('create_role')
                <li class="nav-item">
                    <a href="/admin/roles" class="nav-link @if (Session::get('active') == 'roles') active @endif">
                        <i class="far fa-circle nav-icon"></i>
                        <p>
                            Roles
                        </p>
                    </a>
                </li>
                @endcan

                @can('workFlow')
                <li class="nav-item">
                    <a href="/admin/Workflow" class="nav-link @if (Session::get('active') == 'workflow') active @endif">
                        <i class="far fa-circle nav-icon"></i>
                        <p>
                            NV Workflow
                        </p>
                    </a>
                </li>
                @endcan
                </li>
            </ul>
            @can('create_employee')
            <li class="nav-item">
                <a href="/admin/employees" class="nav-link @if (Session::get('active') == 'employees') active @endif">
                    <i class="nav-icon fas fa-users"></i>
                    <p>
                        Manage Employee
                    </p>
                </a>
            </li>
            @endcan
            @can('create_NV')
            <li class="nav-item">
                <a href="/admin/needvalidation/list"
                    class="nav-link @if (Session::get('active') == 'need_v') active @endif">
                    <i class="nav-icon fas fa-folder"></i>
                    @php
                    $user= \Auth::user();

$role_id= $user->role_id;                

@endphp
@if($role_id =='9' || $role_id == '1')
                    <p>
                        Create Need Validation
                    </p>
                    @else
                    <p>
                     Need Validation List
                    </p>
                    @endif
                </a>
            </li>
            @endcan
            @can('create_employee')
            <li class="nav-item">

                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-list"></i>
                    <p>Report<i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Summary</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>
                        Pendency
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link ">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Budget Utilization</p>
                </a>
            </li>
            @endcan
            </li>
            </ul>

            </ul>
        </nav>
        
    </div>
  
</aside> -->