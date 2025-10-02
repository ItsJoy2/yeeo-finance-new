<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Corona Admin</title>
    @include('user.layouts.partials.__style')
    <!-- End layout styles -->
    <link rel="shortcut icon" href="assets/images/favicon.png" />
  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:partials/_sidebar.html -->
      @include('user.layouts.partials.__sidebar')
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_navbar.html -->
        @include('user.layouts.partials.__navbar')
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">

            @yield('userContent')
            
          <!-- content-wrapper ends -->
          @include('user.layouts.partials.__footer')
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
   @include('user.layouts.partials.__script')
    <!-- End custom js for this page -->
  </body>
</html>
