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
                

                <?php if (URI::segment(2) == 'globalsettings'): ?>

                    <ul class="nav">
                        <?php for ($year=URLParams::$current_year-1; $year<=(URLParams::$current_year+1); $year++): ?>
                        <li class="<?php echo ( URLParams::$year == $year ? 'active' : false ); ?>"><a href="/<?php echo $year; ?>/globalsettings"><?php echo $year; ?></a></li>
                        <?php endfor; ?>
                    </ul>

                <?php elseif (URI::segment(3) == 'programmesettings'): ?>

                    <ul class="nav">
                        <?php for ($year=URLParams::$current_year-1; $year<=(URLParams::$current_year+1); $year++): ?>
                        <li class="<?php echo ( URLParams::$year == $year ? 'active' : false ); ?>"><a href="/<?php echo $year; ?>/<?php echo URLParams::$type; ?>/programmesettings"><?php echo $year; ?></a></li>
                        <?php endfor; ?>
                    </ul>

                <?php elseif (URI::segment(1) == URLParams::$year): ?>

                    <ul class="nav">
                        <?php for ($year=URLParams::$current_year-1; $year<=(URLParams::$current_year+1); $year++): ?>
                        <li class="<?php echo ( URLParams::$year == $year ? 'active' : false ); ?>"><a href="/programmes/<?php echo $year; ?>/<?php echo URLParams::$type; ?>/programmes"><?php echo $year; ?></a></li>
                        <?php endfor; ?>
                    </ul>

                <?php endif;?>

                <ul class="nav pull-right">
                    <li><a href="#"><?php echo Auth::user()->username; ?></a></li>
                    <li><a href="<?php echo url('logout')?>">Logout</a></li>
                </ul>

            </div><!--/.nav-collapse -->
        </div><!--/.container -->
    </div><!--/.navbar-inner -->
</div><!--/.navbar -->