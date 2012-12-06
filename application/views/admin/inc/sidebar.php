<div class="navwell">
  <ul class="nav nav-list">

  <?php
	$selectedYear = date('Y');
	$selectedType = 'ug';
    if(is_numeric(URI::segment(1))) $selectedYear = URI::segment(1);
    if(URI::segment(2) == 'pg') $selectedType = URI::segment(2) ;
	$mainpath = $selectedYear.'/'.$selectedType.'/';
	
	if (URI::segment(2) == 'fields') $selectedType = URI::segment(1);
  ?>


        <li class="nav-header">Core Data</li>
        <li class="<?php echo ( (URI::segment(2) != 'fields' && URI::segment(3) == 'programmes') ? 'active' : false )?>"><a href="<?php echo url($mainpath.'programmes')?>"><i class="icon-home"></i> Programmes</a></li>
       
        
        
        
        <li class="nav-header">Data</li>
        <li class="<?php echo ( URI::segment(3) == 'awards' ? 'active' : false )?>"><a href="<?php echo url($mainpath.'awards')?>"><i class="icon-list-alt"></i> Awards</a></li>
        <li class="<?php echo ( URI::segment(3) == 'campuses' ? 'active' : false )?>"><a href="<?php echo url($mainpath.'campuses')?>"><i class="icon-list-alt"></i> Campuses</a></li>
        <li class="<?php echo ( URI::segment(3) == 'faculties' ? 'active' : false )?>"><a href="<?php echo url($mainpath.'faculties')?>"><i class="icon-list-alt"></i> Faculties</a></li>
        <li class="<?php echo ( URI::segment(3) == 'leaflets' ? 'active' : false )?>"><a href="<?php echo url($mainpath.'leaflets')?>"><i class="icon-list-alt"></i> Leaflets</a></li>
        <li class="<?php echo ( URI::segment(3) == 'schools' ? 'active' : false )?>"><a href="<?php echo url($mainpath.'schools')?>"><i class="icon-list-alt"></i> Schools</a></li>
        <li class="<?php echo ( URI::segment(3) == 'subjects' ? 'active' : false )?>"><a href="<?php echo url($mainpath.'subjects')?>"><i class="icon-list-alt"></i> Subjects</a></li>
        <li class="<?php echo ( URI::segment(3) == 'subjectcategories' ? 'active' : false )?>"><a href="<?php echo url($mainpath.'subjectcategories')?>"><i class="icon-list-alt"></i> Subject categories</a></li>
        <li class="<?php echo ( (URI::segment(2) != 'fields' && URI::segment(3) == 'globalsettings') ? 'active' : false )?>"><a href="<?php echo url($mainpath.'globalsettings')?>"><i class="icon-list-alt"></i> XCRI/KIS globals</a></li>
        <li class="<?php echo ( (URI::segment(2) != 'fields' && URI::segment(3) == 'programmesettings') ? 'active' : false )?>"><a href="<?php echo url($mainpath.'programmesettings')?>"><i class="icon-list-alt"></i> Programme globals</a></li>
        

        <li class="nav-header">Field setup</li>
        <li class="<?php echo ( (URI::segment(2) == 'fields' && URI::segment(3) == 'globalsettings') ? 'active' : false )?>"><a href="<?php echo url($selectedType.'/fields/globalsettings')?>"><i class="icon-cog"></i> XCRI/KIS global fields </a></li>
        <li class="<?php echo ( (URI::segment(2) == 'fields' && URI::segment(3) == 'programmesettings') ? 'active' : false )?>"><a href="<?php echo url($selectedType.'/fields/programmesettings')?>"><i class="icon-cog"></i> Programme global fields </a></li>
        <li class="<?php echo ( (URI::segment(2) == 'fields' && URI::segment(3) == 'programmes') ? 'active' : false )?>"><a href="<?php echo url($selectedType.'/fields/programmes')?>"><i class="icon-cog"></i> Programme fields </a></li>
</ul>
</div>
