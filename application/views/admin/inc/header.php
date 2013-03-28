<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="<?php echo url('/')?>">Programmes Plant</a>
            <div class="nav-collapse collapse">
            
                <?php 
                    $year = $selectedYear = '2014';
                    $selectedType = 'ug';
                ?>
                
                <?php if (strstr(URL::Current(), 'fields')): ?>
                
                <?php
                
                    if (URI::segment(1) == 'pg') $selectedType = URI::segment(1);
                    $url = str_replace(URL::Base().'/'.$selectedType.'/', '', URL::Current()); 
                
                ?>
                <ul class="nav">
                    <li class="<?php echo (  $selectedType == 'ug' ? 'active' : false )?>"><a href="<?php echo url( '/'.'ug'.'/'.$url)?>">Undergraduate</a></li>
                </ul>
                
                <ul class="nav">
                    <li class="active"><a href="">Field setup</a></li>
                </ul>
                
                <?php else: ?>
                  
                <?php
                    if (is_numeric(URI::segment(1))) $selectedYear = URI::segment(1);
                    if (URI::segment(2) == 'pg') $selectedType = URI::segment(2);
                    $url = str_replace(URL::Base().'/'.$selectedYear.'/'.$selectedType.'/', '', URL::Current().'/');
                    
                    $href1 = url($year.'/'.$selectedType.'/'.$url);
                    $href2 = url(($year+1).'/'.$selectedType.'/'.$url);
                    $href3 = url(($year+2).'/'.$selectedType.'/'.$url);
                ?>
                
                <ul class="nav">
                  <li class="<?php echo (  $selectedType == 'ug' ? 'active' : false )?>"><a href="<?php echo url( $selectedYear.'/'.'ug'.'/'.$url)?>">Undergraduate</a></li>
                </ul>
                <ul class="nav">
                    <li class="divider-vertical"></li>
                </ul>
                <ul class="nav">
                    <li class="<?php echo ( $selectedYear == $year ? 'active' : false ); ?>"><a href="<?php echo $href1; ?>"><?php echo $year; ?></a></li>
                    <li class="<?php echo ( $selectedYear == ($year+1) ? 'active' : false ); ?>"><a href="<?php echo $href2; ?>"><?php echo $year+1 ?></a></li>
                    <li class="<?php echo ( $selectedYear == ($year+2) ? 'active' : false ); ?>"><a href="<?php echo $href3; ?>"><?php echo $year+2 ?></a></li>
                </ul>
                
                <?php endif ?>
                
                <ul class="nav pull-right">
                    <li><a href="#"><?php echo Auth::user()->username; ?></a></li>
                    <li><a href="<?php echo url('logout')?>">Logout</a></li>
                </ul>

            </div><!--/.nav-collapse -->
        </div><!--/.container -->
    </div><!--/.navbar-inner -->
</div><!--/.navbar -->