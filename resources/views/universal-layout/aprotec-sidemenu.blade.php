
<div class="overlay_menu px-2 px-sm-0">
    <button class="btn btn-primary btn-icon btn-icon-mini btn-round"><i class="zmdi zmdi-close"></i></button>
    <div class="container">        
        <div class="row clearfix">
            <div class="card">
                <div class="body">
                    <div class="input-group m-b-0">                
                        <input type="text" class="form-control" oninput="searchList(this, '.apps-list > li', 'a')" placeholder="Search...">
                        <span class="input-group-addon">
                            <i class="zmdi zmdi-search"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="card links">
                <div class="body">
                    <div class="row mx-1">
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            <h6>Aprotec</h6>
                            <ul class="list-unstyled apps-list">
                                <li><a href="/aprotec/dashboard">Dashboard</a></li>
                                <li><a href="/aprotec/profile">User Profile</a></li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>            
        </div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12">
                <div class="social">                 
                    <p>
                        Created by <img src="/assets/img/aprotec-old.png" alt="Aprotec Logo" style="width: 5rem;">
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="overlay"></div><!-- Overlay For Sidebars -->

<!-- Left Sidebar -->
<aside id="minileftbar" class="minileftbar">
    <ul class="menu_list">
        <li><a href="javascript:void(0);" class="btn_overlay hidden-sm-down"><i class="zmdi zmdi-search"></i></a></li>        
        <li><a href="javascript:void(0);" class="menu-sm"><i class="zmdi zmdi-swap"></i></a></li>            
        <li><a href="javascript:void(0);" class="fullscreen" data-provide="fullscreen"><i class="zmdi zmdi-fullscreen"></i></a></li>
        <li class="power">
            <a href="javascript:void(0);" class="js-right-sidebar"><i class="zmdi zmdi-settings zmdi-hc-spin"></i></a>            
            <a href="/aprotec/logout" class="mega-menu"><i class="zmdi zmdi-power"></i></a>
        </li>
    </ul>    
</aside>

<aside class="right_menu">
    
    <div id="rightsidebar" class="right-sidebar">
        <ul class="nav nav-tabs">
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#setting">Setting</a></li>
        </ul>
        <div class="tab-content slim_scroll">
            <div class="tab-pane slideRight active" id="setting">
                <div class="card">
                    <div class="header">
                        <h2><strong>Colors</strong> Skins</h2>
                    </div>
                    <div class="body">
                        <ul class="choose-skin list-unstyled m-b-0">
                            <li data-theme="black" class="active">
                                <div class="black"></div>
                            </li>
                            <li data-theme="purple">
                                <div class="purple"></div>
                            </li>                   
                            <li data-theme="blue">
                                <div class="blue"></div>
                            </li>
                            <li data-theme="cyan">
                                <div class="cyan"></div>                    
                            </li>
                            <li data-theme="green">
                                <div class="green"></div>
                            </li>
                            <li data-theme="orange">
                                <div class="orange"></div>
                            </li>
                            <li data-theme="blush">
                                <div class="blush"></div>                    
                            </li>
                        </ul>
                    </div>
                </div>             
            </div>
        </div>
    </div>
    <div id="leftsidebar" class="sidebar">
        <div class="menu">
            <ul class="list">
                <li>
                    <div class="user-info m-0">
                        <div class="image">
                            <a href="#"><img src="/assets/img/maruti-square.png" alt="User" class="w-25"></a>
                        </div>
                        <div class="detail">
                            <h6>{{ session()->get('auth_uname_uid') }}</h6>                          
                        </div>
                    </div>
                </li>
                <li class="active open"> 
                    <a href="/dashboard"><i class="zmdi zmdi-home"></i><span>Dashboard</span></a>
                </li>

                <li class="active open">
                    <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">format_paint</i>
                    <span class="m-0">Aprotec</span></a>
                    <ul class="ml-menu">
                        <li><a href="/aprotec/dashboard">Dashboard</a></li>
                        <li><a href="/aprotec/profile">User Profile</a></li>
                    </ul>
                </li>
             
            </ul>
        </div>
    </div>
</aside>