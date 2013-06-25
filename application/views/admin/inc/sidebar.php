<div class="navwell">
  <ul class="nav nav-list">
        
        <li class="nav-header">Core Data</li>

         <?php if (Auth::user()->can(array("edit_own_programmes", "view_all_programmes", "edit_all_programmes"))): ?>

        <li class="<?php echo ( (URI::segment(2) == 'ug' && URI::segment(2) != 'fields' && URI::segment(3) == 'programmes') ? 'active' : false )?>"><a href="<?php echo url('/' . URLParams::$year . '/ug/programmes')?>"><i class="icon-home"></i> UG Programmes</a></li>
        <li class="<?php echo ( (URI::segment(2) == 'pg' && URI::segment(2) != 'fields' && URI::segment(3) == 'programmes') ? 'active' : false )?>"><a href="<?php echo url('/' . URLParams::$year . '/pg/programmes')?>"><i class="icon-home"></i> PG Programmes</a></li>
       
        <?php endif; ?>

        <?php if (Auth::user()->can("recieve_edit_requests")): ?>
            <li class="nav-header">Editing</li>
            <li class="<?php echo ( (URI::segment(2) == 'inbox' ) ? 'active' : false )?>"><a href="<?php echo url('editor/inbox')?>"><i class="icon-inbox"></i> Inbox</a></li>
        <?php endif; ?>
        
        <?php if (Auth::user()->can("edit_data")): ?>
            <li class="nav-header">Common Data</li>
            
            <li class="<?php echo ( URI::segment(1) == 'campuses' ? 'active' : false )?>"><a href="<?php echo url('campuses')?>"><i class="icon-list-alt"></i> Campuses</a></li>
            <li class="<?php echo ( URI::segment(1) == 'faculties' ? 'active' : false )?>"><a href="<?php echo url('faculties')?>"><i class="icon-list-alt"></i> Faculties</a></li>
            <li class="<?php echo ( URI::segment(1) == 'schools' ? 'active' : false )?>"><a href="<?php echo url('schools')?>"><i class="icon-list-alt"></i> Schools</a></li>            
            <li class="<?php echo ( URI::segment(1) == 'staff' ? 'active' : false )?>"><a href="<?php echo url('staff')?>"><i class="icon-list-alt"></i> Research Staff</a></li>
			<?php if (Auth::user()->can("edit_immutable_data")): ?>
	            <li class="<?php echo ( (URI::segment(1) != 'fields' && URI::segment(2) == 'globalsettings') ? 'active' : false )?>"><a href="<?php echo url(URLParams::$year.'/globalsettings')?>"><i class="icon-list-alt"></i> Immutable fields</a></li>
	        <?php endif; ?>
			
			<li class="nav-header">UG Data</li>
			<li class="<?php echo ( URI::segment(1) == 'awards' ? 'active' : false )?>"><a href="<?php echo url('/ug/awards'?>"><i class="icon-list-alt"></i> Awards</a></li>
			<li class="<?php echo ( URI::segment(1) == 'leaflets' ? 'active' : false )?>"><a href="<?php echo url('/ug/leaflets')?>"><i class="icon-list-alt"></i> Leaflets</a></li>
			<li class="<?php echo ( URI::segment(1) == 'subjects' ? 'active' : false )?>"><a href="<?php echo url('/ug/subjects')?>"><i class="icon-list-alt"></i> Subjects</a></li>
			<li class="<?php echo ( URI::segment(1) == 'subjectcategories' ? 'active' : false )?>"><a href="<?php echo url('ug/subjectcategories')?>"><i class="icon-list-alt"></i> Subject categories</a></li>
			<?php if (Auth::user()->can("edit_overridable_data")): ?>
	            <li class="<?php echo ( (URI::segment(2) == 'ug' && URI::segment(2) != 'fields' && URI::segment(3) == 'programmesettings') ? 'active' : false )?>"><a href="<?php echo url('/' . URLParams::$year . '/ug/programmesettings')?>"><i class="icon-list-alt"></i> UG Overridable fields</a></li>
			<?php endif; ?>


			<li class="nav-header">PG Data</li>
			<li class="<?php echo ( URI::segment(1) == 'awards' ? 'active' : false )?>"><a href="<?php echo url('/pg/awards'?>"><i class="icon-list-alt"></i> Awards</a></li>
			<li class="<?php echo ( URI::segment(1) == 'leaflets' ? 'active' : false )?>"><a href="<?php echo url('/pg/leaflets')?>"><i class="icon-list-alt"></i> Leaflets</a></li>
			<li class="<?php echo ( URI::segment(1) == 'subjects' ? 'active' : false )?>"><a href="<?php echo url('/pg/subjects')?>"><i class="icon-list-alt"></i> Subjects</a></li>
			<li class="<?php echo ( URI::segment(1) == 'subjectcategories' ? 'active' : false )?>"><a href="<?php echo url('ug/subjectcategories')?>"><i class="icon-list-alt"></i> Subject categories</a></li>
			<?php if (Auth::user()->can("edit_overridable_data")): ?>
            	<li class="<?php echo ( (URI::segment(2) == 'pg' && URI::segment(2) != 'fields' && URI::segment(3) == 'programmesettings') ? 'active' : false )?>"><a href="<?php echo url('/' . URLParams::$year . '/pg/programmesettings')?>"><i class="icon-list-alt"></i> PG Overridable fields</a></li> 
        	<?php endif; ?>
    	<?php endif; ?>

        

        

        <?php if (Auth::user()->can("configure_fields")): ?>
            <li class="nav-header">Field setup</li>

            <li class="<?php echo ( (URI::segment(1) == 'fields' && URI::segment(2) == 'immutable') ? 'active' : false )?>"><a href="<?php echo url('fields/immutable')?>"><i class="icon-cog"></i> Immutable fields </a></li>

            <li class="<?php echo ( (URI::segment(1) == 'ug' && URI::segment(2) == 'fields' && URI::segment(3) == 'standard') ? 'active' : false )?>"><a href="<?php echo url('/ug/fields/standard')?>"><i class="icon-cog"></i> UG Programme fields</a></li>

            <li class="<?php echo ( (URI::segment(1) == 'pg' && URI::segment(2) == 'fields' && URI::segment(3) == 'standard') ? 'active' : false )?>"><a href="<?php echo url('/pg/fields/standard')?>"><i class="icon-cog"></i> PG Programme fields</a></li>

        <?php endif; ?>

        <?php if (Auth::user()->can("manage_users")): ?>
            <li class="nav-header">Managment</li>
            <li class="<?php echo ( (URI::segment(1) == 'users' ) ? 'active' : false )?>"><a href="<?php echo url('users')?>"><i class="icon-user"></i> Users </a></li>
           
        <?php endif; ?>
        <?php if (Auth::user()->can("system_settings")): ?>
             <li class="<?php echo ( (URI::segment(1) == 'settings' ) ? 'active' : false )?>"><a href="<?php echo url('settings')?>"><i class="icon-wrench"></i> System settings </a></li>
        <?php endif; ?>
       


</ul>
</div>
