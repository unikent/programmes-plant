<div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="<?php echo url('/')?>">Courses Dashboard</a>
          <div class="nav-collapse">
            <ul class="nav">
              <?php 
              $year = $selectedYear = date('Y');
              $selectedType = 'ug';
              if(is_numeric(URI::segment(1))) $selectedYear = URI::segment(1);
              if(URI::segment(2) == 'pg') $selectedType = URI::segment(2) ;

			  $url = str_replace( URL::Base().'/'.$selectedYear.'/'.$selectedType,'',URL::Current());
			  //strip /
			  if(strlen($url)>1)$url = substr($url,1);
		
              ?>
              <li class="<?php echo (  $selectedYear == ($year) ? 'active' : false )?>"><a href="<?php echo url($year.'/'.( (URI::segment(2) != 'ug' || URI::segment(2) != 'pg') ? 'ug' : URI::segment(2)).'/'.$url)?>"><?php echo  $year?></a></li>
              <li class="<?php echo (  $selectedYear == ($year+1) ? 'active' : false )?>"><a href="<?php echo url(($year+1).'/'.( (URI::segment(2) != 'ug' || URI::segment(2) != 'pg') ? 'ug' : URI::segment(2)).'/'.$url)?>"><?php echo  $year+1?></a></li>
              <li class="<?php echo (  $selectedYear == ($year+2) ? 'active' : false )?>"><a href="<?php echo url(($year+2).'/'.( (URI::segment(2) != 'ug' || URI::segment(2) != 'pg') ? 'ug' : URI::segment(2)).'/'.$url)?>"><?php echo  $year+2?></a></li>
            </ul>

            <ul class="nav">
              <li class="<?php echo (  $selectedType == 'ug' ? 'active' : false )?>"><a href="<?php echo url( $selectedYear.'/'.'ug'.'/'.$url)?>">Undergraduate</a></li>
              <li class="<?php echo (  $selectedType == 'pg' ? 'active' : false )?>"><a href="<?php echo url( $selectedYear.'/'.'pg'.'/'.$url)?>">Postgraduate</a></li>
            </ul>
            
            <ul class="nav pull-right">
            <li><a href="<?php echo url('logout')?>">Logout</a></li>
          </ul>

            <form class="navbar-search pull-right">
              <input type="text" class="search-query" placeholder="UG <?php echo $selectedYear?> search">
            </form>

          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>