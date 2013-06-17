<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="<?php echo url(URLParams::$mainpath)?>">Programmes Plant</a>
            <div class="nav-collapse collapse">

            <?php if (URLParams::$no_header_links): ?>

                    <?php //Display no navigation (for now). The inbox should display everything accross all years and programme types?>
            
            <?php //elseif (URI::segment(1) == 'globalsettings'): ?>

            <?php else: ?>
                <?php if (URLParams::$year_header_links): ?>

                    <ul class="nav">
                        <?php for ($year=URLParams::$current_year; $year<=(URLParams::$current_year+2); $year++): ?>
                        <li class="<?php echo ( URLParams::$year == $year ? 'active' : false ); ?>"><a href="<?php echo url(URLParams::get_header_path_prefix(array('year' => $year)) . URLParams::strip_year_and_type_from_url()); ?>"><?php echo $year; ?></a></li>
                        <?php endfor; ?>
                    </ul>

                <?php endif;?>

                <?php if (URLParams::$type_header_links): ?>

                    <ul class="nav">
                        <li class="<?php echo ( URLParams::$type == 'ug' ? 'active' : false )?>"><a href="<?php echo url(URLParams::get_header_path_prefix(array('type' => 'ug')) . URLParams::strip_year_and_type_from_url())?>">Undergraduate</a></li>
                        <li class="<?php echo ( URLParams::$type == 'pg' ? 'active' : false )?>"><a href="<?php echo url(URLParams::get_header_path_prefix(array('type' => 'pg')) . URLParams::strip_year_and_type_from_url())?>">Postgraduate</a></li>
                    </ul>

                <?php endif;?>

            <?php endif; ?>

                <ul class="nav pull-right">
                    <li><a href="#"><?php echo Auth::user()->username; ?></a></li>
                    <li><a href="<?php echo url('logout')?>">Logout</a></li>
                </ul>

            </div><!--/.nav-collapse -->
        </div><!--/.container -->
    </div><!--/.navbar-inner -->
</div><!--/.navbar -->