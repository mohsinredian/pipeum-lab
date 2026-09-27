<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{Config::get('app.name')}} @if(isset($page_title)) {{' | '.$page_title}} @endif</title>
  <meta name="description" content="Document Management" />
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('theme/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="{{asset('theme/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{asset('theme/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- JQVMap -->
  <!-- Theme select2-->
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{asset('theme/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">

  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">

    @stack('styles')
    <link rel="stylesheet" href="{{asset('theme/dist/css/adminlte.min.css')}}">
    <link href="{{asset('admin/css/styles.css')}}" rel="stylesheet" type="text/css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style type="text/css">
      .mandatory_input{
        color: red;
      }
      .select2-selection__choice{
        color: black !important;
        
      }
    </style>
  <link rel="stylesheet" href="{{asset('theme/plugins/select2/css/select2.min.css')}}">

  
</head>
<body class="hold-transition sidebar-mini layout-fixed" style="padding:0px">
    <div class="wrapper">
        @csrf
      <div class="pre-loader" style="display: block;"></div>
      <!-- Preloader -->
      <!--<div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="{{asset('theme/dist/img/AdminLTELogo.png')}}" alt="AdminLTELogo" height="60" width="60">
      </div>-->

      <!-- Navbar -->
      @include('admin.layout.header')
      <!-- /.navbar -->

      <!-- Main Sidebar Container -->
      @include('admin.layout.sidebar')

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        @yield('content')
        <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->
      @include('admin.layout.footer')
    </div>
   <!-- ./wrapper -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- jQuery -->
    <script src="{{asset('theme/plugins/jquery/jquery.min.js')}}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="{{asset('theme/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
    $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="{{asset('theme/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <!-- Tempusdominus Bootstrap 4 -->
    {{-- <!--<script src="{{asset('theme/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>--> --}}
    <script src="{{asset('theme/plugins/summernote/summernote.js')}}"></script>
    <!-- overlayScrollbars -->
    <script src="{{asset('theme/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7"></script>
    <script src="{{asset('theme/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    {{-- <!-- <script src="{{asset('theme/plugins/jquery-validation/additional-methods.min.js')}}"></script> ---> --}}

    <!-- AdminLTE App -->
    <script src="{{asset('theme/dist/js/adminlte.js')}}"></script>
    @stack('script')
    <script type="text/javascript">
      $(document).ready(function() {
        $('.pre-loader').hide();
      });
    </script>
    {{-- pushpendra --}}
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
  <script>
    AOS.init();
  </script>

<script>
  let mybutton = document.getElementById("btn-back-to-top");

  
// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function () {
  scrollFunction();
};

function scrollFunction() {
  if (
    document.body.scrollTop > 20 ||
    document.documentElement.scrollTop > 20
  ) {
    mybutton.style.display = "block";
  } else {
    mybutton.style.display = "none";
  }
}
// When the user clicks on the button, scroll to the top of the document
mybutton.addEventListener("click", backToTop);

function backToTop() {
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
}
</script>
<script>
     $(document).ready(function(){
        var dfilter= $('#dfilter').val();
        if(dfilter=='today'){
            openCity('View-today-ticket');
        }
        if(dfilter=='tomorrow'){
            // alert(dfilter);
            openCity('View-tomorrow-ticket');
        }
        if(dfilter=='currentMonth'){
            openCity('View-current-month-ticket');
        }
        if(dfilter=='closeTicket'){
            openCity('view-closed-ticket');
        }
    })
</script>

{{-- pushpendra --}}
<script>
   $(function() {
   var url = window.location;
   // for single sidebar menu
   $('ul.nav-sidebar a').filter(function() {
   return this.href == url;
   }).addClass('active');
  
  
  
   // for sidebar menu and treeview
   $('ul.nav-treeview a').filter(function() {
   return this.href == url;
   }).parentsUntil(".nav-sidebar > .nav-treeview")
   .css({
   'display': 'block'
   })
   .addClass('menu-open').prev('a')
   .addClass('active');
 });

 var i = 0;
$(document).ready(function() {
setInterval(function() {
$.ajax({
url: '/admin/notification',
method: 'GET',
success: function(response) {
// Handle the response from the server
console.log(response.fcount);
$('.fcount').html(response.fcount);
var drop = "";
$.each(response.fdata, function(k, v) {
var now = new Date();
var oldDate = new Date(v.created_at);
var timeDiff = now.getTime() - oldDate.getTime();
var minutesDiff = Math.floor(timeDiff / (1000 * 60));
drop = drop+"<a href='' class='dropdown-item'><i class='fas fa-envelope mr-2'></i> <span class='float-right text-muted text-sm'>"+minutesDiff+" minutes ago</span></a>";
});

// $('.drop').html(drop);

},
error: function(xhr, status, error) {
// Handle any errors that occur during the AJAX request
console.log(error);
}
});
i++;
}, 60000); // 5000 milliseconds = 5 seconds
});

  </script>

</body>
</html>