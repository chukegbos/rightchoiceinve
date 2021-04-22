<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $setting->sitename }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  </head>

  <body class="hold-transition sidebar-mini">
    <div class="wrapper" id="app">
      <!-- Navbar -->
      <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars" style="color: green;"></i></a>
          </li>

          <li class="nav-item">
            <a class="nav-link" style="color: green !important;" href="#">{{ $setting->sitename }}</a>
          </li>
        </ul>

        <ul class="navbar-nav ml-auto">
        </ul>
      </nav>
      <!-- /.navbar -->

      <!-- Main Sidebar Container -->
      <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="{{ url('/') }}" class="brand-link" style="background: white">
          <img src="{{ asset('img/logo/logo.jpg') }}" class="brand-image img-circle">
          <span class="brand-text" style="color: black; font-weight: bolder;"> Right Choice </span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
          <!-- Sidebar user panel (optional) -->
          <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
              <h5>{{ Auth::user()->name }}</h5>
              <h6>{{ $my_store->name }}</h6>
            </div>
          </div>

          <!-- Sidebar Menu -->
          <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            
              <li class="nav-item">
                  <router-link to="/home" class="nav-link">
                    <i class="nav-icon fa fa-th"></i>
                    <p>Dashboard</p>
                  </router-link>
              </li>

              @if(Auth::user()->role=='Admin')
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                          <i class="nav-icon fa fa-user"></i>
                          <p>
                            Customers
                            <i class="right fa fa-angle-left"></i>
                          </p>
                        </a>
                        <ul class="nav nav-treeview">
                          <li class="nav-item">
                            <router-link to="/admin/customers" class="nav-link ml-3">
                              List of Customers
                            </router-link>
                          </li>

                     
                          <li class="nav-item">
                            <router-link to="/admin/customers/fund" class="nav-link ml-3">
                              Fund Customer's Account
                            </router-link>
                          </li>

                        </ul>
                    </li>

                    <li class="nav-item">
                      <router-link to="/outlets" class="nav-link">
                        <i class="nav-icon fa fa-home"></i>
                        <p>Outlets</p>
                      </router-link>
                    </li>

               
                    <li class="nav-item">
                      <router-link to="/admin/staff" class="nav-link">
                        <i class="nav-icon fa fa-users"></i>
                        <p>Staff</p>
                      </router-link>
                    </li>

                    <li class="nav-item has-treeview">
                      <a href="#" class="nav-link">
                        <i class="nav-icon fa fa-product-hunt"></i>
                        <p>
                          Inventory
                          <i class="right fa fa-angle-left"></i>
                        </p>
                      </a>
                      <ul class="nav nav-treeview">
                        <li class="nav-item">
                          <router-link to="/admin/inventory/category" class="nav-link ml-3">
                            Item Category
                          </router-link>
                        </li>

                       <li class="nav-item">
                          <router-link to="/admin/inventory" class="nav-link ml-3">
                            Items List
                          </router-link>
                        </li>

                        <li class="nav-item">
                            <router-link to="/movements/outlets" class="nav-link ml-3">
                             Outlet Movements
                            </router-link>
                          </li>

                          <!--<li class="nav-item">
                            <router-link to="/movements/intra-outlets" class="nav-link ml-3">
                             Intra Outlet Movements
                            </router-link>
                          </li>-->
                      </ul>
                    </li>
                    
                    
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                          <i class="nav-icon fa fa-truck"></i>
                          <p>
                            Item Purchase
                            <i class="right fa fa-angle-left"></i>
                          </p>
                        </a>
                        <ul class="nav nav-treeview">
                          <li class="nav-item">
                            <router-link to="/suppliers" class="nav-link ml-3">
                             Suppliers
                            </router-link>
                          </li>

                          <li class="nav-item">
                            <router-link to="/admin/purchases" class="nav-link ml-3">
                             Purchase Material
                            </router-link>
                          </li>

                          
                        </ul>
                    </li>
                    
                    <li class="nav-item has-treeview">
                      <a href="#" class="nav-link">
                        <i class="nav-icon fa fa-shopping-cart"></i>
                        <p>
                          Transactions
                          <i class="right fa fa-angle-left"></i>
                        </p>
                      </a>
                      <ul class="nav nav-treeview">
                        <li class="nav-item">
                          <router-link to="/sale/shopping-cart" class="nav-link ml-3">
                            Generate Invoice
                          </router-link>
                        </li>

                        <li class="nav-item">
                          <router-link to="/sale/quote" class="nav-link ml-3">
                            All Quotes
                          </router-link>
                        </li>
                        <li class="nav-item">
                          <router-link to="/sale/invoice" class="nav-link ml-3">
                            All Invoices
                          </router-link>
                        </li>

                        

                        <li class="nav-item">
                          <router-link to="/sale/orders" class="nav-link ml-3">
                            All Transactions
                          </router-link>
                        </li>
                      </ul>
                    </li>
                   
                    <li class="nav-item has-treeview">
                      <a href="#" class="nav-link">
                        <i class="nav-icon fa fa-book"></i>
                        <p>
                          Accounts Management
                          <i class="right fas fa-angle-left"></i>
                        </p>
                      </a>
                      <ul class="nav nav-treeview">
                        <li class="nav-item">
                          <router-link to="/account/type" class="nav-link">
                            <p class="ml-2">Accounts Type</p>
                          </router-link>
                        </li>

                        <!--<li class="nav-item">
                          <router-link to="/account/tax" class="nav-link">
                            <p class="ml-2">Tax Line</p>
                          </router-link>
                        </li>-->

                        <li class="nav-item">
                          <router-link to="/account/chart-of-account" class="nav-link">
                            <p class="ml-2">Chart of Accounts</p>
                          </router-link>
                        </li>

                        <li class="nav-item">
                          <router-link to="/account/journal" class="nav-link">
                            <p class="ml-2">Journal/Transactions</p>
                          </router-link>
                        </li>

                        <li class="nav-item">
                          <router-link to="/account/ledger" class="nav-link">
                            <p class="ml-2">Ledger</p>
                          </router-link>
                        </li>

                         <!--<li class="nav-item">
                          <router-link to="/account/company-fund" class="nav-link">
                            <p class="ml-2">Inject Capital</p>
                          </router-link>
                        </li>
                       
                        <li class="nav-item">
                          <router-link to="/account/trial-balance" class="nav-link">
                            <p class="ml-2">Trial Balance</p>
                          </router-link>
                        </li>-->

                        <li class="nav-item">
                          <router-link to="/account/reports" class="nav-link">
                           
                            <p class="ml-2">Reports</p>
                          </router-link>
                        </li>
                       
                          
                      
                      </ul>
                    </li> 
                
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                          <i class="nav-icon fa fa-cogs"></i>
                          <p>
                            Administration
                            <i class="right fa fa-angle-left"></i>
                          </p>
                        </a>
                        <ul class="nav nav-treeview">
                          @if(Auth::user()->role=='Admin')
                          <li class="nav-item">
                            <router-link to="/admin/setting" class="nav-link ml-3">
                              Site Settings
                            </router-link>
                          </li>
                          @endif
                          <li class="nav-item">
                            <router-link to="/password" class="nav-link ml-3">
                             Change Password
                            </router-link>
                          </li>

                          <!--<li class="nav-item">
                            <router-link to="/pin" class="nav-link ml-3">
                             Change PIN
                            </router-link>
                          </li>-->

                        </ul>
                    </li> 
              @elseif(Auth::user()->role=='Accounting')
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                          <i class="nav-icon fa fa-user"></i>
                          <p>
                            Customers
                            <i class="right fa fa-angle-left"></i>
                          </p>
                        </a>
                        <ul class="nav nav-treeview">
                          <li class="nav-item">
                            <router-link to="/admin/customers" class="nav-link ml-3">
                              List of Customers
                            </router-link>
                          </li>

                     
                          <li class="nav-item">
                            <router-link to="/admin/customers/fund" class="nav-link ml-3">
                              Fund Customer's Account
                            </router-link>
                          </li>

                        </ul>
                    </li>

                    <li class="nav-item">
                      <router-link to="/outlets" class="nav-link">
                        <i class="nav-icon fa fa-home"></i>
                        <p>Outlets</p>
                      </router-link>
                    </li>

                    <li class="nav-item has-treeview">
                      <a href="#" class="nav-link">
                        <i class="nav-icon fa fa-product-hunt"></i>
                        <p>
                          Inventory
                          <i class="right fa fa-angle-left"></i>
                        </p>
                      </a>
                      <ul class="nav nav-treeview">
                        <li class="nav-item">
                          <router-link to="/outlets/{{ $my_store->code }}" class="nav-link ml-3">
                            Store
                          </router-link>
                        </li>

                       <li class="nav-item">
                          <router-link to="/rooms/{{ $my_store->code }}" class="nav-link ml-3">
                            Show Room
                          </router-link>
                        </li>

                        
                      </ul>
                    </li>
                    
                    
                    <li class="nav-item has-treeview">
                      <a href="#" class="nav-link">
                        <i class="nav-icon fa fa-shopping-cart"></i>
                        <p>
                          Transactions
                          <i class="right fa fa-angle-left"></i>
                        </p>
                      </a>
                      <ul class="nav nav-treeview">
                        <li class="nav-item">
                          <router-link to="/sale/shopping-cart" class="nav-link ml-3">
                            Generate Invoice
                          </router-link>
                        </li>

                        <li class="nav-item">
                          <router-link to="/sale/invoice" class="nav-link ml-3">
                            All Invoices
                          </router-link>
                        </li>

                        <li class="nav-item">
                          <router-link to="/sale/quote" class="nav-link ml-3">
                            All Quotes
                          </router-link>
                        </li>

                        <li class="nav-item">
                          <router-link to="/sale/orders" class="nav-link ml-3">
                            All Transactions
                          </router-link>
                        </li>
                      </ul>
                    </li>
                   
                    <li class="nav-item has-treeview">
                      <a href="#" class="nav-link">
                        <i class="nav-icon fa fa-book"></i>
                        <p>
                          Accounts Management
                          <i class="right fas fa-angle-left"></i>
                        </p>
                      </a>
                      <ul class="nav nav-treeview">
                        <li class="nav-item">
                          <router-link to="/account/type" class="nav-link">
                            <p class="ml-2">Accounts Type</p>
                          </router-link>
                        </li>

                        <!--<li class="nav-item">
                          <router-link to="/account/tax" class="nav-link">
                            <p class="ml-2">Tax Line</p>
                          </router-link>
                        </li>-->

                        <li class="nav-item">
                          <router-link to="/account/chart-of-account" class="nav-link">
                            <p class="ml-2">Chart of Accounts</p>
                          </router-link>
                        </li>

                        <li class="nav-item">
                          <router-link to="/account/journal" class="nav-link">
                            <p class="ml-2">Journal/Transactions</p>
                          </router-link>
                        </li>

                        <li class="nav-item">
                          <router-link to="/account/ledger" class="nav-link">
                            <p class="ml-2">Ledger</p>
                          </router-link>
                        </li>

                         <!--<li class="nav-item">
                          <router-link to="/account/company-fund" class="nav-link">
                            <p class="ml-2">Inject Capital</p>
                          </router-link>
                        </li>
                       
                        <li class="nav-item">
                          <router-link to="/account/trial-balance" class="nav-link">
                            <p class="ml-2">Trial Balance</p>
                          </router-link>
                        </li>-->

                        <li class="nav-item">
                          <router-link to="/account/reports" class="nav-link">
                           
                            <p class="ml-2">Reports</p>
                          </router-link>
                        </li>
                       
                          
                      
                      </ul>
                    </li> 
                
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                          <i class="nav-icon fa fa-cogs"></i>
                          <p>
                            Administration
                            <i class="right fa fa-angle-left"></i>
                          </p>
                        </a>
                        <ul class="nav nav-treeview">
                          @if(Auth::user()->role=='Admin')
                          <li class="nav-item">
                            <router-link to="/admin/setting" class="nav-link ml-3">
                              Site Settings
                            </router-link>
                          </li>
                          @endif
                          <li class="nav-item">
                            <router-link to="/password" class="nav-link ml-3">
                             Change Password
                            </router-link>
                          </li>

                          <!--<li class="nav-item">
                            <router-link to="/pin" class="nav-link ml-3">
                             Change PIN
                            </router-link>
                          </li>-->

                        </ul>
                    </li> 
              @elseif(Auth::user()->role=='Marketing')
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                      <i class="nav-icon fa fa-user"></i>
                      <p>
                        Customers
                        <i class="right fa fa-angle-left"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      <li class="nav-item">
                        <router-link to="/admin/customers" class="nav-link ml-3">
                          List of Customers
                        </router-link>
                      </li>

                 
                      <li class="nav-item">
                        <router-link to="/admin/customers/fund" class="nav-link ml-3">
                          Fund Customer's Account
                        </router-link>
                      </li>

                    </ul>
                </li>

                <li class="nav-item has-treeview">
                  <a href="#" class="nav-link">
                    <i class="nav-icon fa fa-product-hunt"></i>
                    <p>
                      Inventory
                      <i class="right fa fa-angle-left"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <router-link to="/outlets/{{ $my_store->code }}" class="nav-link ml-3">
                        Store
                      </router-link>
                    </li>

                   <li class="nav-item">
                      <router-link to="/rooms/{{ $my_store->code }}" class="nav-link ml-3">
                        Show Room
                      </router-link>
                    </li>

                    
                  </ul>
                </li>
                
                <li class="nav-item has-treeview">
                  <a href="#" class="nav-link">
                    <i class="nav-icon fa fa-shopping-cart"></i>
                    <p>
                      Transactions
                      <i class="right fa fa-angle-left"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <router-link to="/sale/shopping-cart" class="nav-link ml-3">
                        Generate Invoice
                      </router-link>
                    </li>

                    <li class="nav-item">
                      <router-link to="/sale/invoice" class="nav-link ml-3">
                        All Invoices
                      </router-link>
                    </li>

                    <li class="nav-item">
                      <router-link to="/sale/quote" class="nav-link ml-3">
                        All Quotes
                      </router-link>
                    </li>

                    <li class="nav-item">
                      <router-link to="/sale/orders" class="nav-link ml-3">
                        All Transactions
                      </router-link>
                    </li>
                  </ul>
                </li>
            
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                      <i class="nav-icon fa fa-cogs"></i>
                      <p>
                        Administration
                        <i class="right fa fa-angle-left"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      @if(Auth::user()->role=='Admin')
                      <li class="nav-item">
                        <router-link to="/admin/setting" class="nav-link ml-3">
                          Site Settings
                        </router-link>
                      </li>
                      @endif
                      <li class="nav-item">
                        <router-link to="/password" class="nav-link ml-3">
                         Change Password
                        </router-link>
                      </li>

                      <!--<li class="nav-item">
                        <router-link to="/pin" class="nav-link ml-3">
                         Change PIN
                        </router-link>
                      </li>-->

                    </ul>
                </li> 
              @endif


              <li class="nav-item">
                <a class="nav-link" href="{{ route('logout') }}"
                  onclick="event.preventDefault();
                  document.getElementById('logout-form').submit();">
                  <i class="nav-icon fa fa-power-off text-red"></i>
                  <p>{{ __('Logout') }}</p>
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
              </li>
            </ul>
          </nav>

        </div>
      </aside>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <router-view></router-view>
        <vue-progress-bar></vue-progress-bar>
      </div>
 
      <footer class="main-footer">
        <strong>Copyright &copy; {{ date('Y') }} <a href="{{ url('/') }}">{{ $setting->sitename }}</a>.</strong> 
      </footer>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <style type="text/css">
      body{
        font-size: 13px !important;
      }
    </style>
  </body>
</html>
