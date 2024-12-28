<header class="header">
    <div class="header-top">
        <div class="container">
            <div class="header-left">
                <div class="header-dropdown">
                    <a href="#">BDT</a>
                    <div class="header-menu">
                        <ul>
                            <li><a href="#">BDT</a></li>
                            <!-- <li><a href="#">Usd</a></li> -->
                        </ul>
                    </div>
                </div>

                <div class="header-dropdown">
                    <a href="#">BAN</a>
                    <div class="header-menu">
                        <ul>
                            <li><a href="#">Bangla</a></li>
                            <!-- <li><a href="#">English</a></li> -->
                        </ul>
                    </div>
                </div>
            </div>

            <div class="header-right">
                <ul class="top-menu">
                    <li>
                        <a href="#">Links</a>
                        <ul>
               
                            <li>
                                <a href="">About Us</a>
                            </li>
                            <li>
                                <a href="{{ url('contact') }}">Contact Us</a>
                            </li>
                           

                   
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="header-middle sticky-header">
        <div class="container">
            <div class="header-left">
                <button class="mobile-menu-toggler">
                    <span class="sr-only">Toggle mobile menu</span>
                    <i class="icon-bars"></i>
                </button>

                <a href="{{ url('') }}" class="logo">
                    <img src="" alt="" width="105" height="25">
                </a>

                <nav class="main-nav">
                    <ul class="menu sf-arrows">
                        <li class="megamenu-container active">
                            <a href="{{ url('') }}">Home</a>
                        </li>

                        <!-- Shop -->
                        <li>
                            <a href="javaScript:;" class="sf-with-ul">Shop</a>

                            <div class="megamenu megamenu-md">
                                <div class="row no-gutters">
                                    <div class="col-md-12">
                                        <div class="menu-col">
                                            <div class="row">
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        
                    </ul>
                </nav>
            </div>

            <div class="header-right">
                <div class="header-search">
                    <a href="#" class="search-toggle" role="button" title="Search"><i class="icon-search"></i></a>
                    <form action="" method="get">
                        <div class="header-search-wrapper">
                            <label for="q" class="sr-only">Search</label>
                            <input type="search" class="form-control" name="q" id="q" placeholder="Search in..." value="{{ !empty(Request::get('q')) ? Request::get('q') : '' }}" required>
                        </div>
                    </form>
                </div>

                <div class="dropdown cart-dropdown">
                    

           
                </div>
            </div>
        </div>
    </div>
</header>