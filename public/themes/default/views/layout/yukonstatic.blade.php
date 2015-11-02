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
            <!-- flags -->
                <link rel="stylesheet" href="{{ URL::to('yukon')}}/assets/icons/flags/flags.css">
            <!-- scrollbar -->
                <link rel="stylesheet" href="{{ URL::to('yukon')}}/assets/lib/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css">

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
              .select2-container .select2-choice{
                background-color: transparent !important;
              }

              .select2-selection {
                  border-radius: 0px !important;
                  border-top-width: 0px !important;
                  border-left-width: 0px !important;
                  border-right-width: 0px !important;
              }

              .select2-container--default .select2-selection:focus{
                  border-bottom-width: 2px;
                  outline: none !important;
                  border-color: #0054a0;
              }

              .select2-selection__rendered{
                  font-size: initial !important;
                  color: #666666 !important;
                  padding-left: 0px !important;
              }

              ul.ui-autocomplete{
                z-index: 20000 !important;
                border: thin solid #DDD;
              }

              ul.ui-autocomplete li{
                background-color: #fff;
              }

              ul.ui-autocomplete li.ui-state-focus
              {
                  color:white;
                  background:#058E9D;
                  outline:none;
              }

              div.tagsinput span.tag , div.tagsinput span.tag a{
                  color:white;
                  background:#058E9D !important;
                  border-color: #058E9D !important;
              }

                .vtext{
                    -ms-writing-mode: tb-rl;
                    -webkit-writing-mode: vertical-tb;
                    -moz-writing-mode: vertical-tb;
                    -ms-writing-mode: vertical-tb;
                    writing-mode: vertical-tb;
                }


        </style>

    </head>
    <body class="top_menu_active" style="overflow:auto;">
        <div id="page_wrapper">

            <!-- header -->
            <header id="main_header">
                <div class="container-fluid">
                    <div class="brand_section">
                        <a href="dashboard.html"><img src="{{ URL::to('images/jex_top_logo.png')}}" alt="site_logo" width="63" height="26"></a>
                    </div>
                    <ul class="header_notifications clearfix">
                        {{--
                        <li class="dropdown">
                            <span class="label label-danger">8</span>
                            <a data-toggle="dropdown" href="#" class="dropdown-toggle"><i class="el-icon-envelope"></i></a>
                            <div class="dropdown-menu">
                                <ul>
                                    <li>
                                        <img src="{{ URL::to('yukon')}}/assets/img/avatars/avatar02_tn.png" alt="" width="38" height="38">
                                        <p><a href="#">Lorem ipsum dolor sit amet&hellip;</a></p>
                                        <small class="text-muted">14.07.2014</small>
                                    </li>
                                    <li>
                                        <img src="{{ URL::to('yukon')}}/assets/img/avatars/avatar03_tn.png" alt="" width="38" height="38">
                                        <p><a href="#">Lorem ipsum dolor sit&hellip;</a></p>
                                        <small class="text-muted">14.07.2014</small>
                                    </li>
                                    <li>
                                        <img src="{{ URL::to('yukon')}}/assets/img/avatars/avatar04_tn.png" alt="" width="38" height="38">
                                        <p><a href="#">Lorem ipsum dolor&hellip;</a></p>
                                        <small class="text-muted">14.07.2014</small>
                                    </li>
                                    <li>
                                        <img src="{{ URL::to('yukon')}}/assets/img/avatars/avatar05_tn.png" alt="" width="38" height="38">
                                        <p><a href="#">Lorem ipsum dolor sit amet&hellip;</a></p>
                                        <small class="text-muted">14.07.2014</small>
                                    </li>
                                    <li>
                                        <a href="#" class="btn btn-xs btn-primary btn-block">All messages</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="dropdown" id="tasks_dropdown">
                            <span class="label label-danger">14</span>
                            <a data-toggle="dropdown" href="#" class="dropdown-toggle"><i class="el-icon-tasks"></i></a>
                            <div class="dropdown-menu">
                                <ul>
                                    <li>
                                        <div class="clearfix">
                                            <div class="label label-warning pull-right">Medium</div>
                                            <small class="text-muted">YUK-21 (24.07.2014)</small>
                                        </div>
                                        <p>Lorem ipsum dolor sit amet&hellip;</p>
                                    </li>
                                    <li>
                                        <div class="clearfix">
                                            <div class="label label-danger pull-right">High</div>
                                            <small class="text-muted">YUK-8 (26.07.2014)</small>
                                        </div>
                                        <p>Lorem ipsum dolor sit amet&hellip;</p>
                                    </li>
                                    <li>
                                        <div class="clearfix">
                                            <div class="label label-success pull-right">Medium</div>
                                            <small class="text-muted">DES-14 (25.07.2014)</small>
                                        </div>
                                        <p>Lorem ipsum dolor sit amet&hellip;</p>
                                    </li>
                                    <li>
                                        <a href="#" class="btn btn-xs btn-primary btn-block">All tasks</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="dropdown">
                            <span class="label label-primary">2</span>
                            <a data-toggle="dropdown" href="#" class="dropdown-toggle"><i class="el-icon-bell"></i></a>
                            <div class="dropdown-menu">
                                <ul>
                                    <li>
                                        <p>Lorem ipsum dolor sit amet&hellip;</p>
                                        <small class="text-muted">20 minutes ago</small>
                                    </li>
                                    <li>
                                        <p>Lorem ipsum dolor sit&hellip;</p>
                                        <small class="text-muted">44 minutes ago</small>
                                    </li>
                                    <li>
                                        <p>Lorem ipsum dolor&hellip;</p>
                                        <small class="text-muted">10:55</small>
                                    </li>
                                    <li>
                                        <p>Lorem ipsum dolor sit amet&hellip;</p>
                                        <small class="text-muted">14.07.2014</small>
                                    </li>
                                    <li>
                                        <a href="#" class="btn btn-xs btn-primary btn-block">All Alerts</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        --}}
                    </ul>
                    @include('partials.yukonidentity')
                </div>
            </header>

            <!-- breadcrumbs -->
            <nav id="breadcrumbs">
              {{ Breadcrumbs::render() }}
            </nav>

            <!-- main content -->
            <div id="main_wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                          @yield('left')
                        </div>
                        <div class="col-md-6">
                          @yield('right')
                        </div>
                    </div>
                </div>
            </div>

            <!-- main menu -->

            @include('partials.yukonmenu')


        </div>

        <!-- select2 -->
        <link href="{{ URL::to('yukon')}}/assets/lib/select2/select2.css" rel="stylesheet" media="screen">
        <!-- datepicker -->
        <link href="{{ URL::to('yukon')}}/assets/lib/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" media="screen">
        <!-- date range picker -->
        <link href="{{ URL::to('yukon')}}/assets/lib/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" media="screen">

        <!-- jQuery Cookie -->
        <script src="{{ URL::to('yukon')}}/assets/js/jqueryCookie.min.js"></script>
        <!-- Bootstrap Framework -->
        <script src="{{ URL::to('yukon')}}/assets/bootstrap/js/bootstrap.min.js"></script>
        <!-- retina images -->
        <script src="{{ URL::to('yukon')}}/assets/js/retina.min.js"></script>
        <!-- switchery -->
        <script src="{{ URL::to('yukon')}}/assets/lib/switchery/dist/switchery.min.js"></script>
        <!-- typeahead -->
        <script src="{{ URL::to('yukon')}}/assets/lib/typeahead/typeahead.bundle.min.js"></script>
        <!-- fastclick -->
        <script src="{{ URL::to('yukon')}}/assets/js/fastclick.min.js"></script>
        <!-- match height -->
        <script src="{{ URL::to('yukon')}}/assets/lib/jquery-match-height/jquery.matchHeight-min.js"></script>
        <!-- scrollbar -->
        {{--
        <script src="{{ URL::to('yukon')}}/assets/lib/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js"></script>
        --}}

        <!-- Yukon Admin functions -->
        <script src="{{ URL::to('yukon')}}/assets/js/yukon_all.min.js"></script>

        <!-- page specific plugins -->
            <!-- select2 -->
            <script src="{{ URL::to('yukon')}}/assets/lib/select2/select2.min.js"></script>
            <!-- datepicker -->
            <script src="{{ URL::to('yukon')}}/assets/lib/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
            <!-- date range picker -->
            <script src="{{ URL::to('yukon')}}/assets/lib/bootstrap-daterangepicker/daterangepicker.js"></script>

            <!-- datatable -->
            <script src="{{ URL::to('yukon')}}/assets/lib/DataTables/media/js/jquery.dataTables.min.js"></script>
            <script src="{{ URL::to('yukon')}}/assets/lib/DataTables/extensions/FixedHeader/js/dataTables.fixedHeader.min.js"></script>
            <script src="{{ URL::to('yukon')}}/assets/lib/DataTables/media/js/dataTables.bootstrap.js"></script>

            <script>
                $(function() {
                    // footable
                    //yukon_datatables.p_plugins_tables_datatable();
                })
            </script>

        @include('layout.modaljs')
        @include('layout.js')
        {{--
        <!-- style switcher -->
        <div id="style_switcher">
            <a class="switcher_toggle"><i class="icon_cog"></i></a>
            <div class="style_items">
                <div class="heading_b"><span class="heading_text">Top Bar Color</span></div>
                <ul class="clearfix" id="topBar_style_switch">
                    <li class="sw_tb_style_0 style_active" title=""><span class="icon_check_alt2"></span></li>
                    <li class="sw_tb_style_1" title="topBar_style_1"><span class="icon_check_alt2"></span></li>
                    <li class="sw_tb_style_2" title="topBar_style_2"><span class="icon_check_alt2"></span></li>
                    <li class="sw_tb_style_3" title="topBar_style_3"><span class="icon_check_alt2"></span></li>
                    <li class="sw_tb_style_4" title="topBar_style_4"><span class="icon_check_alt2"></span></li>
                    <li class="sw_tb_style_5" title="topBar_style_5"><span class="icon_check_alt2"></span></li>
                    <li class="sw_tb_style_6" title="topBar_style_6"><span class="icon_check_alt2"></span></li>
                    <li class="sw_tb_style_7" title="topBar_style_7"><span class="icon_check_alt2"></span></li>
                    <li class="sw_tb_style_8" title="topBar_style_8"><span class="icon_check_alt2"></span></li>
                    <li class="sw_tb_style_9" title="topBar_style_9"><span class="icon_check_alt2"></span></li>
                    <li class="sw_tb_style_10" title="topBar_style_10"><span class="icon_check_alt2"></span></li>
                    <li class="sw_tb_style_11" title="topBar_style_11"><span class="icon_check_alt2"></span></li>
                    <li class="sw_tb_style_12" title="topBar_style_12"><span class="icon_check_alt2"></span></li>
                    <li class="sw_tb_style_13" title="topBar_style_13"><span class="icon_check_alt2"></span></li>
                    <li class="sw_tb_style_14" title="topBar_style_14"><span class="icon_check_alt2"></span></li>
                </ul>
            </div>
            <hr/>
            <div class="clearfix hidden-sm hidden-md hidden-xs sepH_b">
                <label>Fixed layout</label>
                <div class="pull-right"><input type="checkbox" id="fixed_layout_switch" class="js-switch mini-switch"></div>
            </div>
            <div class="style_items hidden-sm hidden-md hidden-xs" id="fixed_layout_bg_switch">
                <hr/>
                <div class="heading_b"><span class="heading_text">Background</span></div>
                <ul class="clearfix">
                    <li class="sw_bg_0" title="bg_0"></li>
                    <li class="sw_bg_1" title="bg_1"></li>
                    <li class="sw_bg_2" title="bg_2"></li>
                    <li class="sw_bg_3" title="bg_3"></li>
                    <li class="sw_bg_4" title="bg_4"></li>
                    <li class="sw_bg_5" title="bg_5"></li>
                    <li class="sw_bg_6" title="bg_6"></li>
                    <li class="sw_bg_7" title="bg_7"></li>
                </ul>
                <hr/>
            </div>
            <div class="clearfix sepH_b">
                <label>Top Menu</label>
                <div class="pull-right"><input type="checkbox" id="top_menu_switch" class="js-switch mini-switch"></div>
            </div>
            <div class="clearfix sepH_b">
                <label>Hide Breadcrumbs</label>
                <div class="pull-right"><input type="checkbox" id="breadcrumbs_hide" class="js-switch mini-switch"></div>
            </div>
            <div class="text-center sepH_a">
                <button data-toggle="modal" data-target="#showCSSModal" id="showCSS" class="btn btn-default btn-xs btn-outline" type="button">Show CSS</button>
            </div>
        </div>
        <div class="modal fade" id="showCSSModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title" id="myModalLabel">CSS Classes</h4>
                    </div>
                    <div class="modal-body">
                        <pre id="showCSSPre"></pre>
                    </div>
                </div>
            </div>
        </div>
        --}}
    </body>
</html>
