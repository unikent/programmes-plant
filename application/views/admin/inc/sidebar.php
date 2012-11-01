       <ul class="nav nav-list">

<?php
	$selectedYear = date('Y');
	$selectedType = 'ug';
    if(is_numeric(URI::segment(1))) $selectedYear = URI::segment(1);
    if(URI::segment(2) == 'pg') $selectedType = URI::segment(2) ;
	$mainpath = $selectedYear.'/'.$selectedType.'/';
?>

       <li class="nav-header">Main</li>
       <li class="<?php echo ( URI::segment(3) == '' ? 'active' : false )?>"><a href="<?php echo url($mainpath)?>"><i class="icon-home"></i> Home</a></li>
       
        <li class="<?php echo ( URI::segment(3) == 'globals' ? 'active' : false )?>"><a href="<?php echo url($mainpath.'globals')?>"><i class="icon-book"></i> Global Settings</a></li>
       <li class="<?php echo ( URI::segment(3) == 'subjects' ? 'active' : false )?>"><a href="<?php echo url($mainpath.'subjects')?>"><i class="icon-book"></i> Subjects</a></li>
       <li class="<?php echo ( URI::segment(3) == 'supersubjects' ? 'active' : false )?>"><a href="<?php echo url($mainpath.'supersubjects')?>"><i class="icon-book"></i> Super Subjects</a></li>
       <li class="<?php echo ( URI::segment(3) == 'programmes' ? 'active' : false )?>"><a href="<?php echo url($mainpath.'programmes')?>"><i class="icon-book"></i> Programmes</a></li>
          

        <li class="nav-header">User</li>
        <li class="<?php echo ( URI::segment(2) == '12' ? 'active' : false )?>"><a href="<?php echo url('demo/12')?>"><i class="icon-user"></i> My change requests</a></li>
        


        <li class="nav-header">Data</li>
        <li class="<?php echo ( URI::segment(3) == 'campuses' ? 'active' : false )?>"><a href="<?php echo url($mainpath.'campuses')?>"><i class="icon-list-alt"></i> Campuses</a></li>
        <li class="<?php echo ( URI::segment(3) == 'faculties' ? 'active' : false )?>"><a href="<?php echo url($mainpath.'faculties')?>"><i class="icon-list-alt"></i> Faculties</a></li>
        <li class="<?php echo ( URI::segment(3) == 'schools' ? 'active' : false )?>"><a href="<?php echo url($mainpath.'schools')?>"><i class="icon-list-alt"></i> Schools</a></li>
		    <li class="<?php echo ( URI::segment(3) == 'awards' ? 'active' : false )?>"><a href="<?php echo url($mainpath.'awards')?>"><i class="icon-list-alt"></i> Awards</a></li>
        <li class="<?php echo ( URI::segment(3) == 'leaflets' ? 'active' : false )?>"><a href="<?php echo url($mainpath.'leaflets')?>"><i class="icon-list-alt"></i> Leaflets</a></li>
		
     


       <li class="nav-header">Admin</li>
       <li class="<?php echo ( URI::segment(1) == 'changes' ? 'active' : false )?>"><a href="<?php echo url($mainpath.'changes/')?>"><i class="icon-user"></i> Change requests</a></li>
        
       <li class="<?php echo ( URI::segment(1) == 'roles' ? 'active' : false )?>"><a href="<?php echo url($mainpath.'roles')?>"><i class="icon-user"></i> User managment</a></li>
        
     <li class="nav-header">Field Configurtion</li>
     <li class="<?php echo ( URI::segment(4) == 'globals' ? 'active' : false )?>"><a href="<?php echo url($mainpath.'meta/globals')?>"><i class="icon-cog"></i> Globals Config </a></li>
       <li class="<?php echo ( URI::segment(4) == 'subjects' ? 'active' : false )?>"><a href="<?php echo url($mainpath.'meta/subjects')?>"><i class="icon-cog"></i> Subjects Config </a></li>
       <li class="<?php echo ( URI::segment(4) == 'programmes' ? 'active' : false )?>"><a href="<?php echo url($mainpath.'meta/programmes')?>"><i class="icon-cog"></i> Programmes Config </a></li>


       <li class="divider"></li>
       <li class="<?php echo ( URI::segment(1) == 'help' ? 'active' : false )?>"><a href="<?php echo url($mainpath.'help')?>"><i class="icon-flag"></i> Help &amp; Support</a></li>
</ul>