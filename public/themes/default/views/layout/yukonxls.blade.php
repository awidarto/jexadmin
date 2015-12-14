<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>{{ Config::get('site.name') }}</title>
        <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <!-- favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">

        <!-- bootstrap framework -->
        <link href="{{ URL::to('yukon')}}/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">

        <!-- icon sets -->
            <!-- elegant icons -->
                <link href="{{ URL::to('yukon')}}/assets/icons/elegant/style.css" rel="stylesheet" media="screen">
            <!-- elusive icons -->
                <link href="{{ URL::to('yukon')}}/assets/icons/elusive/css/elusive-webfont.css" rel="stylesheet" media="screen">

        @include('layout.css')

        {{ HTML::style('css/typography.css')}}

        <!-- google webfonts -->
        {{--
        <link href='http://fonts.googleapis.com/css?family=Open+Sans&amp;subset=latin,latin-ext' rel='stylesheet' type='text/css'>
        --}}

        <!-- main stylesheet -->
        <link href="{{ URL::to('yukon')}}/assets/css/main.min.css" rel="stylesheet" media="screen" id="mainCss">

        <!-- jQuery -->
        <script src="{{ URL::to('yukon')}}/assets/js/jquery.min.js"></script>
        <script src="{{ URL::to('makeadmin')}}/assets/global/plugins/jquery-ui/jquery-ui-1.11.2.min.js"></script>

        <!-- moment.js (date library) -->
        <script src="{{ URL::to('yukon')}}/assets/js/moment-with-langs.min.js"></script>

        <script type="text/javascript">
          var base = '{{ URL::to('/') }}/';
        </script>

        <style type="text/css">
            body{
                background-color: white !important;
            }

        </style>

    </head>
    <body>
        @yield('content')

        {{--
        <!-- Bootstrap Framework -->
        <script src="{{ URL::to('yukon')}}/assets/bootstrap/js/bootstrap.min.js"></script>
        <!-- retina images -->
        <script src="{{ URL::to('yukon')}}/assets/js/retina.min.js"></script>
        <!-- match height -->
        <script src="{{ URL::to('yukon')}}/assets/lib/jquery-match-height/jquery.matchHeight-min.js"></script>
        <!-- scrollbar -->
        <script src="{{ URL::to('yukon')}}/assets/lib/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js"></script>

        <!-- Yukon Admin functions -->
        <script src="{{ URL::to('yukon')}}/assets/js/yukon_all.min.js"></script>

        <!-- page specific plugins -->

            <script>
                $(function() {
                    // footable
                    //yukon_datatables.p_plugins_tables_datatable();
                })
            </script>

        @include('layout.js')
        --}}

    </body>
</html>
