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
            
                <?php if (URLParams::$fields): ?>
                    <ul class="nav">
                    <li class="<?php echo ( URLParams::$type == 'ug' ? 'active' : false )?>"><a href="<?php echo url('/ug/fields/programmes')?>">Undergraduate</a></li>
                    <li class="<?php echo (  URLParams::$type == 'pg' ? 'active' : false )?>"><a href="<?php echo url('/pg/fields/programmes')?>">Postgraduate</a></li>
                </ul>

            <?php else: ?>

                
                <ul class="nav">
                    <li class="<?php echo ( URLParams::$type == 'ug' ? 'active' : false )?>"><a href="<?php echo url(URLParams::$year.'/ug/programmes')?>">Undergraduate</a></li>
                    <li class="<?php echo (  URLParams::$type == 'pg' ? 'active' : false )?>"><a href="<?php echo url( URLParams::$year.'/pg/programmes')?>">Postgraduate</a></li>
                </ul>
            
                
                <ul class="nav">
                    <?php for ($year=URLParams::$current_year; $year<=(URLParams::$current_year+2); $year++): ?>
                    <li class="<?php echo ( URLParams::$year == $year ? 'active' : false ); ?>"><a href="<?php echo url($year . '/'. URLParams::$type . '/programmes'); ?>"><?php echo $year; ?></a></li>
                    <?php endfor; ?>
                </ul>
            
                
                <?php endif; ?>

                


                <ul class="nav pull-right">
                    <li><a href="#"><?php echo Auth::user()->username; ?></a></li>
                    <li><a href="<?php echo url('logout')?>">Logout</a></li>
                </ul>

            </div><!--/.nav-collapse -->
        </div><!--/.container -->
    </div><!--/.navbar-inner -->
</div><!--/.navbar -->